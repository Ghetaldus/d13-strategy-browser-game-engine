<?php

// ========================================================================================
//
// MESSAGE.CLASS
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
// NOTES:
//
// Responsible for sending and retrieving text messages to/from users. Also takes blocklist
// and profanity filter into consideration. A player cannot receive messages from users on
// his/her blocklist and a player cannot send messages that include words from the blockwords
// list. Blockwords list is located in locales directory as JSON file (cached as data).
//
// a few functions are still missing, but not vital:
//
// 1. sending a message to all alliance (guild/clan) members.
// 2. sending a message to all players (admin only).
// 3. adding resources to a message (gifts for friends or gifts from admin).
//
// ========================================================================================

class d13_message

{
	public $data;
	
	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	public

	function get($id)
	{
		global $d13;
		$result = $d13->dbQuery('select * from messages where id="' . $id . '"');
		$this->data = $d13->dbFetch($result);
		$this->data['body'] = $this->textDecode($this->data['body']);
		if (isset($this->data['id'])) $status = 'done';
		else $status = 'noMessage';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	public

	function set()
	{
		global $d13;
		$message = new d13_message();
		if ($message->get($this->data['id']) == 'done') {
			$d13->dbQuery('update messages set viewed="' . $this->data['viewed'] . '" where id="' . $this->data['id'] . '"');
			if ($d13->dbAffectedRows() > - 1) $status = 'done';
			else $status = 'error';
		}
		else $status = 'noMessage';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	public

	function add()
	{
		global $d13;
		$recipient = new d13_user();
		if ($recipient->get('name', $this->data['recipient']) == 'done') {
			$sender = new d13_user();
			if ($sender->get('name', $this->data['sender']) == 'done') {
				if (!$sender->isBlocked($recipient->data['id'])) {
					$this->data['body'] = $this->textEncode($this->data['body']);
					$this->data['id'] = d13_misc::newId('messages');
					$sent = strftime('%Y-%m-%d %H:%M:%S', time());
					$d13->dbQuery('insert into messages (id, sender, recipient, subject, body, sent, viewed, type) values ("' . $this->data['id'] . '", "' . $sender->data['id'] . '", "' . $recipient->data['id'] . '", "' . $this->data['subject'] . '", "' . $this->data['body'] . '", "' . $sent . '", "' . $this->data['viewed'] . '", "' . $this->data['type'] . '")');
					if ($d13->dbAffectedRows() > - 1) {
						$status = 'done';
					}
					else {
						$status = 'error';
					}
				}
				else {
					$status = 'blocked';
				}
			}
			else {
				$status = 'noSender';
			}
		}
		else {
			$status = 'noRecipient';
		}

		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	public static

	function remove($id)
	{
		global $d13;
		$message = new d13_message();
		if ($message->get($id) == 'done') {
			$ok = 1;
			$d13->dbQuery('insert into free_ids (id, type) values ("' . $id . '", "messages")');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$d13->dbQuery('delete from messages where id="' . $id . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
		}
		else $status = 'noMessage';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	public static

	function removeAll($userId)
	{
		global $d13;
		$result = $d13->dbQuery('select id from messages where recipient="' . $userId . '"');
		$ok = 1;
		while ($row = $d13->dbFetch($result)) {
			$d13->dbQuery('insert into free_ids (id, type) values ("' . $row['id'] . '", "messages")');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$d13->dbQuery('delete from messages where id="' . $row['id'] . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
		}

		if ($ok) $status = 'done';
		else $status = 'error';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	public static

	function getList($recipient, $limit, $offset, $type="all")
	{
		global $d13;
		$messages = array();
		$messages['messages'] = array();
		$result = $d13->dbQuery('select count(*) as count from messages where recipient="' . $recipient . '"');
		$row = $d13->dbFetch($result);
		$messages['count'] = $row['count'];
		
		if ($type == "outbox") {
			$type = "message";
			$result = $d13->dbQuery('select * from messages where sender="' . $recipient . '" and type="' . $type . '" order by sent desc limit ' . $limit . ' offset ' . $offset);
		
		} else if ($type == "all" || $type == "") {
			$result = $d13->dbQuery('select * from messages where recipient="' . $recipient . '" order by sent desc limit ' . $limit . ' offset ' . $offset);
		} else {
			$result = $d13->dbQuery('select * from messages where recipient="' . $recipient . '" and type="' . $type . '" order by sent desc limit ' . $limit . ' offset ' . $offset);
		}
		
		
		
		for ($i = 0; $row = $d13->dbFetch($result); $i++) {
			$messages['messages'][$i] = new d13_message();
			$messages['messages'][$i]->data = $row;
		}

		return $messages;
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	public static

	function getUnreadCount($recipient)
	{
		global $d13;
		$result = $d13->dbQuery('select count(*) as count from messages where recipient="' . $recipient . '" and viewed=0');
		$row = $d13->dbFetch($result);
		return $row['count'];
	}
	
	
	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	private
	
	function textEncode($text)
	{	
		global $d13;
		
		$text = htmlentities($text);
		$text = $d13->dbRealEscapeString($text);
		
		
		return $text;
		
	}
	
	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	private
	
	function textDecode($text)
	{
	
		global $d13;
		
		$text = html_entity_decode($text);
		
		return $text;
	
	}
	
	
}

// =====================================================================================EOF
