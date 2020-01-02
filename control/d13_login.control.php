<?php

// ========================================================================================
//
// LOGIN.CONTROLLER
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_loginController extends d13_controller
{
	
	private $actionId;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct()
	{
		$this->actionId = $_GET['action'];
		$this->doControl();
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function doControl()
	{
	
		switch ($this->actionId)
		{
		
			case 'sit':
				$this->doAssist();
				break;
				
			case 'login':
				$this->doLogin();
				break;
			
		}
		
		$this->getTemplate();

	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function doLogin()
	{
		
		global $d13;
		
		if (isset($_POST['name'], $_POST['password'])) {
			$name = strtolower($_POST['name']);
			$pass = sha1($_POST['password']);
			if (isset($_POST['remember'])) $remember = 1;
			else $remember = 0;
		}
		else
		if (isset($_COOKIE[CONST_PREFIX . 'Name'], $_COOKIE[CONST_PREFIX . 'Password'])) {
			$name = d13_misc::clean($_COOKIE[CONST_PREFIX . 'Name']);
			$pass = d13_misc::clean($_COOKIE[CONST_PREFIX . 'Password']);
			$remember = 1;
		}

		if (isset($name, $pass)) {
			$user = new d13_user();
			$status = $user->get('name', $name);
			if ($status == 'done')
			if ($d13->getGeneral('options', 'enabledLogin') || $user->data['access'] == 3)
			if ($user->data['password'] == $pass)
			if ($user->data['access']) {
				$user->data['ip'] = $_SERVER['REMOTE_ADDR'];
				$user->data['lastVisit'] = strftime('%Y-%m-%d %H:%M:%S', time());
				$user->set();
				$_SESSION[CONST_PREFIX . 'User'] = $user->data;
				if ($remember) {
					setcookie(CONST_PREFIX . 'Name', $name, (CONST_COOKIE_LIFETIME + time()));
					setcookie(CONST_PREFIX . 'Password', $pass, (CONST_COOKIE_LIFETIME + time()));
				}
				else {
					setcookie(CONST_PREFIX . 'Name', $name, (time() - 1));
					setcookie(CONST_PREFIX . 'Password', $pass, (time() - 1));
				}

				header('Location: index.php?p=node&action=list');
			}
			else $message = $d13->getLangUI("inactive");
			else $message = $d13->getLangUI("wrongPassword");
			else $message = $d13->getLangUI("loginDisabled");
			else $message = $d13->getLangUI($status);
		}
	
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function doAssist()
	{
	
		global $d13;
		
		if (isset($_POST['user'], $_POST['sitter'], $_POST['password'])) {
			$user = new d13_user();
			$status = $user->get('name', $_POST['user']);
			if ($status == 'done') {
				$sitter = new d13_user();
				$status = $sitter->get('name', $_POST['sitter']);
				if ($status == 'done')
				if (sha1($_POST['password']) == $sitter->data['password'])
				if ($user->data['sitter'] == $sitter->data['name']) {
					$user->data['ip'] = $_SERVER['REMOTE_ADDR'];
					$user->data['lastVisit'] = strftime('%Y-%m-%d %H:%M:%S', time());
					$user->set();
					$_SESSION[CONST_PREFIX . 'User'] = $user->data;
					header('Location: index.php');
				}
				else $message = $d13->getLangUI("accessDenied");
				else $message = $d13->getLangUI("wrongPassword");
				else $message = $d13->getLangUI($status);
			}
			else $message = $d13->getLangUI($status);
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
		
		switch ($this->actionId)
		{
	
			case 'sit':

			$d13->templateInject($d13->templateSubpage("sub.assist", $tvars));
			$d13->templateRender("login", $tvars);
			break;

			case 'login':

			$d13->templateInject($d13->templateSubpage("sub.login", $tvars));
			$d13->templateRender("login", $tvars);
			break;
		
			default:
			
			$d13->templateInject($d13->templateSubpage("sub.login", $tvars));
			$d13->templateRender("login", $tvars);
			break;
		
		}
		
	}
}

// =====================================================================================EOF

