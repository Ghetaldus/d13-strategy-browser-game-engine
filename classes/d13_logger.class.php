<?php

// ========================================================================================
//
// LOGGER.CLASS
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ABOUT CLASSES:
//
// Represents the lowest layer, next to the database. All logic checks must be performed
// by a controller beforehand. Any class function calls directly access the database. 
// 
// NOTES:
//
// A very simple logger to output error messages to the textlog. Will be expanded or replaced
// with a more sophisticated solution later.
//
// ========================================================================================

class d13_logger

{

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------
	public

	function __construct()
	{
	
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------
	public

	function log($note = "")
	{
		$fd = fopen("./logs/log.txt", "a");
		fwrite($fd, $note . "\n");
		fclose($fd);
	}
	
}

// =====================================================================================EOF

