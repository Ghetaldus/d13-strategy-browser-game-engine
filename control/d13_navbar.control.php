<?php

// ========================================================================================
//
// NAVBAR.CONTROLLER
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_navBarController extends d13_controller
{
	
	private $node;
    private $nodeId;

    // ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct($node)
	{
		
		$this->node = $node;
		
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
		
		$tvars = array();
		$html = '';
		$html_left = '';
		$html_right = '';
	
		// - - - Place all Navigation Options
		foreach($d13->getNavigation() as $nav) {
			if ($nav['active']) {
				if (($nav['login'] && isset($_SESSION[CONST_PREFIX . 'User']['id'])) || (!$nav['login'] && !isset($_SESSION[CONST_PREFIX . 'User']['id']))) {
					if(!$nav['admin'] || $nav['admin'] && (isset($_SESSION[CONST_PREFIX . 'User']['access'])) && ($_SESSION[CONST_PREFIX . 'User']['access'] >= 3)) {
					
						//- - - No Trigger
						if ($nav['trigger'] == '') {
							$icon = $nav['images'][0]['image'];
							$class = $nav['images'][0]['class'];
					
						//- - - Message Trigger
						} else if ($nav['trigger'] == 'messages' && isset($_SESSION[CONST_PREFIX . 'User']['id'])) {
				
							$umc = d13_message::getUnreadCount($_SESSION[CONST_PREFIX . 'User']['id']);
							if ($umc <= 0) {
								$icon = $nav['images'][0]['image'];
								$class = $nav['images'][0]['class'];
							} else {
								$icon = $nav['images'][1]['image'];
								$class = $nav['images'][1]['class'];
							}
					
						}
				
						$html = '<a class="tooltip-top link external" data-tooltip="' . $d13->getLangUI($nav['name']) . '" href="index.php?p=' . $nav['link'] . '"><span><img class="'.$class.' d13-icon" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/icon/' . $icon . '"></span></a>';

						if ($nav['class'] == 'left') {
							$html_left.= $html;
						} else {
							$html_right.= $html;
						}
					
					}
				}
			}
		}

		$tvars['tvar_nodeNavbarLeft'] = $html_left;
		$tvars['tvar_nodeNavbarRight'] = $html_right;

		$subpage = $d13->templateSubpage("sub.navbar", $tvars);
		
		return $subpage;
		
	}

}

// =====================================================================================EOF

