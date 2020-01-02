<?php

//========================================================================================
//
// LOGOUT
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

global $d13;

unset($_POST);
unset($_GET);
session_unset();

header('Location: '.CONST_DIRECTORY.'index.php?p=index');

//=====================================================================================EOF

?>
