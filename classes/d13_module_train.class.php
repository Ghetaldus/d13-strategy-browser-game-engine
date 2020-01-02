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

class d13_module_train extends d13_object_module

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
		$tvars = array();
		$tvars['tvar_sub_popuplist'] = '';
		$tvars['tvar_listID'] = 0;
		$html = '';
		$i=0;
		
		if ($this->data['options']['inventoryList']) {
			foreach($this->node->units as $uid => $unit) {
				if (in_array($uid, $d13->getModule($this->node->data['faction'], $this->data['id'], 'units'))) {
					if ($d13->getUnit($this->node->data['faction'], $uid, 'active') && $unit['value'] > 0) {
						
						$image = $d13->getUnit($this->node->data['faction'], $uid, 'images');
						$image = $image[0]['image'];

						
						$tvars['tvar_listImage'] = '<img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->node->data['faction'] . '/' . $image . '" title="' . $d13->getLangGL('units', $this->node->data['faction'], $uid) ['name'] . '">';
						$tvars['tvar_listLabel'] = $d13->getLangGL('units', $this->node->data['faction'], $uid) ['name'];
						$tvars['tvar_listAmount'] = $unit['value'];
						$tvars['tvar_sub_popuplist'].= $d13->templateSubpage("sub.module.listcontent", $tvars);
						$i++;
					}
				}
			}
			
			if ($i>0) {
				
				$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
				
				$vars['tvar_button_name'] 	 = $this->data['name'] . " " . $d13->getLangUI("inventory");
				$vars['tvar_list_id'] 	 	 = "list-0";
				$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipInventoryTrain"));
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
		$html = '';
		$tvars = array();
		$tvars['tvar_sub_popupswiper'] = '';
		
		foreach($d13->getUnit($this->node->data['faction']) as $uid => $unit) {
			if ($unit['active'] && in_array($uid, $d13->getModule($this->node->data['faction'], $this->data['id'], 'units'))) {
				
				$args = array();
				$args['supertype'] 	= 'unit';
				$args['obj_id'] 	= $uid;
				$args['node'] 		= $this->node;
				
				$tmp_unit = new d13_object_unit($args);
				
				$vars = array();
				$vars = $tmp_unit->getTemplateVariables();
				
				$vars['tvar_duration'] = d13_misc::sToHMS((($tmp_unit->data['duration'] - $tmp_unit->data['duration'] * $this->data['totalIR']) * $d13->getGeneral('users', 'duration', 'train')) * 60, true);
				$vars['tvar_uid'] = $uid;
				$vars['tvar_nodeId'] = $this->node->data['id'];
				$vars['tvar_slotId'] = $this->data['slotId'];
				$vars['tvar_sliderID'] 	= $uid;
				$vars['tvar_sliderMin'] 	= "00";
				$vars['tvar_sliderMax'] 	= $tmp_unit->getMaxProduction();
				$vars['tvar_disableData']		= '';
				if ($tmp_unit->getMaxProduction() <= 0) {
					$vars['tvar_disableData']		= 'disabled';
				}
				$vars['tvar_sliderValue'] 	= "00";
				$vars['tvar_unitDescription'] = $tmp_unit->data['description'];
				$vars['tvar_unitMaxValue'] = $tmp_unit->data['amount'] + $tmp_unit->getMaxProduction();
				
				$tvars['tvar_sub_popupswiper'] .= $d13->templateSubpage("sub.module.train", $vars);
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
		$this->node->queues->getQueue('train');
		
		if (count($this->node->queues->queue['train'])) {
			foreach($this->node->queues->queue['train'] as $item) {
				if ($item['slot'] == $this->data['slotId']) {
					
					$this->data['busy'] = true;
					
					if (!$item['stage']) {
						$stage = $d13->getLangUI('train');
					} else {
						$stage = $d13->getLangUI('remove');
					}
					$remaining = d13_misc::sToHMS(($item['start'] + $item['duration']) - time(), true);
					
					$image = $d13->getUnit($this->node->data['faction'], $item['obj_id'], 'images');
					$image = $image[0]['image'];

					$tvars = array();
					$tvars['tvar_listImage'] 	= '<img class="d13-resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->node->data['faction'] . '/' . $image . '">';
					$tvars['tvar_listLabel'] 	= $stage . ' ' . $item['quantity'] . 'x ' . $d13->getLangGL("units", $this->node->data['faction'], $item['obj_id'], "name");
					$tvars['tvar_listAmount'] 	= '<span id="train_' . $item['id'] . '">' . $remaining . '</span><script type="text/javascript">timedJump("train_' . $item['id'] . '", "?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '");</script> <a class="external" href="?p=module&action=cancelUnit&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '&trainId=' . $item['id'] . '"> <img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>';
				
					$html = $d13->templateSubpage("sub.module.listcontent", $tvars);
				
				}
			}
		}
		
		// - - - Popover if Queue empty

		if ($this->data['busy'] == false) {
			if ($this->node->modules[$this->data['slotId']]['input'] > 0) {
				$tvars = array();
				
				
				$vars['tvar_button_name'] 	 = $d13->getLangUI("launch") . ' ' . $d13->getLangUI("train");
				$vars['tvar_list_id'] 	 	 = "swiper";
				$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI('tipModuleInactive'));
				$html = $d13->templateSubpage("button.popup.swiper", $vars);
				
			} else {
				$vars['tvar_button_name'] 	 = $d13->getLangUI("launch") . ' ' . $d13->getLangUI("train");
				$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI('tipModuleDisabled'));
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
				
		if (isset($this->data['units'])) {
			foreach($this->data['units'] as $unit) {
				if ($d13->getUnit($this->node->data['faction'], $unit, 'active')) {
					$id = $d13->getUnit($this->node->data['faction'], $unit, 'id');
					$image = $d13->getUnit($this->node->data['faction'], $id, 'images');
					$image = $image[0]['image'];
					$html.= '<a class="tooltip-left" data-tooltip="' . $d13->getLangGL("units", $this->node->data['faction'], $unit, "name") . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->node->data['faction'] . '/' . $image . '" title="' . $d13->getLangGL("units", $this->node->data['faction'], $unit, "name") . '"></a>';
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