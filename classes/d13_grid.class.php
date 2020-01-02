<?php

// ========================================================================================
//
// GRID.CLASS
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
// This class accesses the map and is only responsible for retrieving a certain part of the
// map table. It can return a single tile, several tiles (sector) or the whole map.
//
// The image and link creation functions are out of date and will be changed later to the
// new system.
//
// ========================================================================================

class d13_grid

{
	public $data = array();

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------
	public

	function get($x, $y)
	{
		global $d13;
		$result = $d13->dbQuery('select * from grid where (y between ' . ($y - 3) . ' and ' . ($y + 3) . ')  and (x between ' . ($x - 3) . ' and ' . ($x + 3) . ') order by y desc, x asc');
		for ($i = 0; $row = $d13->dbFetch($result); $i++) $this->data[$i] = $row;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------
	public

	function getAll()
	{
		global $d13;
		$result = $d13->dbQuery('select * from grid order by y desc, x asc');
		for ($i = 0; $row = $d13->dbFetch($result); $i++) $this->data[$i] = $row;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------
	public

	function getFree()
	{
		global $d13;
		$key = 0;
		$free_coords = array();
		$this->getAll();
		foreach($this->data as $coord) {
			if ($coord['type'] == 1) {
				$free_coords[] = $coord;
			}
		}

		$key = array_rand($free_coords);
		return $free_coords[$key];
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------
	public static

	function getSector($x, $y)
	{
		global $d13;
		$result = $d13->dbQuery('select * from grid where x="' . $x . '" and y="' . $y . '"');
		$sector = $d13->dbFetch($result);
		return $sector;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------
	public

	function getSectorImage($x, $y, &$i, $template)
	{
		if ((isset($this->data[$i])) && ($this->data[$i]['x'] == $x) && ($this->data[$i]['y'] == $y))
		if ($this->data[$i]['type'] != 2) {
			$output = CONST_DIRECTORY . 'templates/' . $template . '/images/grid/env_' . $this->data[$i]['type'] . $this->data[$i]['id'] . '.png';
			if ($i < count($this->data) - 1) $i++;
		}
		else {
			$output = CONST_DIRECTORY . 'templates/' . $template . '/images/grid/env_' . $this->data[$i]['type'] . '2.png';
			if ($i < count($this->data) - 1) $i++;
		}
		else $output = CONST_DIRECTORY . 'templates/' . $template . '/images/grid/env_x.png';
		return $output;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------
	public

	function getSectorLink($x, $y, &$i)
	{
		if ((isset($this->data[$i])) && ($this->data[$i]['x'] == $x) && ($this->data[$i]['y'] == $y)) {
			if ($this->data[$i]['type'] != 2) $output = 'href="javascript: fetch(\'getGrid.php\', \'x=' . $x . '&y=' . $y . '\')" onMouseOver="setSectorData(labels[' . $this->data[$i]['type'] . '], \'-\', \'-\')" onMouseOut="setSectorData(\'-\', \'-\', \'-\')"';
			else {
				$node = new d13_node();
				$node->get('id', $this->data[$i]['id']);
				$user = new d13_user();
				$user->get('id', $node->data['user']);
				$output = 'href="javascript: fetch(\'getGrid.php\', \'x=' . $x . '&y=' . $y . '\')" onMouseOver="setSectorData(\'' . $node->data['name'] . '\', \'' . $user->data['name'] . '\', \'-\')" onMouseOut="setSectorData(\'-\', \'-\', \'-\')"';
			}

			if ($i < count($this->data) - 1) $i++;
		}
		else $output = 'href="javascript: fetch(\'getGrid.php\', \'x=' . $x . '&y=' . $y . '\')"';
		return $output;
	}
}

// =====================================================================================EOF

