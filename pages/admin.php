<?php

// ========================================================================================
//
// ADMIN
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================
// ----------------------------------------------------------------------------------------
// PROCESS MODEL
// ----------------------------------------------------------------------------------------

global $d13;
$message = NULL;
$d13->dbQuery('start transaction');

if ((isset($_SESSION[CONST_PREFIX . 'User']['access'])) && ($_SESSION[CONST_PREFIX . 'User']['access'] >= 3)) {
	if (isset($_GET['action'], $_POST['password'])) {
		foreach($_POST as $key => $value)
		if ($key == 'maxIdleTime') $_POST[$key] = d13_misc::clean($value, 'numeric');
		else $_POST[$key] = d13_misc::clean($value);
		switch ($_GET['action']) {
		case 'vars':
			if ($_SESSION[CONST_PREFIX . 'User']['password'] == sha1($_POST['password'])) $message = $d13->getLangUI(flags::set($_POST['name'], $_POST['value']));
			else $message = $d13->getLangUI("wrongPassword");
			break;

		case 'bans':
			$user = new d13_user();
			$status = $user->get('name', $_POST['name']);
			if ($_SESSION[CONST_PREFIX . 'User']['password'] == sha1($_POST['password']))
			if ($status == 'done') {
				if ($_POST['level'] > - 1) {
					$user->data['access'] = $_POST['access'];
					$message = $d13->getLangUI($user->set());
				}
				else $message = $d13->getLangUI(d13_user::remove($user->data['id']));
			}
			else $message = $d13->getLangUI($status);
			else $message = $d13->getLangUI("wrongPassword");
			break;

		case 'accounts':
			if ($_SESSION[CONST_PREFIX . 'User']['password'] == sha1($_POST['password']))
			if ($_POST['maxIdleTime'] > 0) {
				$output = d13_user::removeInactive($_POST['maxIdleTime']);
				$message = $output['found'] . ' ' . $d13->getLangUI("accountsFound") . ', ' . $output['removed'] . ' ' . $d13->getLangUI("removed");
			}
			else $message = $d13->getLangUI("insufficientData");
			else $message = $d13->getLangUI("wrongPassword");
			break;

		case 'username':
			if ($_SESSION[CONST_PREFIX . 'User']['password'] == sha1($_POST['password']))
			if ($_POST['name'] != '') {
				$user = new d13_user();
				$status = $user->get('name', $_POST['name']);
				if ($status == 'done') $message = '<div>' . $user->data['name'] . '</div><div><div class="cell">' . $d13->getLangUI("ip") . ': </div><div class="cell">' . $user->data['ip'] . '</div></div><div><div class="cell">' . $d13->getLangUI("email") . ': </div><div class="cell">' . $user->data['email'] . '</div></div>';
				else $message = $d13->getLangUI($status);
			}
			else $message = $d13->getLangUI("insufficientData");
			else $message = $d13->getLangUI("wrongPassword");
			break;

		case 'blacklist':
			if (isset($_GET['blacklistAction'], $_POST['type'], $_POST['value']))
			if ($_SESSION[CONST_PREFIX . 'User']['password'] == sha1($_POST['password'])) switch ($_GET['blacklistAction']) {
			case 'add':
				$message = $d13->getLangUI(d13_blacklist::add($_POST['type'], $_POST['value']));
				break;

			case 'remove':
				foreach($_POST['value'] as $value) $message = $d13->getLangUI(d13_blacklist::remove($_POST['type'], $value));
				break;
			}
			else $message = $d13->getLangUI("wrongPassword");
			else $message = $d13->getLangUI("insufficientData");
			break;
		}
	}

	$flags = $d13->flags->get('id');
	$flagNames = '';
	$flagValues = array();
	foreach($flags as $key => $flag) {
		$flagNames.= '<option value="' . $flag['name'] . '">' . $d13->getLangUI($flag['name']) . '</option>';
		$flagValues[$key] = '"' . $flag['value'] . '"';
	}

	$blacklist = array(
		'ip' => d13_blacklist::get('ip') ,
		'email' => d13_blacklist::get('email')
	);
	$temp = '';
	foreach($blacklist['ip'] as $item) $temp.= '<option value="' . $item['value'] . '">' . $item['value'] . '</option>';
	$blacklist['ip'] = $temp;
	$temp = '';
	foreach($blacklist['email'] as $item) $temp.= '<option value="' . $item['value'] . '">' . $item['value'] . '</option>';
	$blacklist['email'] = $temp;
}
else header('Location: logout.php');

if ((isset($status)) && ($status == 'error')) $d13->dbQuery('rollback');
else $d13->dbQuery('commit');

// ----------------------------------------------------------------------------------------
// PROCESS VIEW
// ----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;
$tvars['tvar_flagNames'] = $flagNames;
$tvars['tvar_flagsValue'] = $flags[0]['value'];
$tvars['tvar_flagsIP'] = $blacklist['ip'];
$tvars['tvar_flagsEmail'] = $blacklist['email'];
$tvars['tvar_flagValues'] = implode(', ', $flagValues);

if (isset($_GET['action'])) {
	$tvars['tvar_getAction'] = 'document.getElementById("action").selectedIndex=indexOfSelectValue(document.getElementById("action"), "' . $_GET['action'] . '");';
}

if (isset($_GET['blacklistAction'])) {
	$tvars['tvar_getblacklistAction'] = 'document.getElementById("blacklistAction").selectedIndex=indexOfSelectValue(document.getElementById("blacklistAction"), "' . $_GET['blacklistAction'] . '");';
}

if (isset($_POST['type'])) {
	$tvars['tvar_postType'] = 'document.getElementById("blacklist_' . $_GET['blacklistAction'] . '_type").selectedIndex=indexOfSelectValue(document.getElementById("blacklist_' . $_GET['blacklistAction'] . '_type"), "' . $_POST['type'] . '");';
}

// ----------------------------------------------------------------------------------------
// RENDER OUTPUT
// ----------------------------------------------------------------------------------------

$d13->templateRender("admin", $tvars);

// =====================================================================================EOF

?>