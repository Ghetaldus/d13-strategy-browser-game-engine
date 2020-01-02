<?php

// ========================================================================================
//
// OBJECT_BASE.CLASS
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
// ABOUT OBJECTS:
// 
// The most important objects in the game have been grouped into a class "objects". This
// includes modules, technologies, units, components and so on. 
//
// NOTES:
//
// This is the base object all other object classes are derived from. It was decided to use
// this base object because they all share a lot of similarities. Even after months of
// development, a new object type was rarely added. Therefore it was decided to keep it
// that way.
//
// there is still a lot of object specific code in this class (with switch statements), but
// the amount of non-specific code outweighs it. For example the upgrade system is the same
// for all object types and can now be easily modified by altering this class only.
//
// ========================================================================================

abstract class d13_object_base

{
	
	public $data, $node, $checkRequirements, $checkCost;

	// ----------------------------------------------------------------------------------------
	// construct
	// @ main contructor for all object types, sets up all basic attributes and stats
	// ----------------------------------------------------------------------------------------
	public

	function __construct($args = array())
	{
	
		global $d13;
		
		$this->data = array();
		$this->setNode($args);
		$this->checkStatsBase($args);
		$this->checkStatsUpgrade();
		$this->checkStatsExtended();
		
	}

	// ----------------------------------------------------------------------------------------
	// setNode
	// @ sets up the parent node this object belongs to
	// ----------------------------------------------------------------------------------------
	public

	function setNode($args)
	{
		if (isset($args['node'])) {
			$this->node = $args['node'];
			#$this->node->getModules();
			$this->node->getTechnologies();
			#$this->node->getComponents();
			#$this->node->getUnits();
		}
	}
	
	// ----------------------------------------------------------------------------------------
	// checkStatsBase
	// @ Calculates all basic stats of the object
	// ----------------------------------------------------------------------------------------
	public

	function checkStatsBase($args)
	{
		global $d13;
		
		//add upgrade stat array entries
		foreach($d13->getGeneral('stats') as $stat) {
			$this->data[$stat] = 0;
			$this->data['upgrade_' . $stat] = 0;
		}
		
		switch ($args['supertype'])
		{
		
			case 'module':
				$data 	= $d13->getModule($this->node->data['faction'], $args['obj_id']);
				$name 	= $d13->getLangGL("modules", $this->node->data['faction'], $args['obj_id'], "name");
				$desc 	= $d13->getLangGL("modules", $this->node->data['faction'], $args['obj_id'], "description");
				$level 	= $this->node->modules[$args['slotId']]['level'];
				$type	= $d13->getModule($this->node->data['faction'], $args['obj_id'], 'type');
				$input	= $this->node->modules[$args['slotId']]['input'];
				$ctype 	= 'build';
				$slot	= $args['slotId'];	
				$amount = $this->node->getModuleCount($args['obj_id']);
				$imgdir = 'modules' . '/' . $this->node->data['faction'];
				$resimg = $data['icon'];
				$resname= $name;
				break;
						
			case 'component':
				$data 	= $d13->getComponent($this->node->data['faction'], $args['obj_id']);
				$name 	= $d13->getLangGL("components", $this->node->data['faction'], $args['obj_id'], "name");
				$desc 	= $d13->getLangGL("components", $this->node->data['faction'], $args['obj_id'], "description");
				$level 	= 0;
				$type	= $args['supertype'];
				$input	= 0;
				$ctype 	= 'craft';
				$slot	= 0;
				$amount = $this->node->components[$args['obj_id']]['value'];
				$imgdir = 'components' . '/' . $this->node->data['faction'];
				$resimg = $data['icon'];
				$resname= $d13->getLangGL("resources", $data['storageResource'], "name");
				break;
				
			case 'technology':
				$data 	= $d13->getTechnology($this->node->data['faction'], $args['obj_id']);
				$name 	= $d13->getLangGL("technologies", $this->node->data['faction'], $args['obj_id'], "name");
				$desc 	= $d13->getLangGL("technologies", $this->node->data['faction'], $args['obj_id'], "description");
				$level 	= $this->node->technologies[$args['obj_id']]['level'];
				$type	= $args['supertype'];
				$input	= 0;
				$ctype 	= 'research';
				$slot	= 0;
				$amount = 0;
				$imgdir = 'technologies' . '/' . $this->node->data['faction'];
				$resimg = $data['icon'];
				$resname= $name;
				break;	
					
			case 'unit':
				$data 	= $d13->getUnit($this->node->data['faction'], $args['obj_id']);
				$name 	= $d13->getLangGL("units", $this->node->data['faction'], $args['obj_id'], "name");
				$desc 	= $d13->getLangGL("units", $this->node->data['faction'], $args['obj_id'], "description");
				$level 	= 0;
				$type	= $d13->getUnit($this->node->data['faction'], $args['obj_id'], "type");
				$input	= 0;
				$ctype 	= 'train';
				$slot	= 0;
				$amount = $this->node->units[$args['obj_id']]['value'];
				$imgdir = 'units' . '/' . $this->node->data['faction'];
				$resimg = $data['icon'];
				$resname= '';
				break;
				
			case 'turret':
				$data 	= $d13->getUnit($this->node->data['faction'], $args['obj_id']);
				$name 	= $d13->getLangGL("units", $this->node->data['faction'], $args['obj_id'], "name");
				$desc 	= $d13->getLangGL("units", $this->node->data['faction'], $args['obj_id'], "description");
				$level 	= $args['level'];
				$type	= $d13->getUnit($this->node->data['faction'], $args['obj_id'], "type");
				$input	= $args['input'];
				$ctype 	= 'train';
				$slot	= 0;
				$amount = 0;
				$imgdir = 'units' . '/' . $this->node->data['faction'];
				$resimg = $data['icon'];
				$resname= '';
				break;
				
			case 'shield':
				$data 	= $d13->getShield($args['obj_id']);
				$name 	= $d13->getLangGL("shields", $args['obj_id'], "name");
				$desc 	= $d13->getLangGL("shields", $args['obj_id'], "description");
				$level 	= 0;
				$type	= $args['supertype'];
				$input	= 0;
				$ctype 	= 'shield';
				$slot	= 0;
				$amount = 0;
				$imgdir = 'icon';
				$resimg = $data['icon'];
				$resname= $name;
				break;		
				
			case 'buff':	
				$data 	= $d13->getBuff($args['obj_id']);
				$name 	= $d13->getLangGL("buffs", $args['obj_id'], "name");
				$desc 	= $d13->getLangGL("buffs", $args['obj_id'], "description");
				$level 	= 0;
				$type	= $args['supertype'];
				$input	= 0;
				$ctype 	= 'buff';
				$slot	= 0;
				$amount = 0;
				$imgdir = 'icon';
				$resimg = $data['icon'];
				$resname= $name;
				break;		
			
			case 'resource':	
				$data 	= $d13->getResource($args['obj_id']);
				$name 	= $d13->getLangGL("resources", $args['obj_id'], "name");
				$desc 	= $d13->getLangGL("resources", $args['obj_id'], "description");
				$level 	= 0;
				$type	= $args['supertype'];
				$input	= 0;
				$ctype 	= 'buy';
				$slot	= 0;
				$amount = $this->node->resources[$args['obj_id']]['value'];
				$imgdir = 'resources';
				$resimg = $data['icon'];
				$resname= $name;
				break;		
			
			default:
				$data 	= NULL;
				$name 	= $d13->getLangUI("none");
				$desc 	= $d13->getLangUI("none");
				$level 	= 0;
				$type	= '';
				$input	= 0;
				$ctype 	= '';
				$slot	= 0;
				$amount = 0;
				$imgdir = '';
				$resimg = '';
				$resname= $name;
				break;
		}
		
		$this->data 					= array_merge($this->data, $data);
		$this->data['id']				= $args['obj_id'];
		$this->data['moduleId']			= $args['obj_id'];						#TODO! obsolete
		$this->data['supertype'] 		= $args['supertype'];
		$this->data['name'] 			= $name;
		$this->data['description'] 		= $desc;
		$this->data['level'] 			= $level;
		$this->data['type'] 			= $type;
		$this->data['input'] 			= $input;
		$this->data['slotId'] 			= $slot;
		$this->data['costType'] 		= $ctype;
		$this->data['amount']			= $amount;
		$this->data['imgdir']			= $imgdir;
		$this->data['storageResImg']	= $resimg;
		$this->data['storageResName']	= $resname;
		
		$this->data['costData'] 		= $this->getCheckCost();
		$this->data['reqData'] 			= $this->getCheckRequirements();
		$this->getObjectImage();
		
	}

	// ----------------------------------------------------------------------------------------
	// checkStatsUpgrade
	// @ Calculates and applies all bonus stats that affect this object. this includes upgrades
	// via module level, components, technologies and other upgrades.
	// ----------------------------------------------------------------------------------------

	public

	function checkStatsUpgrade()
	{
	
		global $d13;
		
		$upgrade_list = array();
		$object_upgrades = array();

		// - - - - - - - - - - - - - - - CHECK OBJECT TYPE AND UPGRADE LIST
		switch ($this->data['supertype'])
		{
			case 'module':
				$upgrade_list = $d13->getUpgradeTechnology($this->node->data['faction']);
				break;
			case 'component':
				$upgrade_list = $d13->getUpgradeComponent($this->node->data['faction']);
				break;
			case 'technology':
				$upgrade_list = $d13->getUpgradeTechnology($this->node->data['faction']);
				break;
			case 'unit':
				$upgrade_list = $d13->getUpgradeUnit($this->node->data['faction']);
				break;
			case 'turret':
				$upgrade_list = $d13->getUpgradeTurret($this->node->data['faction']);
				break;
				
			#TODO: add upgradeBuff and upgradeShield as well
			
		}
		
		// - - - - - - - - - - - - - - - GATHER COST & ATTRIBUTES
		foreach($upgrade_list as $upgrade) {
			if ($upgrade['type'] == $this->data['type'] && $upgrade['id'] == $this->data['id']) {

				// - - - - - - - - - - - - - - - COST
				if (isset($upgrade['cost'])) {
					$this->data['cost_upgrade'] = $upgrade['cost'];
				}

				// - - - - - - - - - - - - - - - ATTRIBUTES
				if (isset($upgrade['attributes'])) {
					$this->data['attributes_upgrade'] = $upgrade['attributes'];
				}
			}
		}
		
		// - - - - - - - - - - - - - - - Gather Module Level Upgrades
		if ($this->data['supertype'] == 'module' || $this->data['supertype'] == 'turret') {
			if (!empty($this->data['upgrades']) && $this->data['level'] > 1) {
				foreach ($this->data['upgrades'] as $upgrade_id) {
										
					if ($this->data['supertype'] == 'module') {
						$tmp_upgrade = $d13->getUpgradeModule($this->node->data['faction'], $upgrade_id);
					} else if ( $this->data['supertype'] == 'turret') {
						$tmp_upgrade = $d13->getUpgradeTurret($this->node->data['faction'], $upgrade_id);
					}
										
					if ($tmp_upgrade['active'] && $tmp_upgrade['type'] == $this->data['type'] && $tmp_upgrade['id'] == $this->data['id']) {
						$tmp_upgrade['level'] = $this->data['level']-1;		#!important 
						$object_upgrades[] = $tmp_upgrade;
					}
				}
			}
		}

		// - - - - - - - - - - - - - - - Gather Technology Upgrades
		foreach($this->node->technologies as $technologies) {
			if ($technologies['level'] > 0) {
				$technology = $d13->getTechnology($this->node->data['faction'], $technologies['id']);
				if ($technology['type'] == $this->data['type'] && in_array($this->data['id'], $technology['targets'])) {
					foreach ($technology['upgrades'] as $upgrade_id) {
						$tmp_upgrade = array();
						$tmp_upgrade = $upgrade_list[$upgrade_id];
						$tmp_upgrade['id'] = $technologies['id'];
						$tmp_upgrade['level'] = $technologies['level'];
						$object_upgrades[] = $tmp_upgrade;
					}
				}			
			}
		}

		// - - - - - - - - - - - - - - - Apply all Upgrades
		foreach($object_upgrades as $my_upgrade) {
			if ($my_upgrade['level'] > 0) {

				// - - - - - - - - - - Battlestats scale on percentage base
				if (isset($my_upgrade['battlestats']) && is_array($my_upgrade['battlestats'])) {
					foreach($my_upgrade['battlestats'] as $stats) {
						if ($stats['stat'] == 'all') {
							foreach($d13->getGeneral('stats') as $stat) {
								$this->data['upgrade_' . $stat] += d13_misc::upgraded_value($stats['value'] * $my_upgrade['level'], $this->data[$stat]);
							}
						} else {
							$this->data['upgrade_' . $stats['stat']] += d13_misc::upgraded_value($stats['value'] * $my_upgrade['level'], $this->data[$stats['stat']]);
						}
					}
				// - - - - - - - - - - Attributes scale on a fixed base
				} else if (isset($my_upgrade['attributes']) && is_array($my_upgrade['attributes'])) {
					foreach($my_upgrade['attributes'] as $stats) {
						if ($stats['stat'] == 'all') {
							foreach($d13->getGeneral('stats') as $stat) {
								$this->data['upgrade_' . $stat] += $stats['value'] * $my_upgrade['level'];
							}
						} else {
							$this->data['upgrade_' . $stats['stat']] += $stats['value'] * $my_upgrade['level'];
						}
					}
				}
				
			}
		}

	}
	
	// ----------------------------------------------------------------------------------------
	// getCheckRequirements
	// @ Return TRUE if requirements are met (tech, components etc.), otherwise returns FALSE
	// ----------------------------------------------------------------------------------------
	public

	function getCheckRequirements()
	{
		$this->checkRequirements = $this->node->checkRequirements($this->data['requirements']);
		if ($this->checkRequirements['ok']) {
			return true;
		}
		else {
			return false;
		}
	}

	// ----------------------------------------------------------------------------------------
	// getCheckCost
	// @ Returns TRUE if cost is covered (resources), otherwise returns FALSE
	// ----------------------------------------------------------------------------------------
	public

	function getCheckCost()
	{
	
		$this->checkCost = $this->node->checkCost($this->data['cost'], $this->data['costType']);
		if ($this->checkCost['ok']) {
			return true;
		}
		else {
			return false;
		}
	}
	
	
	// ----------------------------------------------------------------------------------------
	// getCheckConvertedCost
	// Converts the whole resource cost into a single cost of stated resource type.
	// Returns TRUE if cost is covered (resources), otherwise returns FALSE
	// ----------------------------------------------------------------------------------------
	public
	
	function getCheckConvertedCost($resid, $modifier=1)
	{
		
		$convertedCost = 0;
		foreach ($this->data['cost'] as $cost) {
			$convertedCost += $cost['value'];
		}
		$convertedCost *= $modifier;
	
		$cost_array = array();
		$cost_array['resource'] = $resid;
		$cost_array['value'] = $convertedCost;
	
		$this->checkCost = $this->node->checkCost($cost_array, $this->data['costType']);
		if ($this->checkCost['ok']) {
			return true;
		}
		else {
			return false;
		}
	
	
	}

	// ----------------------------------------------------------------------------------------
	// getObjectImage
	// @ Return the current image of the object, according to it's level (modules only)
	// ----------------------------------------------------------------------------------------
	public

	function getObjectImage()
	{
		global $d13;
		$this->data['image'] = '';
		
		foreach($this->data['images'] as $image) {
			if ($image['level'] <= $this->data['level']) {
				$this->data['image'] = $image['image'];
			}
			if ($image['level'] == 1) {
				$this->data['trueimage'] = $image['image'];
			}
		}
	}

	// ----------------------------------------------------------------------------------------
	// getPendingImage
	// @ Return the very first image of the object, is used as pending image (modules only)
	// ----------------------------------------------------------------------------------------
	public

	function getPendingImage()
	{
		global $d13;
		
		foreach($this->data['images'] as $image) {
			if ($image['level'] == 0) {
				return $image['image'];
			}
		}
		return NULL;
	}

	// ----------------------------------------------------------------------------------------
	// getRequirements
	// @ Gather and return all requirements of this object as an array
	// ----------------------------------------------------------------------------------------
	public

	function getRequirements()
	{
		global $d13;
		$req_array = array();
		foreach($this->data['requirements'] as $key => $requirement) {
			$tmp_array = array();
			if (isset($requirement['level'])) {
				$tmp_array['value'] = $requirement['level'];
			}
			else {
				$tmp_array['value'] = $requirement['value'];
			}

			$tmp_array['name'] = $d13->getLangGL($requirement['type'], $this->node->data['faction'], $requirement['id'], 'name');
			$tmp_array['type'] = $requirement['type'];
			$tmp_array['icon'] = $requirement['id'] . '.png'; //TODO!
			$tmp_array['type_name'] = $d13->getLangUI($requirement['type']);
			$req_array[] = $tmp_array;
		}

		return $req_array;
	}

	// ----------------------------------------------------------------------------------------
	// getCost
	// @ Gather and return all costs of this object as an array
	// Note: Can optionally return UPGRADE costs instead of BUY costs (modules and tech only)
	// ----------------------------------------------------------------------------------------
	public

	function getCost($upgrade = false)
	{
	
		global $d13;
		
		$cost_array = array();
		foreach($this->data['cost'] as $key => $cost) {
			$tmp_array = array();
			$tmp_array['resource'] = $cost['resource'];
			$tmp_array['value'] = $cost['value'] * $d13->getGeneral('users', 'efficiency', $this->data['costType']);
			$tmp_array['name'] = $d13->getLangGL('resources', $cost['resource'], 'name');
			$tmp_array['icon'] = $d13->getResource($cost['resource'], 'icon');
			$tmp_array['factor'] = 1;
			if ($upgrade) {
				foreach($this->data['cost_upgrade'] as $key => $upcost) {
					$tmp2_array = array();
					$tmp2_array['resource'] = $upcost['resource'];
					$tmp2_array['value'] = $upcost['value'] * $d13->getGeneral('users', 'efficiency', $this->data['costType']);
					$tmp2_array['name'] = $d13->getLangGL('resources', $upcost['resource'], 'name');
					$tmp2_array['icon'] = $d13->getResource($upcost['resource'], 'icon');
					$tmp2_array['factor'] = $upcost['factor'];
					if ($tmp_array['resource'] == $tmp2_array['resource']) {
						$tmp_array['value'] = $tmp_array['value'] + floor($tmp2_array['value'] * $tmp2_array['factor'] * $this->data['level']);
					}
				}
			}

			$cost_array[] = $tmp_array;
		}

		return $cost_array;
	}
	
	// ----------------------------------------------------------------------------------------
	// getConvertedCost
	// Gather and return the converted cost, converted into stated resource id
	// Note: Can optionally return UPGRADE costs instead of BUY costs (modules and tech only)
	// ----------------------------------------------------------------------------------------
	public
	
	function getConvertedCost($resid, $upgrade=false, $modifier=1)
	{
	
		global $d13;
		
		$convertedCost = 0;
		$cost_array = array();
		$cost_array = $this->getCost($upgrade);
		
		foreach ($cost_array as $cost) {
			$convertedCost += $cost['value'];
		}
		
		$convertedCost *= $modifier;
		
		$converted_cost_array = array();
		$converted_cost_array['resource'] 	= $resid;
		$converted_cost_array['value'] 		= $convertedCost;
		$converted_cost_array['name'] 		= $d13->getLangGL('resources', $resid, 'name');
		$converted_cost_array['icon'] 		= $d13->getResource($resid, 'icon');
		$converted_cost_array['factor'] 	= $modifier;
					
		return $converted_cost_array;
	
	
	}

	// ----------------------------------------------------------------------------------------
	// getStats
	// @ Gather and return all base stats of the object
	// ----------------------------------------------------------------------------------------
	public

	function getStats()
	{
	
		global $d13;
		
		$stats = array();
		foreach($d13->getGeneral('stats') as $stat) {
			$stats[$stat] = $this->data[$stat];
		}

		return $stats;
	}

	// ----------------------------------------------------------------------------------------
	// getUpgrades
	// @ Gather and return all upgrade bonus stats of the object
	// ----------------------------------------------------------------------------------------
	public

	function getUpgrades()
	{
		global $d13;
		$stats = array();
		foreach($d13->getGeneral('stats') as $stat) {
			$stats[$stat] = $this->data['upgrade_' . $stat];
		}

		return $stats;
	}
	
	// ----------------------------------------------------------------------------------------
	// getCostList
	// @ Gather and return a complete list of Costs ready for output
	// Note: Todo: The markup should be moved to a template later.
	// ----------------------------------------------------------------------------------------
	public

	function getCostList($upgrade = false)
	{
		global $d13;
		
		$get_costs = $this->getCost($upgrade);
		
		$costData = '';
		
		foreach($get_costs as $cost) {
			if ($cost['value'] > 0) {
				$costData.= '<div class="cell">' . $cost['value'] . '</div><div class="cell"><a class="tooltip-left" data-tooltip="' . $cost['name'] . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['icon'] . '" title="' . $cost['name'] . '"></a></div>';
			}
		}

		return $costData;

	}
	
	// ----------------------------------------------------------------------------------------
	// getConvertedCostList
	// @ 
	// Note: Todo: The markup should be moved to a template later.
	// ----------------------------------------------------------------------------------------
	public
	
	function getConvertedCostList($resid, $upgrade = false, $modifier = 1)
	{
		
		global $d13;
		
		$cost = $this->getConvertedCost($resid, $upgrade, $modifier);
		
		$costData = $d13->getLangUI("none");
		
		if ($cost['value'] > 0) {
			$costData = '<div class="cell">' . $cost['value'] . '</div><div class="cell"><a class="tooltip-left" data-tooltip="' . $cost['name'] . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['icon'] . '" title="' . $cost['name'] . '"></a></div>';
		}
		
		return $costData;
	
	}
	
	// ----------------------------------------------------------------------------------------
	// getRequirementsList
	// @ Gather and return a complete list of Requirements ready for output
	// Note: Todo: The markup should be moved to a template later.
	// ----------------------------------------------------------------------------------------
	public

	function getRequirementsList()
	{
	
		global $d13;
		$html = '';
		
		if (!count($this->data['requirements'])) {
			$html = $d13->getLangUI('none');
		} else {
			foreach($this->data['requirements'] as $key => $requirement) {
				
				if (isset($requirement['level'])) {
					$value = $requirement['level'];
					$tooltip = $d13->getLangGL($requirement['type'], $this->node->data['faction'], $requirement['id'], 'name') . " [L".$value."]";
				}
				else {
					$value = $requirement['value'];
					$tooltip = $d13->getLangGL($requirement['type'], $this->node->data['faction'], $requirement['id'], 'name') . " [x".$value."]";
				}

				if ($requirement['type'] == 'modules') {
					$images = array();
					$images = $d13->getModule($this->node->data['faction'], $requirement['id'], 'images');
					$image = $images[1]['image'];
				} else if ($requirement['type'] == 'technology') {
					$image = $d13->getTechnology($this->node->data['faction'], $requirement['id'], 'image');
				} else if ($requirement['type'] == 'component') {
					$image = $d13->getComponent($this->node->data['faction'], $requirement['id'], 'image');
				} else {
					$image = $requirement['id'];
				}

				$html.= '<div class="cell">' . $value . '</div><div class="cell"><a class="tooltip-left" data-tooltip="' . $tooltip . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/' . $requirement['type'] . '/' . $this->node->data['faction'] . '/' . $image . '" title="' . $d13->getLangUI($requirement['type']) . ' - ' . $d13->getLangGL($requirement['type'], $this->node->data['faction'], $requirement['id'], 'name') . '"></a></div>';
			}
		}

		return $html;
	}

	// ----------------------------------------------------------------------------------------
	// getMaxProduction
	// @ Return the maximum amount of object instances that the player can currently build/buy/craft
	// Note: This works for all object types including Modules (Buildings)
	// ----------------------------------------------------------------------------------------
	public

	function getMaxProduction()
	{
		global $d13;
		
			switch ($this->data['supertype'])
			{
				case 'unit':
					$nowUpkeep 	= $this->data['upkeep'];
					$upRes		= $this->data['upkeepResource'];
					$limit 		= $d13->getGeneral('types', $this->data['type'], 'limit');
					break;
				
				case 'component':
					$nowUpkeep 	= $this->data['storage'];
					$upRes		= $this->data['storageResource'];
					$limit 		= $d13->getGeneral('types', $this->data['type'], 'limit');
					break;
				
				case 'module':
					$nowUpkeep 	= 1;
					$limit 		= $d13->getModule($this->node->data['faction'], $this->data['id'], 'maxInstances');
					break;
				
				case 'resource':
					$nowUpkeep 	= 1;
					$limit		= $this->node->storage[$this->data['id']];
					break;
					
				case 'buff':
					$nowUpkeep 	= 1;
					$limit		= 1;
					break;
					
				case 'shield':
					$nowUpkeep 	= 1;
					$limit		= 1;
					break;
				
				default:
					$nowUpkeep 	= 0;
					$upRes		= 0;
					$limit 		= 99999; #TODO! change to max constant, defined in config
					break;
			}						
		
			if ($this->data['supertype'] == 'unit' || $this->data['supertype'] == 'component') {
			
				$costLimit 		= $this->node->checkCostMax($this->data['cost'], $this->data['costType']);
				$reqLimit 		= $this->node->checkRequirementsMax($this->data['requirements']);
				$upkeepLimit 	= floor($this->node->resources[$upRes]['value'] / $nowUpkeep);
		
				if ($this->data['amount'] < $limit) {
					$unitLimit		= $limit - $this->data['amount'];
				} else {
					$unitLimit		= 0;
				}
		
				$limitData 		= min($costLimit, $reqLimit, $upkeepLimit, $unitLimit);

			} else if ($this->data['supertype'] == 'module') {
			
				if ($this->data['amount'] < $limit) {
					$limitData		= $limit - $this->data['amount'];
				} else {
					$limitData		= 0;
				}
						
			} else {
				return $limit;
			}
		
			return $limitData;
	}		

	// ----------------------------------------------------------------------------------------
	// getTemplateVariables
	// @
	//
	// ----------------------------------------------------------------------------------------
	public

	function getTemplateVariables()
	{
		global $d13;
		$tvars = array();
			
		$tvars = array_merge($this->getStats(), $this->getUpgrades());
		
		foreach ($this->data as $key => $value) {
			if (!is_array($value)) {
				$tvars['tvar_'.$key] = $value;
			}
		}
		
		return $tvars;	
	}

}

// =====================================================================================EOF