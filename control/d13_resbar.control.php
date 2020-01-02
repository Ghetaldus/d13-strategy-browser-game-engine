<?php

// ========================================================================================
//
// RESBAR.CONTROLLER
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_resBarController extends d13_controller
{
	
	private $node, $user;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct($node)
	{
		
		$this->node 	= $node;
		$this->node->getResources();
	
		$this->user 	= new d13_user();
		
	}

	// ----------------------------------------------------------------------------------------
	// getTemplate
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getTemplate()
	{
	
		global $d13;
		
		$html = '';
		$tvars = array();
		
		$tvars['tvar_resEntry'] 	= '';
		
		//- - - - - Left Panel (not used ATM)
		$tvars['tvar_leftOptions'] 	= '';
		
		//- - - - - Player Stats
		$status = $this->user->get('id', $_SESSION[CONST_PREFIX . 'User']['id']);
		if ($status == 'done') {
			foreach($d13->getGeneral("userstats") as $stat) {
				if ($stat['active'] && $stat['visible']) {
					$tvars['tvar_resImage'] 		= $stat['icon'];
					$tvars['tvar_resValue'] 		= $this->user->data[$stat['value']];
					$tvars['tvar_resColor'] 		= $stat['color'];
					if ($stat['isExp']) {
					$tvars['tvar_resPercentage'] 	= d13_misc::percentage(floor($this->user->data[$stat['value']]), d13_misc::nextlevelexp($this->user->data['level']));
					} else {
					$tvars['tvar_resPercentage'] 	= 0;
					}
					$tvars['tvar_resTooltip'] 		= $d13->getLangUI($stat['name']);
					if ($stat['isExp']) {
					$tvars['tvar_resTooltip'] 		.= ' '. floor($this->user->data[$stat['value']]) . '/' . d13_misc::nextlevelexp($this->user->data['level']);
					} else {
					$tvars['tvar_resTooltip'] 		.= ' '. floor($this->user->data[$stat['value']]) ;
					}
					$tvars['tvar_resEntry']			.= $d13->templateSubpage("sub.resource.entry", $tvars);
				}
			}
		}
	
		//- - - - - Resources
		if (isset($this->node) && isset($this->node->resources)) {
			foreach($this->node->resources as $resource) {
				if ($d13->getResource($resource['id'], 'active') && $d13->getResource($resource['id'], 'visible')) {
				
					$tvars['tvar_resImage'] = $d13->getResource($resource['id'], 'icon');
					$tvars['tvar_resColor'] = $d13->getResource($resource['id'], 'color');
					$tvars['tvar_resValue'] 	= 0;
					$tvars['tvar_resPercentage'] = 0;
					$tvars['tvar_resTooltip'] 	= '';
				
					$tvars['tvar_resTooltip'] .= $d13->getLangGL('resources', $resource['id'], 'name') . ' ';
					if ($d13->getResource($resource['id'], 'limited')) {
						$tvars['tvar_resValue'] = floor($resource['value']) . '/' . floor($this->node->storage[$resource['id']]);
						$tvars['tvar_resPercentage'] = d13_misc::percentage(floor($resource['value']), floor($this->node->storage[$resource['id']]));
						$tvars['tvar_resTooltip'] .= floor($resource['value']) . '/' . floor($this->node->storage[$resource['id']]);
					} else {
						$tvars['tvar_resValue'] = floor($resource['value']);
						$tvars['tvar_resTooltip'] .= floor($resource['value']);
					
					}
					if ($this->node->production[$resource['id']]) {
						if (floor($resource['value']) < $this->node->storage[$resource['id']]) {
							$tvars['tvar_resTooltip'] .= ' [+' . round($this->node->production[$resource['id']]) . $d13->getLangUI('perHour') . ']';
						}
						else {
							if ($d13->getResource($resource['id'], 'limited')) {
								$tvars['tvar_resTooltip'] .= ' [' . $d13->getLangUI("full") . ']';
							}
						}
					}

					$tvars['tvar_resEntry'].= $d13->templateSubpage("sub.resource.entry", $tvars);
				}
			}
		}
	
		$tvars['tvar_nodeResources'] = $tvars['tvar_resEntry'];

		//- - - - - Open Right Panel
		$tvars['tvar_rightOptions'] = '';
		$html = $this->node->queues->getQueueExpireNext();
	
		if ($this->node->queues->getQueueCount() > 1) {
		$tvars['tvar_linkClass'] 	= 'open-panel"';
		$tvars['tvar_linkData'] 	= 'data-panel="right"';
		$tvars['tvar_linkImage'] 	= 'previous.png';
		$tvars['tvar_linkTooltip'] 	= $d13->getLangUI('active') . " " . $d13->getLangUI('task');
		$tvars['tvar_linkLabel'] 	= '';
		$tvars['tvar_rightOptions']	= $d13->templateSubpage("sub.resource.link", $tvars);
		}
		if (!empty($html)) {
		$tvars['tvar_rightOptions'] .= $html;
		}
		
		return $d13->templateSubpage("sub.resources", $tvars);
		
	}

}

// =====================================================================================EOF

