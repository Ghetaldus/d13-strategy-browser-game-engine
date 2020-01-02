<?php

// ========================================================================================
//
// GRID
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================
// ----------------------------------------------------------------------------------------
//
// ----------------------------------------------------------------------------------------

global $d13, $grid;
$message = NULL;
$d13->dbQuery('start transaction');

if (isset($_GET['x'], $_GET['y'])) {
	$x = d13_misc::clean($_GET['x'], 'numeric');
	$y = d13_misc::clean($_GET['y'], 'numeric');
	$vars = 'x=' . $x . '&y=' . $y;
}

$grid = new d13_grid();
$grid->getAll();

if ((isset($status)) && ($status == 'error')) {
	$d13->dbQuery('rollback');
}
else {
	$d13->dbQuery('commit');
}

// ----------------------------------------------------------------------------------------
// Setup Template Variables
// ----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;
$sc = count($grid->data);
$rc = sqrt($sc);
$minimap = "";

for ($i = 0; $i < $sc; $i++) {
	switch ($grid->data[$i]['type']) {
	case 0:
		$sectorColor = 'blue';
		break;

	case 1:
		$sectorColor = 'green';
		break;

	case 2:
		$sectorColor = 'brown';
		break;
	}

	$minimap.= '<div class="sector" style="background-color: ' . $sectorColor . ';" id="sector_' . $grid->data[$i]['x'] . '_' . $grid->data[$i]['y'] . '" onClick="fetch(\'' . CONST_DIRECTORY . 'index.php?p=getGrid&x=' . $grid->data[$i]['x'] . '&y=' . $grid->data[$i]['y'] . '\')"></div>';
	if (!(($i + 1) % $rc)) {
		$minimap.= '<br />';
	}
}

$tvars['tvar_gridHTML'] = $minimap;

// - - -

$tvars['tvar_vars'] = "";

if (!empty($vars)) {
	$tvars['tvar_vars'] = "," . $vars;
}

// ----------------------------------------------------------------------------------------
// Parse & Render Template
// ----------------------------------------------------------------------------------------

$d13->templateRender("grid", $tvars);

// =====================================================================================EOF

?>