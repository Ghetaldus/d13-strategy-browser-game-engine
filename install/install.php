<?php

// ========================================================================================
//
// INSTALL
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

global $d13;
require_once ("../config/d13_core.inc.php");

$action = "";
$message = "";
// ----------------------------------------------------------------------------------------
//
// ----------------------------------------------------------------------------------------

chdir("../");

if (isset($_POST['email'], $_POST['name'], $_POST['password'], $_POST['rePassword'])) {

	if ((($_POST['email'] != '')) && ($_POST['name'] != '') && (($_POST['password'] != ''))) {
		$user = new d13_user();
		$user->get('name', $_POST['name']);
		if ($_POST['password'] == $_POST['rePassword'])
		if (!$user->data['id']) {
			$user->data['name'] = $_POST['name'];
			$user->data['email'] = $_POST['email'];
			$user->data['password'] = sha1($_POST['password']);
			$user->data['access'] = 3;
			$user->data['joined'] = strftime('%Y-%m-%d', time());
			$user->data['lastVisit'] = strftime('%Y-%m-%d %H:%M:%S', time());
			$user->data['ip'] = $_SERVER['REMOTE_ADDR'];
			$user->data['template'] = CONST_DEFAULT_TEMPLATE;
			$user->data['color'] = CONST_DEFAULT_COLOR;
			$user->data['locale'] = CONST_DEFAULT_LOCALE;
			$imageStats = getimagesize('install/grid.png');
			$image = imagecreatefrompng('install/grid.png');
			$query = array();
			for ($i = 0; $i < $imageStats[0]; $i++)
			for ($j = 0; $j < $imageStats[1]; $j++) {
				$pixelRGB = imagecolorat($image, $i, $j);
				$pixelG = ($pixelRGB >> 8) & 0xFF;
				$pixelB = $pixelRGB & 0xFF;
				if ($pixelB) {
					$sectorType = 0;
					$sectorId = rand(1, 4);
				}
				else
				if ($pixelG) {
					$sectorType = 1;
					$sectorId = rand(1, 10);
				}

				array_push($query, '(' . $i . ', ' . $j . ', ' . $sectorType . ', ' . $sectorId . ')');
			}

			$d13->dbQuery('insert into grid (x, y, type, id) values ' . implode(', ', $query));
			$user->add();
			$message = $d13->getLangUI("installed");
		}
		else $message = $d13->getLangUI("nameTaken");
		else $message = $d13->getLangUI("rePassNotMatch");
	}
	else $message = $d13->getLangUI("insufficientData");
}

// ----------------------------------------------------------------------------------------
// Setup Template Variables
// ----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;

// ----------------------------------------------------------------------------------------
// Parse & Render Template
// ----------------------------------------------------------------------------------------

$d13->templateRender("install", $tvars);

// =====================================================================================EOF

?>