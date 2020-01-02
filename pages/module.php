<?php

// ========================================================================================
//
// MODULE
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

global $d13;
$module = NULL;
$message = "";
$d13->dbQuery('start transaction');

if (isset($_SESSION[CONST_PREFIX . 'User']['id'], $_GET['action'], $_GET['nodeId'], $_GET['slotId'])) {
	#$flags = $d13->flags->get('name');
	$node = new d13_node();
	
	$status = $node->get('id', $_GET['nodeId']);
	$node->getModules();
	
	#$mid = $node->modules[$_GET['slotId']]['module'];
	#$sid = $node->modules[$_GET['slotId']]['slot'];
	
	if ($status == 'done') {
		$node->checkAll(time());
		$node->getLocation();
		if ($node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id'])
		if (isset($node->modules[$_GET['slotId']])) switch ($_GET['action']) {

			// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

		case 'get':
			/*
			$mid = $node->modules[$_GET['slotId']]['module'];
			$sid = $node->modules[$_GET['slotId']]['slot'];
			if ($mid > - 1) $module = $d13->getModule($node->data['faction'], $mid);
			if (isset($module)) switch ($module['type']) {
			case 'research':
				$node->getQueue('research', 'technology', $d13->getModule($node->data['faction'], $mid) ['technologies']);
				$totalIR = 0;
				foreach($node->modules as $key => $item)
				if ($item['module'] == $mid) $totalIR+= $item['input'] * $d13->getModule($node->data['faction'], $item['module']) ['ratio'];
				break;

			case 'craft':
				$node->getQueue('craft', 'component', $d13->getModule($node->data['faction'], $mid) ['components']);
				$totalIR = 0;
				foreach($node->modules as $key => $item)
				if ($item['module'] == $mid) $totalIR+= $item['input'] * $d13->getModule($node->data['faction'], $item['module']) ['ratio'];
				break;

			case 'train':
				$node->getQueue('train', 'unit', $d13->getModule($node->data['faction'], $mid) ['units']);
				$totalIR = 0;
				foreach($node->modules as $key => $item)
				if ($item['module'] == $mid) $totalIR+= $item['input'] * $d13->getModule($node->data['faction'], $item['module']) ['ratio'];
				break;
			}
			else {
				$message = 'emptySlot';
			}
			*/
			break;

			// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

		case 'set':
			if (isset($_POST['input'])) {
				$status = $node->setModule($_GET['slotId'], $_POST['input']);
				if ($status == 'done') {
					header('Location: index.php?p=module&action=get&nodeId=' . $node->data['id'] . '&slotId=' . $_GET['slotId']);
				}
				else {
					$message = $d13->getLangUI($status);
				}
			}

			break;

			// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

		case 'add':
			#if ($flags['build']) {
				if (isset($_GET['moduleId'])) {
					$status = $node->addModule($_GET['slotId'], $_GET['moduleId'], $_POST['input']);
					if ($status == 'done') header('Location: index.php?p=node&action=get&nodeId=' . $node->data['id']);
					else $message = $d13->getLangUI($status);
				}
			#}
			else $message = $d13->getLangUI("featureDisabled");
			break;

			// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

		case 'upgrade':
			#if ($flags['upgrade']) {
				if (isset($_GET['slotId']) && isset($_GET['moduleId'])) {
					$status = $node->upgradeModule($_GET['slotId'], $_GET['moduleId'], $_POST['input']);
					if ($status == 'done') header('Location: index.php?p=node&action=get&nodeId=' . $node->data['id']);
					else $message = $d13->getLangUI($status);
				}
			#}
			else $message = $d13->getLangUI("featureDisabled");
			break;

			// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

		case 'remove':
			#if ($flags['build']) {
				$status = $node->removeModule($_GET['slotId']);
				if ($status == 'done') header('Location: index.php?p=node&action=get&nodeId=' . $node->data['id']);
				else $message = $d13->getLangUI($status);
			#}
			#else $message = $d13->getLangUI("featureDisabled");
			break;

			// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

		case 'cancel':
			$status = $node->cancelModule($_GET['slotId']);
			if ($status == 'done') header('Location: index.php?p=node&action=get&nodeId=' . $node->data['id']);
			else $message = $d13->getLangUI($status);
			break;

			// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

		case 'list':
			break;

		case 'addTechnology':
			#if ($flags['research']) {
				if (isset($_GET['technologyId'])) {
					$status = $node->addTechnology($_GET['technologyId'], $_GET['slotId']);
					if ($status == 'done') header('Location: index.php?p=module&action=get&nodeId=' . $node->data['id'] . '&slotId=' . $_GET['slotId']);
					else $message = $d13->getLangUI($status);
				}
			#}
			#else $message = $d13->getLangUI("featureDisabled");
			break;

		case 'cancelTechnology':
			if (isset($_GET['technologyId'])) {
				$status = $node->cancelTechnology($_GET['technologyId'], $node->modules[$_GET['slotId']]['module']);
				if ($status == 'done') header('Location: index.php?p=module&action=get&nodeId=' . $node->data['id'] . '&slotId=' . $_GET['slotId']);
				else $message = $d13->getLangUI($status);
			}

			break;

		case 'addComponent':
			#if ($flags['craft']) {
				if (isset($_GET['componentId'], $_POST['quantity']))
				if ($_POST['quantity'] > 0) {
					$status = $node->addComponent($_GET['componentId'], $_POST['quantity'], $_GET['slotId']);
					if ($status == 'done') header('Location: index.php?p=module&action=get&nodeId=' . $node->data['id'] . '&slotId=' . $_GET['slotId']);
					else $message = $d13->getLangUI($status);
				}
				else header('Location: index.php?p=module&action=get&nodeId=' . $node->data['id'] . '&slotId=' . $_GET['slotId']);
			#}
			#else $message = $d13->getLangUI("featureDisabled");
			break;

		case 'removeComponent':
			#if ($flags['craft']) {
				if (isset($_GET['componentId'], $_POST['quantity'])) {
					$status = $node->removeComponent($_GET['componentId'], $_POST['quantity'], $node->modules[$_GET['slotId']]['module']);
					if ($status == 'done') header('Location: index.php?p=module&action=get&nodeId=' . $node->data['id'] . '&slotId=' . $_GET['slotId']);
					else $message = $d13->getLangUI($status);
				}
			#}
			#else $message = $d13->getLangUI("featureDisabled");
			break;

		case 'cancelComponent':
			if (isset($_GET['craftId'])) {
				$status = $node->cancelComponent($_GET['craftId'], $node->modules[$_GET['slotId']]['module']);
				if ($status == 'done') header('Location: index.php?p=module&action=get&nodeId=' . $node->data['id'] . '&slotId=' . $_GET['slotId']);
				else $message = $d13->getLangUI($status);
			}

			break;

		case 'addUnit':
			#if ($flags['train']) {
				if (isset($_GET['unitId'], $_POST['quantity']))
				if ($_POST['quantity'] > 0) {
					$status = $node->addUnit($_GET['unitId'], $_POST['quantity'], $_GET['slotId']);
					if ($status == 'done') header('Location: index.php?p=module&action=get&nodeId=' . $node->data['id'] . '&slotId=' . $_GET['slotId']);
					else $message = $d13->getLangUI($status);
				}
				else header('Location: index.php?p=module&action=get&nodeId=' . $node->data['id'] . '&slotId=' . $_GET['slotId']);
			#}
			#else $message = $d13->getLangUI("featureDisabled");
			break;

		case 'removeUnit':
			#if ($flags['train']) {
				if (isset($_GET['unitId'], $_POST['quantity'])) {
					$status = $node->removeUnit($_GET['unitId'], $_POST['quantity'], $node->modules[$_GET['slotId']]['module']);
					if ($status == 'done') header('Location: index.php?p=module&action=get&nodeId=' . $node->data['id'] . '&slotId=' . $_GET['slotId']);
					else $message = $d13->getLangUI($status);
				}
			#}
			#else $message = $d13->getLangUI("featureDisabled");
			break;

		case 'cancelUnit':
			if (isset($_GET['trainId'])) {
				$status = $node->cancelUnit($_GET['trainId'], $node->modules[$_GET['slotId']]['module']);
				if ($status == 'done') header('Location: index.php?p=module&action=get&nodeId=' . $node->data['id'] . '&slotId=' . $_GET['slotId']);
				else $message = $d13->getLangUI($status);
			}

			break;
		}
		else $message = $d13->getLangUI("noSlot");
		else $message = $d13->getLangUI("noSlot");
	}
	else {
		$message = $d13->getLangUI($status);
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
// Setup Template Variables
// ----------------------------------------------------------------------------------------

if (isset($_SESSION[CONST_PREFIX . 'User']['id'], $_GET['action'], $_GET['nodeId'], $_GET['slotId'])) {

	switch ($_GET['action']) {

	// - - - -
	case 'get':
		$the_module = d13_module_factory::create($node->modules[$_GET['slotId']]['module'], $_GET['slotId'], $node);
		$d13->templateRender($the_module->getTemplate() , $the_module->getTemplateVariables());
		break;

	// - - - - -
	case 'list':
		$moduleList = new d13_moduleListController($node, $_GET['slotId']);
		$d13->templateRender($moduleList->getTemplate() , $moduleList->getTemplateVariables());
		break;
		
	}
}

// =====================================================================================EOF

?>