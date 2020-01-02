<?php

// ========================================================================================
//
// COMPONENT.CLASS
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
// ABOUT OBJECTS:
// 
// The most important objects in the game have been grouped into a class "objects". This
// includes modules, technologies, units, components and so on. 
//
// NOTES:
//
// Components are like material required in order to build buildings or train units.
// Components are optional, other objects can be bought with resources alone if the game
// designers wishes to do that. The reason for components is an extended economic system
// that requires players to craft components before being able to train the more powerful
// units (e.g. use resource "Iron" to craft a "Sword" - require "Swords" to train "Swordman").
//
// a system like this has been seen in the old PC game classic "Knights & Merchants"
//
// ========================================================================================

class d13_object_component extends d13_object_base

{
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @ Calls base object constructor with an array based argument list
	// ----------------------------------------------------------------------------------------
	public

	function __construct($args)
	{
		parent::__construct($args);
	}

		// ----------------------------------------------------------------------------------------
	// checkStatsExtended
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function checkStatsExtended()
	{
		global $d13;

		
		
	}


}

// =====================================================================================EOF

