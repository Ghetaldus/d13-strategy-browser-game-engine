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

class d13_nodeController extends d13_controller
{
	
	private $node;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct()
	{
		
		$tvars = array();
		
		$tvars = $this->doControl();
		$this->getTemplate($tvars);
	}
	
	// ----------------------------------------------------------------------------------------
	// doControl
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function doControl()
	{
				
		switch ($_GET['action'])
		{
		
			case 'get':
				return $this->nodeGet();
				break;
				
			case 'set':
				return $this->nodeSet();
				break;
				
			case 'add':
				return $this->nodeAdd();
				break;
				
			case 'random':
				return $this->nodeRandom();
				break;
				
			case 'remove':
				return $this->nodeRemove();
				break;
				
			case 'move':
				return $this->nodeMove();
				break;
				
			case 'cancelShield':
				return $this->nodeCancelShield();
				break;
			
			case 'cancelBuff':
				return $this->nodeCancelBuff();
				break;
			
			case 'cancelMarket':
				return $this->nodeCancelMarket();
				break;
			
			case 'list':
				return $this->nodeList();
				break;
				
		}
		
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeGet()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['nodeId'])) {
			$nodeId = $_GET['nodeId'];
		} else {
			$nodeId = $_SESSION[CONST_PREFIX . 'User']['node'];
		}
		
		$this->node = new d13_node();
		$status = $this->node->get('id', $nodeId);
		
		if ($status == 'done') {
			if ($this->node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
				$_SESSION[CONST_PREFIX . 'User']['node'] = $nodeId;
				$this->node->checkAll(time());
				$this->node->getLocation();
				$this->node->queues->getQueue('build');
				$this->node->queues->getQueue('combat');			
				$tvars = $this->nodeRender();
			} else {
				$message = $d13->getLangUI("accessDenied");
			}
		} else {
			$message = $d13->getLangUI($status);
		}
		
		return $tvars;
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeSet()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['nodeId'])) {
			$this->node = new d13_node();
			$status = $this->node->get('id', $_GET['nodeId']);
			if ($status == 'done') {
				if ((isset($_POST['name'], $_POST['focus'])) && ($_POST['name']))
				if (in_array($_POST['focus'], array(
					'hp',
					'armor',
					'damage'
				)))
				if ($this->node->checkOptions('nodeEdit') && $this->node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
					$oldName = $this->node->data['name'];
					$oldFocus = $this->node->data['focus'];
					$this->node->data['name'] = $_POST['name'];
					$this->node->data['focus'] = $_POST['focus'];
					$status = $this->node->set();
					if ($status != 'done') {
						$this->node->data['name'] = $oldName;
						$this->node->data['focus'] = $oldFocus;
					}

					$message = $d13->getLangUI($status);
				}
				else $message = $d13->getLangUI("accessDenied");
				else $message = $d13->getLangUI("invalidFocus");
			}
			else $message = $d13->getLangUI($status);
		}
		else $message = $d13->getLangUI("accessDenied");
		
		
		
		$costData = '';
		foreach($d13->getFaction($this->node->data['faction'], 'costs', 'set') as $key => $cost) {
			$costData.= '<div class="cell">' . $cost['value'] . '</div><div class="cell"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"></div>';
		}

		$selectedFocus = array(
			'hp' => '',
			'damage' => '',
			'armor' => ''
		);
		
		$selectedFocus[$this->node->data['focus']] = ' selected';
		$tvars['tvar_costData'] 		= $costData;
		$tvars['tvar_selFocusHP'] 		= $selectedFocus['hp'];
		$tvars['tvar_selFocusDamage'] 	= $selectedFocus['damage'];
		$tvars['tvar_selFocusArmor'] 	= $selectedFocus['armor'];
		$tvars['tvar_page'] 			= "node.set";
		
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeAdd()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_POST['faction'], $_POST['name'], $_POST['x'], $_POST['y'])) {
			if ($_POST['faction'] != '' && !empty($_POST['name']) && !empty($_POST['x']) && !empty($_POST['y'])) {
				$this->node = new d13_node();
				$this->node->data['faction'] = $_POST['faction'];
				$this->node->data['user'] = $_SESSION[CONST_PREFIX . 'User']['id'];
				$this->node->data['name'] = $_POST['name'];
				$this->node->location['x'] = $_POST['x'];
				$this->node->location['y'] = $_POST['y'];
				$message = $d13->getLangUI($this->node->add($_SESSION[CONST_PREFIX . 'User']['id']));
			}
			else {
				$message = $d13->getLangUI("insufficientData");
			}
		}
		else {
			$message = $d13->getLangUI("insufficientData");
		}
		
		
		$tvars['tvar_factionDescriptions'] = "";
		$tvars['tvar_factionOptions'] = "";
		foreach($d13->getLangGL('factions') as $key => $faction) {
			$tvars['tvar_factionOptions'].= '<option value="' . $key . '">' . $faction['name'] . '</option>';
		}

		foreach($d13->getLangGL('factions') as $key => $faction) {
			$descriptions[$key] = '"' . $faction['description'] . '"';
		}

		$tvars['tvar_factionText'] = $descriptions[0];
		$tvars['tvar_factionDescriptions'].= implode(', ', $descriptions);
		$tvars['tvar_page'] = "node.add";
		
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeRandom()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_POST['faction'])) {
			if ($_POST['faction'] != '') {
				$coord = array();
				$grid = new d13_grid();
				$coord = $grid->getFree();
				$this->node = new d13_node();
				$this->node->data['faction'] = $_POST['faction'];
				$this->node->data['user'] = $_SESSION[CONST_PREFIX . 'User']['id'];
				$this->node->data['name'] = $_SESSION[CONST_PREFIX . 'User']['name'];
				$this->node->location['x'] = $coord['x'];
				$this->node->location['y'] = $coord['y'];
				$message = $d13->getLangUI($this->node->add($_SESSION[CONST_PREFIX . 'User']['id']));
				header('Location: ?p=node&action=list');
			}
			else {
				$message = $d13->getLangUI("insufficientData");
			}
		}
				
		$tvars['tvar_getHTMLFactions'] = "";
		$tvars['tvar_factionName'] = "";
		$tvars['tvar_factionText'] = "";
		$tvars['tvar_factionID'] = - 1;
		$factionId = - 1;

		// - - - - Check for Faction Fixation

		if ($d13->getGeneral('options', 'factionFixation')) {
			$nodes = d13_node::getList($_SESSION[CONST_PREFIX . 'User']['id']);
			$t = count($nodes);
			if ($t > 0) {
				$factionId = $nodes[0]->data['faction'];
			}
		}

		// - - - - Add Factions to Swiper Slide

		foreach($d13->getFaction() as $faction) {
			if ($faction['active']) {
				if (!$d13->getGeneral('options', 'factionFixation') || ($d13->getGeneral('options', 'factionFixation') && $faction['id'] == $factionId) || ($d13->getGeneral('options', 'factionFixation') && $factionId == - 1)) {
					$tvars['tvar_factionName'] = $d13->getLangGL('factions', $faction['id'], 'name');
					$tvars['tvar_factionText'] = $d13->getLangGL('factions', $faction['id'], 'description');
					$tvars['tvar_factionID'] = $faction['id'];
					$tvars['tvar_getHTMLFactions'].= $d13->templateSubpage("sub.node.faction", $tvars);
				}
			}
		}

		$d13->templateInject($d13->templateSubpage("sub.swiper.horizontal", $tvars));
		$tvars['tvar_page'] = "node.random";
					
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeRemove()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['nodeId'])) {
			$this->node = new d13_node();
			$status = $this->node->get('id', $_GET['nodeId']);
			if ($status == 'done') {
				if ((isset($_GET['go'])) && ($_GET['go'])) {
					if ($this->node->checkOptions('nodeRemove') && $this->node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
						$status = $this->node->remove($_GET['nodeId']);
						if ($status == 'done') {
							header('location: p=node&action=list');
							exit();
							
						}
						else {
							$message = $d13->getLangUI($status);
						}
					}
					else {
						$message = $d13->getLangUI("accessDenied");
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
		
		$tvars['tvar_page'] = "node.remove";
		
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeMove()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['nodeId'])) {
			$this->node = new d13_node();
			$status = $this->node->get('id', $_GET['nodeId']);
			if ($status == 'done') {
				if (isset($_POST['x'], $_POST['y']))
				if ($this->node->checkOptions('nodeMove') && $this->node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id'])
					if ($d13->getFaction($this->node->data['faction'], 'costs', 'move', 0, 'resource') > - 1) {
						$message = $d13->getLangUI($this->node->move($_POST['x'], $_POST['y']));
					}
				else $message = $d13->getLangUI("nodeMoveDisabled");
				else $message = $d13->getLangUI("accessDenied");
			}
			else $message = $d13->getLangUI($status);
		}
		else $message = $d13->getLangUI("insufficientData");
		
		$costData = '';
		foreach($d13->getFaction($this->node->data['faction'], 'costs', 'move') as $key => $cost) {
			$costData.= '<div class="cell">' . $cost['value'] . '</div><div class="cell"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"></div>';
		}
		$this->node->getLocation();
		$tvars['tvar_nodeX'] = $this->node->location['x'];
		$tvars['tvar_nodeY'] = $this->node->location['y'];
		$tvars['tvar_costData'] = $costData;
		$tvars['tvar_page'] = "node.move";
				
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeCancelShield()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['shieldId'])) {
			$this->node = new d13_node();
			$status = $this->node->get('id', $_GET['nodeId']);
			$status = $this->node->cancelShield($_GET['shieldId']);
			if ($status == 'done') {
				header('Location: index.php?p=node&action=get&nodeId=' . $this->node->data['id']);
				exit();
				
			} else {
				$message = $d13->getLangUI($status);
			}
		}
		else $message = $d13->getLangUI("insufficientData");
			
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeCancelBuff()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['buffId']) && isset($_GET['nodeId'])) {
			$this->node = new d13_node();
			$status = $this->node->get('id', $_GET['nodeId']);
			$status = $this->node->cancelBuff($_GET['buffId']);
			if ($status == 'done') {
				header('Location: index.php?p=node&action=get&nodeId=' . $this->node->data['id']);
				exit();
				
			} else {
				$message = $d13->getLangUI($status);
			}
		}
		else $message = $d13->getLangUI("insufficientData");
			
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeCancelMarket()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['slotId']) && isset($_GET['nodeId'])) {
			$this->node = new d13_node();
			$status = $this->node->get('id', $_GET['nodeId']);
			$status = $this->node->cancelMarket($_GET['slotId']);
			if ($status == 'done') {
				header('Location: index.php?p=node&action=get&nodeId=' . $this->node->data['id']);
				exit();
				
			} else {
				$message = $d13->getLangUI($status);
			}
		}
		else $message = $d13->getLangUI("insufficientData");
			
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeList()
	{
		global $d13;
		$tvars = array();
		$tvars['tvar_nodeList'] = "";
		
		$nodes = d13_node::getList($_SESSION[CONST_PREFIX . 'User']['id']);
		$t = count($nodes);

		// - - - - - - - - - - - - - - - - - - - - 0 towns - create new town

		if ($t == 0) {
			header("location: index.php?p=node&action=random");
			exit();
		// - - - - - - - - - - - - - - - - - - - - 1 town - jump to town

		} else if ($t == 1) {
			$link = "?p=node&action=get&nodeId=" . $nodes[0]->data['id'];
			header("location: " . $link);
			exit();
			
		// - - - - - - - - - - - - - - - - - - - - 2+ towns - list all towns

		} else {
			foreach($nodes as $key => $node) {
				$tvars['tvar_nodeList'].= '<div><a class="external" href="index.php?p=node&action=get&nodeId=' . $node->data['id'] . '">' . $node->data['name'] . '</a></div>';
			}

			$tvars['tvar_page'] = "node.list";
		}
	
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function nodeRender()
	{
		global $d13;
		$tvars = array();
		
		$nodecount = $d13->getGeneral('users', 'maxModules') * $d13->getGeneral('users', 'maxSectors');
		$buildQueue = array();
		
		for ($i = 0; $i < $nodecount; $i++) {
			$buildQueue[$i]['check'] = 0;
			$buildQueue[$i]['image'] = '';
		}
		
		foreach($this->node->queues->queue['build'] as $item) {
			$buildQueue[$item['slot']]['check'] = 1;
			$buildQueue[$item['slot']]['image'] = $d13->getModule($this->node->data['faction'], $item['obj_id'], 'images');
		}
				
		$tvars['tvar_getHTMLSectors'] = "";
		for ($sector = 1; $sector <= $d13->getGeneral('users', 'maxSectors'); $sector++) {

			// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Render Sector View
			
			$tvars['tvar_getHTMLNode'] = "";
			
			$offset_start = ($sector - 1) * $d13->getGeneral('users', 'maxModules');
			$offset_end = $offset_start + $d13->getGeneral('users', 'maxModules');
			$this->node->getModules();
			
			$add = true;
			$limit = 4;
			$i = 0;
			
			foreach($this->node->modules as $module) {
				if ($module['slot'] >= $offset_start && $module['slot'] <= $offset_end) {
					
					// - - - end current row
					if ($i == $limit) {
						$tvars['tvar_getHTMLNode'].= '</div>';
						$i = 0;
						if ($add) {
							$limit++;
							$add = false;
						} else {
							$limit--;
							$add = true;
						}
					}
					// - - - begin new row
					if ($i == 0) {
						$tvars['tvar_getHTMLNode'].= '<div class="row no-gutter">';
					}

					// - - - Add modules to row
					if ($module['module'] > - 1) {
						$the_module = d13_module_factory::create($module['module'], $module['slot'], $this->node);
					}
					
					if ($buildQueue[$module['slot']]['check']) {
						$tvars['tvar_moduleLink'] = "";
						$tvars['tvar_moduleImage'] = $buildQueue[$module['slot']]['image'][0]['image'];
						$tvars['tvar_moduleClass'] = '<img class="spinner resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/icon/gear.png">';
						$tvars['tvar_moduleLabel'] = $d13->getLangUI("underConstruction");
						$tvars['tvar_getHTMLNode'].= $d13->templateSubpage("sub.node.module", $tvars);
					} else if ($module['module'] > - 1) {
						$tvars['tvar_moduleLink'] = "index.php?p=module&action=get&nodeId=" . $this->node->data['id'] . "&slotId=" . $module['slot'];
						$tvars['tvar_moduleImage'] = $the_module->data['image'];
						if (($module['input'] <= 0 && $d13->getModule($this->node->data['faction'], $module['module'], 'maxInput') > 0) || ($d13->getModule($this->node->data['faction'], $module['module'], 'type') == 'defense' && $module['input'] < $d13->getModule($this->node->data['faction'], $module['module'], 'maxInput'))) {
							$tvars['tvar_moduleClass'] = '<a href="#" class="tooltip-left" data-tooltip="' . $d13->getLangUI("no") . " " . $d13->getLangGL('resources', $the_module->data['inputResource'], 'name') . '"><img class="animated bounce resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/icon/exclamation.png"></a>';
						} else {
							$tvars['tvar_moduleClass'] = "";
						}
						if ($the_module->data['maxLevel'] > 1) {
							$tvars['tvar_moduleLabel'] = $d13->getLangGL("modules", $this->node->data['faction'], $module['module'], "name") . " [L" . $module['level'] . "]";
						} else {
							$tvars['tvar_moduleLabel'] = $d13->getLangGL("modules", $this->node->data['faction'], $module['module'], "name");
						}
						$tvars['tvar_getHTMLNode'].= $d13->templateSubpage("sub.node.module", $tvars);
					} else {
						$tvars['tvar_moduleLink'] = "index.php?p=module&action=list&nodeId=" . $this->node->data['id'] . "&slotId=" . $module['slot'];
						mt_srand($module['slot']*$sector);
						$imgid = mt_rand(0,9);
						$tvars['tvar_moduleImage'] = "module_empty_".$imgid.".png";
						$tvars['tvar_moduleClass'] = "";
						$tvars['tvar_moduleLabel'] = $d13->getLangUI("emptySlot") . ' ' . $d13->getLangUI("clickToBuild");
						$tvars['tvar_getHTMLNode'].= $d13->templateSubpage("sub.node.module", $tvars);
					}

					// - - - advance or new row
					if ($i != $limit) {
					
						$i++;
					}
					
					$offset_start++;
					if ($offset_start == $offset_end) {
						$i = 0;
						break;
					}
				}
			}

			if ($i > 0 && $i < $limit) {
				$i = $limit - $i;
				for ($j = $i; $j <= $limit; $j++) {
					$tvars['tvar_getHTMLNode'].= $d13->templateSubpage("sub.node.filler", $tvars);
				}
				$tvars['tvar_getHTMLNode'].= '</div>';
			} else if ($i == 0) {
				$tvars['tvar_getHTMLNode'].= '</div>';
			}
			$tvars['tvar_getHTMLSectors'].= $d13->templateSubpage("sub.node.sector", $tvars);
		}
		
		$tvars['tvar_page'] = "node.get";
		$d13->templateInject($d13->templateSubpage("sub.swiper.horizontal", $tvars));
		
		return $tvars;
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
		
		$tvars['tvar_nodeID'] 		= $this->node->data['id'];
		$tvars['tvar_nodeName'] 	= $this->node->data['name'];
		$tvars['tvar_nodeX'] 		= $this->node->data['x'];	// TODO; not always available
		$tvars['tvar_nodeY'] 		= $this->node->data['y'];	// TODO: not always available
	
		$d13->templateRender($tvars['tvar_page'], $tvars, $this->node);
		
	}

}

// =====================================================================================EOF

