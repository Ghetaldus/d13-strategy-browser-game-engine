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
// ABOUT OBJECTS:
// 
// The most important objects in the game have been grouped into a class "objects". This
// includes modules, technologies, units, components and so on. 
//
// NOTES:
//
// Modules represent Buildings and are always part of a Node (town). This is the most
// complex object type and features several child classes as well. Almost all of the gameplay
// functionality is represented using modules.
//
// ========================================================================================

class d13_object_module extends d13_object_base

{

	// ----------------------------------------------------------------------------------------
	// construct
	// @ Calls base object constructor with an array based argument list
	// ----------------------------------------------------------------------------------------

	public

	function __construct($args)
	{
		global $d13;
		
		parent::__construct($args);
		
	}

	
	// ----------------------------------------------------------------------------------------
	// checkStatsExtended
	// @ check and setup stats that are unique to this object type
	//
	// ----------------------------------------------------------------------------------------

	public

	function checkStatsExtended()
	{
		global $d13;
				
		$this->data['busy'] 		= false;			// is this building currently busy?
		$this->data['count'] 		= 0;				// count of other objects in this building?
		$this->data['available'] 	= 0;				// is any action available in this building?
		
		$this->data['base_maxinput'] 	 = $this->data['maxInput'];
		$this->data['base_ratio']		 = $this->data['ratio'];
		$this->data['moduleProduction']  = $this->data['ratio'] * $d13->getGeneral('users', 'efficiency', 'harvest') * $this->node->getBuff('efficiency', 'harvest') * $this->node->modules[$this->data['slotId']]['input'];
		$this->data['totalIR'] 			 = $this->node->modules[$this->data['slotId']]['input'] * $this->data['ratio'];
		$this->data['cost'] 			 = $this->getCost();
		
		$this->data['inputLimit'] = floor(min($this->data['maxInput'], $this->node->resources[$this->data['inputResource']]['value'], $this->node->modules[$this->data['slotId']]['input']));
		
		if (isset($this->data['inputResource'])) {
			$this->data['moduleInput'] = $this->data['inputResource'];
			$this->data['moduleInputName'] = $d13->getLangGL('resources', $this->data['inputResource'], 'name');
			$this->data['moduleSlotInput'] = $this->node->modules[$this->data['slotId']]['input'];
		}

		if (isset($this->data['outputResource'])) {
			
			$i = 0;
			foreach($this->data['outputResource'] as $res) {
				$this->data['moduleOutput' . $i] = $res;
				$this->data['moduleOutputName' . $i] = $d13->getLangGL("resources", $res, "name");
				$i++;
			}
		}

		if (isset($this->data['storedResource'])) {
			$this->data['moduleStorage'] = $this->data['ratio'] * $this->node->modules[$this->data['slotId']]['input'];
			$i = 0;
			foreach($this->data['storedResource'] as $res) {
				$this->data['moduleStorageRes' . $i] = $res;
				$this->data['moduleStorageResName' . $i] = $d13->getLangGL("resources", $res, "name");
				$i++;
			}
		}
		
		foreach($d13->getGeneral('stats') as $stat) {
			$this->data[$stat] = $this->data[$stat] + $this->data['upgrade_'.$stat];
		}
		
		
		
	}

	// ----------------------------------------------------------------------------------------
	// getTemplateVariables
	// @ retrieve all template variables that the tpl class requires to display this object type
	//
	// ----------------------------------------------------------------------------------------

	public

	function getTemplateVariables()
	{
		global $d13;
		$tvars = array();
		
		$tvars = parent::getTemplateVariables();
				
		$tvars['tvar_nodeFaction'] = $this->node->data['faction'];
		$tvars['tvar_nodeID'] = $this->node->data['id'];
		$tvars['tvar_slotID'] = $this->data['slotId'];
		$tvars['tvar_demolishLink'] 		= $this->getDemolish();
		$tvars['tvar_linkData'] 			= $this->getModuleUpgrade();
		
		$tvars['tvar_image'] = $this->data['image'];
		$tvars['tvar_moduleDescription'] = $this->data['description'];
		$tvars['tvar_moduleProduction'] = $this->data['moduleProduction'];
		$tvars['tvar_inventoryLink'] 	= $this->getInventory();
		
		if ($this->data['level'] > 0) {
			$tvars['tvar_popup'] = $this->getPopup();
			$tvars['tvar_queue'] = $this->getQueue();
			
			
			if ($this->node->resources[$this->data['inputResource']]['value'] < $this->data['maxInput']) {
				$max = $this->node->modules[$this->data['slotId']]['input'] + $this->node->resources[$this->data['inputResource']]['value'];
				$max = min($max, $this->data['maxInput']);
			} else {
				$max = $this->data['maxInput'];
			}

			if ($this->node->resources[$this->data['inputResource']]['value'] < $this->node->storage[$this->data['inputResource']]) {
				$min = $this->node->modules[$this->data['slotId']]['input'] - ($this->node->storage[$this->data['inputResource']] - $this->node->resources[$this->data['inputResource']]['value']);
				if ($min<0) { $min=0; }
			} else {
				$min = $this->node->modules[$this->data['slotId']]['input'];
			}
			
			$tvars['tvar_inputSlider'] = $this->getInputSlider(
				"?p=module&action=set&nodeId=".$this->node->data['id']."&slotId=".$this->data['slotId'], 
				$this->data['slotId'].'_'.$this->data['moduleId'], 
				floor($min), 
				floor($max), 
				$this->node->modules[$this->data['slotId']]['input'], 
				$this->data['busy']);
		}
		
		$tvars['tvar_levelLabel'] = '';
		if ($this->data['maxLevel'] > 1) {
			$tvars['tvar_levelLabel'] = '('.$this->data['level'].'/'.$this->data['maxLevel'].')';
		}
		
		$tvars['tvar_costData'] = $this->getCostList();
		$tvars['tvar_requirementsData'] = $this->getRequirementsList();
		$tvars['tvar_outputData'] = $this->getOutputList();
		
		if ($this->data['level'] <= 0) {
		
			if ($this->data['reqData']) {
				$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.ok");
			}
			else {
				$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.notok");
			}

			if ($this->data['costData']) {
				$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.ok");
			}
			else {
				$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.notok");
			}
		
		}
		
		if (isset($this->data['storedResource'])) {
			$i = 0;
			while (isset($this->data['moduleStorageRes' . $i])) {
				$tvars['tvar_moduleStorageRes' . $i] = $this->data['moduleStorageRes' . $i];
				$tvars['tvar_moduleStorageResName' . $i] = $this->data['moduleStorageResName' . $i];
				$i++;
			}
		}

		if (isset($this->data['outputResource'])) {

			$i = 0;
			while (isset($this->data['moduleOutput' . $i])) {
				$tvars['tvar_moduleOutput' . $i] = $this->data['moduleOutput' . $i];
				$tvars['tvar_moduleOutputName' . $i] = $this->data['moduleOutputName' . $i];
				$i++;
			}
		}

		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// getInputSlider
	// Generate and return a Range Input Slider with given parameters for min/max/disabling
	// ----------------------------------------------------------------------------------------
	public

	function getInputSlider($action, $id, $min, $max, $value, $disabled=false, $tooltip=true)
	{
	
		global $d13;

		$vars = array();		
		$vars['tvar_formAction'] 		= $action;
		$vars['tvar_sliderID'] 			= $id;
		$vars['tvar_sliderValue'] 		= max($min, $value);
		$vars['tvar_sliderMin'] 		= $min;
		$vars['tvar_sliderMax'] 		= $max;
		$vars['tvar_disableData']		= '';
		$vars['tvar_sliderTooltip']		= '';
		
		if ($tooltip) {
		$vars['tvar_sliderTooltip']		= d13_misc::toolTip($d13->getLangUI("tipRangeSliderTooltip"));
		}
		
		if ($disabled || $max <= 0) {
			$vars['tvar_disableData']	= 'disabled';
			$vars['tvar_sliderTooltip']	= d13_misc::toolTip($d13->getLangUI("tipRangeSliderDisabled"));
		}
		
		return $d13->templateSubpage("sub.range.slider", $vars);
				
	}

	// ----------------------------------------------------------------------------------------
	// getCheckDemolish
	// Checks if the module can be demolished and returns either an enabled or disabled button
	// ----------------------------------------------------------------------------------------
	public

	function getDemolish()
	{
		global $d13;
		$html = '';
		
		if ($this->data['level'] > 0) {
			if ($d13->getGeneral('options', 'moduleDemolish')) {
				if ($this->node->modules[$this->data['slotId']]['input'] <= 0) {

					$vars['tvar_button_name'] 	 = $d13->getLangUI("removeModule");
					$vars['tvar_button_link'] 	 = "?p=module&action=remove&nodeId='.$this->node->data['id'].'&slotId='.$this->data['slotId'].'";
					$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipDemolishModule"));
					$html = $d13->templateSubpage("button.external.enabled", $vars);
					
				} else {
					
					$vars['tvar_button_name'] 	 = $d13->getLangUI("removeModule");
					$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipRemoveWorkersfirst"));
					$html = $d13->templateSubpage("button.popup.disabled", $vars);
				}
			}
		}

		return $html;
	}
	
	// ----------------------------------------------------------------------------------------
	// getModuleUpgrade
	// generates and returns either a build or an upgrade button template snippet
	// ----------------------------------------------------------------------------------------

	public

	function getModuleUpgrade()
	{
		global $d13;
		
		$html = '';
		
		if ($this->data['level'] > 0 && $this->data['maxLevel'] == 1) {
			return $html;
		} else {
		
			if ($this->node->resources[$this->data['inputResource']]['value'] < $this->data['maxInput']) {
				$max = $this->node->modules[$this->data['slotId']]['input'] + $this->node->resources[$this->data['inputResource']]['value'];
				if ($max > $this->data['maxInput']) {
					$max = $this->data['maxInput'];
				}
			} else {
				$max = $this->data['maxInput'];
			}	
			
			
			if ($this->data['reqData']) {
				$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.ok");
			}
			else {
				$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.notok");
			}

			if ($this->data['costData']) {
				$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.ok");
			}
			else {
				$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.notok");
			}
			
			if ($this->data['level'] <= 0) {
		
				if (($this->node->resources[$this->data['inputResource']]['value']+$this->data['moduleSlotInput']) > 0 && $this->data['costData'] && $this->data['reqData'] && ($this->node->getModuleCount($this->data['slotId'], $this->data['moduleId']) < $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'maxInstances'))) {
					
					$tvars['tvar_title'] 			= $d13->getLangUI("addModule");
					$tvars['tvar_moduleInputName'] 	= $this->data['moduleInputName'];
					$tvars['tvar_moduleInputImage'] = $d13->getResource($this->data['moduleInput'], 'image');
					$tvars['tvar_moduleDuration'] 	= $this->data['duration'];
					$tvars['tvar_costData'] = $this->getCostList();
					$tvars['tvar_requirementsData'] = $this->getRequirementsList();
					$tvars['tvar_moduleAction'] 	= '?p=module&action=add&nodeId=' . $this->node->data['id'] . '&moduleId=' . $this->data['moduleId'] . '&slotId=' . $this->data['slotId'];
					$tvars['tvar_id'] 				= $this->data['moduleId'];
					$tvars['tvar_moduleInput']		= $this->data['moduleSlotInput'];
					$tvars['tvar_moduleLimit'] 		= floor(min($this->node->resources[$this->data['inputResource']]['value']+$this->data['moduleSlotInput'],$this->data['maxInput']));
					$tvars['tvar_disableData'] 		= '';
					$tvars['tvar_inputSlider'] 		= $this->getInputSlider(
						'?p=module&action=add&nodeId=' . $this->node->data['id'] . '&moduleId=' . $this->data['moduleId'] . '&slotId=' . $this->data['slotId'], 
						'b'.$this->data['slotId'].'_'.$this->data['moduleId'], 
						1, 
						floor($max), 
						0,
						false,
						false);
					
					$d13->templateInject($d13->templateSubpage("sub.popup.build" , $tvars, true));
									
					$vars['tvar_button_name'] 	 = $d13->getLangUI("addModule");
					$vars['tvar_list_id'] 	 	 = "build-".$this->data['moduleId'];
					$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("addModule") . ' ' . $d13->getLangUI("tipModuleBuildup"));
					$html.= $d13->templateSubpage("button.popup.enabled", $vars);
					
				} else {
					$vars['tvar_button_name'] 	 = $d13->getLangUI("addModule") . " " . $d13->getLangUI("impossible");
					$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipModuleBuildupDisabled"));
					$html = $d13->templateSubpage("button.popup.disabled", $vars);
				}
			
			} else {
				
				if ($d13->getGeneral('options', 'moduleUpgrade')) {
					if ($this->data['level'] < $this->data['maxLevel']) {
						if (($this->node->resources[$this->data['inputResource']]['value']+$this->data['moduleSlotInput']) > 0 && $this->data['costData'] && $this->data['reqData'] && $this->node->modules[$this->data['slotId']]['level'] < $this->data['maxLevel'] && $this->data['maxLevel'] > 1) {
						
							$tvars['tvar_title'] 			= $d13->getLangUI("upgrade");
							$tvars['tvar_moduleInputName'] 	= $this->data['moduleInputName'];
							$tvars['tvar_moduleInputImage'] = $d13->getResource($this->data['moduleInput'], 'image');
							$tvars['tvar_moduleDuration'] 	= $this->data['duration'];
							$tvars['tvar_costData'] 		= $this->getCostList(true);
							$tvars['tvar_requirementsData'] = $this->getRequirementsList();
							$tvars['tvar_moduleAction'] 	= '?p=module&action=upgrade&nodeId=' . $this->node->data['id'] . '&moduleId=' . $this->data['moduleId'] . '&slotId=' . $this->data['slotId'];
							$tvars['tvar_id'] 				= $this->data['moduleId'];
							$tvars['tvar_moduleInput']		= $this->data['moduleSlotInput'];
							$tvars['tvar_moduleLimit'] 		= floor(min($this->node->resources[$this->data['inputResource']]['value']+$this->data['moduleSlotInput'],$this->data['maxInput']));
							$tvars['tvar_disableData'] 		= '';
							$tvars['tvar_inputSlider']		= $this->getInputSlider(
								'?p=module&action=upgrade&nodeId=' . $this->node->data['id'] . '&moduleId=' . $this->data['moduleId'] . '&slotId=' . $this->data['slotId'], 
								'u'.$this->data['slotId'].'_'.$this->data['moduleId'], 
								1, 
								floor($max), 
								$this->node->modules[$this->data['slotId']]['input'],
								false,
								false);
				
							$d13->templateInject($d13->templateSubpage("sub.popup.build" , $tvars, true));
							
							$vars['tvar_button_name'] 	 = $d13->getLangUI("upgrade");
							$vars['tvar_list_id'] 	 	 = "build-".$this->data['moduleId'];
							$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("upgrade") . ' ' . $d13->getLangUI("tipModuleBuildup"));
							$html = $d13->templateSubpage("button.popup.enabled", $vars);
							
						} else {
							$vars['tvar_button_name'] 	 = $d13->getLangUI("upgrade") . " " . $d13->getLangUI("impossible");
							$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipModuleBuildupDisabled"));
							$html = $d13->templateSubpage("button.popup.disabled", $vars);
						}
					} else {
						$vars['tvar_button_name'] 	 = $d13->getLangUI("maxModuleLevel");
						$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipModuleMaxLevel"));
						$html = $d13->templateSubpage("button.popup.disabled", $vars);
					}
				}
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
	}

	// ----------------------------------------------------------------------------------------
	// getTemplate
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getTemplate()
	{
		return "module.get." . $this->data['type'];
	}

	// ----------------------------------------------------------------------------------------
	// getInventory
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getInventory()
	{
		return '';
	}

	// ----------------------------------------------------------------------------------------
	// getQueue
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getQueue()
	{
		return '';
	}

	// ----------------------------------------------------------------------------------------
	// getPopup
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getPopup()
	{
		return '';
	}

	
}

// =====================================================================================EOF
