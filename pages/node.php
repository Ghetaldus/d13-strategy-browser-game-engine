<?php

// ========================================================================================
//
// NODE
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
// PROCESS MODEL
// ----------------------------------------------------------------------------------------

global $d13;
$node = NULL;
$message = NULL;
$x = NULL;
$y = NULL;
$html = NULL;
$d13->dbQuery('start transaction');

if (isset($_SESSION[CONST_PREFIX . 'User']['id'], $_GET['action'])) {
	switch ($_GET['action']) {

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = GET NODE DATA

	case 'get':
		if (isset($_GET['nodeId'])) {
			$node = new d13_node();
			$status = $node->get('id', $_GET['nodeId']);
			if ($status == 'done') {
				if ($node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
					$_SESSION[CONST_PREFIX . 'User']['node'] = $_GET['nodeId'];
					$node->checkAll(time());
					$node->getLocation();
					$node->getQueue('build');
					$node->getQueue('combat');
					$nodecount = $d13->getGeneral('users', 'maxModules') * $d13->getGeneral('users', 'maxSectors');
					$buildQueue = array();
					for ($i = 0; $i < $nodecount; $i++) {
						$buildQueue[$i]['check'] = 0;
						$buildQueue[$i]['image'] = '';
					}

					foreach($node->queue['build'] as $item) {
						$buildQueue[$item['slot']]['check'] = 1;
						$buildQueue[$item['slot']]['image'] = $d13->getModule($node->data['faction'], $item['obj_id'], 'images');
					}
				}
				else {
					$message = $d13->getLangUI("accessDenied");
				}
			}
			else {
				$message = $d13->getLangUI($status);
			}
		}
		else {
			$message = $d13->getLangUI("insufficientData");
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = SET NODE DATA

	case 'set':
		if (isset($_GET['nodeId'])) {
			$node = new d13_node();
			$status = $node->get('id', $_GET['nodeId']);
			if ($status == 'done') {
				if ((isset($_POST['name'], $_POST['focus'])) && ($_POST['name']))
				if (in_array($_POST['focus'], array(
					'hp',
					'armor',
					'damage'
				)))
				if ($node->checkOptions('nodeEdit') && $node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
					$oldName = $node->data['name'];
					$oldFocus = $node->data['focus'];
					$node->data['name'] = $_POST['name'];
					$node->data['focus'] = $_POST['focus'];
					$status = $node->set();
					if ($status != 'done') {
						$node->data['name'] = $oldName;
						$node->data['focus'] = $oldFocus;
					}

					$message = $d13->getLangUI($status);
				}
				else $message = $d13->getLangUI("accessDenied");
				else $message = $d13->getLangUI("invalidFocus");
			}
			else $message = $d13->getLangUI($status);
		}
		else $message = $d13->getLangUI("accessDenied");
		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = ADD NEW NODE

	case 'add':
		if (isset($_POST['faction'], $_POST['name'], $_POST['x'], $_POST['y'])) {
			if ($_POST['faction'] != '' && !empty($_POST['name']) && !empty($_POST['x']) && !empty($_POST['y'])) {
				$node = new d13_node();
				$node->data['faction'] = $_POST['faction'];
				$node->data['user'] = $_SESSION[CONST_PREFIX . 'User']['id'];
				$node->data['name'] = $_POST['name'];
				$node->location['x'] = $_POST['x'];
				$node->location['y'] = $_POST['y'];
				$message = $d13->getLangUI($node->add($_SESSION[CONST_PREFIX . 'User']['id']));
			}
			else {
				$message = $d13->getLangUI("insufficientData");
			}
		}
		else {
			$message = $d13->getLangUI("insufficientData");
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = ADD NEW RANDOM NODE

	case 'random':
		if (isset($_POST['faction'])) {
			if ($_POST['faction'] != '') {
				$coord = array();
				$grid = new d13_grid();
				$coord = $grid->getFree();
				$node = new d13_node();
				$node->data['faction'] = $_POST['faction'];
				$node->data['user'] = $_SESSION[CONST_PREFIX . 'User']['id'];
				$node->data['name'] = $_SESSION[CONST_PREFIX . 'User']['name'];
				$node->location['x'] = $coord['x'];
				$node->location['y'] = $coord['y'];
				$message = $d13->getLangUI($node->add($_SESSION[CONST_PREFIX . 'User']['id']));
				header('Location: ?p=node&action=list');
			}
			else {
				$message = $d13->getLangUI("insufficientData");
			}
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = REMOVE NODE

	case 'remove':
		if (isset($_GET['nodeId'])) {
			$node = new d13_node();
			$status = $node->get('id', $_GET['nodeId']);
			if ($status == 'done') {
				if ((isset($_GET['go'])) && ($_GET['go'])) {
					if ($node->checkOptions('nodeRemove') && $node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
						$status = d13_node::remove($_GET['nodeId']);
						if ($status == 'done') {
							header('location: p=node&action=list');
						}
						else {
							$message = $d13->getLangUI($status);
						}
					}
					else {
						$message = $d13->getLangUI("accessDenied");
					}
				}
				else {
					$message = $d13->getLangUI($status);
				}
			}
			else {
				$message = $d13->getLangUI("insufficientData");
			}
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = MOVE NODE

	case 'move':
		if (isset($_GET['nodeId'])) {
			$node = new d13_node();
			$status = $node->get('id', $_GET['nodeId']);
			if ($status == 'done') {
				if (isset($_POST['x'], $_POST['y']))
				if ($node->checkOptions('nodeMove') && $node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id'])
				if ($d13->getFaction($node->data['faction'], 'costs', 'move', 0, 'resource') > - 1) $message = $d13->getLangUI($node->move($_POST['x'], $_POST['y']));
				else $message = $d13->getLangUI("nodeMoveDisabled");
				else $message = $d13->getLangUI("accessDenied");
			}
			else $message = $d13->getLangUI($status);
		}
		else $message = $d13->getLangUI("insufficientData");
		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = CANCEL SHIELD

	case 'cancelShield':
		if (isset($_GET['shieldId'])) {
			$node = new d13_node();
			$status = $node->get('id', $_GET['nodeId']);
			$status = $node->cancelShield($_GET['shieldId']);
			if ($status == 'done') {
				header('Location: index.php?p=node&action=get&nodeId=' . $node->data['id']);
			} else {
				$message = $d13->getLangUI($status);
			}
		}
		else $message = $d13->getLangUI("insufficientData");
		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = LIST NODE

	case 'list':
		$nodes = d13_node::getList($_SESSION[CONST_PREFIX . 'User']['id']);
		break;
	}
}
else {
	$message = $d13->getLangUI("accessDenied");
}

if ((isset($status)) && ($status == 'error')) {
	$d13->dbQuery('rollback');
}
else {
	$d13->dbQuery('commit');
}

// ----------------------------------------------------------------------------------------
// PROCESS VIEW
// ----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;
$page = "node";

if (isset($node)) {
	$tvars['tvar_nodeFaction'] = $node->data['faction'];
	$tvars['tvar_nodeID'] = $node->data['id'];
	$tvars['tvar_nodeName'] = $node->data['name'];
	if (isset($node->data['x'])) {
		$tvars['tvar_nodeX'] = $node->data['x'];
	}

	if (isset($node->data['y'])) {
		$tvars['tvar_nodeY'] = $node->data['y'];
	}
}

if (isset($_SESSION[CONST_PREFIX . 'User']['id'], $_GET['action'])) {
	switch ($_GET['action']) {

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = GET NODE

	case 'get':
		if ((isset($node->data['id'])) && ($node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id'])) {
			$tvars['tvar_getHTMLSectors'] = "";
			for ($sector = 1; $sector <= $d13->getGeneral('users', 'maxSectors'); $sector++) {

				// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Render Sector View
				
				$tvars['tvar_getHTMLNode'] = "";
				
				$offset_start = ($sector - 1) * $d13->getGeneral('users', 'maxModules');
				$offset_end = $offset_start + $d13->getGeneral('users', 'maxModules');
				$node->getModules();
				
				$add = true;
				$limit = 4;
				$i = 0;
				
				foreach($node->modules as $module) {
					if ($module['slot'] >= $offset_start && $module['slot'] <= $offset_end) {
						
						// - - - end current row
						if ($i == $limit) {
							$tvars['tvar_getHTMLNode'].= '</div>';
							$i = 0;
							if ($add) {
								$limit++;
								$add = false;
							} else {
								$limit--;
								$add = true;
							}
						}
						// - - - begin new row
						if ($i == 0) {
							$tvars['tvar_getHTMLNode'].= '<div class="row no-gutter">';
						}
	
						// - - - Add modules to row
						if ($module['module'] > - 1) {
							$the_module = d13_module_factory::create($module['module'], $module['slot'], $node);
						}
						
						if ($buildQueue[$module['slot']]['check']) {
							$tvars['tvar_moduleLink'] = "";
							$tvars['tvar_moduleImage'] = $buildQueue[$module['slot']]['image'][0]['image'];
							$tvars['tvar_moduleClass'] = '<img class="spinner resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/icon/gear.png">';
							$tvars['tvar_moduleLabel'] = $d13->getLangUI("underConstruction");
							$tvars['tvar_getHTMLNode'].= $d13->templateSubpage("sub.node.module", $tvars);
						} else if ($module['module'] > - 1) {
							$tvars['tvar_moduleLink'] = "index.php?p=module&action=get&nodeId=" . $node->data['id'] . "&slotId=" . $module['slot'];
							$tvars['tvar_moduleImage'] = $the_module->data['image'];
							if (($module['input'] <= 0 && $d13->getModule($node->data['faction'], $module['module'], 'maxInput') > 0) || ($d13->getModule($node->data['faction'], $module['module'], 'type') == 'defense' && $module['input'] < $d13->getModule($node->data['faction'], $module['module'], 'maxInput'))) {
								$tvars['tvar_moduleClass'] = '<a href="#" class="tooltip-left" data-tooltip="' . $d13->getLangUI("no") . " " . $d13->getLangGL('resources', $the_module->data['inputResource'], 'name') . '"><img class="animated bounce resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/icon/exclamation.png"></a>';
							} else {
								$tvars['tvar_moduleClass'] = "";
							}
							if ($the_module->data['maxLevel'] > 1) {
								$tvars['tvar_moduleLabel'] = $d13->getLangGL("modules", $node->data['faction'], $module['module'], "name") . " [L" . $module['level'] . "]";
							} else {
								$tvars['tvar_moduleLabel'] = $d13->getLangGL("modules", $node->data['faction'], $module['module'], "name");
							}
							$tvars['tvar_getHTMLNode'].= $d13->templateSubpage("sub.node.module", $tvars);
						} else {
							$tvars['tvar_moduleLink'] = "index.php?p=module&action=list&nodeId=" . $node->data['id'] . "&slotId=" . $module['slot'];
							mt_srand($module['slot']*$sector);
							$imgid = mt_rand(0,9);
							$tvars['tvar_moduleImage'] = "module_empty_".$imgid.".png";
							$tvars['tvar_moduleClass'] = "";
							$tvars['tvar_moduleLabel'] = $d13->getLangUI("emptySlot") . ' ' . $d13->getLangUI("clickToBuild");
							$tvars['tvar_getHTMLNode'].= $d13->templateSubpage("sub.node.module", $tvars);
						}

						// - - - advance or new row
						if ($i != $limit) {
						
							$i++;
						}
						
						$offset_start++;
						if ($offset_start == $offset_end) {
							$i = 0;
							break;
						}
					}
				}

				if ($i > 0 && $i < $limit) {
					$i = $limit - $i;
					for ($j = $i; $j <= $limit; $j++) {
						$tvars['tvar_getHTMLNode'].= $d13->templateSubpage("sub.node.filler", $tvars);
					}
					$tvars['tvar_getHTMLNode'].= '</div>';
				} else if ($i == 0) {
					$tvars['tvar_getHTMLNode'].= '</div>';
				}
				$tvars['tvar_getHTMLSectors'].= $d13->templateSubpage("sub.node.sector", $tvars);
			}
			
		}
		$d13->templateInject($d13->templateSubpage("sub.swiper.horizontal", $tvars));
		$page = "node.get";
		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = SET NODE

	case 'set':
		if (isset($node->data['id'])) {
			if ($node->checkOptions('nodeRemove')) {
				$costData = '';
				foreach($d13->getFaction($node->data['faction'], 'costs', 'set') as $key => $cost) {
					$costData.= '<div class="cell">' . $cost['value'] . '</div><div class="cell"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"></div>';
				}

				$selectedFocus = array(
					'hp' => '',
					'damage' => '',
					'armor' => ''
				);
				$selectedFocus[$node->data['focus']] = ' selected';
				$tvars['tvar_costData'] = $costData;
				$tvars['tvar_selFocusHP'] = $selectedFocus['hp'];
				$tvars['tvar_selFocusDamage'] = $selectedFocus['damage'];
				$tvars['tvar_selFocusArmor'] = $selectedFocus['armor'];
				$page = "node.set";
			}
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = ADD NODE

	case 'add':
		$tvars['tvar_factionDescriptions'] = "";
		$tvars['tvar_factionOptions'] = "";
		foreach($d13->getLangGL('factions') as $key => $faction) {
			$tvars['tvar_factionOptions'].= '<option value="' . $key . '">' . $faction['name'] . '</option>';
		}

		foreach($d13->getLangGL('factions') as $key => $faction) {
			$descriptions[$key] = '"' . $faction['description'] . '"';
		}

		$tvars['tvar_factionText'] = $descriptions[0];
		$tvars['tvar_factionDescriptions'].= implode(', ', $descriptions);
		$page = "node.add";
		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = ADD RANDOM NODE

	case 'random':
		$tvars['tvar_getHTMLFactions'] = "";
		$tvars['tvar_factionName'] = "";
		$tvars['tvar_factionText'] = "";
		$tvars['tvar_factionID'] = - 1;
		$factionId = - 1;

		// - - - - Check for Faction Fixation

		if ($d13->getGeneral('options', 'factionFixation')) {
			$nodes = d13_node::getList($_SESSION[CONST_PREFIX . 'User']['id']);
			$t = count($nodes);
			if ($t > 0) {
				$factionId = $nodes[0]->data['faction'];
			}
		}

		// - - - - Add Factions to Swiper Slide

		foreach($d13->getFaction() as $faction) {
			if ($faction['active']) {
				if (!$d13->getGeneral('options', 'factionFixation') || ($d13->getGeneral('options', 'factionFixation') && $faction['id'] == $factionId) || ($d13->getGeneral('options', 'factionFixation') && $factionId == - 1)) {
					$tvars['tvar_factionName'] = $d13->getLangGL('factions', $faction['id'], 'name');
					$tvars['tvar_factionText'] = $d13->getLangGL('factions', $faction['id'], 'description');
					$tvars['tvar_factionID'] = $faction['id'];
					$tvars['tvar_getHTMLFactions'].= $d13->templateSubpage("sub.node.faction", $tvars);
				}
			}
		}

		$d13->templateInject($d13->templateSubpage("sub.swiper.horizontal", $tvars));
		$page = "node.random";
		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = REMOVE NODE

	case 'remove':
		if (isset($node->data['id'])) {
			if ($node->checkOptions('nodeRemove')) {
				$page = "node.remove";
			}
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = MOVE NODE

	case 'move':
		if (isset($node->data['id'])) {
			if ($node->checkOptions('nodeMove')) {
				$costData = '';
				foreach($d13->getFaction($node->data['faction'], 'costs', 'move') as $key => $cost) {
					$costData.= '<div class="cell">' . $cost['value'] . '</div><div class="cell"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"></div>';
				}
				$node->getLocation();
				$tvars['tvar_nodeX'] = $node->location['x'];
				$tvars['tvar_nodeY'] = $node->location['y'];
				$tvars['tvar_costData'] = $costData;
				$page = "node.move";
			}
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = LIST TOWNS

	case 'list':
		$tvars['tvar_nodeList'] = "";
		$t = count($nodes);

		// - - - - - - - - - - - - - - - - - - - - 0 towns - create new town

		if ($t == 0) {
			
			header("location: index.php?p=node&action=random");
			
			// - - - - - - - - - - - - - - - - - - - - 1 town - jump to town

		}
		else
		if ($t == 1) {
			$link = "?p=node&action=get&nodeId=" . $nodes[0]->data['id'];
			header("location: " . $link);

			// - - - - - - - - - - - - - - - - - - - - 2+ towns - list all towns

		}
		else {
			foreach($nodes as $key => $node) {
				$tvars['tvar_nodeList'].= '<div><a class="external" href="index.php?p=node&action=get&nodeId=' . $node->data['id'] . '">' . $node->data['name'] . '</a></div>';
			}

			$page = "node.list";
		}

		break;
	}
}

// ----------------------------------------------------------------------------------------
// RENDER OUTPUT
// ----------------------------------------------------------------------------------------

$d13->templateRender($page, $tvars, $node);

// =====================================================================================EOF

?>