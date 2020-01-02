<?php

// ========================================================================================
//
// OBJECT.FACTORY.CLASS
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
// A simple factory class that is responsible for creating new instances of objects.
//
//
// ========================================================================================

class d13_object_factory

{
    public static

    function create($type, $objectId, $node)
    {

        global $d13;
        
        $args = array();
        $args['supertype']	= $type;
        $args['obj_id'] 	= $objectId;
        $args['node'] 		= $node;

        switch ($args['supertype']) {
        
            case 'resource':
                return new d13_object_resource($args);
                break;

            case 'technology':
                return new d13_object_technology($args);
                break;

            case 'component':
                return new d13_object_component($args);
                break;

            case 'unit':
                return new d13_object_unit($args);
                break;

            case 'turret':
                return new d13_object_turret($args);
                break;

			case 'shield':
                return new d13_object_shield($args);
                break;
            
            case 'buff':
                return new d13_object_buff($args);
                break;
           
			
            default:
                return NULL;
                break;
        }
    }
    
}