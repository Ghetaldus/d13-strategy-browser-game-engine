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

class d13_allianceController extends d13_controller
{
	
	private $node, $alliance, $node_status, $ally_status, $own;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct()
	{
		
		global $d13;
		
		$tvars 				= array();
		$this->node 		= new d13_node();
		$this->node_status 	= $this->node->get('id', $_SESSION[CONST_PREFIX . 'User']['node']);
		$this->alliance 	= new d13_alliance();
		$this->ally_status 	= $this->alliance->get('id', $_SESSION[CONST_PREFIX . 'User']['alliance']);
		$this->own = true;
		
		$tvars = $this->doControl();
		$this->getTemplate($tvars);
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function doControl()
	{
	
		switch ($_GET['action']) {
		
			case 'get':
				return $this->allianceGet();
				break;
			
			case 'set':
				return $this->allianceSet();
				break;
			
			case 'add':
				return $this->allianceAdd();
				break;
			
			case 'remove':
				return $this->allianceRemove();
				break;
			
			case 'addInvitation':
				return $this->allianceAddInvitation();
				break;
			
			case 'removeInvitation':
				return $this->allianceRemoveInvitation();
				break;
			
			case 'acceptInvitation':
				return $this->allianceAcceptInvitation();
				break;
			
			case 'removeMember':
				return $this->allianceRemoveMember();
				break;
			
			case 'addWar':
				return $this->allianceAddWar();
				break;
			
			case 'proposePeace':
				return $this->allianceProposePeace();
				break;
			
			case 'removePeace':
				return $this->allianceRemovePeace();
				break;
				
			case 'acceptPeace':
				return $this->allianceAcceptPeace();
				break;

		}
	
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceGet()
	{
		
		global $d13;
		
		$tvars = array();
		$html = "";
		
		if ($this->node->checkOptions('allianceGet')) {
			if ($_SESSION[CONST_PREFIX . 'User']['alliance']) {
				if ($this->ally_status == 'done') {
					$this->alliance->getAll();
				} else {
					$message = $d13->getLangUI($this->ally_status);
				}
			} else {
				$invitations = d13_alliance::getInvitations('user', $_SESSION[CONST_PREFIX . 'User']['id']);
			}
		
			if (isset($this->alliance->data['id'])) {
				if ($this->alliance->members) {
					$html.= '<div class="section"> -> {{tvar_ui_members}}</div>';
					foreach($this->alliance->members as $member) {
						$html.= '<div class="right">' . $member['name'];
						if (($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) || ($member['id'] == $_SESSION[CONST_PREFIX . 'User']['id'])) $html.= ' | <a class="external" href="index.php?p=alliance&action=removeMember&user=' . $member['id'] . '">x</a>';
						$html.= '</div>';
					}
				}

				if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) $html.= '<div class="section" style="margin-top: 5px;"> -> {{tvar_ui_invitations}} | <a class="external" href="index.php?p=alliance&action=addInvitation">{{tvar_ui_invite}}</a></div>';
				foreach($this->alliance->invitations as $invitation) {
					$user = new d13_user();
					if ($user->get('id', $invitation['user']) == 'done') {
						$accept = '';
						$removeLabel = 'x';
						if ($user->data['id'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
							$accept = '<a class="external" href="?p=alliance&action=acceptInvitation&alliance=' . $invitation['alliance'] . '&user=' . $invitation['user'] . '">{{tvar_ui_accept}}</a> | ';
							$removeLabel = $d13->getLangUI('decline');
						}

						$html.= '<div class="right"> ' . $user->data['name'] . ' | ' . $accept . '<a class="external" href="index.php?p=alliance&action=removeInvitation&alliance=' . $invitation['alliance'] . '&user=' . $invitation['user'] . '">' . $removeLabel . '</a></div>';
					}
				}

				if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) $html.= '<div class="section" style="margin-top: 5px;"> -> {{tvar_ui_wars}} | <a class="external" href="index.php?p=alliance&action=addWar">{{tvar_ui_goToWar}}</a></div>';
				foreach($this->alliance->wars as $war) {
					$otherAlliance = new d13_alliance();
					if ($this->alliance->data['id'] == $war['sender']) $otherAllianceId = $war['recipient'];
					else $otherAllianceId = $war['sender'];
					if ($otherAlliance->get('id', $otherAllianceId) == 'done')
					if ($war['type']) $html.= '<div class="right">' . $otherAlliance->data['name'] . ' | <a class="external" href="index.php?p=alliance&action=proposePeace&recipient=' . $otherAlliance->data['id'] . '">{{tvar_ui_peace}}</a></div>';
					else {
						if ($this->alliance->data['id'] == $war['recipient']) $ad = '<a class="external" href="index.php?p=alliance&action=acceptPeace&sender=' . $war['sender'] . '&recipient=' . $war['recipient'] . '">{{tvar_ui_accept}}</a> / <a class="external" href="index.php?p=alliance&action=removePeace&recipient=' . $otherAlliance->data['id'] . '">{{tvar_ui_decline}}</a>';
						else $ad = '<a class="external" href="?p=alliance&action=removePeace&recipient=' . $otherAlliance->data['id'] . '">x</a>';
						$html.= '<div class="right">' . $otherAlliance->data['name'] . ' {{tvar_ui_peace}} | ' . $ad . '</div>';
					}
				}
			} else {
				$html.= '<div class="section"><a class="external" href="?p=alliance&action=add">{{tvar_ui_add}}</a></div>';
				if ($invitations) {
					$html.= '<div class="section"> -> {{tvar_ui_invitations}}</div>';
					foreach($invitations as $invitation) {
						$user = new d13_user();
						if ($user->get('id', $invitation['user']) == 'done') {
							$accept = '';
							$removeLabel = 'x';
							if ($user->data['id'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
								$accept = '<a class="external" href="?p=alliance&action=acceptInvitation&alliance=' . $invitation['alliance'] . '&user=' . $invitation['user'] . '">{{tvar_ui_accept}}</a> | ';
								$removeLabel = $d13->getLangUI('decline');
							}

							$html.= '<div class="right"> ' . $user->data['name'] . ' | ' . $accept . '<a class="external" href="index.php?p=alliance&action=removeInvitation&alliance=' . $invitation['alliance'] . '&user=' . $invitation['user'] . '">' . $removeLabel . '</a></div>';
						}
					}
				}
			}

			$tvars['tvar_allianceHTML'] = $html;

			
		
		}
		
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceSet()
	{
		
		global $d13;
		
		$tvars = array();
		
		if ($this->node->checkOptions('allianceSet')) {

			$nodes = d13_node::getList($_SESSION[CONST_PREFIX . 'User']['id']);
			$nodeList = '';
			foreach($nodes as $node) {
				$nodeList.= '<option value="' . $node->data['id'] . '">' . $node->data['name'] . '</option>';
			}
			
			if (isset($_POST['nodeId'], $_POST['name']))
			if ($_POST['name'] != '')
			if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
				$node = new d13_node();
				$status = $node->get('id', $_POST['nodeId']);
				if ($status == 'done')
				if ($node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
					$this->alliance->data['name'] 	= $_POST['name'];
					$this->alliance->data['tag'] 	= $_POST['tag'];
					$this->alliance->data['avatar'] = $_POST['avatar'];
					$status = $this->alliance->set($node->data['id']);
					$message = $d13->getLangUI($status);
				}
				else $message = $d13->getLangUI("accessDenied");
				else $message = $d13->getLangUI($status);
			}
			else $message = $d13->getLangUI("accessDenied");
			else $message = $d13->getLangUI("insufficientData");
		}
		else {
			$message = $d13->getLangUI("accessDenied");
		}

		
		$costData = '';
		foreach($d13->getFaction($this->node->data['faction'], 'costs', 'alliance') as $key => $cost) {
			$costData.= '<div class="cell">' . $cost['value'] . '</div><div class="cell"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"></div>';
		}
		
		if (isset($_GET['avatarId'])) {
			$tvars['tvar_allianceAvatar'] = $d13->getAlliance($_GET['avatarId'], 'image');
			$tvars['tvar_avatarId'] = $_GET['avatarId'];
		} else {
			$tvars['tvar_allianceAvatar'] = $d13->getAlliance($this->alliance->data['avatar'], 'image');
			$tvars['tvar_avatarId'] = $this->alliance->data['avatar'];
		}

		$tvars['tvar_nodeList'] = $nodeList;
		$tvars['tvar_costData'] = $costData;
		$tvars['tvar_allianceName'] = $this->alliance->data['name'];
		$tvars['tvar_allianceTag'] = $this->alliance->data['tag'];
		
		$tvars['tvar_nodeID'] = $this->node->data['id'];
		$tvars['tvar_nodeName'] = $this->node->data['name'];
		
		
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceAdd()
	{
		
		global $d13;
		
		$tvars = array();
		
		if ($this->node->checkOptions('allianceAdd')) {
			if ($this->ally_status == 'noAlliance') {
			
				$nodeList = '';
				$nodes = d13_node::getList($_SESSION[CONST_PREFIX . 'User']['id']);
				if ($nodes) {
					
					foreach($nodes as $node) {
						$nodeList.= '<option value="' . $node->data['id'] . '">' . $node->data['name'] . '</option>';
					}
					
					if (isset($_POST['nodeId'], $_POST['name']))
					if ($_POST['name'] != '') {
						$alliance = new d13_alliance();
						$node = new d13_node();
						$status = $node->get('id', $_POST['nodeId']);
						if ($status == 'done') {
							if ($node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
								$alliance->data['name'] = $_POST['name'];
								$alliance->data['user'] = $_SESSION[CONST_PREFIX . 'User']['id'];
								$status = $alliance->add($node->data['id']);
								if ($status == 'done') {
									$status = $alliance->get('name', $_POST['name']);
									if ($status == 'done') {
										$_SESSION[CONST_PREFIX . 'User']['alliance'] = $alliance->data['id'];
									}
								}
								$message = $d13->getLangUI($status);
							} else {
								$message = $d13->getLangUI("accessDenied");
							}
						} else {
							$message = $d13->getLangUI($status);
						}
					} else {
						$message = $d13->getLangUI("insufficientData");
					}
				} else {
					$message = $d13->getLangUI("noNode");
				}
			} else {
				$message = $d13->getLangUI("allianceSet");
			}
		} else {
			$message = $d13->getLangUI("accessDenied");
		}
	
		$costData = '';
		foreach($d13->getFaction($this->node->data['faction'], 'costs', 'alliance') as $key => $cost) {
			$costData.= '<div class="cell">' . $cost['value'] . '</div><div class="cell"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"></div>';
		}

		$tvars['tvar_nodeList'] = $nodeList;
		$tvars['tvar_costData'] = $costData;
		$tvars['tvar_nodeID'] = $this->node->data['id'];
		$tvars['tvar_nodeName'] = $this->node->data['name'];
			
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceRemove()
	{
		
		global $d13;
		
		$tvars = array();
		
		if ($this->node->checkOptions('allianceRemove')) {
			if ((isset($_GET['go'])) && ($_GET['go']))
			if ($_SESSION[CONST_PREFIX . 'User']['alliance']) {
				if ($this->ally_status == 'done')
				if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
					$status = d13_alliance::remove($_SESSION[CONST_PREFIX . 'User']['alliance']);
					if ($status == 'done') {
						$_SESSION[CONST_PREFIX . 'User']['alliance'] = 0;
						header('Location ?p=alliance&action=get');
					}
					else $message = $d13->getLangUI($status);
				}
				else $message = $d13->getLangUI("accessDenied");
				else $message = $d13->getLangUI($status);
			}
			else $message = $d13->getLangUI("insufficientData");
		}
		else {
			$message = $d13->getLangUI("accessDenied");
		}

		return $tvars;
	
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceAddInvitation()
	{
		
		global $d13;
		
		$tvars = array();
		
		if ($this->node->checkOptions('allianceInvite')) {
			if (isset($_POST['name']))
			if ($_POST['name'] != '')
			if ($this->ally_status == 'done')
			if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
				$user = new d13_user();
				if ($user->get('name', $_POST['name']) == 'done') {
					$status = $this->alliance->addInvitation($user->data['id']);
					if ($status == 'done') {
						$user->getPreferences('name');
						if ($user->preferences['allianceReports']) {
							$msg = new d13_message();
							$msg->data['sender'] = $_SESSION[CONST_PREFIX . 'User']['name'];
							$msg->data['recipient'] = $user->data['name'];
							$msg->data['subject'] = $d13->getLangUI("allianceInvitation");
							$msg->data['body'] = '<a class=\"link\" href=\"index.php?p=alliance&action=acceptInvitation&alliance=' . $this->alliance->data['id'] . '&user=' . $user->data['id'] . '\">' . $d13->getLangUI("accept") . '</a> ' . $this->alliance->data['name'] . ' ' . $d13->getLangUI("alliance");
							$msg->data['type'] = 'alliance';
							$msg->data['viewed'] = 0;
							$status = $msg->add();
						}
					}

					$message = $d13->getLangUI($status);
				}
				else $message = $d13->getLangUI("noUser");
			}
			else $message = $d13->getLangUI("accessDenied");
			else $message = $d13->getLangUI("noAlliance");
			else $message = $d13->getLangUI("insufficientData");
		}
		else {
			$message = $d13->getLangUI("accessDenied");
		}

		return $tvars;
	
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceRemoveInvitation()
	{
		
		global $d13;
		
		$tvars = array();
		
		if (isset($_GET['alliance'], $_GET['user'])) {
			$senderAlliance = new d13_alliance();
			if ($senderAlliance->get('id', $_GET['alliance']) == 'done')
			if (in_array($_SESSION[CONST_PREFIX . 'User']['id'], array(
				$_GET['user'],
				$senderAlliance->data['user']
			))) {
				$status = d13_alliance::removeInvitation($_GET['alliance'], $_GET['user']);
				if ($status == 'done') header('Location: ?p=alliance&action=get');
				else $message = $d13->getLangUI($status);
			}
			else $message = $d13->getLangUI("accessDenied");
			else $message = $d13->getLangUI("noAlliance");
		}
		else $message = $d13->getLangUI("insufficientData");
	
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceAcceptInvitation()
	{
		
		global $d13;
		
		$tvars = array();
		
		if (isset($_GET['alliance'], $_GET['user']))
		if ($_SESSION[CONST_PREFIX . 'User']['id'] == $_GET['user']) {
			$status = d13_alliance::acceptInvitation($_GET['alliance'], $_GET['user']);
			if ($status == 'done') $_SESSION[CONST_PREFIX . 'User']['alliance'] = $_GET['alliance'];
			$message = $d13->getLangUI($status);
		}
		else $message = $d13->getLangUI("accessDenied");
		else $message = $d13->getLangUI("insufficientData");
	
		return $tvars;
	
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceRemoveMember()
	{
		
		global $d13;
		
		$tvars = array();
		
		if ($this->node->checkOptions('allianceRemoveMember')) {
			if ($this->ally_status == 'done')
			if (isset($_GET['user']))
			if ((($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) && ($_GET['user'] != $_SESSION[CONST_PREFIX . 'User']['id'])) || (($this->alliance->data['user'] != $_SESSION[CONST_PREFIX . 'User']['id']) && ($_GET['user'] == $_SESSION[CONST_PREFIX . 'User']['id']))) {
				$status = $this->alliance->removeMember($_GET['user']);
				if ($status == 'done') {
					if ($_GET['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) $_SESSION[CONST_PREFIX . 'User']['alliance'] = 0;
					header('Location: ?p=alliance&action=get');
				}

				$message = $d13->getLangUI($status);
			}
			else $message = $d13->getLangUI("accessDenied");
			else $message = $d13->getLangUI("insufficientData");
			else $message = $d13->getLangUI("noAlliance");
		}
		else {
			$message = $d13->getLangUI("accessDenied");
		}
	
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceAddWar()
	{
		
		global $d13;
		
		$tvars = array();
		
		if ($this->node->checkOptions('allianceWar')) {
			if (isset($_POST['name']))
			if ($_POST['name'] != '')
			if ($this->ally_status == 'done')
			if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
				$recipientAlliance = new d13_alliance();
				if ($recipientAlliance->get('name', $_POST['name']) == 'done')
				if ($this->alliance->data['id'] != $recipientAlliance->data['id']) {
					$status = $this->alliance->addWar($recipientAlliance->data['id']);
					if ($status == 'done') {
						$user = new d13_user();
						if ($user->get('id', $recipientAlliance->data['user']) == 'done') {
							$user->getPreferences('name');
							if ($user->preferences['allianceReports']) {
								$msg = new d13_message();
								$msg->data['sender'] = $_SESSION[CONST_PREFIX . 'User']['name'];
								$msg->data['recipient'] = $user->data['name'];
								$msg->data['subject'] = $d13->getLangUI("warDeclaration");
								$msg->data['body'] = $d13->getLangUI("sender") . ': ' . $this->alliance->data['name'] . ' ' . $d13->getLangUI("alliance");
								$msg->data['type'] = 'alliance';
								$msg->data['viewed'] = 0;
								$status = $msg->add();
								if ($status == 'done') header('Location ?p=alliance&action=get');
							}
						}
						else $message = $d13->getLangUI("noUser");
					}

					$message = $d13->getLangUI($status);
				}
				else $message = $d13->getLangUI("accessDenied");
				else $message = $d13->getLangUI("noAlliance");
			}
			else $message = $d13->getLangUI("accessDenied");
			else $message = $d13->getLangUI("noAlliance");
			else $message = $d13->getLangUI("insufficientData");
		}
		else {
			$message = $d13->getLangUI("accessDenied");
		}
		
		return $tvars;
	
	}
	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceProposePeace()
	{
		
		global $d13;
		
		$tvars = array();
		
		if ($this->node->checkOptions('alliancePeace')) {
			if (isset($_GET['recipient']))
			if ($this->ally_status == 'done')
			if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
				$recipientAlliance = new d13_alliance();
				if ($recipientAlliance->get('id', $_GET['recipient']) == 'done') {
					$status = $this->alliance->proposePeace($recipientAlliance->data['id']);
					if ($status == 'done') header('Location ?p=alliance&action=get');
					$message = $d13->getLangUI($status);
				}
				else $message = $d13->getLangUI("noAlliance");
			}
			else $message = $d13->getLangUI("accessDenied");
			else $message = $d13->getLangUI("noAlliance");
			else $message = $d13->getLangUI("insufficientData");
		}
		else {
			$message = $d13->getLangUI("accessDenied");
		}
	
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceRemovePeace()
	{
		
		global $d13;
		
		$tvars = array();
		
		if (isset($_GET['recipient']))
		if ($this->ally_status == 'done')
		if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
			$recipientAlliance = new d13_alliance();
			if ($recipientAlliance->get('id', $_GET['recipient']) == 'done') {
				$status = $this->alliance->removePeace($recipientAlliance->data['id']);
				if ($status == 'done') header('Location ?p=alliance&action=get');
				else $message = $d13->getLangUI($status);
			}
			else $message = $d13->getLangUI("noAlliance");
		}
		else $message = $d13->getLangUI("accessDenied");
		else $message = $d13->getLangUI("noAlliance");
		else $message = $d13->getLangUI("insufficientData");
	
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceAcceptPeace()
	{
	
		global $d13;
	
		$tvars = array();
		
		if (isset($_GET['sender'], $_GET['recipient']))
		if ($this->ally_status == 'done')
		if (($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) && ($this->alliance->data['id'] == $_GET['recipient'])) {
			$senderAlliance = new d13_alliance();
			if ($senderAlliance->get('id', $_GET['sender']) == 'done') {
				$status = $this->alliance->acceptPeace($senderAlliance->data['id']);
				if ($status == 'done') {
					$user = new d13_user();
					if ($user->get('id', $senderAlliance->data['user']) == 'done') {
						$user->getPreferences('name');
						if ($user->preferences['allianceReports']) {
							$msg = new d13_message();
							$msg->data['sender'] = $_SESSION[CONST_PREFIX . 'User']['name'];
							$msg->data['recipient'] = $user->data['name'];
							$msg->data['subject'] = $d13->getLangUI("peaceAccepted");
							$msg->data['body'] = $d13->getLangUI("sender") . ': ' . $this->alliance->data['name'] . ' ' . $d13->getLangUI("alliance");
							$msg->data['type'] = 'alliance';
							$msg->data['viewed'] = 0;
							$status = $msg->add();
							if ($status == 'done') header('Location ?p=alliance&action=get');
						}
					}
					else $message = $d13->getLangUI("noUser");
				}

				$message = $d13->getLangUI($status);
			}
			else $message = $d13->getLangUI("noAlliance");
		}
		else $message = $d13->getLangUI("accessDenied");
		else $message = $d13->getLangUI("noAlliance");
		else $message = $d13->getLangUI("insufficientData");
	
		return $tvars;
	
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
		
			foreach ($d13->getAlliance() as $avatar) {
				if ($avatar['active']) {

					if ($avatar['level'] <= $this->alliance->data['level']) {
						$vars = array();
						$vars['tvar_Image'] 		= "/alliances/" . $avatar['image'];
						$vars['tvar_Link'] 			= "?p=alliance&action=set&avatarId=" . $avatar['id'];
						
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
				$vars['tvar_list_id'] 	 	 = "list-1";
				$vars['tvar_button_tooltip'] = "";
				$html = $d13->templateSubpage("button.popup.enabled", $vars);
				
			} else {
				$vars['tvar_button_name'] 	 = $d13->getLangUI("set") . " " . $d13->getLangUI("avatar");
				$vars['tvar_button_tooltip'] = "";
				$html . $d13->templateSubpage("button.popup.disabled", $vars);
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
		
		$tvars['tvar_avatarLink'] = $this->getAvatarPopup();
		
		$tvars['tvar_allianceGet'] = '<a class="external" href="?p=alliance&action=get">' . $this->alliance->data['name'] . '</a>';
		$tvars['tvar_allianceSet'] = "";
		$tvars['tvar_allianceRemove'] = "";

		if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
			$tvars['tvar_allianceSet'] = '<a class="external" href="?p=alliance&action=set">' . $d13->getLangUI("set") . '</a>';
			$tvars['tvar_allianceRemove'] = '<a class="external" href="?p=alliance&action=remove">' . $d13->getLangUI("remove") . '</a>';
		}

		$tvars['tvar_tpl_allianceMenu'] = $d13->templateSubpage("alliance.menu", $tvars);

		
		switch ($_GET['action']) {
		
			case 'get':
				$d13->templateRender('alliance.get', $tvars);
				break;
				
			case 'set':
				$d13->templateRender('alliance.set', $tvars);
				break;
			
			case 'add':
				$d13->templateRender('alliance.add', $tvars);
				break;
			
			case 'remove':
				$d13->templateRender('alliance.remove', $tvars);
				break;
			
			case 'addInvitation':
				$d13->templateRender('alliance.addInvitation', $tvars);
				break;
			
			case 'addWar':
				$d13->templateRender('alliance.addWar', $tvars);
				break;
			
			default:
				$d13->templateRender('alliance', $tvars);
				break;

		}
		
	}

}

// =====================================================================================EOF

