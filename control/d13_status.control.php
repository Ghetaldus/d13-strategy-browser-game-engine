<?php

// ========================================================================================
//
// EMPTY.CONTROLLER
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_statusController extends d13_controller
{
	
	private $user, $own;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct($userId=0)
	{
		
		$tvars = array();
		
		if (isset($_SESSION[CONST_PREFIX . 'User']['id'])) {

			if (isset($_GET['userId'])) {
				$userId = $_GET['userId'];
				$this->own = false;
			} else if ($userId > 0) {
				$this->own = false;
			} else {
				$userId = $_SESSION[CONST_PREFIX . 'User']['id'];
				$this->own = true;
			}

			$this->user = new d13_user($userId);

		} else {
			$this->own = false;
			$message = $d13->getLangUI("accessDenied");
		}
		
		$tvars = $this->doControl();
		$this->getTemplate($tvars);
		
	}
	
	// ----------------------------------------------------------------------------------------
	// doControl
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function doControl()
	{
	
		$tvars = array();
		
		if (isset($_GET['action'])) {
		
			switch ($_GET['action'])
			{
		
				case 'setAvatar':
					$this->userSetAvatar();
					break;
			
		
			}
	
		}
		
		$tvars = $this->user->getTemplateVariables();
		$tvars['tvar_avatarLink'] = $this->getAvatarPopup();
		$tvars['tvar_page'] = "status";
		
		return $tvars;
		
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function userSetAvatar()
	{
		if (isset($_GET['avatarId']))
		{
		
			$status = $this->user->setStat('avatar', $_GET['avatarId']);
			if ($status == 1) {
				header("Location: ?p=status");
				exit();
			}
			
		}
	
	}
	
	// ----------------------------------------------------------------------------------------
	// getAvatarPopup
	// @
	//
	// ----------------------------------------------------------------------------------------
	public

	function getAvatarPopup()
	{
	
		global $d13;
		
		$html = '';
		
		if ($this->own) {
		
			$i = 0;
			$open = false;
			$tvars = array();
			$tvars['tvar_sub_popuplist'] = '';
			$tvars['tvar_listID'] = 1;
		
			foreach ($d13->getAvatar() as $avatar) {
				if ($avatar['active']) {

					if ($avatar['level'] <= $this->user->data['level']) {
						$vars = array();
						$vars['tvar_Image'] 		= "/avatars/" . $avatar['image'];
						$vars['tvar_Link'] 			= "?p=status&action=setAvatar&avatarId=" . $avatar['id'];
						
						if ($i %2 == 0 || $i == 0) {
						$open = true;
						$tvars['tvar_sub_popuplist'] .= '<div class="row">';
						}
						
						$tvars['tvar_sub_popuplist'] .= $d13->templateSubpage("sub.module.imagecontent", $vars);
						if ($i %2 != 0) {
						$tvars['tvar_sub_popuplist'] .= '</div>';
						}
						$i++;
					}

				}
			}
			
			if ($open) {
				$tvars['tvar_sub_popuplist'] .= '</div>';
			}
			
			if ($i > 0) {
				$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
				
				$vars['tvar_button_name'] 	 = $d13->getLangUI("set") . " " . $d13->getLangUI("avatar");
				$vars['tvar_list_id'] 		 = "list-1"; 
				$vars['tvar_button_tooltip'] = ""; 
				$html = $d13->templateSubpage("button.popup.enabled", $vars);
				
			} else {
				$vars['tvar_button_name'] 	 = $d13->getLangUI("set") . " " . $d13->getLangUI("avatar");
				$vars['tvar_button_tooltip'] = ""; 
				$html = $d13->templateSubpage("button.popup.disabled", $vars);
			}
		
		}
		
		return $html;

	}

	// ----------------------------------------------------------------------------------------
	// getTemplate
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getTemplate($tvars)
	{
	
		global $d13;
		
		
		
		$d13->templateRender($tvars['tvar_page'], $tvars);

	}

}

// =====================================================================================EOF

