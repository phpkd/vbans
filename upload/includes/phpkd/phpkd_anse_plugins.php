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


if (!defined('VB_AREA'))
{
	exit;
}

$hookobj =& vBulletinHook::init();
require_once(DIR . '/includes/phpkd/phpkd_anse_functions.php');

switch (strval($hookobj->last_called))
{
	case 'ajax_start':
		{
			if ($_POST['do'] == 'phpkd_anse_dismiss')
			{
				$vbulletin->input->clean_array_gpc('p', array(
					'noticeid' => TYPE_UINT
				));


				// allow dimiss if...
				if ($vbulletin->userinfo['userid'] > 0 AND $vbulletin->noticecache[$vbulletin->GPC['noticeid']]['persistent'] == 1 AND ($permissions['phpkdanse'] & $vbulletin->bf_ugp_phpkdanse['candismiss']))
				{
					$dismissed_notices = phpkd_anse_fetch_dismiss();
					if (!in_array($vbulletin->GPC['noticeid'], $dismissed_notices))
					{
						$dismissed_notices[] = $vbulletin->GPC['noticeid'];
					}
					phpkd_anse_save($dismissed_notices);
					$divstyle = "none";
				}
				else
				{
					$divstyle = "inherit";
				}

				$xml = new vB_AJAX_XML_Builder($vbulletin, 'text/xml');
				$xml->add_tag('divstyle', $divstyle);
				$xml->print_xml();
			}

			if ($_REQUEST['do'] == 'phpkd_anse_restore')
			{
				$vbulletin->input->clean_array_gpc('r', array(
					'noticeid' => TYPE_UINT
				));


				// allow dimiss if...
				if ($vbulletin->userinfo['userid'] > 0 AND $vbulletin->noticecache[$vbulletin->GPC['noticeid']]['persistent'] == 1 AND ($permissions['phpkdanse'] & $vbulletin->bf_ugp_phpkdanse['canrestore']))
				{
					$dismissed_notices = phpkd_anse_fetch_dismiss();
					if (in_array($vbulletin->GPC['noticeid'], $dismissed_notices))
					{
						unset($dismissed_notices[$vbulletin->GPC['noticeid']]);
					}
					phpkd_anse_save($dismissed_notices);
					$divstyle = "none";
				}
				else
				{
					$divstyle = "inherit";
				}

				$xml = new vB_AJAX_XML_Builder($vbulletin, 'text/xml');
				$xml->add_tag('divstyle', $divstyle);
				$xml->print_xml();
			}
		}
		break;
	case 'notices_list_criteria':
		{
			// Subscriptions Criteria
			require_once(DIR . '/includes/class_paid_subscription.php');
			$subobj = new vB_PaidSubscription($vbulletin);
			// cache all the subscriptions
			$subobj->cache_user_subscriptions();

			$subscription_options1 = array('0' => $vbphrase['phpkd_anse_no_active_paid_subs']);
			foreach ($subobj->subscriptioncache AS $subscriptionid => $subscription)
			{
				$subscription_options1["$subscriptionid"] = $vbphrase['sub' . $subscriptionid . '_title'];
			}

			$subscription_options2 = array('1' => $vbphrase['phpkd_anse_paid_sub_expired'], '2' => $vbphrase['phpkd_anse_paid_sub_x_expire_in_y']);

			$criteria_options['phpkd_anse_subscription_x_y_in_z'] = array(
				'<select name="criteria[phpkd_anse_subscription_x_y_in_z][condition1]" tabindex="1">' .
					construct_select_options($subscription_options1, (empty($criteria_cache['phpkd_anse_subscription_x_y_in_z']) ? 0 : $criteria_cache['phpkd_anse_subscription_x_y_in_z']['condition1'])) .
				'</select>',
				'<select name="criteria[phpkd_anse_subscription_x_y_in_z][condition2]" tabindex="1">' .
					construct_select_options($subscription_options2, (empty($criteria_cache['phpkd_anse_subscription_x_y_in_z']) ? 1 : $criteria_cache['phpkd_anse_subscription_x_y_in_z']['condition2'])) .
				'</select>',
				'<input type="text" name="criteria[phpkd_anse_subscription_x_y_in_z][condition3]" size="10" class="bginput" tabindex="1" value="' .
					(empty($criteria_cache['phpkd_anse_subscription_x_y_in_z']) ? 15 : $criteria_cache['phpkd_anse_subscription_x_y_in_z']['condition3']) .
				'" />'
			);


			// THIS_SCRIPT Criteria
			$this_script_cases = array('in' => $vbphrase['phpkd_anse_this_script_is_in'], 'notin' => $vbphrase['phpkd_anse_this_script_isnot_in']);
			$criteria_options['phpkd_anse_this_script_is_x'] = array(
				'<select name="criteria[phpkd_anse_this_script_is_x][condition1]" tabindex="1">' .
					construct_select_options($this_script_cases, (empty($criteria_cache['phpkd_anse_this_script_is_x']) ? "in" : $criteria_cache['phpkd_anse_this_script_is_x']['condition1'])) .
				'</select>',
				'<input type="text" name="criteria[phpkd_anse_this_script_is_x][condition2]" size="10" class="bginput" tabindex="1" value="' .
					(empty($criteria_cache['phpkd_anse_this_script_is_x']) ? "" : $criteria_cache['phpkd_anse_this_script_is_x']['condition2']) .
				'" />'
			);


			// Language Criteria
			require_once(DIR . '/includes/functions_misc.php');
			$language_options = fetch_language_titles_array();
			$criteria_options['phpkd_anse_language_is_x'] = array(
				'<select name="criteria[phpkd_anse_language_is_x][condition1]" tabindex="1">' .
					construct_select_options($language_options, $criteria_cache['phpkd_anse_language_is_x']['condition1']) .
				'</select>'
			);


			// Browsing X Thread Criteria
			$criteria_options['phpkd_anse_browsing_thread_x'] = array(
				'<input type="text" name="criteria[phpkd_anse_browsing_thread_x][condition1]" size="10" class="bginput" tabindex="1" value="' .
					(empty($criteria_cache['phpkd_anse_browsing_thread_x']) ? "" : $criteria_cache['phpkd_anse_browsing_thread_x']['condition1']) .
				'" />'
			);


			// Browsing X Poll Results/Edit X Poll Criteria
			$criteria_options['phpkd_anse_browsing_poll_x'] = array(
				'<input type="text" name="criteria[phpkd_anse_browsing_poll_x][condition1]" size="10" class="bginput" tabindex="1" value="' .
					(empty($criteria_cache['phpkd_anse_browsing_poll_x']) ? "" : $criteria_cache['phpkd_anse_browsing_poll_x']['condition1']) .
				'" />'
			);


			// Poll Voting Criteria
			$polls = $db->query_read("
				SELECT poll.*, thread.pollid, thread.title, open, threadid, forumid
				FROM " . TABLE_PREFIX . "poll AS poll
				INNER JOIN " . TABLE_PREFIX . "thread AS thread USING (pollid)
				WHERE open <> 10 AND visible = 1 AND active = 1
				ORDER BY poll.pollid
			");

			while ($poll = $db->fetch_array($polls))
			{
				$poll_options["$poll[pollid]"] = construct_phrase($vbphrase['phpkd_anse_not_voted_on_poll_x'], $poll['question'], $poll['title']);
			}
			$db->free_result($polls);

			$vote_on_poll_cases = array('yes' => $vbphrase['phpkd_anse_vote_on_poll_x_y_yes'], 'no' => $vbphrase['phpkd_anse_vote_on_poll_x_y_no']);
			$criteria_options['phpkd_anse_vote_on_poll_x_y'] = array(
				'<select name="criteria[phpkd_anse_vote_on_poll_x_y][condition1]" tabindex="1">' .
					construct_select_options($vote_on_poll_cases, $criteria_cache['phpkd_anse_vote_on_poll_x_y']['condition1']) .
				'</select>',
				'<select name="criteria[phpkd_anse_vote_on_poll_x_y][condition2]" tabindex="1">' .
					construct_select_options($poll_options, $criteria_cache['phpkd_anse_vote_on_poll_x_y']['condition2']) .
				'</select>'
			);

			// Browsing X Profile Criteria
			$criteria_options['phpkd_anse_browsing_profile_x'] = array(
				'<input type="text" name="criteria[phpkd_anse_][condition1]" size="10" class="bginput" tabindex="1" value="' .
					(empty($criteria_cache['phpkd_anse_']) ? "" : $criteria_cache['phpkd_anse_']['condition1']) .
				'" />'
			);


			// Browsing X Album Criteria
			$criteria_options['phpkd_anse_browsing_album_x'] = array(
				'<input type="text" name="criteria[phpkd_anse_browsing_album_x][condition1]" size="10" class="bginput" tabindex="1" value="' .
					(empty($criteria_cache['phpkd_anse_browsing_album_x']) ? "" : $criteria_cache['phpkd_anse_browsing_album_x']['condition1']) .
				'" />'
			);


			// Browsing X Social Group Criteria
			$criteria_options['phpkd_anse_browsing_socialgroup_x'] = array(
				'<input type="text" name="criteria[phpkd_anse_browsing_socialgroup_x][condition1]" size="10" class="bginput" tabindex="1" value="' .
					(empty($criteria_cache['phpkd_anse_browsing_socialgroup_x']) ? "" : $criteria_cache['phpkd_anse_browsing_socialgroup_x']['condition1']) .
				'" />'
			);


			// $_SERVER Criteria
			$_server_options = array(
				'PHP_SELF' => 'PHP_SELF',
				'GATEWAY_INTERFACE' => 'GATEWAY_INTERFACE',
				'SERVER_ADDR' => 'SERVER_ADDR',
				'SERVER_NAME' => 'SERVER_NAME',
				'SERVER_SOFTWARE' => 'SERVER_SOFTWARE',
				'SERVER_PROTOCOL' => 'SERVER_PROTOCOL',
				'REQUEST_METHOD' => 'REQUEST_METHOD',
				'REQUEST_TIME' => 'REQUEST_TIME',
				'QUERY_STRING' => 'QUERY_STRING',
				'DOCUMENT_ROOT' => 'DOCUMENT_ROOT',
				'HTTP_ACCEPT' => 'HTTP_ACCEPT',
				'HTTP_ACCEPT_CHARSET' => 'HTTP_ACCEPT_CHARSET',
				'HTTP_ACCEPT_ENCODING' => 'HTTP_ACCEPT_ENCODING',
				'HTTP_ACCEPT_LANGUAGE' => 'HTTP_ACCEPT_LANGUAGE',
				'HTTP_CONNECTION' => 'HTTP_CONNECTION',
				'HTTP_HOST' => 'HTTP_HOST',
				'HTTP_REFERER' => 'HTTP_REFERER',
				'HTTP_USER_AGENT' => 'HTTP_USER_AGENT',
				'HTTPS' => 'HTTPS',
				'REMOTE_ADDR' => 'REMOTE_ADDR',
				'REMOTE_HOST' => 'REMOTE_HOST',
				'REMOTE_PORT' => 'REMOTE_PORT',
				'SCRIPT_FILENAME' => 'SCRIPT_FILENAME',
				'SERVER_ADMIN' => 'SERVER_ADMIN',
				'SERVER_PORT' => 'SERVER_PORT',
				'SERVER_SIGNATURE' => 'SERVER_SIGNATURE',
				'PATH_TRANSLATED' => 'PATH_TRANSLATED',
				'SCRIPT_NAME' => 'SCRIPT_NAME',
				'REQUEST_URI' => 'REQUEST_URI',
				'PHP_AUTH_DIGEST' => 'PHP_AUTH_DIGEST',
				'PHP_AUTH_USER' => 'PHP_AUTH_USER',
				'PHP_AUTH_PW' => 'PHP_AUTH_PW',
				'AUTH_TYPE' => 'AUTH_TYPE',
			);
			$criteria_options['phpkd_anse_server_environment_x'] = array(
				'<select name="criteria[phpkd_anse_server_environment_x][condition1]" tabindex="1">' .
					construct_select_options($_server_options, (empty($criteria_cache['phpkd_anse_server_environment_x']) ? "PHP_SELF" : $criteria_cache['phpkd_anse_server_environment_x']['condition1'])) .
				'</select>',
				'<input type="text" name="criteria[phpkd_anse_server_environment_x][condition2]" size="10" class="bginput" tabindex="1" value="' .
					(empty($criteria_cache['phpkd_anse_server_environment_x']) ? "" : $criteria_cache['phpkd_anse_server_environment_x']['condition2']) .
				'" />'
			);


			// $_REQUEST & $vbulletin->GPC['xxx'] Parameters Criteria
			$criteria_options['phpkd_anse_request_params_x_y'] = array(
				'<input type="text" name="criteria[phpkd_anse_request_params_x_y][condition1]" size="10" class="bginput" tabindex="1" value="' .
					(empty($criteria_cache['phpkd_anse_request_params_x_y']) ? "" : $criteria_cache['phpkd_anse_request_params_x_y']['condition1']) .
				'" />',
				'<input type="text" name="criteria[phpkd_anse_request_params_x_y][condition2]" size="10" class="bginput" tabindex="1" value="' .
					(empty($criteria_cache['phpkd_anse_request_params_x_y']) ? "" : $criteria_cache['phpkd_anse_request_params_x_y']['condition2']) .
				'" />'
			);


			// Date/Time Criteria
			$weekdays = array(-1 => '*', 0 => $vbphrase['sunday'], $vbphrase['monday'], $vbphrase['tuesday'], $vbphrase['wednesday'], $vbphrase['thursday'], $vbphrase['friday'], $vbphrase['saturday']);
			$hours = array(-1 => '*', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23);
			$days = array(-1 => '*', 1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31);
			$minutes = array(-1 => '*');
			for ($x = 0; $x < 60; $x++)
			{
				$minutes[] = $x;
			}

			$seconds = array(-1 => '*');
			for ($y = 0; $y < 60; $y++)
			{
				$seconds[] = $y;
			}
			$criteria_options['phpkd_anse_date_time_v_w_x_y_z'] = array(
				'<select name="criteria[phpkd_anse_date_time_v_w_x_y_z][condition1]" tabindex="1">' .
					construct_select_options($weekdays, (empty($criteria_cache['phpkd_anse_date_time_v_w_x_y_z']) ? -1 : $criteria_cache['phpkd_anse_date_time_v_w_x_y_z']['condition1'])) .
				'</select>',
				'<select name="criteria[phpkd_anse_date_time_v_w_x_y_z][condition2]" tabindex="1">' .
					construct_select_options($days, (empty($criteria_cache['phpkd_anse_date_time_v_w_x_y_z']) ? -1 : $criteria_cache['phpkd_anse_date_time_v_w_x_y_z']['condition2'])) .
				'</select>',
				'<select name="criteria[phpkd_anse_date_time_v_w_x_y_z][condition3]" tabindex="1">' .
					construct_select_options($hours, (empty($criteria_cache['phpkd_anse_date_time_v_w_x_y_z']) ? -1 : $criteria_cache['phpkd_anse_date_time_v_w_x_y_z']['condition3'])) .
				'</select>',
				'<select name="criteria[phpkd_anse_date_time_v_w_x_y_z][condition4]" tabindex="1">' .
					construct_select_options($minutes, (empty($criteria_cache['phpkd_anse_date_time_v_w_x_y_z']) ? -1 : $criteria_cache['phpkd_anse_date_time_v_w_x_y_z']['condition4'])) .
				'</select>',
				'<select name="criteria[phpkd_anse_date_time_v_w_x_y_z][condition5]" tabindex="1">' .
					construct_select_options($seconds, (empty($criteria_cache['phpkd_anse_date_time_v_w_x_y_z']) ? -1 : $criteria_cache['phpkd_anse_date_time_v_w_x_y_z']['condition5'])) .
				'</select>'
			);


			// Age Younger/Older Than Criteria
			$age_cases = array('younger' => $vbphrase['phpkd_anse_age_x_y_younger'], 'youngerequal' => $vbphrase['phpkd_anse_age_x_y_youngerequal'], 'equal' => $vbphrase['phpkd_anse_age_x_y_equal'], 'older' => $vbphrase['phpkd_anse_age_x_y_older'], 'olderequal' => $vbphrase['phpkd_anse_age_x_y_olderequal']);
			$criteria_options['phpkd_anse_age_x_y'] = array(
				'<select name="criteria[phpkd_anse_age_x_y][condition1]" tabindex="1">' .
					construct_select_options($age_cases, (empty($criteria_cache['phpkd_anse_age_x_y']) ? "equal" : $criteria_cache['phpkd_anse_age_x_y']['condition1'])) .
				'</select>',
				'<input type="text" name="criteria[phpkd_anse_age_x_y][condition2]" size="10" class="bginput" tabindex="1" value="' .
					(empty($criteria_cache['phpkd_anse_age_x_y']) ? "" : $criteria_cache['phpkd_anse_age_x_y']['condition2']) .
				'" />'
			);


			// Registration Period Criteria
			$regperiod_cases = array('less' => $vbphrase['phpkd_anse_regperiod_x_y_less'], 'lessequal' => $vbphrase['phpkd_anse_regperiod_x_y_lessequal'], 'equal' => $vbphrase['phpkd_anse_regperiod_x_y_equal'], 'more' => $vbphrase['phpkd_anse_regperiod_x_y_more'], 'moreequal' => $vbphrase['phpkd_anse_regperiod_x_y_moreequal']);
			$criteria_options['phpkd_anse_regperiod_x_y'] = array(
				'<select name="criteria[phpkd_anse_regperiod_x_y][condition1]" tabindex="1">' .
					construct_select_options($regperiod_cases, (empty($criteria_cache['phpkd_anse_regperiod_x_y']) ? "equal" : $criteria_cache['phpkd_anse_regperiod_x_y']['condition1'])) .
				'</select>',
				'<input type="text" name="criteria[phpkd_anse_regperiod_x_y][condition2]" size="10" class="bginput" tabindex="1" value="' .
					(empty($criteria_cache['phpkd_anse_regperiod_x_y']) ? "" : $criteria_cache['phpkd_anse_regperiod_x_y']['condition2']) .
				'" />'
			);
		}
		break;
	case 'notices_check_criteria':
		{
			// Subscriptions Criteria
			if ($criteriaid == 'phpkd_anse_subscription_x_y_in_z')
			{
				require_once(DIR . '/includes/class_paid_subscription.php');
				$subobj = new vB_PaidSubscription($vbulletin);

				// fetch all active subscriptions the user is subscribed too
				$susers = $vbulletin->db->query_read_slave("
					SELECT *
					FROM " . TABLE_PREFIX . "subscriptionlog
					WHERE userid = " . $vbulletin->userinfo['userid']
				);

				$subscribed = array();
				while ($suser = $vbulletin->db->fetch_array($susers))
				{
					$subscribed["$suser[subscriptionid]"] = $suser;
				}

				// cache all the subscriptions
				$subobj->cache_user_subscriptions();
				foreach ($subobj->subscriptioncache AS $subscription)
				{
					if ($subscription['active'] == 1)
					{
						$subscriptionid =& $subscription['subscriptionid'];
						if ($conditions[0] == 0 AND !isset($subscribed["$subscription[subscriptionid]"]))
						{
							$display_notices["$noticeid"] = $noticeid;
						}
						else if ($conditions[0] == $subscriptionid AND $conditions[1] == 1 AND isset($subscribed["$subscription[subscriptionid]"]) AND $subscribed["$subscription[subscriptionid]"]["expirydate"] < TIMENOW)
						{
							$period["$subscription[subscriptionid]"] = floor((TIMENOW - $subscribed["$subscription[subscriptionid]"]["expirydate"]) / 86400);
							$display_notices["$noticeid"] = $noticeid;
						}
						else if ($conditions[0] == $subscriptionid AND $conditions[1] == 2 AND isset($subscribed["$subscription[subscriptionid]"]) AND $subscribed["$subscription[subscriptionid]"]["expirydate"] > TIMENOW)
						{
							$period["$subscription[subscriptionid]"] = floor(($subscribed["$subscription[subscriptionid]"]["expirydate"] - TIMENOW) / 86400);
							if ($period["$subscription[subscriptionid]"] <= $conditions[2])
							{
								$display_notices["$noticeid"] = $noticeid;
							}
						}
					}
				}

				$abort = true;
			}


			// THIS_SCRIPT Criteria
			if ($criteriaid == 'phpkd_anse_this_script_is_x')
			{
				if ($conditions[0] == 'in')
				{
					if (THIS_SCRIPT != $conditions[1])
					{
						$abort = true;
					}
				}
				else if ($conditions[0] == 'notin')
				{
					if (THIS_SCRIPT == $conditions[1])
					{
						$abort = true;
					}
				}
			}


			// Language Criteria
			if ($criteriaid == 'phpkd_anse_language_is_x')
			{
				if (LANGUAGEID != intval($conditions[0]))
				{
					$abort = true;
				}
			}


			// Browsing X Thread Criteria
			if ($criteriaid == 'phpkd_anse_browsing_thread_x')
			{
				if ($threadinfo['threadid'] != intval($conditions[0]) OR THIS_SCRIPT != 'showthread')
				{
					$abort = true;
				}
			}


			// Browsing X Poll Results/Edit X Poll Criteria
			if ($criteriaid == 'phpkd_anse_browsing_poll_x')
			{
				if ($_REQUEST['pollid'] != intval($conditions[0]) OR THIS_SCRIPT != 'poll')
				{
					$abort = true;
				}
			}


			// Poll Voting Criteria
			if ($criteriaid == 'phpkd_anse_vote_on_poll_x_y')
			{
				if ($conditions[0] == 'yes')
				{
					$poll_vote = $vbulletin->db->query_first("
						SELECT poll.*, thread.pollid, thread.title, thread.open, thread.threadid, thread.forumid
						FROM " . TABLE_PREFIX . "poll AS poll
						INNER JOIN " . TABLE_PREFIX . "thread AS thread USING (pollid)
						LEFT JOIN " . TABLE_PREFIX . "pollvote AS pollvote ON (pollvote.pollid = poll.pollid AND pollvote.userid = '" . $vbulletin->userinfo['userid'] . "')
						WHERE thread.open <> '10' AND thread.visible = '1' AND poll.active = '1'
							AND poll.pollid = '" . $conditions[1] . "' AND pollvote.userid IS NOT NULL
						ORDER BY poll.pollid
					");

					if (!$poll_vote['pollid'])
					{
						$abort = true;
					}
				}
				else if ($conditions[0] == 'no')
				{
					$poll_vote = $vbulletin->db->query_first("
						SELECT poll.*, thread.pollid, thread.title, thread.open, thread.threadid, thread.forumid
						FROM " . TABLE_PREFIX . "poll AS poll
						INNER JOIN " . TABLE_PREFIX . "thread AS thread USING (pollid)
						LEFT JOIN " . TABLE_PREFIX . "pollvote AS pollvote ON (pollvote.pollid = poll.pollid AND pollvote.userid = '" . $vbulletin->userinfo['userid'] . "')
						WHERE thread.open <> '10' AND thread.visible = '1' AND poll.active = '1'
							AND poll.pollid = '" . $conditions[1] . "' AND pollvote.userid IS NULL
						ORDER BY poll.pollid
					");

					if (!$poll_vote['pollid'])
					{
						$abort = true;
					}
				}
			}


			// Browsing X Profile Criteria
			if ($criteriaid == 'phpkd_anse_browsing_profile_x')
			{
				if ($userinfo['userid'] != intval($conditions[0]) OR THIS_SCRIPT != 'member')
				{
					$abort = true;
				}
			}


			// Browsing X Album Criteria
			if ($criteriaid == 'phpkd_anse_browsing_album_x')
			{
				if ($_REQUEST['albumid'] != intval($conditions[0]) OR THIS_SCRIPT != 'album')
				{
					$abort = true;
				}
			}


			// Browsing X Social Group Criteria
			if ($criteriaid == 'phpkd_anse_browsing_socialgroup_x')
			{
				if ($_REQUEST['groupid'] != intval($conditions[0]) OR THIS_SCRIPT != 'group')
				{
					$abort = true;
				}
			}


			// $_SERVER Criteria
			if ($criteriaid == 'phpkd_anse_server_environment_x')
			{
				if ($_SERVER[$conditions[0]] != $conditions[1])
				{
					$abort = true;
				}
			}


			// $_REQUEST & $vbulletin->GPC['xxx'] Parameters Criteria
			if ($criteriaid == 'phpkd_anse_request_params_x_y')
			{
				$request = explode('=>', $conditions[0]);
				if ($_REQUEST[$request[0]] == $request[1])
				{
					$params = explode(';', $conditions[1]);
					if (is_array($params) AND !empty($params)) 
					{
						foreach ($params AS $param)
						{
							$param_bits = explode('=>', $param);
							if ($_REQUEST[$param_bits[0]] == $param_bits[1])
							{
								$abort = true;
								// $display_notices["$noticeid"] = $noticeid;
							}
						}
					}
				}

				$abort = true;
			}


			// Date/Time Criteria
			if ($criteriaid == 'phpkd_anse_date_time_v_w_x_y_z')
			{
				$todaydate = getdate(TIMENOW);
				for ($x = 0; $x <= 4; $x++)
				{
					if ($conditions[$x] != -1)
					{
						$cond[$x] = $conditions[$x];
					}
				}

				if (isset($cond[0]) AND isset($cond[1]))
				{
					unset($cond[1]);
				}

				$condarray = array();
				foreach ($cond AS $key => $val)
				{
					switch ($key)
					{
						case 0:
							$condarray['wday'] = $val;
							break;
						case 1:
							$condarray['mday'] = $val;
							break;
						case 2:
							$condarray['hours'] = $val;
							break;
						case 3:
							$condarray['minutes'] = $val;
							break;
						case 4:
							$condarray['seconds'] = $val;
							break;
					}
				}

				if (isset($condarray['wday']) AND isset($condarray['mday']))
				{
					unset($condarray['mday']);
				}

				$intersect = array_intersect_key($condarray, $todaydate);

				$finalok = array();
				foreach ($intersect AS $conkey => $conval)
				{
					if ($condarray[$conkey] == $todaydate[$conkey])
					{
						$finalok[$conkey] = $conval;
					}
				}

				if (count($finalok) != count($intersect))
				{
					$abort = true;
				}
			}


			// Age Younger/Older Than Criteria
			if ($criteriaid == 'phpkd_anse_age_x_y')
			{
				if ($vbulletin->userinfo['birthday'])
				{
					$bday = explode('-', $vbulletin->userinfo['birthday']);
					$year = vbdate('Y', TIMENOW, false, false);
					if ($year > $bday[2] AND $bday[2] != '0000')
					{
						$age = $year - $bday[2];
						if ($age)
						{
							switch ($conditions[0])
							{
								case 'younger':
									if ($age >= $conditions[1])
									{
										$abort = true;
									}
									break;
								case 'youngerequal':
									if (!check_notice_criteria_between($age, "", $conditions[1]))
									{
										$abort = true;
									}
									break;
								case 'equal':
									if (!check_notice_criteria_between($age, $conditions[1], $conditions[1]))
									{
										$abort = true;
									}
									break;
								case 'older':
									if ($age <= $conditions[1])
									{
										$abort = true;
									}
									break;
								case 'olderequal':
									if (!check_notice_criteria_between($age, $conditions[1], ""))
									{
										$abort = true;
									}
									break;
							}
						}
					}
				}
			}


			// Registration Period Criteria
			if ($criteriaid == 'phpkd_anse_regperiod_x_y')
			{
				if ($vbulletin->userinfo['joindate'])
				{
					$requiredperiod = $conditions[1];
					$joinperiod = floor((TIMENOW - $vbulletin->userinfo['joindate']) / 86400);
					if ($joinperiod)
					{
						switch ($conditions[0])
						{
							case 'less':
								if ($joinperiod >= $requiredperiod)
								{
									$abort = true;
								}
								break;
							case 'lessequal':
								if (!check_notice_criteria_between($joinperiod, "", $requiredperiod))
								{
									$abort = true;
								}
								break;
							case 'equal':
								if (!check_notice_criteria_between($joinperiod, $requiredperiod, $requiredperiod))
								{
									$abort = true;
								}
								break;
							case 'more':
								if ($joinperiod <= $requiredperiod)
								{
									$abort = true;
								}
								break;
							case 'moreequal':
								if (!check_notice_criteria_between($joinperiod, $requiredperiod, ""))
								{
									$abort = true;
								}
								break;
						}
					}
				}
			}
		}
		break;
	case 'cache_templates':
		{
			if (!empty($vbulletin->noticecache) AND is_array($vbulletin->noticecache))
			{
				$phpkd_anse_noticebit_navbar = '';

				$globaltemplates[] = 'phpkd_anse_notices_navbar';
				$globaltemplates[] = 'phpkd_anse_noticebit_navbar';

				if (THIS_SCRIPT == 'usercp')
				{
					$globaltemplates[] = 'phpkd_anse_notices_usercp';
					$globaltemplates[] = 'phpkd_anse_noticebit_usercp';
				}
			}
		}
		break;
	case 'global_setup_complete':
		{
			$vbulletin->templatecache['navbar'] = str_replace($vbulletin->options['phpkd_anse_notices_navbar'], fetch_template('phpkd_anse_notices_navbar'), $vbulletin->templatecache['navbar']);
		}
		break;
	case 'usercp_complete':
		{
			if (!empty($vbulletin->noticecache) AND is_array($vbulletin->noticecache))
			{
				$phpkd_anse_noticebit_usercp = '';
				require_once(DIR . '/includes/functions_notice.php');
				foreach (fetch_relevant_notice_ids() AS $notice_id)
				{
					$notice_html_usercp = str_replace(array('{musername}', '{username}', '{userid}', '{sessionurl}'), array($vbulletin->userinfo['musername'], $vbulletin->userinfo['username'], $vbulletin->userinfo['userid'], $vbulletin->session->vars['sessionurl']), $vbphrase["notice_{$notice_id}_html"]);
					if (!in_array($notice_id, explode(',', $vbulletin->userinfo['phpkd_anse_dismissed'])))
					{
						continue;
					}

					if (array_key_exists('subscription_x_y_in_z', $vbulletin->noticecache[$notice_id]))
					{
						$notice_html_usercp = str_replace(array('{subxperiod}', '{subxtitle}', '{subxexpiredate}', '{subxexpireddate}'), array($period[$vbulletin->noticecache[$notice_id]['subscription_x_y_in_z']['0']], $vbphrase['sub' . $vbulletin->noticecache[$notice_id]['subscription_x_y_in_z']['0'] . '_title'], vbdate($vbulletin->options['dateformat'], ($period[$vbulletin->noticecache[$notice_id]['subscription_x_y_in_z']['0']] * 86400) + TIMENOW), vbdate($vbulletin->options['dateformat'], (TIMENOW - $period[$vbulletin->noticecache[$notice_id]['subscription_x_y_in_z']['0']] * 86400))), $notice_html_usercp);
					}

					if (!$show['phpkd_anse_ajax_js_usercp'] AND ($permissions['phpkdanse'] & $vbulletin->bf_ugp_phpkdanse['canrestore']))
					{
						$show['phpkd_anse_ajax_js_usercp'] = true;
					}

					if ($vbulletin->noticecache[$notice_id]['persistent'] == 1 AND ($permissions['phpkdanse'] & $vbulletin->bf_ugp_phpkdanse['canrestore']))
					{
						$show['phpkd_anse_restore'] = true;
					}
					else
					{
						$show['phpkd_anse_restore'] = false;
					}

					$show['phpkd_anse_notices_usercp'] = true;
					exec_switch_bg();
					phpkd_anse_prepare($notice_html_usercp, 'usercp');
					eval('$phpkd_anse_noticebit_usercp .= "' . fetch_template('phpkd_anse_noticebit_usercp') . '";');
				}
			}

			// $template_hook[usercp_main_pos1] .= fetch_template('phpkd_anse_notices_usercp');
			$vbulletin->templatecache['USERCP'] = str_replace('$template_hook[usercp_main_pos1]', '$template_hook[usercp_main_pos1]' . fetch_template('phpkd_anse_notices_usercp'), $vbulletin->templatecache['USERCP']);
		}
		break;
	case 'notices_noticebit':
		{
			$show['notices'] = false;
			if (in_array($_noticeid, explode(',', $vbulletin->userinfo['phpkd_anse_dismissed'])))
			{
				continue;
			}

			if (array_key_exists('subscription_x_y_in_z', $vbulletin->noticecache[$_noticeid]))
			{
				$notice_html_navbar = str_replace(array('{subxperiod}', '{subxtitle}', '{subxexpiredate}', '{subxexpireddate}'), array($period[$vbulletin->noticecache[$_noticeid]['subscription_x_y_in_z']['0']], $vbphrase['sub' . $vbulletin->noticecache[$_noticeid]['subscription_x_y_in_z']['0'] . '_title'], vbdate($vbulletin->options['dateformat'], ($period[$vbulletin->noticecache[$_noticeid]['subscription_x_y_in_z']['0']] * 86400) + TIMENOW), vbdate($vbulletin->options['dateformat'], (TIMENOW - $period[$vbulletin->noticecache[$_noticeid]['subscription_x_y_in_z']['0']] * 86400))), $notice_html);
			}

			if (!$show['phpkd_anse_ajax_js_navbar'] AND ($permissions['phpkdanse'] & $vbulletin->bf_ugp_phpkdanse['candismiss']))
			{
				$show['phpkd_anse_ajax_js_navbar'] = true;
			}

			if ($vbulletin->noticecache[$_noticeid]['persistent'] == 1 AND ($permissions['phpkdanse'] & $vbulletin->bf_ugp_phpkdanse['candismiss']))
			{
				$show['phpkd_anse_dismiss'] = true;
			}
			else
			{
				$show['phpkd_anse_dismiss'] = false;
			}

			$show['phpkd_anse_notices_navbar'] = true;
			exec_switch_bg();
			phpkd_anse_prepare($notice_html_navbar, 'navbar');
			eval('$phpkd_anse_noticebit_navbar .= "' . fetch_template('phpkd_anse_noticebit_navbar') . '";');
		}
		break;
	default:
		{
			if (!empty($vbulletin->noticecache) AND is_array($vbulletin->noticecache))
			{
				foreach ($vbulletin->noticecache as $noticecache)
				{
					if (array_key_exists('subscription_x_y_in_z', $noticecache))
					{
						fetch_phrase_group('subscription');
					}
				}
			}

			$hookobj = new vBulletinHook_phpkd_anse($hookobj->pluginlist, $hookobj->hookusage);
		}
		break;
}

/*==================================================================================*\
|| ################################################################################ ||
|| # Downloaded: {Downloaded}
|| # CVS: $RCSfile$ - $Revision: 10000 $
|| ################################################################################ ||
\*==================================================================================*/
?>