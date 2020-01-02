<?php

// ========================================================================================
//
// PROFILER.CLASS
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
// a simple class to measure execution time, queries and number of queries per page.
//
// ========================================================================================

class d13_profiler

{
	private $starttime, $endtime, $duration;

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------
	public

	function __construct()
	{
		$this->starttime = 0;
		$this->endtime = 0;
		$this->duration = 0;
		$this->subdata = '';
		$this->profile_start();
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function profile_start()
	{
		$this->starttime = $this->microtime_float();
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function profile_end($note = "")
	{
		$this->endtime = $this->microtime_float();
		$this->duration = round(($this->endtime - $this->starttime) * 1000, 3);
		$fd = fopen("./logs/profile.txt", "a");
		fwrite($fd, ($this->duration) . "," . $note . "\n");
		fclose($fd);
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function profile_get($note = "")
	{
		$this->profile_end($note);
		return $this->duration;
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	
	// ----------------------------------------------------------------------------------------
	// debugLog
	// 
	// 
	// ----------------------------------------------------------------------------------------
	public
	
	function debugLog($page)
	{
	
		global $d13;
	
		$queries = array();
		$count 		= $d13->getQueryCount();
		$queries 	= $d13->getQueries();
	
		$d13->logger("======================= PROFILE (".$page.") =======================");
		#$d13->logger(" Page..........: " . $page );
		$d13->logger( "Execution Time: " . $d13->profileGet() . ' ' . $d13->getLangUI("ms"));

		$d13->logger("-------------------- QUERIES (".$count.") --------------------");
		
		
		foreach ($queries as $query) {
			$d13->logger($query);
			#$d13->logger($this->generateCallTrace());
		}
		$d13->logger("=================================================================");
		
	}
	
	// ----------------------------------------------------------------------------------------
	// generateCallTrace
	// 
	// 
	// ----------------------------------------------------------------------------------------
	public
	
	function generateCallTrace()
	{
		$e = new Exception();
		$trace = explode("\n", $e->getTraceAsString());
		// reverse array to make steps line up chronologically
		$trace = array_reverse($trace);
		array_shift($trace); // remove {main}
		array_pop($trace); // remove call to this method
		$length = count($trace);
		$result = array();
	
		for ($i = 0; $i < $length; $i++)
		{
			$result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
		}
	
		return "\t" . implode("\n\t", $result);
	}
	
}

// =====================================================================================EOF

