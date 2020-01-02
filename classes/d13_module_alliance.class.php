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

class d13_module_alliance extends d13_object_module

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
		global $d13;
		$html = '';
		$i = 0;
		$tvars = array();
		$tvars['tvar_sub_popuplist'] = '';
		$tvars['tvar_listID'] = 1;
		
		// - - - - Option: Alliance List

		if ($this->data['options']['allianceGet']) {
			$tvars['tvar_Label'] = $d13->getLangUI("new") . ' ' . $d13->getLangUI("alliance");
			$tvars['tvar_Link'] = '?p=alliance&action=get&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("new") . ' ' . $d13->getLangUI("alliance");
			$tvars['tvar_description'] = '';
			$tvars['tvar_sub_popuplist'] .= $d13->templateSubpage("sub.module.itemcontent", $tvars);
			$i++;
		}

		// - - - - Option: Alliance Edit

		if ($this->data['options']['allianceEdit']) {
			$tvars['tvar_Label'] = $d13->getLangUI("edit") . ' ' . $d13->getLangUI("alliance");
			$tvars['tvar_Link'] = '?p=alliance&action=set&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("edit") . ' ' . $d13->getLangUI("alliance");
			$tvars['tvar_description'] = '';
			$tvars['tvar_sub_popuplist'] .= $d13->templateSubpage("sub.module.itemcontent", $tvars);
			$i++;
		}

		// - - - - Option: Alliance Remove

		if ($this->data['options']['allianceRemove']) {
			$tvars['tvar_Label'] = $d13->getLangUI("remove") . ' ' . $d13->getLangUI("alliance");
			$tvars['tvar_Link'] = '?p=alliance&action=remove&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("remove") . ' ' . $d13->getLangUI("alliance");
			$tvars['tvar_description'] = '';
			$tvars['tvar_sub_popuplist'] .= $d13->templateSubpage("sub.module.itemcontent", $tvars);
			$i++;
		}

		// - - - - Option: Alliance Invite

		if ($this->data['options']['allianceInvite']) {
			$tvars['tvar_Label'] = $d13->getLangUI("invite") . ' ' . $d13->getLangUI("members");
			$tvars['tvar_Link'] = '?p=alliance&action=addInvitation&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("invite") . ' ' . $d13->getLangUI("members");
			$tvars['tvar_description'] = '';
			$tvars['tvar_sub_popuplist'] .= $d13->templateSubpage("sub.module.itemcontent", $tvars);
			$i++;
		}

		// - - - - Option: Alliance Go to War

		if ($this->data['options']['allianceWar']) {
			$tvars['tvar_Label'] = $d13->getLangUI("warDeclaration");
			$tvars['tvar_Link'] = '?p=alliance&action=addWar&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("warDeclaration");
			$tvars['tvar_description'] = '';
			$tvars['tvar_sub_popuplist'] .= $d13->templateSubpage("sub.module.itemcontent", $tvars);
			$i++;
		}
		
		if ($this->node->modules[$this->data['slotId']]['input'] > 0 && $i > 0) {
			$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
			
			$vars['tvar_button_name'] 	 = $d13->getLangUI("launch") . ' ' . $d13->getLangUI("alliance");
			$vars['tvar_list_id'] 	 	 = "list-1";
			$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI('tipModuleInactive'));
			$html = $d13->templateSubpage("button.popup.enabled", $vars);
			
		} else {
			
			$vars['tvar_button_name'] 	 = $d13->getLangUI("launch") . ' ' . $d13->getLangUI("alliance");
			$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI('tipModuleDisabled'));
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
	
		return '';
		
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

// =====================================================================================EOF