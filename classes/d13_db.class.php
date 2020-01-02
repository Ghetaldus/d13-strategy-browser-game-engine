<?php

// ========================================================================================
//
// DATABASE.CLASS
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
// Just a very simple wrapper for database access, like database abstraction layer.
//
// ========================================================================================

class d13_db

{

	private $db, $queries, $lastquery;

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public

	function __construct()
	{
		$this->create(CONST_DB_HOST, CONST_DB_USER, CONST_DB_PASS, CONST_DB_NAME);
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function create($dbHost, $dbUser, $dbPass, $dbName)
	{
		$this->db = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
		if ($this->db->connect_error) {
			die('Connect Error (' . $this->db->connect_errno . ') ' . $mysqli->connect_error);
		}
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public

	function query($query)
	{
		if ($result = $this->db->query($query)) {
			
			if (CONST_FLAG_PROFILER) {
				if ($query != "commit" && $query != "start transaction") {
					$this->lastquery = $query;
				} else if ($query == "commit" && $query != "start transaction") {
					$this->queries[] = $this->lastquery;
				}
			}
			
			return $result;
		
		} else {
			echo "Query:".$query." Error:".$this->db->error;
		}

		return NULL;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public

	function fetch($result)
	{
		return $result->fetch_array(MYSQLI_ASSOC);
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public

	function real_escape_string($string)
	{
		return $this->db->real_escape_string($string);
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public

	function affected_rows()
	{
		return $this->db->affected_rows;
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getQueryCount()
	{
		return count($this->queries);
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getQueries()
	{
	
		return $this->queries;
	
	}
	
	
}

// =====================================================================================EOF