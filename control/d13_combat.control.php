<?php

// ========================================================================================
//
// COMBAT.CONTROLLER
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_combatController extends d13_controller
{
	
	private $node;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct()
	{
		
		$tvars = array();
		
		$this->node = new d13_node();
		$status	= $this->node->get('id', $_SESSION[CONST_PREFIX . 'User']['node']);
		$this->node->checkAll(time());
		$this->node->getResources();
		$this->node->getComponents();
		$tvars = $this->doControl();
		$this->getTemplate($tvars);

	}

	// ----------------------------------------------------------------------------------------
	// doControl
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function doControl()
	{
	
		global $d13;
		
		$tvars = array();
		
		switch ($_GET['action']) {
		
			case 'add':
				$tvars = $this->doCombatAdd();
				break;
			
			case 'cancel':
				$tvars = $this->doCombatCancel();
				break;
		
		}
		
		return $tvars;
		
	}
	
	// ----------------------------------------------------------------------------------------
	// doCombatAdd
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function doCombatAdd()
	{
		
		global $d13;
		
		$tvars = array();
	
		//====================================================== ASSEMBLE ATTACK ARMY AND START COMBAT
		
		if (isset($_GET['type'], $_GET['slotId'])) {
		
			$pass = false;
			
			// - - - - Check if Node allows this combat type
			if ($this->node->checkOptions($_GET['type'])) {
				$pass = true;
			} else {
				$message = $d13->getLangUI("featureDisabled");
			}
			
			if ($pass && isset($_POST['type'], $_POST['id'], $_POST['attackerGroupUnitIds'], $_POST['attackerGroups'])) {
				
				$target = new d13_node();
				if ($target->get('id', $_POST['id']) == 'done') {
					
					// - - - - Check Alliance Status
					$targetUser = new d13_user();
					if ($targetUser->get('id', $target->data['user']) == 'done') {
						$pass = true;
						$alliance = new d13_alliance();
						$targetAlliance = new d13_alliance();
						if (($targetAlliance->get('id', $targetUser->data['alliance']) == 'done') && ($alliance->get('id', $_SESSION[CONST_PREFIX . 'User']['alliance']) == 'done')) {
							$war = $alliance->getWar($targetAlliance->data['id']);
							if (isset($war['type'])) {
								$pass = true;
							}
							else {
								$pass = false;
								$message = $d13->getLangUI("noWar");
							}
						}

						if ($pass) {
						
							$gotNoArmy = true;
							$gotIllegal = false;
							$gotLeader = false;
							$gotLimits = array();
							
							foreach ($d13->getGeneral('types') as $key => $type) {
								$gotLimits[$key] = 0;
							}
							
							$data = array();
							$data['input']['attacker']['focus'] = $this->node->data['focus'] ;
							$data['input']['attacker']['faction'] = $this->node->data['faction'];
							
							// - - - - Check for static, multiple leaders and limited units
							
							foreach($_POST['attackerGroupUnitIds'] as $key => $unitId) {
								$data['input']['attacker']['groups'][$key] = array(
									'unitId' => $unitId,
									'quantity' => $_POST['attackerGroups'][$key]
								);
								
								if ($_POST['attackerGroups'][$key] > 0) {
									$gotNoArmy = false;
								}
								
								
								if (!$d13->getUnit($this->node->data['faction'], $unitId, 'speed')) {
									$gotIllegal = true;
								}
								if (!in_array($d13->getUnit($this->node->data['faction'], $key, 'movementType'), $d13->getCombat($_GET['type'], 'movementTypes') )) {
									$gotIllegal = true;
								}
								
								$type = $d13->getUnit($this->node->data['faction'], $unitId, 'type');
								
								if ($d13->getGeneral('types', $type, 'unique')) {
									$gotLimits[$type] += $_POST['attackerGroups'][$key];
								}
								
							}
							
							$pass = true;
							
							if ($gotIllegal || $gotNoArmy) {
								$pass = false;
							}
							
							foreach ($gotLimits as $key => $limit) {
								if ($limit > $d13->getGeneral('types', $key, 'limit')) {
									$pass = false;
								}
								if ($key == 'leader' && $limit == 1) {
									$gotLeader = true;
								}
							}
							
							if (!$gotLeader && $d13->getCombat($_GET['type'], 'requiresLeader')) {
								$pass = false;
							}
							
							if ($pass) {
								$status = $this->node->addCombat($target->data['id'], $data, $_GET['type'], $_GET['slotId']);
								header("location: ?p=node&action=list&nodeId=0");
							} else {
								$status = 'error';
							}
							$message = $d13->getLangUI($status);
							
						}
					} else {
						$message = $d13->getLangUI("noUser");
					}
				} else {
					$message = $d13->getLangUI("noNode");
				}
			}
			
		}
		
		//----
		

		$tvars['tvar_unitImagePath'] 	= $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->node->data['faction'];
		$tvars['tvar_nodeFaction'] 		= $this->node->data['faction'];
		$tvars['tvar_nodeID'] 			= $this->node->data['id'];
		$tvars['tvar_combatType'] 		= $_GET['type'];

		if (isset($_GET['type'])) {
			$tvars['tvar_type'] 		= $_GET['type'];
		}

		if (isset($_GET['slotId'])) {
			$tvars['tvar_slotId'] 		= $_GET['slotId'];
		}

		//====================================================== BUILD AVAILABLE UNITS LIST

		$tvars['tvar_units'] = "";
		$tvars['tvar_unitsHTML'] = "";

		foreach($this->node->units as $key => $unit) {
			if ($unit['value'] > 0 && $d13->getUnit($this->node->data['faction'], $key, 'speed') > 0) {
				if ( in_array($d13->getUnit($this->node->data['faction'], $key, 'movementType'), $d13->getCombat($_GET['type'], 'movementTypes') )) {
					
					$id = $d13->getUnit($this->node->data['faction'], $key, 'id');
					
					$args = array();
					$args['supertype'] 	= 'unit';
					$args['obj_id'] 	= $id;
					$args['node'] 		= $this->node;
				
					$tmp_unit = new d13_object_unit($args);
					
					$d13->templateInject($d13->templateSubpage("sub.popup.unit", $tmp_unit->getTemplateVariables()));
	
					$tvars['tvar_unitName'] 	= $d13->getLangGL('units', $this->node->data['faction'], $id, 'name');
					$tvars['tvar_unitId'] 		= $id;
					$tvars['tvar_unitImage']	= $tmp_unit->data['image'];
					$tvars['tvar_unitType'] 	= $tmp_unit->data['type'];
					$tvars['tvar_unitUnique'] 	= (int)$d13->getGeneral('types', $tmp_unit->data['type'], 'unique');
					$tvars['tvar_unitAmount'] 	= min($unit['value'], $d13->getGeneral('types', $tmp_unit->data['type'], 'limit'));			
					$tvars['tvar_unitFuel'] 	= $tmp_unit->data['fuel'];
		
					$tvars['tvar_unitdamage'] 	= $tmp_unit->data['damage'] + $tmp_unit->data['upgrade_damage'];
					$tvars['tvar_unitspeed'] 	= $tmp_unit->data['speed'] + $tmp_unit->data['upgrade_speed'];
					$tvars['tvar_unitstealth'] 	= $tmp_unit->data['stealth'] + $tmp_unit->data['upgrade_stealth'];
					$tvars['tvar_unithp'] 		= $tmp_unit->data['hp'] + $tmp_unit->data['upgrade_hp'];
					$tvars['tvar_unitarmor'] 	= $tmp_unit->data['armor'] + $tmp_unit->data['upgrade_armor'];
					$tvars['tvar_unitcritical'] = $tmp_unit->data['critical'] + $tmp_unit->data['upgrade_critical'];
					$tvars['tvar_unitcapacity'] = $tmp_unit->data['capacity'] + $tmp_unit->data['upgrade_capacity'];
			
					foreach ($tmp_unit->data['attackModifier'] as $modifier) {
						$tvars['tvar_unit'.$modifier['stat']] += floor($d13->getUnit($this->node->data['faction'], $id, $modifier['stat']) * $modifier['value']);
					}
		
					$modifiers = array();
					foreach ($d13->getGeneral('stats') as $stat) {
						$modifiers[$stat] = 0;
					}
		
					foreach ($tmp_unit->data['armyAttackModifier'] as $modifier) {
						$modifiers[$modifier['stat']] += $modifier['value'];
					}
		
					foreach ($d13->getGeneral('stats') as $stat) {
						$tvars['tvar_armyMod'.$stat] = $modifiers[$stat];
					}
		
					$tvars['tvar_unitsHTML']	.= $d13->templateSubpage("sub.combat.unit", $tvars);
	
				}
			}
		}

		$d13->templateInject($d13->templateSubpage("sub.swiper.horizontal", $tvars));

		//====================================================== BUILD AVAILABLE ENEMIES LIST
		
		$showAll = true;
		$tvars['tvar_nodeList'] = '<option disabled>...</option>';
		$target_nodes = d13_node::getList($_SESSION[CONST_PREFIX . 'User']['id'], TRUE);
		$this->node->getLocation();

		foreach($target_nodes as $target_node) {
			$disabled = '';
			$text = '';
			$target_node->getLocation();
			$distance = ceil(sqrt(pow(abs($this->node->location['x'] - $target_node->location['x']) , 2) + pow(abs($this->node->location['y'] - $target_node->location['y']) , 2)));
	
			if ($target_node->getShield($_GET['type'])) {
				$disabled = 'disabled';
				$text = ' (shielded)';
			}
	
			if ($showAll || empty($disabled))  {
				$tvars['tvar_nodeList'].= '<option value="'.$target_node->data['id'].'" '.$disabled.'>' . $target_node->data['name'] . ' [' . $distance . $d13->getLangUI('miles') . '] ' .$text . '</option>';
			}
		}

		// - - - - Is a leader required?
		$tvars['tvar_leaderRequired'] = '';
		$tvars['tvar_leader'] = 0;
		if ($d13->getCombat($_GET['type'], 'requiresLeader')) {
			$tvars['tvar_leaderRequired'] = $d13->templateGet("sub.combat.leader");
			$tvars['tvar_leader'] = 1;
		}
					
		$tvars['tvar_wipeoutRequired'] = '';						
		if ($d13->getCombat($_GET['type'], 'requiresWipeout')) {
			$tvars['tvar_wipeoutRequired'] = $d13->templateGet("sub.combat.wipeout");
		}
					
		// - - - - Combat Cost & Fuel
		$cost = $d13->getFaction($this->node->data['faction'], 'costs', $_GET['type']);
		$tvars['tvar_costData'] = '';
		$tvars['tvar_resources'] = '';

		foreach ($cost as $res) {

			if (isset($res['resource'])) {
		
				$idv = "";
				$idr = "";
		
				$resource = $res['resource'];
				$cost =  $res['value'];
		
				if (isset($res['isFuel']) && $res['isFuel']) {
					$idv = "availableFuel";
					$idr = "totalFuel";
					$cost = 0;
					$tvars['tvar_fuelFactor'] = floor($res['value']);
					$tvars['tvar_fuelResource'] = floor($this->node->resources[$res['resource']]['value']);
				}
		
				$tvars['tvar_resources'] .= '<input type="hidden" name="availableRes[]" id="'.$idv.'" value="'.floor($this->node->resources[$resource]['value']).'">';
				$tvars['tvar_costData'] .=  '<span class="badge"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $d13->getResource($resource, 'image') . '" title="' . $d13->getLangGL('resources', $resource, 'name') . '"><span name="totalRes[]" id="'.$idr.'">'.$cost . '</span></span>';

			} else if (isset($res['component'])) {
		
				$idv = "";
				$idr = "";
				
				$args = array();
				$args['supertype'] 	= 'component';
				$args['obj_id'] 	= $res['component'];
				$args['node'] 		= $this->node;
				
				$tmp_component = new d13_object_component($args);
		
				$resource = $res['component'];
				$cost =  $res['value'];
		
				if (isset($res['isFuel']) && $res['isFuel']) {
					$idv = "availableFuel";
					$idr = "totalFuel";
					$cost = 0;
					$tvars['tvar_fuelFactor'] = floor($res['value']);
					$tvars['tvar_fuelResource'] = floor($this->node->components[$res['resource']]['value']);
				}
		
				$tvars['tvar_resources'] .= '<input type="hidden" name="availableRes[]" id="'.$idv.'" value="'.floor($this->node->components[$resource]['value']).'">';
				$tvars['tvar_costData'] .=  '<span class="badge"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/components/' . $this->node->data['faction'] . "/" . $tmp_component->data['image'] . '" title="' . $tmp_component->data['name'] . '"><span name="totalRes[]" id="'.$idr.'">'.$cost . '</span></span>';

			}
	
		}

		$tvars['tvar_page'] = "combat.add";

		return $tvars;
	
	}
	
	// ----------------------------------------------------------------------------------------
	// doCombatCancel
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function doCombatCancel()
	{
		
		$tvars = array();
		
		if (isset($_GET['combatId'])) {
			$combat = $this->node->getCombat($_GET['combatId']);
			if (isset($combat['id'])) {
				if ($combat['sender'] == $this->node->data['id']) {
					$status = $this->node->cancelCombat($combat['id']);
					if ($status == 'done') {
						header('Location: ?p=node&action=get&nodeId=' . $this->node->data['id']);
						exit();
					} else {
						$message = $d13->getLangUI($status);
					}
				} else {
					$message = $d13->getLangUI("accessDenied");
				}
			} else {
				$message = $d13->getLangUI("noCombat");
			}
		} else {
			$message = $d13->getLangUI("noCombat");
		}
		
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// getTemplate
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getTemplate($tvars)
	{
	
		global $d13;
		
		$d13->templateRender($tvars['tvar_page'], $tvars);
		
	}

}

// =====================================================================================EO