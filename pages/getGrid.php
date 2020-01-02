<?php

// ========================================================================================
//
// GETGRID
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
	$x = d13_misc::clean($_GET['x']);
	$y = d13_misc::clean($_GET['y']);
}
else {
	$x = rand(0, 49);
	$y = rand(0, 49);
}

$grid = new d13_grid();
$grid->get($x, $y);

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

// - - - X Numeration

$tvars['tvar_mapDiv_1'] = "";

for ($k = 3; $k >= - 3; $k--) {
	$st_x = ($k + 3) * 40 + 5;
	$st_y = (3 - $k) * 20;

	// $tvars['tvar_mapDiv_1'] .= '<div style="position: absolute; left: '.$st_x.'px; top: '.$st_y.'px; width: 50px;">'.$y+$k.'</div>';

}

// - - - Y Numeration

$tvars['tvar_mapDiv_2'] = "";

for ($j = - 3; $j <= 3; $j++) {
	$st_x = ($j + 3) * 40 + 5;
	$st_y = 165 + ($j + 3) * 20 - 12;

	// $tvars['tvar_mapDiv_2'] .= '<div style="position: absolute; left: '.$st_x.'px; top: '.$st_y.'px; width: 50px;">'.$x+$j.'</div>';

}

$tvars['tvar_mapDiv_3'] = "";
$i = 0;

for ($k = 3; $k >= - 3; $k--) {
	for ($j = - 3; $j <= 3; $j++) {
		$st_x = ($k + 3) * 40 + ($j + 3) * 40;
		$st_y = (3 - $k) * 20 + ($j + 3) * 20;
		$tvars['tvar_mapDiv_3'].= '<img style="position: absolute; left: ' . $st_x . 'px; top: ' . $st_y . 'px; width: 80px; height: 80px;" src="' . $grid->getSectorImage($x + $j, $y + $k, $i, $_SESSION[CONST_PREFIX . 'User']['template']) . '">';
	}
}

$tvars['tvar_mapDiv_4'] = "";
$i = 0;

for ($k = 3; $k >= - 3; $k--) {
	for ($j = - 3; $j <= 3; $j++) {
		$st_x = ($k + 3) * 40 + ($j + 3) * 40;
		$st_y = (3 - $k) * 20 + ($j + 3) * 20;
		$coords = ($st_x + 38) . ',' . ($st_y - 2) . ',' . ($st_x + 78) . ',' . ($st_y + 18) . ',' . ($st_x + 40) . ',' . ($st_y + 40) . ',' . $st_x . ',' . ($st_y + 20);
		$tvars['tvar_mapDiv_4'].= '<area shape="poly" coords="' . $coords . '" "' . $grid->getSectorLink($x + $j, $y + $k, $i) . '>';
	}
}

$tvars['tvar_mapControls_North'] = 'x=' . $x . '&y=' . ($y + 3);
$tvars['tvar_mapControls_South'] = 'x=' . $x . '&y=' . ($y - 3);
$tvars['tvar_mapControls_East'] = 'x=' . ($x + 3) . '&y=' . $y;
$tvars['tvar_mapControls_West'] = 'x=' . ($x - 3) . '&y=' . $y;
$tvars['tvar_x'] = $x;
$tvars['tvar_y'] = $y;

// ----------------------------------------------------------------------------------------
// Parse & Render Template
// ----------------------------------------------------------------------------------------

echo $d13->templateSubpage("getGrid", $tvars);

// =====================================================================================EOF

?>