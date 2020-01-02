<?php

// ========================================================================================
//
// MODUELIST.CONTROLLER
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_moduleListController extends d13_controller
{
	
	private $node, $slotId;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct($node, $slotId)
	{
		$this->node 	= $node;
		$this->slotId 	= $slotId;	
	}


	// ----------------------------------------------------------------------------------------
	// getTemplate
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getTemplate()
	{

		return "module.list";
		
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
		
		$tvars['tvar_sub_list'] = "";
		$tvars['tvar_nodeFaction'] = $this->node->data['faction'];
		
		$this->node->getQueue("build");
		
		foreach($d13->getModule($this->node->data['faction']) as $module) {
		
			$count = $this->node->getModuleCount($module['id']);
			
			if (count($this->node->queue["build"])) {
				foreach($this->node->queue["build"] as $item) {
					if ($item['obj_id'] == $module['id']) {
						$count++;
					}
				}
			}
			
			if ($count < $d13->getModule($this->node->data['faction'], $module['id'], 'maxInstances')) {
				$tmp_module = NULL;
				$tmp_module = d13_module_factory::create($module['id'], $this->slotId, $this->node);
				$tvars['tvar_sub_list'].= $d13->templateSubpage("sub.module.list", $tmp_module->getTemplateVariables());
			}
		}
		
		// - - - - Add Slider Initalize at bottom of the page
		$d13->templateInject($d13->templateSubpage("sub.swiper.horizontal", $tvars));
				
		return $tvars;
	}
	
}

// =====================================================================================EOF

