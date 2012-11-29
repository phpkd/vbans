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


/**
* Adds ondblclick events to appropriate elements for notices dismiss
*
* @package	vBulletin
* @version	$Revision: 26385 $
* @date		$Date: 2008-04-22 05:40:28 -0500 (Tue, 22 Apr 2008) $
* @author	Kier Darby, vBulletin Development Team
*
* @param	string	The ID of the notice list element (usually 'noticelist')
*/
function vB_AJAX_Noticelist_Init(noticelistid)
{
	if (AJAX_Compatible && (typeof vb_disable_ajax == 'undefined' || vb_disable_ajax < 2))
	{
		var spans = fetch_tags(fetch_object(noticelistid), 'span');
		for (var i = 0; i < spans.length; i++)
		{
			if (spans[i].hasChildNodes() && spans[i].id && spans[i].id.substr(0, 5) == 'span_')
			{
				var anchors = fetch_tags(spans[i], 'a');
				for (var j = 0; j < anchors.length; j++)
				{
					if (anchors[j].rel && anchors[j].rel.indexOf('vB::AJAX') != -1)
					{
						var details = spans[i].id.split('_');

						switch (details[1])
						{
							case 'dismissnotice':
							{
								spans[i].style.cursor = pointer_cursor;
								spans[i].ondblclick = vB_AJAX_NoticeList_Events.prototype.dismissicon_doubleclick;
							}
							break;
							case 'restorenotice':
							{
								spans[i].style.cursor = pointer_cursor;
								spans[i].ondblclick = vB_AJAX_NoticeList_Events.prototype.restoreicon_doubleclick;
							}
							break;
						}

						break;
					}
				}
			}
		}
	}
}

// #############################################################################
// vB_AJAX_NoticeDismiss
// #############################################################################

/**
* Class to handle notice dismiss with XML-HTTP
*
* @package	vBulletin
* @version	$Revision: 26385 $
* @date		$Date: 2008-04-22 05:40:28 -0500 (Tue, 22 Apr 2008) $
* @author	Kier Darby, vBulletin Development Team
*
* @param	object	The clickable dismiss icon for the notice
*/
function vB_AJAX_NoticeDismiss(obj)
{
	this.obj = obj;
	this.noticeid = this.obj.id.substr(this.obj.id.lastIndexOf('_') + 1);
	this.divobj = fetch_object('div_navbarnotice_' + this.noticeid);
	this.tableobj = fetch_object(NOTICELIST_NAVBAR);
	this.divs = fetch_tags(fetch_object(NOTICELIST_NAVBAR), 'div');
	this.count = this.divs.length;

	// =============================================================================
	// vB_AJAX_NoticeDismiss methods

	/**
	* Function to dismiss noctice
	*/
	this.toggle = function()
	{
		YAHOO.util.Connect.asyncRequest("POST", "ajax.php?do=phpkd_anse_dismiss&noticeid=" + this.noticeid, {
			success: this.handle_ajax_response,
			failure: vBulletin_AJAX_Error_Handler,
			timeout: vB_Default_Timeout,
			scope: this
		}, SESSIONURL + "securitytoken=" + SECURITYTOKEN + "&do=phpkd_anse_dismiss&noticeid=" + this.noticeid);
	}

	/**
	* Handles AJAX response request
	*
	* @param	object	YUI AJAX
	*/
	this.handle_ajax_response = function(ajax)
	{
		if (ajax.responseXML)
		{
			this.divobj.style.display = ajax.responseXML.getElementsByTagName('divstyle')[0].firstChild.nodeValue;

			if (this.count == 1 && ajax.responseXML.getElementsByTagName('divstyle')[0].firstChild.nodeValue == 'none')
			{
				this.tableobj.style.display = ajax.responseXML.getElementsByTagName('divstyle')[0].firstChild.nodeValue;
			}
		}
	}

	// send the data
	this.toggle();
}



// #############################################################################
// vB_AJAX_NoticeRestore
// #############################################################################

/**
* Class to handle notice restore with XML-HTTP
*
* @package	vBulletin
* @version	$Revision: 26385 $
* @date		$Date: 2008-04-22 05:40:28 -0500 (Tue, 22 Apr 2008) $
* @author	Kier Darby, vBulletin Development Team
*
* @param	object	The clickable restore icon for the notice
*/
function vB_AJAX_NoticeRestore(obj)
{
	this.obj = obj;
	this.noticeid = this.obj.id.substr(this.obj.id.lastIndexOf('_') + 1);
	this.divobj = fetch_object('div_usercpnotice_' + this.noticeid);
	this.tableobj = fetch_object(NOTICELIST_USERCP);
	this.divs = fetch_tags(fetch_object(NOTICELIST_USERCP), 'div');
	this.count = this.divs.length;

	// =============================================================================
	// vB_AJAX_NoticeRestore methods

	/**
	* Function to restore noctice
	*/
	this.toggle = function()
	{
		YAHOO.util.Connect.asyncRequest("POST", "ajax.php?do=phpkd_anse_restore&noticeid=" + this.noticeid, {
			success: this.handle_ajax_response,
			failure: vBulletin_AJAX_Error_Handler,
			timeout: vB_Default_Timeout,
			scope: this
		}, SESSIONURL + "securitytoken=" + SECURITYTOKEN + "&do=phpkd_anse_restore&noticeid=" + this.noticeid);
	}

	/**
	* Handles AJAX response request
	*
	* @param	object	YUI AJAX
	*/
	this.handle_ajax_response = function(ajax)
	{
		if (ajax.responseXML)
		{
			this.divobj.style.display = ajax.responseXML.getElementsByTagName('divstyle')[0].firstChild.nodeValue;

			if (this.count == 1 && ajax.responseXML.getElementsByTagName('divstyle')[0].firstChild.nodeValue == 'none')
			{
				this.tableobj.style.display = ajax.responseXML.getElementsByTagName('divstyle')[0].firstChild.nodeValue;
			}
		}
	}

	// send the data
	this.toggle();
}


// #############################################################################
// NoticeList event handlers

/**
* Class to handle events in the noticelist
*/
function vB_AJAX_NoticeList_Events()
{
}

/**
* Handles double-clicking on dismiss icon toggle notice state
*/
vB_AJAX_NoticeList_Events.prototype.dismissicon_doubleclick = function(e)
{
	noticedismiss = new vB_AJAX_NoticeDismiss(this);
};


/**
* Handles double-clicking on restore icon toggle notice state
*/
vB_AJAX_NoticeList_Events.prototype.restoreicon_doubleclick = function(e)
{
	noticerestore = new vB_AJAX_NoticeRestore(this);
};


/*======================================================================*\
|| ####################################################################
|| # 
|| # 
|| ####################################################################
\*======================================================================*/