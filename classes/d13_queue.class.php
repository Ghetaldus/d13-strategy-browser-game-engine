<?php

// ========================================================================================
//
// QUEUE.CLASS
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
// Nodes (towns) contain several Task-Queues. The queues are used to keep track of all
// tasks that are currently active. This includes building, upgrading, crafting, training
// and so on. Includes army movement/returning as well. 
//
// ========================================================================================

class d13_queue

{
	
	private $node;
	public $queue;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// ----------------------------------------------------------------------------------------
	public

	function __construct($node)
	{
		
		global $d13;
			
		$this->node = $node;
		$this->queue = array();
		
		foreach ($d13->getGeneral('queues') as $type) {
			$this->queue[$type] = array();
		}
		
	}
	
	
	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------
	public

	function getQueue($type, $field = 0, $values = 0)
	{
	
		global $d13;
	
		if (empty($this->queue[$type])) {
		
			switch ($type)
			{
		
				case 'combat':
					$result = $d13->dbQuery('select * from ' . $type . ' where sender="' . $this->node->data['id'] . '" or recipient="' . $this->node->data['id'] . '" order by start asc');
					break;

				default:
					if ($field) {
						$values = '(' . implode(', ', $values) . ')';
						$result = $d13->dbQuery('select * from ' . $type . ' where node="' . $this->node->data['id'] . '" and ' . $field . ' in ' . $values . ' order by start asc');
					} else {
						$result = $d13->dbQuery('select * from ' . $type . ' where node="' . $this->node->data['id'] . '" order by start asc');
					}
					break;
			}

			for ($i = 0; $row = $d13->dbFetch($result); $i++) {
				$this->queue[$type][$i] = $row;
				$this->queue[$type][$i]['start'] = strtotime($this->queue[$type][$i]['start']);
			}
		
		}
		
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// ----------------------------------------------------------------------------------------
	
	public
	
	function getQueueCount()
	{
		global $d13;
		
		$i = 0;
		foreach ($d13->getGeneral('queues') as $queues) {
			$this->getQueue($queues);
			$i += count($this->queue[$queues]);
		}
		return $i;
	
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// ----------------------------------------------------------------------------------------
	
	public
	
	function getQueueExpireNext()
	{
	
		global $d13;
		
		$html = '';
		$tmp_queues = array();
		
		foreach ($d13->getGeneral('queues') as $queues) {
			$this->getQueue($queues);
			foreach($this->queue[$queues] as $item) {
				$data = array();
				$item['type'] = $queues;
				$data['remaining'] = $item['start'] + $item['duration']  - time();
				$data['type'] =	$queues; 
				$data['item'] = $item;
				
				if (!isset($item['obj_id'])) {
				$data['obj_id'] = $item['id'];
				} else {
				$data['obj_id'] = $item['obj_id'];
				}
				
				$tmp_queues[] = $data;
			}
		}
		
		if (count($tmp_queues)) {
			$tmp_queues = d13_misc::record_sort($tmp_queues, 'remaining');
			$html = $this->getQueueItem($tmp_queues[0]['item']);
		}
		return $html;
	
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// ----------------------------------------------------------------------------------------
	
	public
	
	function getQueueItem($item)
	{
		global $d13;
		
		$html = '';
		
		//- - some items do not feature all data, fill with dummy data
		if (!isset($item['obj_id'])) {
			if (isset($item['id'])) {
				$item['obj_id'] = $item['id'];
			} else {
				$item['obj_id'] = $item['slot'];
			}
		}
		if (!isset($item['slot'])) {
			$item['slot'] = 0;
		}
		
		$remaining = $item['start'] + $item['duration']  - time();
		$token = rand(0,9);
		
		switch ($item['type'])
		{

			case 'shield':
				$icon 	= '/icon/'.$d13->getShield($item['obj_id'], 'image');
				$action = $d13->getLangUI("active");
				$name 	= $d13->getLangGL("shields", $item['obj_id'], "name");
				$cancel = '<a class="external" href="?p=node&action=cancelShield&nodeId=' . $this->node->data['id'] . '&shieldId=' . $item['obj_id'] . '">';
				break;

			case 'buff':
				$icon 	= '/icon/'.$d13->getBuff($item['obj_id'], 'image');
				$action = $d13->getLangUI("active");
				$name 	= $d13->getLangGL("buffs", $item['obj_id'], "name");
				$cancel = '<a class="external" href="?p=node&action=cancelBuff&nodeId=' . $this->node->data['id'] . '&buffId=' . $item['obj_id'] . '">';
				break;
			
			case 'market':
				$icon 	= '/icon/refresh.png';
				$action = '';
				$name 	= $d13->getLangGL("modules", $this->node->data['faction'], $this->node->modules[$item['slot']]['module'], "name");
				$cancel = '<a class="external" href="?p=node&action=cancelMarket&nodeId=' . $this->node->data['id'] . '&slotId=' . $item['slot'] . '">';
				break;
			
			case 'build':
				$icon 	= $d13->getModule($this->node->data['faction'], $item['obj_id'], 'images');
				$icon 	= 'modules/'.$this->node->data['faction'].'/'.$icon[1]['image'];
				$action = $d13->getLangUI($item['action']);
				$name 	= $d13->getLangGL("modules", $this->node->data['faction'], $item['obj_id'], "name");
				$cancel = '<a class="external" href="?p=module&action=cancel&nodeId=' . $this->node->data['id'] . '&slotId=' . $item['slot'] . '">';
				break;

			case 'research':
				$icon 	= $d13->getTechnology($this->node->data['faction'], $item['obj_id'], 'images');
				$icon 	= 'technologies/'.$this->node->data['faction'].'/'.$icon[0]['image'];
				$action = $d13->getLangUI("research_short");
				$name 	= $d13->getLangGL("technologies", $this->node->data['faction'], $item['obj_id'], "name");
				$cancel = '<a class="external" href="?p=module&action=cancelTechnology&nodeId=' . $this->node->data['id'] . '&slotId=' . $item['slot'] . '&technologyId=' . $item['obj_id'] . '"> ';
				break;

			case 'craft':
				if ($item['stage'] == 0) {
					$action = $d13->getLangUI('craft_short');
				} else {
					$action = $d13->getLangUI('remove_short');
				}
				$icon 	= $d13->getComponent($this->node->data['faction'], $item['obj_id'], 'images');
				$icon 	= 'components/'.$this->node->data['faction'].'/'.$icon[0]['image'];
				$name 	= $d13->getLangGL("components", $this->node->data['faction'], $item['obj_id'], "name");
				$cancel = '<a class="external" href="?p=module&action=cancelComponent&nodeId=' . $this->node->data['id'] . '&slotId=' . $item['slot'] . '&craftId=' . $item['id'] . '"> ';
				break;
				
			case 'train':
				if ($item['stage'] == 0) {
					$action = $d13->getLangUI('train_short');
				} else {
					$action = $d13->getLangUI('remove_short');
				}
				$icon 	= $d13->getUnit($this->node->data['faction'], $item['obj_id'], 'images');
				$icon 	= 'units/'.$this->node->data['faction'].'/'.$icon[0]['image'];
				$name 	= $d13->getLangGL("units", $this->node->data['faction'], $item['obj_id'], "name");
				$cancel = '<a class="external" href="?p=module&action=cancelunit&nodeId=' . $this->node->data['id'] . '&slotId=' . $item['slot'] . '&trainId=' . $item['id'] . '"> ';
				break;
				
			case 'combat':
				$action = '';
				$cancel = '';
				$status = '';
				$icon 	= '/icon/armies.png';
				$item['node'] = $item['sender'];
				$name = '';
				$otherNode = new d13_node();
				if ($item['sender'] == $this->node->data['id']) {
					$status = $otherNode->get('id', $item['recipient']);
				} else {
					$status = $otherNode->get('id', $item['sender']);
				}
				if ($status == 'done') {
					if (!$item['stage']) {
						if ($item['sender'] == $this->node->data['id']) {
							$action = $d13->getLangUI('outgoing') . " " . $d13->getLangUI("army") . " " . $d13->getLangUI("to") . ": " . $otherNode->data['name'];
							$cancel = '<div class="d13-cell"><a class="external" href="?p=combat&action=cancel&nodeId=' . $this->node->data['id'] . '&combatId=' . $item['id'] . '">';
						} else {
							$action = $d13->getLangUI('incoming') . " " . $d13->getLangUI("army"). " " . $d13->getLangUI("from") . ": " . $otherNode->data['name'];
						}
					} else {
						$action = $d13->getLangUI('returning') . " " . $d13->getLangUI("army") . " " . $d13->getLangUI("from") . ": " . $otherNode->data['name'];
					}
				}
				break;
			/*
			case 'trade':
				$icon 	= 
				$action = $d13->getLangUI("trade");
				$name 	= 
				$cancel = 
				break;
			*/
		}
	
		$tvars['tvar_itemImage'] 		= '<img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/'.$icon.'">';
		$tvars['tvar_itemTitle'] 		= $action . ' ' . $name;
		$tvars['tvar_itemTimer'] 		= '<span id="'.$item['type'].'_' . $token . '_' . $item['node'] . '_' . $item['slot'] . '_' .  $item['obj_id'] . '">' . implode(':', d13_misc::sToHMS($remaining)) . '</span>';
		$tvars['tvar_itemScript']		= '<script type="text/javascript">timedJump("'.$item['type'].'_' . $token . '_' . $item['node'] . '_' . $item['slot'] . '_' . $item['obj_id'] .'", "index.php?p=node&action=get&nodeId=' . $this->node->data['id'] . '");</script>';
		$tvars['tvar_itemCancel'] 		= $cancel . ' <img class="d13-micron" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>';
		$tvars['tvar_itemPercentage']	= d13_misc::percentage($remaining, $item['duration']);
		$html .= $d13->templateSubpage("sub.queue.item", $tvars);

		return $html;
		
	}
	
	// ----------------------------------------------------------------------------------------
	// getQueuesList
	// ----------------------------------------------------------------------------------------
	
	public
	
	function getQueuesList()
	{
		
		global $d13;
		
		$tvars['tvar_activeQueues'] = '';
		$tvars['tvar_queueHeader'] = $d13->getLangUI("active") . ' ' . $d13->getLangUI("task");
		$tvars['tvar_queueItems'] = '';
		
		foreach ($d13->getGeneral('queues') as $queue) {
			$this->getQueue($queue);
			if (count($this->queue[$queue])) {
				foreach($this->queue[$queue] as $item) {
					$item['type'] = $queue;
					$tvars['tvar_queueItems'] .= $this->getQueueItem($item);
				}
			}
		}
		
		if (!empty($tvars['tvar_queueItems'])) {
			$tvars['tvar_activeQueues'] .= $d13->templateSubpage("sub.queue.right", $tvars);
		}
	
		return $tvars['tvar_activeQueues'];
	
	}
	
}


// =====================================================================================EOF

