<?php

// ========================================================================================
//
// ACCOUNT
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

if (isset($_SESSION[CONST_PREFIX . 'User']['id'])) {

	// - - - - - Locales

	$locales = '<option value="' . $_SESSION[CONST_PREFIX . 'User']['locale'] . '">' . $_SESSION[CONST_PREFIX . 'User']['locale'] . '</option>';
	if ($handle = opendir('locales')) {
		while (false != ($file = readdir($handle))) {
			$fileName = explode('.', $file);
			$fileName = $fileName[0];
			if (($file != '.') && ($file != '..') && ($_SESSION[CONST_PREFIX . 'User']['locale'] != $fileName)) {
				$locales.= '<option value="' . $fileName . '">' . $fileName . '</option>';
			}
		}

		closedir($handle);
	}

	// - - - - - Templates

	$templates = '<option value="' . $_SESSION[CONST_PREFIX . 'User']['template'] . '">' . $_SESSION[CONST_PREFIX . 'User']['template'] . '</option>';
	if ($handle = opendir('templates')) {
		while (false != ($file = readdir($handle))) {
			if ((strpos($file, '.') === false) && ($_SESSION[CONST_PREFIX . 'User']['template'] != $file)) {
				$templates.= '<option value="' . $file . '">' . $file . '</option>';
			}
		}

		closedir($handle);
	}

	// - - - - - Colors
	global $template;
	$colors = '<option value="' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">' . $_SESSION[CONST_PREFIX . 'User']['color'] . '</option>';
	foreach($d13->getGeneral('colors') as $color) {
		if ($_SESSION[CONST_PREFIX . 'User']['color'] != $color) {
			$colors.= '<option value="' . $color . '">' . $color . '</option>';
		}
	}

	// - - - - - User

	$user = new d13_user();
	$user->get('id', $_SESSION[CONST_PREFIX . 'User']['id']);
	if (isset($_GET['action'], $_POST['password'])) {
		foreach($_POST as $key => $value) $_POST[$key] = d13_misc::clean($value);
		switch ($_GET['action']) {

			// - - - - -

		case 'misc':
			if ($_SESSION[CONST_PREFIX . 'User']['password'] == sha1($_POST['password'])) {
				$user->data['email'] = $_SESSION[CONST_PREFIX . 'User']['email'] = $_POST['email'];
				$user->data['sitter'] = $_SESSION[CONST_PREFIX . 'User']['sitter'] = $_POST['sitter'];
				$user->data['locale'] = $_SESSION[CONST_PREFIX . 'User']['locale'] = $_POST['locale'];
				$user->data['color'] = $_SESSION[CONST_PREFIX . 'User']['color'] = $_POST['color'];
				$user->data['template'] = $_SESSION[CONST_PREFIX . 'User']['template'] = $_POST['template'];
				$message = $d13->getLangUI($user->set());
			}
			else {
				$message = $d13->getLangUI("wrongPassword");
			}

			break;

			// - - - - -

		case 'preferences':
			if ($_SESSION[CONST_PREFIX . 'User']['password'] == sha1($_POST['password'])) $message = $d13->getLangUI($user->setPreference($_POST['name'], $_POST['value']));
			else $message = $d13->getLangUI("wrongPassword");
			break;

			// - - - - -

		case 'blocklist':
			if ($_SESSION[CONST_PREFIX . 'User']['password'] == sha1($_POST['password'])) $message = $d13->getLangUI($user->setBlocklist($_POST['name']));
			else $message = $d13->getLangUI("wrongPassword");
			break;

			// - - - - -

		case 'password':
			if ($_SESSION[CONST_PREFIX . 'User']['password'] == sha1($_POST['password']))
			if ($_POST['newPassword'] == $_POST['rePassword']) {
				$user->data['password'] = $_SESSION[CONST_PREFIX . 'User']['password'] = sha1($_POST['newPassword']);
				$message = $d13->getLangUI($user->set());
			}
			else $message = $d13->getLangUI("rePassNotMatch");
			else $message = $d13->getLangUI("wrongPassword");
			break;

			// - - - - -

		case 'remove':
			if ($_SESSION[CONST_PREFIX . 'User']['password'] == sha1($_POST['password'])) {
				$status = d13_user::remove($user->data['id']);
				if ($status == 'done') header('Location: index.php?p=logout');
				else $message = $d13->getLangUI($status);
			}
			else $message = $d13->getLangUI("wrongPassword");
			break;
		}
	}

	$user->getPreferences('id');
	$preferenceNames = '';
	$preferenceValues = array();
	foreach($user->preferences as $key => $preference) {
		$preferenceNames.= '<option value="' . $preference['name'] . '">' . $d13->getLangUI($preference['name']) . '</option>';
		$preferenceValues[$key] = '"' . $preference['value'] . '"';
	}

	$user->getBlocklist();
	$blocklistNames = '';
	foreach($user->blocklist as $item) $blocklistNames.= $item['senderName'] . ' ';
}
else header('Location: ?p=logout');

if ((isset($status)) && ($status == 'error')) {
	$d13->dbQuery('rollback');
}
else {
	$d13->dbQuery('commit');
}

// ----------------------------------------------------------------------------------------
// PROCESS VIEW
// ----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;
$tvars['tvar_user_email'] = $_SESSION[CONST_PREFIX . 'User']['email'];
$tvars['tvar_user_sitter'] = $_SESSION[CONST_PREFIX . 'User']['sitter'];
$tvars['tvar_locales'] = $locales;
$tvars['tvar_templates'] = $templates;
$tvars['tvar_colors'] = $colors;
$tvars['tvar_preferenceNames'] = $preferenceNames;
$tvars['tvar_user_preferences'] = $user->preferences[0]['value'];
$tvars['tvar_blocklistNames'] = $blocklistNames;
$tvars['tvar_preferenceValues'] = implode(', ', $preferenceValues);

// ----------------------------------------------------------------------------------------
// RENDER OUTPUT
// ----------------------------------------------------------------------------------------

$d13->templateRender("account", $tvars);

// =====================================================================================EOF

?>