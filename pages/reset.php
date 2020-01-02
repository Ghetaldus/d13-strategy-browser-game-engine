<?php

// ========================================================================================
//
// RESET
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
//
// ----------------------------------------------------------------------------------------

global $d13;
$d13->dbQuery('start transaction');
$message = '';

if (isset($_POST['name'], $_POST['email'])) {
	foreach($_POST as $key => $value) {
		$_POST[$key] = d13_misc::clean($value);
	}

	if ((($_POST['name'] != '')) && ($_POST['email'] != '')) {
		$user = new d13_user();
		$status = $user->get('name', $_POST['name']);
		if ($status == 'done') {
			$newPass = rand(1000000000, 9999999999);
			$status = $user->resetPassword($_POST['email'], $newPass);
			include (CONST_INCLUDE_PATH . 'api/email.api.php');

			$body = CONST_GAME_TITLE . ' ' . $d13->getLangUI("newPassword") . ': ' . $newPass;
			if ($status == 'done') {
				$status = email(CONST_EMAIL, CONST_GAME_TITLE, $user->data['email'], CONST_GAME_TITLE . ' ' . $d13->getLangUI("resetPassword") , $body);
				$message = $d13->getLangUI($status);
			}
		}
		else {
			$message = $d13->getLangUI($status);
		}
	}
	else {
		$message = $d13->getLangUI("insufficientData");
	}
}

if ((isset($status)) && ($status == 'error')) {
	$d13->dbQuery('rollback');
}
else {
	$d13->dbQuery('commit');
}

// ----------------------------------------------------------------------------------------
// Setup Template Variables
// ----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;
$tvars['tvar_user_email'] = '';
$tvars['tvar_user_name'] = '';

if (isset($_SESSION[CONST_PREFIX . 'User']['email'], $_SESSION[CONST_PREFIX . 'User']['name'])) {
	$tvars['tvar_user_email'] = $_SESSION[CONST_PREFIX . 'User']['email'];
	$tvars['tvar_user_name'] = $_SESSION[CONST_PREFIX . 'User']['name'];
}

// ----------------------------------------------------------------------------------------
// Parse & Render Template
// ----------------------------------------------------------------------------------------

$d13->templateRender("reset", $tvars);

// =====================================================================================EOF

?>