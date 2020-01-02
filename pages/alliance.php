<?php

// ========================================================================================
//
// ALLIANCE
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================
// ----------------------------------------------------------------------------------------
// PROCESS MODEL
// ----------------------------------------------------------------------------------------

global $d13;
$message = NULL;
$d13->dbQuery('start transaction');

if (isset($_SESSION[CONST_PREFIX . 'User']['id'], $_GET['action'])) {

	$node = new d13_node();
	$status = $node->get('id', $_GET['nodeId']);
	$alliance = new d13_alliance();
	$status = $alliance->get('id', $_SESSION[CONST_PREFIX . 'User']['alliance']);
	switch ($_GET['action']) {

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

	case 'get':
		if ($node->checkOptions('allianceGet')) {
			if ($_SESSION[CONST_PREFIX . 'User']['alliance']) {
				if ($status == 'done') {
					$alliance->getAll();
				}
				else {
					$message = $d13->getLangUI($status);
				}
			}
			else {
				$invitations = d13_alliance::getInvitations('user', $_SESSION[CONST_PREFIX . 'User']['id']);
			}
		}
		else {
			$message = $d13->getLangUI("accessDenied");
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

	case 'set':
		if ($node->checkOptions('allianceSet')) {
			$nodes = d13_node::getList($_SESSION[CONST_PREFIX . 'User']['id']);
			$nodeList = '';
			foreach($nodes as $node) $nodeList.= '<option value="' . $node->data['id'] . '">' . $node->data['name'] . '</option>';
			if (($status == 'done') && (isset($_POST['nodeId'], $_POST['name'])))
			if ($_POST['name'] != '')
			if ($alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
				$node = new d13_node();
				$status = $node->get('id', $_POST['nodeId']);
				if ($status == 'done')
				if ($node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
					$alliance->data['name'] = $_POST['name'];
					$status = $alliance->set($node->data['id']);
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

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

	case 'add':
		if ($node->checkOptions('allianceAdd')) {
			if ($status == 'noAlliance') {
				$nodes = d13_node::getList($_SESSION[CONST_PREFIX . 'User']['id']);
				if ($nodes) {
					$nodeList = '';
					foreach($nodes as $node) $nodeList.= '<option value="' . $node->data['id'] . '">' . $node->data['name'] . '</option>';
					if (isset($_POST['nodeId'], $_POST['name']))
					if ($_POST['name'] != '') {
						$alliance = new d13_alliance();
						$node = new d13_node();
						$status = $node->get('id', $_POST['nodeId']);
						if ($status == 'done')
						if ($node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
							$alliance->data['name'] = $_POST['name'];
							$alliance->data['user'] = $_SESSION[CONST_PREFIX . 'User']['id'];
							$status = $alliance->add($node->data['id']);
							if ($status == 'done') {
								$status = $alliance->get('name', $_POST['name']);
								if ($status == 'done') $_SESSION[CONST_PREFIX . 'User']['alliance'] = $alliance->data['id'];
							}

							$message = $d13->getLangUI($status);
						}
						else $message = $d13->getLangUI("accessDenied");
						else $message = $d13->getLangUI($status);
					}
					else $message = $d13->getLangUI("insufficientData");
				}
				else $message = $d13->getLangUI("noNode");
			}
			else $message = $d13->getLangUI("allianceSet");
		}
		else {
			$message = $d13->getLangUI("accessDenied");
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

	case 'remove':
		if ($node->checkOptions('allianceRemove')) {
			if ((isset($_GET['go'])) && ($_GET['go']))
			if ($_SESSION[CONST_PREFIX . 'User']['alliance']) {
				if ($status == 'done')
				if ($alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
					$status = d13_alliance::remove($_SESSION[CONST_PREFIX . 'User']['alliance']);
					if ($status == 'done') {
						$_SESSION[CONST_PREFIX . 'User']['alliance'] = 0;
						header('location: alliance.php?action=get');
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

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

	case 'addInvitation':
		if ($node->checkOptions('allianceInvite')) {
			if (isset($_POST['name']))
			if ($_POST['name'] != '')
			if ($status == 'done')
			if ($alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
				$user = new d13_user();
				if ($user->get('name', $_POST['name']) == 'done') {
					$status = $alliance->addInvitation($user->data['id']);
					if ($status == 'done') {
						$user->getPreferences('name');
						if ($user->preferences['allianceReports']) {
							$msg = new d13_message();
							$msg->data['sender'] = $_SESSION[CONST_PREFIX . 'User']['name'];
							$msg->data['recipient'] = $user->data['name'];
							$msg->data['subject'] = $d13->getLangUI("allianceInvitation");
							$msg->data['body'] = '<a class=\"link\" href=\"index.php?p=alliance&action=acceptInvitation&alliance=' . $alliance->data['id'] . '&user=' . $user->data['id'] . '\">' . $d13->getLangUI("accept") . '</a> ' . $alliance->data['name'] . ' ' . $d13->getLangUI("alliance");
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

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

	case 'removeInvitation':
		if (isset($_GET['alliance'], $_GET['user'])) {
			$senderAlliance = new d13_alliance();
			if ($senderAlliance->get('id', $_GET['alliance']) == 'done')
			if (in_array($_SESSION[CONST_PREFIX . 'User']['id'], array(
				$_GET['user'],
				$senderAlliance->data['user']
			))) {
				$status = d13_alliance::removeInvitation($_GET['alliance'], $_GET['user']);
				if ($status == 'done') header('Location: alliance.php?action=get');
				else $message = $d13->getLangUI($status);
			}
			else $message = $d13->getLangUI("accessDenied");
			else $message = $d13->getLangUI("noAlliance");
		}
		else $message = $d13->getLangUI("insufficientData");
		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

	case 'acceptInvitation':
		if (isset($_GET['alliance'], $_GET['user']))
		if ($_SESSION[CONST_PREFIX . 'User']['id'] == $_GET['user']) {
			$status = d13_alliance::acceptInvitation($_GET['alliance'], $_GET['user']);
			if ($status == 'done') $_SESSION[CONST_PREFIX . 'User']['alliance'] = $_GET['alliance'];
			$message = $d13->getLangUI($status);
		}
		else $message = $d13->getLangUI("accessDenied");
		else $message = $d13->getLangUI("insufficientData");
		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

	case 'removeMember':
		if ($node->checkOptions('allianceRemoveMember')) {
			if ($status == 'done')
			if (isset($_GET['user']))
			if ((($alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) && ($_GET['user'] != $_SESSION[CONST_PREFIX . 'User']['id'])) || (($alliance->data['user'] != $_SESSION[CONST_PREFIX . 'User']['id']) && ($_GET['user'] == $_SESSION[CONST_PREFIX . 'User']['id']))) {
				$status = $alliance->removeMember($_GET['user']);
				if ($status == 'done') {
					if ($_GET['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) $_SESSION[CONST_PREFIX . 'User']['alliance'] = 0;
					header('Location: alliance.php?action=get');
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

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

	case 'addWar':
		if ($node->checkOptions('allianceWar')) {
			if (isset($_POST['name']))
			if ($_POST['name'] != '')
			if ($status == 'done')
			if ($alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
				$recipientAlliance = new d13_alliance();
				if ($recipientAlliance->get('name', $_POST['name']) == 'done')
				if ($alliance->data['id'] != $recipientAlliance->data['id']) {
					$status = $alliance->addWar($recipientAlliance->data['id']);
					if ($status == 'done') {
						$user = new d13_user();
						if ($user->get('id', $recipientAlliance->data['user']) == 'done') {
							$user->getPreferences('name');
							if ($user->preferences['allianceReports']) {
								$msg = new d13_message();
								$msg->data['sender'] = $_SESSION[CONST_PREFIX . 'User']['name'];
								$msg->data['recipient'] = $user->data['name'];
								$msg->data['subject'] = $d13->getLangUI("warDeclaration");
								$msg->data['body'] = $d13->getLangUI("sender") . ': ' . $alliance->data['name'] . ' ' . $d13->getLangUI("alliance");
								$msg->data['viewed'] = 0;
								$status = $msg->add();
								if ($status == 'done') header('Location: alliance.php?action=get');
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

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

	case 'proposePeace':
		if ($node->checkOptions('alliancePeace')) {
			if (isset($_GET['recipient']))
			if ($status == 'done')
			if ($alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
				$recipientAlliance = new d13_alliance();
				if ($recipientAlliance->get('id', $_GET['recipient']) == 'done') {
					$status = $alliance->proposePeace($recipientAlliance->data['id']);
					if ($status == 'done') header('Location: alliance.php?action=get');
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

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

	case 'removePeace':
		if (isset($_GET['recipient']))
		if ($status == 'done')
		if ($alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
			$recipientAlliance = new d13_alliance();
			if ($recipientAlliance->get('id', $_GET['recipient']) == 'done') {
				$status = $alliance->removePeace($recipientAlliance->data['id']);
				if ($status == 'done') header('Location: alliance.php?action=get');
				else $message = $d13->getLangUI($status);
			}
			else $message = $d13->getLangUI("noAlliance");
		}
		else $message = $d13->getLangUI("accessDenied");
		else $message = $d13->getLangUI("noAlliance");
		else $message = $d13->getLangUI("insufficientData");
		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

	case 'acceptPeace':
		if (isset($_GET['sender'], $_GET['recipient']))
		if ($status == 'done')
		if (($alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) && ($alliance->data['id'] == $_GET['recipient'])) {
			$senderAlliance = new d13_alliance();
			if ($senderAlliance->get('id', $_GET['sender']) == 'done') {
				$status = $alliance->acceptPeace($senderAlliance->data['id']);
				if ($status == 'done') {
					$user = new d13_user();
					if ($user->get('id', $senderAlliance->data['user']) == 'done') {
						$user->getPreferences('name');
						if ($user->preferences['allianceReports']) {
							$msg = new d13_message();
							$msg->data['sender'] = $_SESSION[CONST_PREFIX . 'User']['name'];
							$msg->data['recipient'] = $user->data['name'];
							$msg->data['subject'] = $d13->getLangUI("peaceAccepted");
							$msg->data['body'] = $d13->getLangUI("sender") . ': ' . $alliance->data['name'] . ' ' . $d13->getLangUI("alliance");
							$msg->data['viewed'] = 0;
							$status = $msg->add();
							if ($status == 'done') header('Location: alliance.php?action=get');
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
		break;
	}
}
else $message = $d13->getLangUI("accessDenied");

if ((isset($status)) && ($status == 'error')) $d13->dbQuery('rollback');
else $d13->dbQuery('commit');

// ----------------------------------------------------------------------------------------
// PROCESS VIEW
// ----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;
$page = "alliance";
$tvars['tvar_allianceGet'] = '<a class="external" href="index.php?p=alliance&action=get">' . $alliance->data['name'] . '</a>';
$tvars['tvar_allianceSet'] = "";
$tvars['tvar_allianceRemove'] = "";

if ($alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
	$tvars['tvar_allianceSet'] = '<a class="external" href="index.php?p=alliance&action=set">' . $d13->getLangUI("set") . '</a>';
	$tvars['tvar_allianceRemove'] = '<a class="external" href="index.php?p=alliance&action=remove">' . $d13->getLangUI("remove") . '</a>';
}

$tvars['tvar_tpl_allianceMenu'] = $d13->templateSubpage("alliance.menu", $tvars);

// - - - -

if (isset($_SESSION[CONST_PREFIX . 'User']['id'], $_GET['action'])) {
	switch ($_GET['action']) {

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = GET ALLIANCE

	case 'get':
		$html = "";
		if ($node->checkOptions('allianceGet')) {
			if (isset($alliance->data['id'])) {
				if ($alliance->members) {
					$html.= '<div class="section"> -> {{tvar_ui_members}}</div>';
					foreach($alliance->members as $member) {
						$html.= '<div class="right">' . $member['name'];
						if (($alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) || ($member['id'] == $_SESSION[CONST_PREFIX . 'User']['id'])) $html.= ' | <a class="external" href="index.php?p=alliance&action=removeMember&user=' . $member['id'] . '">x</a>';
						$html.= '</div>';
					}
				}

				if ($alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) $html.= '<div class="section" style="margin-top: 5px;"> -> {{tvar_ui_invitations}} | <a class="external" href="index.php?p=alliance&action=addInvitation">{{tvar_ui_invite}}</a></div>';
				foreach($alliance->invitations as $invitation) {
					$user = new d13_user();
					if ($user->get('id', $invitation['user']) == 'done') {
						$accept = '';
						$removeLabel = 'x';
						if ($user->data['id'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
							$accept = '<a class="external" href="index.php?p=alliance&action=acceptInvitation&alliance=' . $invitation['alliance'] . '&user=' . $invitation['user'] . '">{{tvar_ui_accept}}</a> | ';
							$removeLabel = $d13->getLangUI('decline');
						}

						$html.= '<div class="right"> ' . $user->data['name'] . ' | ' . $accept . '<a class="external" href="index.php?p=alliance&action=removeInvitation&alliance=' . $invitation['alliance'] . '&user=' . $invitation['user'] . '">' . $removeLabel . '</a></div>';
					}
				}

				if ($alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) $html.= '<div class="section" style="margin-top: 5px;"> -> {{tvar_ui_wars}} | <a class="external" href="index.php?p=alliance&action=addWar">{{tvar_ui_goToWar}}</a></div>';
				foreach($alliance->wars as $war) {
					$otherAlliance = new d13_alliance();
					if ($alliance->data['id'] == $war['sender']) $otherAllianceId = $war['recipient'];
					else $otherAllianceId = $war['sender'];
					if ($otherAlliance->get('id', $otherAllianceId) == 'done')
					if ($war['type']) $html.= '<div class="right">' . $otherAlliance->data['name'] . ' | <a class="external" href="index.php?p=alliance&action=proposePeace&recipient=' . $otherAlliance->data['id'] . '">{{tvar_ui_peace}}</a></div>';
					else {
						if ($alliance->data['id'] == $war['recipient']) $ad = '<a class="external" href="index.php?p=alliance&action=acceptPeace&sender=' . $war['sender'] . '&recipient=' . $war['recipient'] . '">{{tvar_ui_accept}}</a> / <a class="external" href="index.php?p=alliance&action=removePeace&recipient=' . $otherAlliance->data['id'] . '">{{tvar_ui_decline}}</a>';
						else $ad = '<a class="external" href="index.php?p=alliance&action=removePeace&recipient=' . $otherAlliance->data['id'] . '">x</a>';
						$html.= '<div class="right">' . $otherAlliance->data['name'] . ' {{tvar_ui_peace}} | ' . $ad . '</div>';
					}
				}
			}
			else {
				$html.= '<div class="section"><a class="external" href="index.php?p=alliance&action=add">{{tvar_ui_add}}</a></div>';
				if ($invitations) {
					$html.= '<div class="section"> -> {{tvar_ui_invitations}}</div>';
					foreach($invitations as $invitation) {
						$user = new d13_user();
						if ($user->get('id', $invitation['user']) == 'done') {
							$accept = '';
							$removeLabel = 'x';
							if ($user->data['id'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
								$accept = '<a class="external" href="index.php?p=alliance&action=acceptInvitation&alliance=' . $invitation['alliance'] . '&user=' . $invitation['user'] . '">{{tvar_ui_accept}}</a> | ';
								$removeLabel = $d13->getLangUI('decline');
							}

							$html.= '<div class="right"> ' . $user->data['name'] . ' | ' . $accept . '<a class="external" href="index.php?p=alliance&action=removeInvitation&alliance=' . $invitation['alliance'] . '&user=' . $invitation['user'] . '">' . $removeLabel . '</a></div>';
						}
					}
				}
			}

			$tvars['tvar_allianceHTML'] = $html;
			$page = "alliance.get";
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = SET ALLIANCE

	case 'set':
		if (isset($alliance->data['id'])) {
			if ($node->checkOptions('allianceSet')) {
				$costData = '';
				foreach($d13->getFaction($node->data['faction'], 'costs', 'alliance') as $key => $cost) {
					$costData.= '<div class="cell">' . $cost['value'] . '</div><div class="cell"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"></div>';
				}

				$tvars['tvar_nodeList'] = $nodeList;
				$tvars['tvar_costData'] = $costData;
				$tvars['tvar_allianceName'] = $alliance->data['name'];
				$tvars['tvar_nodeID'] = $node->data['id'];
				$tvars['tvar_nodeName'] = $node->data['name'];
				$page = "alliance.set";
			}
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = CREATE ALLIANCE

	case 'add':
		if (isset($nodeList)) {
			if ($node->checkOptions('allianceAdd')) {
				$costData = '';
				foreach($d13->getFaction($node->data['faction'], 'costs', 'alliance') as $key => $cost) {
					$costData.= '<div class="cell">' . $cost['value'] . '</div><div class="cell"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"></div>';
				}

				$tvars['tvar_nodeList'] = $nodeList;
				$tvars['tvar_costData'] = $costData;
				$tvars['tvar_nodeID'] = $node->data['id'];
				$tvars['tvar_nodeName'] = $node->data['name'];
				$page = "alliance.add";
			}
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = REMOVE ALLIANCE

	case 'remove':
		if ($node->checkOptions('allianceRemove')) {
			$page = "alliance.remove";
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = ADD ALLIANCE INVITE

	case 'addInvitation':
		if ($node->checkOptions('allianceInvite')) {
			$page = "alliance.addInvitation";
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = ADD ALLIANCE WAR

	case 'addWar':
		if ($node->checkOptions('allianceWar')) {
			$page = "alliance.addWar";
		}

		break;
	}
}

// ----------------------------------------------------------------------------------------
// RENDER OUTPUT
// ----------------------------------------------------------------------------------------

$d13->templateRender($page, $tvars);

// =====================================================================================EOF

?>