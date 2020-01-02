<?php

// ========================================================================================
//
// MODULE.MARKET.CLASS
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

class d13_module_market extends d13_object_module

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
		$data = array();
		$tvars['tvar_sub_popuplist'] = '';
		$tvars['tvar_listID'] = 0;
		$i=0;
		
		if ($this->data['options']['inventoryList']) {

			foreach ($this->data['inventory'] as $object) {
			
				$tmp_object = d13_object_factory::create($object['object'], $object['id'], $this->node);
				
				if ($tmp_object->data['active']) {
					$tvars['tvar_listImage'] = '<img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/' . $tmp_object->data['imgdir'] . '/' . $tmp_object->data['image'] . '" title="' . $tmp_object->data['name'] . '">';
					$tvars['tvar_listLabel'] = $tmp_object->data['name'];
					$tvars['tvar_listAmount'] = "";
					$tvars['tvar_sub_popuplist'].= $d13->templateSubpage("sub.module.listcontent", $tvars);
					$i++;
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
		
		$resid = 0;
		$modifier = 2;
		
		$limit = 10;												# could go to config later
		$i = 0;
		
		// - - - Market Popup

		$this->node->queues->getQueue('market');
		
		if (count($this->node->queues->queue['market'])) {
			foreach($this->node->queues->queue['market'] as $object) {
				if ($object['slot'] == $this->data['slotId']) {
				
				$inventory = array();
				$inventory = json_decode($object['inventory'], TRUE);
				
				foreach ($inventory as $item) {
						
					$tmp_object = d13_object_factory::create($item['object'], $item['id'], $this->node);
					
					if ($tmp_object->data['active']) {

						$tvars['tvar_itemName'] 			= $tmp_object->data['name'];
						$tvars['tvar_itemDescription'] 		= $tmp_object->data['description'];
						$tvars['tvar_itemImageDirectory'] 	= $tmp_object->data['imgdir'];
						$tvars['tvar_itemImage'] 			= $tmp_object->data['image'];
						
						$tvars['tvar_itemResource'] 		= $tmp_object->data['storageResImg'];
						$tvars['tvar_itemResourceName'] 	= $tmp_object->data['storageResName'];
					
						$tvars['tvar_itemValue'] 			= floor($tmp_object->data['amount']);
						$tvars['tvar_itemMaxValue'] 		= $tmp_object->getMaxProduction();
						
						
						if ($tmp_object->getCheckConvertedCost($resid, $modifier)) {
							$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.ok");
						}
						else {
							$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.notok");
						}
						
						$tvars['tvar_costData'] 			= $tmp_object->getConvertedCostList($resid, false, $modifier);
					
						$linkData = '';
						$objType = $item['object'];
						$objId = $item['id'];
						
						if ($tmp_object->getCheckConvertedCost($resid, $modifier)) {
							$vars['tvar_button_name'] 	 = $d13->getLangUI("buy");
							$vars['tvar_button_link'] 	 = '?p=module&action=buyMarket&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '&objType=' . $objType . '&objId=' . $objId;
							$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipInventoryResearch"));
							$linkData .= $d13->templateSubpage("button.external.enabled", $vars);
						} else {
							$vars['tvar_button_name'] 	 = $d13->getLangUI("buy");
							$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipInventoryEmpty"));
							$linkData.= $d13->templateSubpage("button.popup.disabled", $vars);
						}
						
						$tvars['tvar_linkData'] 			= $linkData;

						$tvars['tvar_sub_popupswiper'].= $d13->templateSubpage("sub.module.market", $tvars);
						
						$i++;
						
						if ($i == $limit) {
							break;
						}
					
					}
				
				}

				}
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
		$this->node->queues->getQueue('market');
		
		if (count($this->node->queues->queue['market'])) {
			foreach($this->node->queues->queue['market'] as $item) {
				if ($item['slot'] == $this->data['slotId']) {
					
					$this->data['busy'] = true;
								
					$remaining = d13_misc::sToHMS(($item['start'] + $item['duration']) - time(), true);
					
					$image = "refresh.png";
					
					$tvars = array();
					$tvars['tvar_listImage'] 	= '<img class="d13-resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/icon/refresh.png">';
					$tvars['tvar_listLabel'] 	= $d13->getLangUI("refresh") . " " . $this->data['name'];
					$tvars['tvar_listAmount'] 	= '<span id="market_' . $this->data['slotId'] . '">' . $remaining . '</span><script type="text/javascript">timedJump("market_' . $this->data['slotId'] . '", "?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '");</script> <a class="external" href="?p=module&action=cancelMarket&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '"> <img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>';
				
					$html = $d13->templateSubpage("sub.module.listcontent", $tvars);
				
				}
			}
		}

		// - - - Refresh button if queue empty

		if ((bool)$this->data['busy'] === false) {
			
			$vars['tvar_button_name'] 	 = $d13->getLangUI("refresh") . ' ' . $d13->getLangUI("market");
			$vars['tvar_button_link'] 	 = '?p=module&action=addMarket&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'];
			$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipRefreshMarket"));
			$html .= $d13->templateSubpage("button.external.enabled", $vars);
		
		// - - - Popover Button if queue full
			
		} else {
		
			if ($this->node->modules[$this->data['slotId']]['input'] > 0) {
				$vars['tvar_button_name'] 	 = $d13->getLangUI("open") . ' ' . $d13->getLangUI("market");
				$vars['tvar_list_id'] 	 	 = "swiper";
				$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipModuleInactive"));
				$html = $d13->templateSubpage("button.popup.swiper", $vars);
			} else {
				$vars['tvar_button_name'] 	 = $d13->getLangUI("open") . ' ' . $d13->getLangUI("market");
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
		$data = array();
		
		if (isset($this->data['inventory'])) {
			foreach($this->data['inventory'] as $object) {
				
				$tmp_object = d13_object_factory::create($object['object'], $object['id'], $this->node);
				
				if ($tmp_object->data['active']) {
					$html.= '<a class="tooltip-left" data-tooltip="' . $tmp_object->data['name'] . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/' . $tmp_object->data['imgdir'] . '/' . $tmp_object->data['image'] . '" title="' . $tmp_object->data['name'] . '"></a>';
				}
			}
		}

		if (empty($html)) {
			$html = $d13->getLangUI("none");
		}

		return $html;
		
	}
	
	// ----------------------------------------------------------------------------------------
	// getTemplateVariables
	// @
	//
	// ----------------------------------------------------------------------------------------

	
	public function getTemplateVariables()
	{
	
		global $d13;
		$tvars = array();
		
		$tvars = parent::getTemplateVariables();
		
		
		$tvars['tvar_refreshTime'] = 24 - $this->data['totalIR'];
		
		return $tvars;
	
	}
	
	
	
}

?>