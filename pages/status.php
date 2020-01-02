<?php

//========================================================================================
//
// STATUS
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

//----------------------------------------------------------------------------------------
// PROCESS MODEL
//----------------------------------------------------------------------------------------

global $d13;

$message = NULL;

if (isset($_SESSION[CONST_PREFIX . 'User']['id'])) {

	if (isset($_GET['userId'])) {
		$userId = $_GET['userId'];
	} else {
		$userId = $_SESSION[CONST_PREFIX . 'User']['id'];
	}

	$user = new d13_user($userId);


} else {
	$message = $d13->getLangUI("accessDenied");
}

//----------------------------------------------------------------------------------------
// RENDER OUTPUT
//----------------------------------------------------------------------------------------

$d13->templateRender("status", $user->getTemplateVariables());

//=====================================================================================EOF

?>