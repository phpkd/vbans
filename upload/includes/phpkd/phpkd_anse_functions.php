<?php
/*==================================================================================*\
|| ################################################################################ ||
|| # Product Name: vB Username Change Manager               Version: 1.0.0 Beta.1 # ||
|| # Licence Number: {LicenceNumber}
|| # ---------------------------------------------------------------------------- # ||
|| # 																			  # ||
|| #          Copyright ©2005-2008 PHP KingDom, Ltd. All Rights Reserved.         # ||
|| #       This file may not be redistributed in whole or significant part.       # ||
|| # 																			  # ||
|| # ------------- vB Username Change Manager IS NOT FREE SOFTWARE -------------- # ||
|| #           http://www.phpkd.org | http://www.phpkd.org/license.html           # ||
|| ################################################################################ ||
\*==================================================================================*/


class vBulletinHook_phpkd_anse extends vBulletinHook
{
	var $last_called = '';

	function vBulletinHook_phpkd_anse(&$pluginlist, &$hookusage)
	{
		$this->pluginlist =& $pluginlist;
		$this->hookusage =& $hookusage;
	}

	function &fetch_hook_object($hookname)
	{
		$this->last_called = $hookname;
		return parent::fetch_hook_object($hookname);
	}
}


function phpkd_anse_prepare($notice, $where)
{
	global $vbulletin, $vbphrase, $_noticeid, $notice_html_navbar, $notice_html_usercp;

	$phpkd_anse_regex = '/\{\w+:\w+\}/';
	preg_match_all($phpkd_anse_regex, $notice, $matches);
	foreach ($matches AS $match)
	{
		foreach ($match AS $matchbit)
		{
			$newmatch = str_replace(array('{', '}'), array('', ''), $matchbit);
			$finalbit = explode(':', $newmatch);

			if (isset($vbulletin->{$finalbit[0]}[$finalbit[1]]))
			{
				if ($where == 'navbar')
				{
					$notice_html_navbar = str_replace($matchbit, $vbulletin->{$finalbit[0]}[$finalbit[1]], $notice_html_navbar);
				}

				if ($where == 'usercp')
				{
					$notice_html_usercp = str_replace($matchbit, $vbulletin->{$finalbit[0]}[$finalbit[1]], $notice_html_usercp);
				}
			}
		}
	}
}


function phpkd_anse_fetch_dismiss()
{
	global $vbulletin;

	$dismissed = array();
	if ($vbulletin->userinfo['phpkd_anse_dismissed'])
	{
		$notices = $vbulletin->db->query_read_slave("
			SELECT noticeid
			FROM " . TABLE_PREFIX . "notice
			WHERE noticeid IN(" . $vbulletin->userinfo['phpkd_anse_dismissed'] . ")
			ORDER BY displayorder, title
		");

		while ($notice = $vbulletin->db->fetch_array($notices))
		{
			$dismissed[$notice['noticeid']] = $notice['noticeid'];
		}

		$vbulletin->db->free_result($notices);
	}

	return $dismissed;
}


function phpkd_anse_save($dismissed)
{
	global $vbulletin;

	if (is_array($dismissed) AND !empty($dismissed))
	{
		$userdata =& datamanager_init('User', $vbulletin, ERRTYPE_STANDARD);
		$userdata->set_existing($vbulletin->userinfo);
		$userdata->do_set('phpkd_anse_dismissed', implode(',', $dismissed));
		$userdata->save();
	}
	else
	{
		$dismissed = NULL;
		$userdata =& datamanager_init('User', $vbulletin, ERRTYPE_STANDARD);
		$userdata->set_existing($vbulletin->userinfo);
		$userdata->do_set('phpkd_anse_dismissed', $dismissed);
		$userdata->save();
	}
}



/*==================================================================================*\
|| ################################################################################ ||
|| # Downloaded: {Downloaded}
|| # CVS: $RCSfile$ - $Revision: 10000 $
|| ################################################################################ ||
\*==================================================================================*/
?>