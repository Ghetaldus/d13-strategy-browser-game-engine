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

class d13_module_warfare extends d13_object_module

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
	// Assemble this modules inventory, build a popup window and return a link
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
			
			if ($i>0) {
				
				$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
				
				$vars['tvar_button_name'] 	 =  $this->data['name'] . " " . $d13->getLangUI("inventory");
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
		$i = 0;
		$tvars = array();
		$tvars['tvar_sub_popuplist'] = '';
		$tvars['tvar_listID'] = 1;
		
		foreach ($d13->getCombat() as $key => $combatType) {
			if ($combatType['active']) {
				if (isset($this->data['options'][$key]) && $this->data['options'][$key]) {
					if ($this->data['moduleSlotInput'] > 0 && $this->data['level'] >= $combatType['level']) {
						$tvars['tvar_Label'] = $d13->getLangGL("combatTypes", $combatType['id'], "name");
						$tvars['tvar_Description'] = $d13->getLangGL("combatTypes", $combatType['id'], "description");
						$tvars['tvar_Link'] = '?p=combat&action=add&nodeId=' . $this->node->data['id'] . '&type='.$combatType['string'].'&slotId='.$this->data['slotId'];
						$tvars['tvar_LinkLabel'] = $d13->getLangUI("set");
						$tvars['tvar_sub_popuplist'] .= $d13->templateSubpage("sub.module.itemcontent", $tvars);
						$i++;
					}
				}
			}
		}
		
		if ($i > 0) {
			$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
			
			$vars['tvar_button_name'] 	 = $d13->getLangUI("launch") . " " . $d13->getLangUI("combat");
			$vars['tvar_list_id'] 	 	 = "list-1";
			$vars['tvar_button_tooltip'] = "";
			$html = $d13->templateSubpage("button.popup.enabled", $vars);
		} else {
			$vars['tvar_button_name'] 	 = $d13->getLangUI("unit") . " " . $d13->getLangUI("launch") . " " . $d13->getLangUI("combat");
			$vars['tvar_button_tooltip'] = "";
			$html = $d13->templateSubpage("button.popup.disabled", $vars);
		}
		
		return $html;

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
		
		if (count($this->node->queue['combat'])) {
			foreach($this->node->queue['combat'] as $item) {
				if ($item['slot'] == $this->data['slotId']) {
					
					$this->data['busy'] = true;
					$stage = '';
					$cancel = '';
					
					if (!$item['stage']) {
						if ($item['sender'] == $this->node->data['id']) {
							$stage = $d13->getLangUI('outgoing');
							$cancel = '<div class="cell"><a class="external" href="?p=combat&action=cancel&nodeId=' . $this->node->data['id'] . '&combatId=' . $item['id'] . '"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a></div>';
						} else {
							$stage = $d13->getLangUI('incoming');
						}
					} else if ($item['sender'] == $this->node->data['id']) {
						$stage = $d13->getLangUI('returning');
					}
					
					$remaining = ($item['start'] + $item['duration']) - time();
					
					$otherNode = new d13_node();
					if ($item['sender'] == $this->node->data['id']) {
						$status = $otherNode->get('id', $item['recipient']);
					}
					else {
						$status = $otherNode->get('id', $item['sender']);
					}
					
					if ($status == 'done') {
						$tvars = array();
						$tvars['tvar_listImage'] 	= '<img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/flag.png">';
						$tvars['tvar_listLabel'] 	= $stage . ' ' . $d13->getLangUI("combat") . ' ' . $otherNode->data['name'];
						$tvars['tvar_listAmount'] 	= '<span id="combat_' . $item['id'] . '">' . implode(':', d13_misc::sToHMS($remaining)) . '</span><script type="text/javascript">timedJump("combat_' . $item['id'] . '", "?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '");</script> '.$cancel;
				
					
				
						$html = $d13->templateSubpage("sub.module.listcontent", $tvars);
					}
					
				}
			}
		}
		
		// - - - Popover
		
		if ($this->data['busy'] == false) {
			if ($this->node->modules[$this->data['slotId']]['input'] > 0 && $this->data['count'] > 0) {
				
				$vars['tvar_button_name'] 	 =  $d13->getLangUI("launch") . ' ' . $d13->getLangUI("combat");
				$vars['tvar_list_id'] 	 	 = "list-1";
				$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI('tipModuleInactive'));
				$html = $d13->templateSubpage("button.popup.enabled", $vars);
				
			} else {
				$vars['tvar_button_name'] 	 = $d13->getLangUI("launch") . ' ' . $d13->getLangUI("combat");
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
		return $d13->getLangUI("none");
	}
}

?>