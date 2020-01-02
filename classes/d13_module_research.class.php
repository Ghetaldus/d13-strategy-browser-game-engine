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

class d13_module_research extends d13_object_module

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
		$tvars['tvar_sub_popuplist'] = '';
		$tvars['tvar_listID'] = 0;
		$i=0;
		
		if ($this->data['options']['inventoryList']) {

			foreach($d13->getTechnology($this->node->data['faction']) as $tid => $tech) {
				if ($tech['active'] && in_array($tid, $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'technologies'))) {
					if ($this->node->technologies[$tid]['level'] > 0) {
						
						$image = $d13->getTechnology($this->node->data['faction'], $tid, 'images');
						$image = $image[0]['image'];

						
						$tvars['tvar_listImage'] = '<img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/technologies/' . $this->node->data['faction'] . '/' . $image .'" title="' . $d13->getLangGL('technologies', $this->node->data['faction'], $tid) ['name'] . '">';
						$tvars['tvar_listLabel'] = $d13->getLangGL('technologies', $this->node->data['faction'], $tid) ['name'];
						$tvars['tvar_listAmount'] = $d13->getLangUI("level") . " " . $this->node->technologies[$tid]['level'];
						$tvars['tvar_sub_popuplist'].= $d13->templateSubpage("sub.module.listcontent", $tvars);
						$i++;
					}
				}
			}
			if ($i>0) {
				
				$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
				
				$vars['tvar_button_name'] 	 = $this->data['name'] . " " . $d13->getLangUI("inventory");
				$vars['tvar_list_id'] 	 	 = "list-0";
				$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipInventoryResearch"));
				$html = $d13->templateSubpage("button.popup.enabled", $vars);
				
			}else {
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
		$html = '';
		$i = 0;
		
		// - - - Research Popup

		$tvars['tvar_sub_popupswiper'] = "";
		foreach($d13->getTechnology($this->node->data['faction']) as $tid => $technology) {
			if ($technology['active'] && in_array($tid, $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'technologies')) && ($this->node->technologies[$tid]['level'] < $technology['maxLevel'])) {
				
				$i++;
				
				$args = array();
				$args['supertype'] 	= 'technology';
				$args['obj_id'] 	= $tid;
				$args['node'] 		= $this->node;
				
				$tmp_technology = new d13_object_technology($args);
				
				// - - - - - Check Cost & Requirements
				$costData = $tmp_technology->getCostList();
				$requirementsData = $tmp_technology->getRequirementsList();

				// - - - - - Check Permissions

				$linkData = '';
				$check_requirements = NULL;
				$check_cost = NULL;
				$check_requirements = $this->node->checkRequirements($tmp_technology->data['requirements']);
				$check_cost = $this->node->checkCost($tmp_technology->data['cost'], 'research');
				
				if ($check_requirements['ok'] && $check_cost['ok'] && $this->node->technologies[$tid]['level'] < $technology['maxLevel']) {
					
					$vars['tvar_button_name'] 	 = $d13->getLangUI("launch") . ' ' . $d13->getLangUI("research");
					$vars['tvar_button_link'] 	 = '?p=module&action=addTechnology&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '&technologyId=' . $tid;
					$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipInventoryResearch"));
					$linkData .= $d13->templateSubpage("button.external.enabled", $vars);
					
				}
				else {
					$vars['tvar_button_name'] 	 = $d13->getLangUI("launch") . ' ' . $d13->getLangUI("research");
					$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipInventoryEmpty"));
					$linkData.= $d13->templateSubpage("button.popup.disabled", $vars);
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

				$tvars['tvar_nodeFaction'] = $this->node->data['faction'];
				$tvars['tvar_linkData'] = $linkData;
				$tvars['tvar_costData'] = $costData;
				$tvars['tvar_requirementsData'] = $requirementsData;
				$tvars['tvar_tid'] = $tid;
				$tvars['tvar_image'] = $tmp_technology->data['image'];
				$tvars['tvar_techName'] = $tmp_technology->data['name'];
				$tvars['tvar_techDescription'] = $tmp_technology->data['description'];
				$tvars['tvar_techTier'] = $tmp_technology->data['level'];
				$tvars['tvar_techMaxTier'] = $tmp_technology->data['maxLevel'];
				$tvars['tvar_duration'] = d13_misc::sToHMS((($technology['duration'] - $technology['duration'] * $this->data['totalIR']) * $d13->getGeneral('users', 'duration', 'research')) * 60, true);
				$tvars['tvar_sub_popupswiper'].= $d13->templateSubpage("sub.module.research", $tvars);
				
			}
		}
		
		$this->data['available'] = $i;
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
		
		if (count($this->node->queues->queue['research'])) {
			foreach($this->node->queues->queue['research'] as $item) {
				if ($item['slot'] == $this->data['slotId']) {
					
					$this->data['busy'] = true;
					
					
					$remaining = ($item['start'] + $item['duration'] ) - time();
					
					$image = $d13->getTechnology($this->node->data['faction'], $item['obj_id'], 'images');
					$image = $image[0]['image'];

					
					$tvars = array();
					$tvars['tvar_listImage'] 	= '<img class="d13-resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/technologies/' . $this->node->data['faction'] . '/' . $image .'">';
					$tvars['tvar_listLabel'] 	= $d13->getLangGL("technologies", $this->node->data['faction'], $item['obj_id'], "name");
					$tvars['tvar_listAmount'] 	= '<span id="research_' . $item['obj_id'] . '">' . implode(':', d13_misc::sToHMS($remaining)) . '</span><script type="text/javascript">timedJump("research_' . $item['obj_id'] . '", "?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '");</script> <a class="external" href="?p=module&action=cancelTechnology&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '&technologyId=' . $item['obj_id'] . '"> <img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>';
				
					$html = $d13->templateSubpage("sub.module.listcontent", $tvars);
				
				}
			}
		}

		// - - - Popover if Queue empty

		if ((bool)$this->data['busy'] === false) {
			if ($this->node->modules[$this->data['slotId']]['input'] > 0 && $this->data['available'] > 0) {
				
				$vars['tvar_button_name'] 	 = $d13->getLangUI("launch") . ' ' . $d13->getLangUI("research");
				$vars['tvar_list_id'] 	 	 = "swiper";
				$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI('tipModuleInactive'));
				$html = $d13->templateSubpage("button.popup.swiper", $vars);
			
			} else {
				$vars['tvar_button_name'] 	 = $d13->getLangUI("launch") . ' ' . $d13->getLangUI("research");
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
		if (isset($this->data['technologies'])) {
			foreach($this->data['technologies'] as $technology) {
				if ($d13->getTechnology($this->node->data['faction'], $technology, 'active')) {
					$id = $d13->getTechnology($this->node->data['faction'], $technology, 'id');
					$image = $d13->getTechnology($this->node->data['faction'], $id, 'images');
					$image = $image[0]['image'];
					$html.= '<a class="tooltip-left" data-tooltip="' . $d13->getLangGL("technologies", $this->node->data['faction'], $technology, "name") . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/technologies/' . $this->node->data['faction'] . '/' . $image . '" title="' . $d13->getLangGL("technologies", $this->node->data['faction'], $technology, "name") . '"></a>';
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