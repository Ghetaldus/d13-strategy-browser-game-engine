<?php

// ========================================================================================
//
// MODULE.CLASS
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
// ABOUT MODULES:
//
// Modules are Building Objects. Each Node (Town) can contain one or more Modules. Modules
// are the only objects that feature a level and can be upgraded directly. Most of the
// main gameplay features are handled using modules. Modules require a worker resource in
// order to be built/upgraded and require this worker resource in order to function as well.
//
// NOTES:
//
// 
//
// ========================================================================================

class d13_module_craft extends d13_object_module

{

	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public

	function __construct($args)
	{
		parent::__construct($args);
	}
	
	// ----------------------------------------------------------------------------------------
	// getInventory
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getInventory()
	{
		global $d13;
		$html = '';
		$inventoryData = '';
		$tvars['tvar_sub_popuplist'] = '';
		$tvars['tvar_listID'] = 0;
		$i=0;
		
		if ($this->data['options']['inventoryList']) {

			foreach($this->node->components as $uid => $unit) {
				if (in_array($uid, $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'components'))) {
					if ($unit['value'] > 0) {
						
						$image = $d13->getComponent($this->node->data['faction'], $uid, 'images');
						$image = $image[0]['image'];

						
						$tvars['tvar_listImage'] = '<img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/components/' . $this->node->data['faction'] . '/' . $image . '" title="' . $d13->getLangGL('components', $this->node->data['faction'], $uid) ['name'] . '">';
						$tvars['tvar_listLabel'] = $d13->getLangGL('components', $this->node->data['faction'], $uid) ['name'];
						$tvars['tvar_listAmount'] = $unit['value'];
						$tvars['tvar_sub_popuplist'].= $d13->templateSubpage("sub.module.listcontent", $tvars);
						$i++;
					}
				}
			}
			
			if ($i>0) {
				
				$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
				
				$vars['tvar_button_name'] 	 =  $this->data['name'] . " " . $d13->getLangUI("inventory");
				$vars['tvar_list_id'] 	 	 = "list-0";
				$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipInventoryCraft"));
				$html = $d13->templateSubpage("button.popup.enabled", $vars);
				
			} else {
				$vars['tvar_button_name'] 	 = $this->data['name'] . " " . $d13->getLangUI("inventory");
				$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipInventoryEmpty"));
				$html = $d13->templateSubpage("button.popup.disabled", $vars);
			}
		}
		
		$this->data['count'] = $i;
		
		return $html;
	}

	// ----------------------------------------------------------------------------------------
	// getPopup
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getPopup()
	{
	
		global $d13;
		
		$tvars['tvar_sub_popupswiper'] = '';
		$html = '';

		// - - - Craft Popup

		foreach($d13->getComponent($this->node->data['faction']) as $cid => $component) {
			if ($component['active'] && in_array($cid, $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'components'))) {
				
				$args = array();
				$args['supertype'] 	= 'component';
				$args['obj_id'] 	= $cid;
				$args['node'] 		= $this->node;
				
				$tmp_component = new d13_object_component($args);
				
				// - - - - Cost and Requirements
				$costData = $tmp_component->getCostList();
				$requirementsData = $tmp_component->getRequirementsList();

				// - - - Check Affordable Maximum

				$costLimit = $this->node->checkCostMax($component['cost'], 'craft');
				$reqLimit = $this->node->checkRequirementsMax($component['requirements']);
				$upkeepLimit = floor($this->node->resources[$d13->getComponent($this->node->data['faction'], $cid, 'storageResource') ]['value'] / $d13->getComponent($this->node->data['faction'], $cid, 'storage'));
				$unitLimit = abs($this->node->components[$cid]['value'] - $d13->getGeneral('types', $component['type'], 'limit'));
				$limitData = min($costLimit, $reqLimit, $upkeepLimit, $unitLimit);
				$limitData = floor($limitData);

				// - - - - - Check Permissions

				$disableData = '';
				$check_requirements = $this->node->checkRequirements($component['requirements']);
				$check_cost = $this->node->checkCost($component['cost'], 'research');
				if ($check_requirements['ok'] && $check_cost['ok']) {
					$disableData = '';
				}
				else {
					$disableData = 'disabled';
				}

				if ($check_requirements['ok']) {
					$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.ok");
				}
				else {
					$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.notok");
				}

				if ($check_cost['ok']) {
					$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.ok");
				}
				else {
					$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.notok");
				}

				$tvars['tvar_nodeID'] = $this->node->data['id'];
				$tvars['tvar_slotID'] = $this->data['slotId'];
				$tvars['tvar_nodeFaction'] = $this->node->data['faction'];
				$tvars['tvar_costData'] = $costData;
				$tvars['tvar_requirementsData'] = $requirementsData;
				$tvars['tvar_disableData'] = $disableData;
				$tvars['tvar_cid'] = $cid;
				$tvars['tvar_componentName'] = $tmp_component->data['name'];
				$tvars['tvar_componentDescription'] = $tmp_component->data['description'];
				$tvars['tvar_duration'] = d13_misc::sToHMS( (($component['duration'] - $component['duration'] * $this->data['totalIR']) * $d13->getGeneral('users', 'duration', 'craft')) * 60, true);
				$tvars['tvar_compLimit'] = $limitData;
				$vars['tvar_disableData']		= '';
				if ($limitData <= 0) {
					$vars['tvar_disableData']		= 'disabled';
				}
				$tvars['tvar_compValue'] = $tmp_component->data['amount'];
				$tvars['tvar_compStorage'] = $component['storage'];
				$tvars['tvar_compResource'] = $component['storageResource'];
				$tvars['tvar_compResourceName'] = $d13->getLangGL("resources", $component['storageResource'], "name");
				$tvars['tvar_compMaxValue'] = $tmp_component->data['amount'] + $limitData;
				
				$tvars['tvar_sliderID'] 	= $cid;
				$tvars['tvar_sliderMin'] 	= "00";
				$tvars['tvar_sliderMax'] 	= $limitData;
				$tvars['tvar_sliderValue'] 	= "00";

				$tvars['tvar_sub_popupswiper'].= $d13->templateSubpage("sub.module.craft", $tvars);
			}
		}

		$d13->templateInject($d13->templateSubpage("sub.popup.swiper", $tvars));
		$d13->templateInject($d13->templateSubpage("sub.swiper.horizontal", $tvars));
		return $tvars['tvar_sub_popupswiper'];
	}

	// ----------------------------------------------------------------------------------------
	// getQueue
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getQueue()
	{
		global $d13;
		$html = '';

		// - - - Check Queue
		
		$this->data['busy'] = false;
		$this->node->queues->getQueue('craft');
		
		if (count($this->node->queues->queue['craft'])) {
			foreach($this->node->queues->queue['craft'] as $item) {
				if ($item['slot'] == $this->data['slotId']) {
					
					$this->data['busy'] = true;
					
					if (!$item['stage']) {
						$stage = $d13->getLangUI('craft');
					} else {
						$stage = $d13->getLangUI('remove');
					}
					
					$remaining = d13_misc::sToHMS(($item['start'] + $item['duration']) - time(), true);
					
					$image = $d13->getComponent($this->node->data['faction'], $item['obj_id'], 'images');
					$image = $image[0]['image'];
									
					$tvars = array();
					$tvars['tvar_listImage'] 	= '<img class="d13-resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/components/' . $this->node->data['faction'] . '/' . $image .'">';
					$tvars['tvar_listLabel'] 	= $stage . ' ' . $item['quantity'] . 'x ' . $d13->getLangGL("components", $this->node->data['faction'], $item['obj_id'], "name");
					$tvars['tvar_listAmount'] 	= '<span id="craft_' . $item['id'] . '">' . $remaining . '</span><script type="text/javascript">timedJump("craft_' . $item['id'] . '", "?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '");</script> <a class="external" href="?p=module&action=cancelComponent&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '&craftId=' . $item['id'] . '"> <img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>';
				
					$html = $d13->templateSubpage("sub.module.listcontent", $tvars);
				
				}
			}
		}

		// - - - Popover if Queue empty

		if ((bool)$this->data['busy'] === false) {
			if ($this->node->modules[$this->data['slotId']]['input'] > 0) {
			
				$vars['tvar_button_name'] 	 = $d13->getLangUI("launch") . ' ' . $d13->getLangUI("craft");
				$vars['tvar_list_id'] 	 	 = "swiper";
				$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipModuleInactive"));
				$html = $d13->templateSubpage("button.popup.swiper", $vars);
				
			} else {
				$vars['tvar_button_name'] 	 = $d13->getLangUI("launch") . ' ' . $d13->getLangUI("craft");
				$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipModuleDisabled"));
				$html = $d13->templateSubpage("button.popup.disabled", $vars);
			}
		}

		return $html;
	}

	// ----------------------------------------------------------------------------------------
	// getOutputList
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getOutputList()
	{
		global $d13;
		$html = '';
		if (isset($this->data['components'])) {
			foreach($this->data['components'] as $component) {
				if ($d13->getComponent($this->node->data['faction'], $component, 'active')) {
					$html.= '<a class="tooltip-left" data-tooltip="' . $d13->getLangGL("components", $this->node->data['faction'], $component, "name") . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/components/' . $this->node->data['faction'] . '/' . $component . '.png" title="' . $d13->getLangGL("components", $this->node->data['faction'], $component, "name") . '"></a>';
				}
			}
		}

		if (empty($html)) {
			$html = $d13->getLangUI("none");
		}

		return $html;
	}
	
}

?>