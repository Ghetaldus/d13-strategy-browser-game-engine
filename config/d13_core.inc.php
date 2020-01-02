<?php

//========================================================================================
//
// CORE.INC
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

//- - - - - INCLUDES
include("d13_config.inc.php");

spl_autoload_register(function ($className){

    $className = str_replace('Controller','.control',$className);
    $fullPath = CONST_INCLUDE_PATH."classes/".$className.".class.php";
    
    if(is_file($fullPath)){
        require_once  $fullPath;
        return;
    }
    $fullPath = CONST_INCLUDE_PATH."control/".strtolower($className).".php";

    if(is_file($fullPath)){
        require_once  $fullPath;
        return;
    }
});

//- - - - - CREATE ENGINE
$d13 = new d13_engine();

//=====================================================================================EOF

?>