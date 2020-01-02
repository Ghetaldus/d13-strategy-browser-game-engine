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

class d13_resetController extends d13_controller
{
	
	private $node, $nodeId;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct()
	{
		
	}

	// ----------------------------------------------------------------------------------------
	// doControl
	// @
	//
	// ----------------------------------------------------------------------------------------
	private function
	
	doControl()
	{
	
		$d13->dbQuery('start transaction');

		if (isset($_POST['name'], $_POST['email'])) {
			
			if ((($_POST['name'] != '')) && ($_POST['email'] != '')) {
				$user = new d13_user();
				$status = $user->get('name', $_POST['name']);
				if ($status == 'done') {
					$newPass = rand(1000000000, 9999999999);
					$status = $user->resetPassword($_POST['email'], $newPass);
					include (CONST_INCLUDE_PATH . 'api/email.api.php');

					$body = CONST_GAME_TITLE . ' ' . $d13->getLangUI("newPassword") . ': ' . $newPass;			//TODO move to template
					
					if ($status == 'done') {
						$status = email(CONST_EMAIL, CONST_GAME_TITLE, $user->data['email'], CONST_GAME_TITLE . ' ' . $d13->getLangUI("resetPassword") , $body);
						$message = $d13->getLangUI($status);
					}
				}
				else {
					$message = $d13->getLangUI($status);
				}
			}
			else {
				$message = $d13->getLangUI("insufficientData");
			}
		}

		if ((isset($status)) && ($status == 'error')) {
			$d13->dbQuery('rollback');
		}
		else {
			$d13->dbQuery('commit');
		}
	
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
		$tvars['tvar_user_email'] = '';
		$tvars['tvar_user_name'] = '';

		if (isset($_SESSION[CONST_PREFIX . 'User']['email'], $_SESSION[CONST_PREFIX . 'User']['name'])) {
			$tvars['tvar_user_email'] 	= $_SESSION[CONST_PREFIX . 'User']['email'];
			$tvars['tvar_user_name']	= $_SESSION[CONST_PREFIX . 'User']['name'];
		}
		
		$d13->templateRender("reset", $tvars);
		
	}

}

// =====================================================================================EOF

