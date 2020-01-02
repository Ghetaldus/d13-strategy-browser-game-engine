<?php

// ========================================================================================
//
// CONTROLLER.CLASS
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
// A really simple router that interprets the current URL and all of its POST and GET parameters.
// Compares the URL with an internal list, checks if login or admin access is required. Does not
// allow to call any other URL besides those in the list (list is defined in data directory as
// JSON file).
//
// also performs a rather bad sanitizing of all POST and GET parameters. after that, the
// POST and GET vars can be used like usual.
//
// ========================================================================================

class d13_router

{

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function route()
	{
		
		global $d13;
		
		$this->sanitize_vars();
		
		$page_name = 'd13_indexController';
		
		if (isset($_GET['p'])) {
			$page_access = $_GET['p'];
		} else {
			$page_access = "index";
		}
		
		foreach ($d13->getRoute() as $route) {
			if ($route['page'] == $page_access && $route['active'] == TRUE) {
				if (($route['login'] && isset($_SESSION[CONST_PREFIX . 'User']['id'])) || (!$route['login'] && !isset($_SESSION[CONST_PREFIX . 'User']['id']))) {
					if(!$route['admin'] || $nav['admin'] && (isset($_SESSION[CONST_PREFIX . 'User']['access'])) && ($_SESSION[CONST_PREFIX . 'User']['access'] >= 3)) {
						$page_name = 'd13_' . $route['page'] . 'Controller';
						break;
					}
				}
			}
		}
				
		$page_object = new $page_name;
		exit();
		
		// this is obsolete once the remaining pages are refactored!
		/*
		$page = CONST_INCLUDE_PATH . "pages/" . $page_access . ".php";
		if (file_exists($page)) {
			include_once ($page);
		} else {
			header("location:index.php");
			exit();
		}
		*/
		// end obsolete
		
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function sanitize_vars()
	{
		foreach($_POST as $key => $value) {
			if ($key == 'name' || $key == 'p' || $key == 'action') {
				$value = preg_replace('/[^a-zA-Z0-9]/', '', $value);
			}

			if (in_array($key, array(
				'x',
				'y',
				'faction'
				))) {
				$_POST[$key] = $this->clean($value, 'numeric');
			} else {
				if (!in_array($key, array(
				'msgbody'
				))) {
				$_POST[$key] = $this->clean($value);
				}
			}
		}

		foreach($_GET as $key => $value) {
			if ($key == 'nodeId') {
				$_GET[$key] = $this->clean($value, 'numeric');
			} else {
				$_GET[$key] = $this->clean($value);
			}
		}
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public static

	function clean($data, $type = 0)
	{
		global $d13;
		if (is_array($data)) {
			foreach($data as $key => $value) {
				if ($type && $type == 'numeric') {
					if (!is_numeric($value)) {
						$value = 0;
					}
					else {
						$value = floor(abs($value));
					}
				} else if (is_string($data)) {

					$data = basename($data);
				}

				$value = $d13->dbRealEscapeString($value);
				$data[$key] = htmlspecialchars($value);
			}
		}
		else {
			if ($type && $type == 'numeric') {
				if (!is_numeric($data)) {
					$data = 0;
				}
				else {
					$data = floor(abs($data));
				}
			} else if (is_string($data)) {

				$data = basename($data);
			}
			
			$data = $d13->dbRealEscapeString($data);
			$data = htmlspecialchars($data);
		}

		return $data;
	}
}

// =====================================================================================EOF

