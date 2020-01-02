<?php

// ========================================================================================
//
// MESSAGE
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
//
// ----------------------------------------------------------------------------------------

global $d13;
$message = NULL;
$d13->dbQuery('start transaction');

if (isset($_SESSION[CONST_PREFIX . 'User']['id'], $_GET['action'])) {

	foreach($_GET as $key => $value)
	if (in_array($key, array(
		'page',
		'messageId'
	))) $_GET[$key] = d13_misc::clean($value, 'numeric');
	else $_GET[$key] = d13_misc::clean($value);
	
	switch ($_GET['action']) {
	
	
	// - - - - - - - - - Get Message
	case 'get':
		if (isset($_GET['messageId'])) {
			$msg = new d13_message();
			$status = $msg->get($_GET['messageId']);
			if ($status == 'done')
			if ($msg->data['recipient'] == $_SESSION[CONST_PREFIX . 'User']['id'] || $msg->data['sender'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
			
				if (!$msg->data['viewed']) {
					$msg->data['viewed'] = 1;
					$msg->set();
				}

				$user = new d13_user();
				$status = $user->get('id', $msg->data['sender']);
				if ($status == 'done') $msg->data['senderName'] = $user->data['name'];
				else $msg->data['senderName'] = $d13->getLangUI("game");
				
				

			}
			else $message = $d13->getLangUI("accessDenied");
			else $message = $d13->getLangUI($status);
		}
		else $message = $d13->getLangUI("insufficientData");
		break;

	// - - - - - - - - - Send Message
	case 'add':
		if (isset($_GET['messageId'])) {
			$msg = new d13_message();
			$status = $msg->get($_GET['messageId']);
			if ($status != 'done') {
				$msg = 0;
			}
			else
			if ($msg->data['recipient'] != $_SESSION[CONST_PREFIX . 'User']['id']) {
				$msg = 0;
			}
		}

		if (isset($_POST['recipient'], $_POST['subject'], $_POST['msgbody'])) {
			if ($_POST['recipient'] != '' && $_POST['subject'] != '' && $_POST['msgbody'] != '' && !$d13->getLangBW($_POST['msgbody']) && !$d13->getLangBW($_POST['subject'])) {
				$msg = new d13_message();
				$msg->data['sender'] = $_SESSION[CONST_PREFIX . 'User']['name'];
				$msg->data['recipient'] = $_POST['recipient'];
				$msg->data['subject'] = $_POST['subject'];
				$msg->data['body'] = $_POST['msgbody'];
				$msg->data['viewed'] = 0;
				$msg->data['type'] = 'message';
				$message = $d13->getLangUI($msg->add());
			}
			else {
				$message = $d13->getLangUI("insufficientData");
			}
		}
		else {
			$message = $d13->getLangUI("insufficientData");
		}

		break;
	
	// - - - - - 
	case 'remove':
		if (isset($_GET['messageId'])) {
			$msg = new d13_message();
			$status = $msg->get($_GET['messageId']);
			if ($status == 'done') {
				if ($msg->data['recipient'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
					$status = d13_message::remove($_GET['messageId']);
					if ($status == 'done') header('location: ?p=message&action=list');
					else $message = $d13->getLangUI($status);
				}
				else $message = $d13->getLangUI("accessDenied");
			}
			else $message = $d13->getLangUI("noMessage");
		}
		else
		if (isset($_POST['messageId'])) {
			foreach($_POST['messageId'] as $id) d13_message::remove($id);
			header('location: ?p=message&action=list');
		}
		else $message = $d13->getLangUI("insufficientData");
		break;
		
	// - - - - - 
	case 'removeAll':
		$status = d13_message::removeAll($_SESSION[CONST_PREFIX . 'User']['id']);
		if ($status == 'done') header('location: ?p=message&action=list');
		else $message = $d13->getLangUI($status);
		break;
	// - - - - - 
	
	case 'list':
		$limit = 8;
		$filter = 'all';
		if (isset($_GET['page'])) {
			$offset = $limit * $_GET['page'];
		} else {
			$offset = 0;
		}
		if (isset($_POST['filter'])) {
			$filter = $_POST['filter'];
		}
		$messages = d13_message::getList($_SESSION[CONST_PREFIX . 'User']['id'], $limit, $offset, $filter);
		$pageCount = ceil($messages['count'] / $limit);
		break;
	}
}
else $message = $d13->getLangUI("accessDenied");

if ((isset($status)) && ($status == 'error')) $d13->dbQuery('rollback');
else $d13->dbQuery('commit');

// ----------------------------------------------------------------------------------------
// Setup Template Variables
// ----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;

$page = "message";

// - - - - -

if (isset($_SESSION[CONST_PREFIX . 'User']['id'], $_GET['action'])) {
	switch ($_GET['action']) {
	
	// - - - - - Read Message
	case 'get':
		if (isset($msg->data['recipient'])) {
			$tvars['tvar_senderName'] = $msg->data['senderName'];
			$tvars['tvar_subject'] = $msg->data['subject'];
			$tvars['tvar_body'] = $msg->data['body'];
			$tvars['tvar_id'] = $msg->data['id'];
			$page = "message.get";
		}

		break;
		
	// - - - - - New Message
	case 'add':
		$recipient = $subject = $body = '';
		if (isset($msg->data['id'])) {
			$user = new d13_user();
			$status = $user->get('id', $msg->data['sender']);
			if ($status == 'done') {
				$recipient = $user->data['name'];
			}
			$subject = 're: ' . $msg->data['subject'];
			$body .= $msg->data['body'];
		}

		$tvars['tvar_subject'] = $subject;
		$tvars['tvar_recipient'] = $recipient;
		$tvars['tvar_body'] = $body;
		$page = "message.add";
		break;
	
	// - - - - - View Messages List
	case 'list':
	
		$tvars['tvar_removeAll'] 		= "";
		$tvars['tvar_messages'] 		= "";
		$tvars['tvar_remove'] 			= "";
		$tvars['tvar_controls'] 		= "";
		$tvars['tvar_filterSelect'] 	= "";
		
		// - - - Build Filter Select
		$tvars['tvar_filterSelect'] .= '<select class="pure-input" name="filter" id="filter" onChange="this.form.submit();">';
		
		foreach ($d13->getGeneral('message') as $msg) {
			$sel = "";
			if (isset($_POST['filter']) && $_POST['filter'] == $msg) {
				$sel = 'selected';
			}
			$tvars['tvar_filterSelect'] .= '<option value="'.$msg.'" '.$sel.'>'.$d13->getLangUI($msg).'</option>';		
		}
		$tvars['tvar_filterSelect'] .= '</select>';
		
		// - - - Build Remove All
		if (count($messages['messages'])) {
			$removeAll = '<a class="button external" href="?p=message&action=removeAll">' . $d13->getLangUI("removeAll") . '</a>';
		} else {
			$removeAll = '';
		}
		$tvars['tvar_removeAll'] = $removeAll;

		// - - - Build Remove Selected
		if (count($messages['messages'])) {
			$tvars['tvar_remove'] = '<a class="button external" href="javascript: document.getElementById(\'messageList\').submit()">' . $d13->getLangUI("remove") . ' ' . $d13->getLangUI("selected") . '</a>';
		}
		
		// - - - Build Message List
		foreach($messages['messages'] as $message) {
			if (!$message->data['viewed']) {
				$new = 'mail_on.png';
			}
			else {
				$new = 'mail_off.png';
			}
			$hours = floor((time() - strtotime($message->data['sent'])) / 3600);
			$vars = array();
			$vars['tvar_listImage'] = '<img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/'.$new.'">';
			$vars['tvar_listLabel'] = '<input type="checkbox" name="messageId[]" value="' . $message->data['id'] . '"> <a class="external" href="index.php?p=message&action=get&messageId=' . $message->data['id'] . '"' . $new . '>' . $message->data['subject'] . '</a>';
			$vars['tvar_listAmount'] = $hours . ' {{tvar_ui_hours}} {{tvar_ui_ago}} <a class="external" href="?p=message&action=remove&messageId=' . $message->data['id'] . '"><img class="d13-micron" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>';
			$tvars['tvar_messages'] .= $d13->templateSubpage("sub.module.listcontent", $vars);
		}
		
		// - - - Build Pagination
		if ($pageCount > 1) {
			$previous = '';
			$next = '';
			if (isset($_GET['page'])) {
				if ($_GET['page']) {
					$previous = '<a class="external" href="?p=message&action=list&page=' . ($_GET['page'] - 1) . '">' . $d13->getLangUI("previous") . '</a>';
				}
			} else if (!isset($_GET['page'])) {
				if ($pageCount) {
					$next = '<a class="external" href="?p=message&action=list&page=1">' . $d13->getLangUI("next") . '</a>';
				}
			}

			if (isset($_GET['page']) && $pageCount - $_GET['page'] - 1) {
				$next = '<a class="external" href="?p=message&action=list&page=' . ($_GET['page'] + 1) . '">' . $d13->getLangUI("next") . '</a>';
			}

			$tvars['tvar_controls'].= $d13->getLangUI("page") . $previous . ' <select class="dropdown" id="page" onChange="window.location.href=\'index.php?p=message&action=list&page=\'+this.value">';
			for ($i = 0; $i < $pageCount; $i++) {
				$tvars['tvar_controls'].= '<option value="' . $i . '">' . $i . '</option>';
			}

			$tvars['tvar_controls'].= '</select> ' . $next;
			if (isset($_GET['page'])) {
				$tvars['tvar_controls'].= '<script type="text/javascript">document.getElementById("page").selectedIndex=' . $_GET['page'] . '</script>';
			}
		}

		$page = "message.list";
		break;
	}
}

// ----------------------------------------------------------------------------------------
// Render Template
// ----------------------------------------------------------------------------------------

$d13->templateRender($page, $tvars);

// =====================================================================================EOF

?>