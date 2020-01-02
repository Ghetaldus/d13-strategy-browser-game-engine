<?php

// ========================================================================================
//
// COLLECTION.CLASS
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//
// ABOUT CLASSES:
//
// Represents the lowest layer, next to the database. All logic checks must be performed
// by a controller beforehand. Any class function calls directly access the database. 
// 
// NOTES:
//
// A wrapper class for IteratorAggregate used instead of arrays to handle large amounts
// of data (Still arrays at their core). Allows to iterate through object lists in the
// same way as arrays.
//
// ========================================================================================

class d13_collection implements IteratorAggregate
{
    private $data = array();
	
	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
    public

    function __construct($data)
    {
        $this->data = $data;
    }

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
    public

    function getIterator()
    {
        return new ArrayIterator($this->data);
    }
	
	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
    public function getAll()
    {
        return $this->data;
    }
    
	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
    public function getByKey($key, $field)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key][$field];
        } else {
            return NULL;
        }
    }
    
	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
    public function getByID($id, $field = "")
    {
        foreach ($this->data as $entry) {
            if ($entry['id'] == $id) {
                if ($field = "" || !isset($entry[$field])) {
                    return $entry;
                } else {
                    return $entry[$field];
                }
            }
        }

        return NULL;
    }
    
	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
    public function get($indices)
    {

        if (empty($indices)) {
            return $this->data;
        }

        $array = $this->data;
        if (!is_array($indices)) {
            $indices = array(
                $indices
            );
        }

        foreach ($indices as $index) {
            if (isset($array[$index])) {
                $array = $array[$index];
            } else {
                return FALSE;
            }
        }

        if ((array)$array === $array) {
            return (array)$array;
        } else if ((string)$array === $array) {
            return (string)$array;
        } else if ((integer)$array === $array) {
            return (integer)$array;
        }

        return $array;

    }
}

// ====================================================================================EOF