1. Open the file admincp/notice.php & search in it for the following code:
<?php
		foreach (array('condition1', 'condition2', 'condition3') AS $condition)
?>

And replace it with the following code:
<?php
		foreach (array('condition1', 'condition2', 'condition3', 'condition4', 'condition5') AS $condition)
?>
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// ######################################################################

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
2. In the same file admincp/notice.php & search in it for the following code:
<?php
				'" . $db->escape_string(trim($criteria['condition3'])) . "'
?>

And replace it with the following code:
<?php
				'" . $db->escape_string(trim($criteria['condition3'])) . "',
				'" . $db->escape_string(trim($criteria['condition4'])) . "',
				'" . $db->escape_string(trim($criteria['condition5'])) . "'
?>
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// ######################################################################

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
3. In the same file admincp/notice.php & search in it for the following code:
<?php
			(noticeid, criteriaid, condition1, condition2, condition3)
?>

And replace it with the following code:
<?php
			(noticeid, criteriaid, condition1, condition2, condition3, condition4, condition5)
?>
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// ######################################################################

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
4. In the same file admincp/notice.php & search in it for the following code:
<?php
				"<span id=\"span_$criteria_option_id\">" . construct_phrase($vbphrase[$criteria_option_id . '_criteria'], $criteria_option[0], $criteria_option[1], $criteria_option[2]) . '</span></label>'
?>

And replace it with the following code:
<?php
				"<span id=\"span_$criteria_option_id\">" . construct_phrase($vbphrase[$criteria_option_id . '_criteria'], $criteria_option[0], $criteria_option[1], $criteria_option[2], $criteria_option[3], $criteria_option[4]) . '</span></label>'
?>
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// ######################################################################

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
5. In the same file admincp/notice.php & search in it for the following code:
<?php
				"</label>$criteria_option[2]<label>"
?>

And replace it with the following code:
<?php
				"</label>$criteria_option[2]<label>",
				"</label>$criteria_option[3]<label>",
				"</label>$criteria_option[4]<label>"
?>

// ###############################################################################
// ###############################################################################
// ## Edits for the Advanced Notice System Enhancements Completed successfully! ##
// ###############################################################################
// ###############################################################################