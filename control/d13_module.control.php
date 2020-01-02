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

class d13_moduleController extends d13_controller
{
	
	private $node, $node_status;
	
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
		
		if ($this->node_status == 'done') {
			$this->node->getModules();
			$this->node->checkAll(time());
			$this->node->getLocation();
		}
		
		$tvars = $this->doControl();
		$this->getTemplate($tvars);

	}
	
	// ----------------------------------------------------------------------------------------
	// doControl
	// @
	// ----------------------------------------------------------------------------------------
	
	private
	
	function doControl()
	{
	
		global $d13;
				
		switch ($_GET['action'])
		{
		
			case 'get':
				return $this->moduleGet();
				break;
				
			case 'set':
				return $this->moduleSet();
				break;
			
			case 'add':
				return $this->moduleAdd();
				break;
		
			case 'upgrade':
				return $this->moduleUpgrade();
				break;
			
			case 'remove':
				return $this->moduleRemove();
				break;
		
			case 'cancel':
				return $this->moduleCancel();
				break;
		
			case 'list':
				return $this->moduleList();
				break;
				
			case 'addMarket':
				return $this->moduleAddMarket();
				break;
			
			case 'buyMarket':
				return $this->moduleBuyMarket();
				break;
			
			case 'cancelMarket':
				return $this->moduleCancelMarket();
				break;
			
			case 'addTechnology':
				return $this->moduleAddTechnology();
				break;
		
			case 'cancelTechnology':
				return $this->moduleCancelTechnology();
				break;
		
			case 'addComponent':
				return $this->moduleAddComponent();
				break;
			
			case 'removeComponent':
				return $this->moduleRemoveComponent();
				break;
			
			case 'cancelComponent':
				return $this->moduleCancelComponent();
				break;
			
			case 'addUnit':
				return $this->moduleAddUnit();
				break;
			
			case 'removeUnit':
				return $this->moduleRemoveUnit();
				break;
			
			case 'cancelUnit':
				return $this->moduleCancelUnit();
				break;
		
		}
	
	}

	// ----------------------------------------------------------------------------------------
	// moduleGet
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleGet()
	{
	
		global $d13;
		$tvars = array();
		
		if (isset($_GET['moduleId'])) {
			$moduleId = $_GET['moduleId'];
		} else {
			$moduleId = $this->node->modules[$_GET['slotId']]['module'];
		}
		
		$tmp_module = d13_module_factory::create($moduleId, $_GET['slotId'], $this->node);
		
		$tvars = $tmp_module->getTemplateVariables();
		$tvars['tvar_page'] = $tmp_module->getTemplate();
		
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// moduleSet
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleSet()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_POST['input'])) {
			$status = $this->node->setModule($_GET['slotId'], $_POST['input']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $d13->getLangUI($status);
			}
		}
		
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// moduleAdd
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleAdd()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['moduleId'])) {
			$status = $this->node->addModule($_GET['slotId'], $_GET['moduleId'], $_POST['input']);
			if ($status == 'done') {
				
				header('Location: index.php?p=node&action=get&nodeId=' . $this->node->data['id']);
				exit();
			} else {
				$message = $d13->getLangUI($status);
			}
		}
				
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleUpgrade()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['slotId']) && isset($_GET['moduleId'])) {
			$status = $this->node->upgradeModule($_GET['slotId'], $_GET['moduleId'], $_POST['input']);
			if ($status == 'done') {
				
				header('Location: index.php?p=node&action=get&nodeId=' . $this->node->data['id']);
				exit();
			} else {
				$message = $d13->getLangUI($status);
			}
		}
				
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleRemove()
	{
		global $d13;
		$tvars = array();
		
		$status = $this->node->removeModule($_GET['slotId']);
		if ($status == 'done') {
			
			header('Location: index.php?p=node&action=get&nodeId=' . $this->node->data['id']);
			exit();
		} else {
			$message = $d13->getLangUI($status);
		}
				
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleCancel()
	{
		global $d13;
		$tvars = array();
		
		$status = $this->node->cancelModule($_GET['slotId']);
		if ($status == 'done') {
			
			header('Location: index.php?p=node&action=get&nodeId=' . $this->node->data['id']);
			exit();
		} else {
			$message = $d13->getLangUI($status);
		}
			
		return $tvars;

	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleList()
	{
		global $d13;
		$tvars = array();
		
		$moduleList = new d13_moduleListController($this->node, $_GET['slotId']);
		
		$tvars = $moduleList->getTemplateVariables();
		$tvars['tvar_page'] = $moduleList->getTemplate();
		
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleAddMarket()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['slotId'])) {
			$status = $this->node->addMarket($_GET['slotId']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $d13->getLangUI($status);
			}
		}
			
		return $tvars;
		
	}


	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleBuyMarket()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['slotId']) && isset($_GET['objType']) && isset($_GET['objId'])) {
			$status = $this->node->buyMarket($_GET['slotId'], $_GET['objType'], $_GET['objId']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $d13->getLangUI($status);
			}
		}
			
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleCancelMarket()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['slotId'])) {
			$status = $this->node->cancelMarket($_GET['slotId']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $d13->getLangUI($status);
			}
		}

		return $tvars;
		
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleAddTechnology()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['technologyId'])) {
			$status = $this->node->addTechnology($_GET['technologyId'], $_GET['slotId']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $d13->getLangUI($status);
			}
		}
			
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleCancelTechnology()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['technologyId'])) {
			$status = $this->node->cancelTechnology($_GET['technologyId'], $this->node->modules[$_GET['slotId']]['module']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $d13->getLangUI($status);
			}
		}

		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleAddComponent()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['componentId'], $_POST['quantity'])) {
			if ($_POST['quantity'] > 0) {
				$status = $this->node->addComponent($_GET['componentId'], $_POST['quantity'], $_GET['slotId']);
				if ($status == 'done') {
					
					header('Location: index.php?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId']);
					exit();
				} else {
					$message = $d13->getLangUI($status);
				}
			} else {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			}
		}
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleRemoveComponent()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['componentId'], $_POST['quantity'])) {
			$status = $this->node->removeComponent($_GET['componentId'], $_POST['quantity'], $node->modules[$_GET['slotId']]['module'], $_GET['slotId']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $d13->getLangUI($status);
			}
		}
		
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleCancelComponent()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['craftId'])) {
			$status = $this->node->cancelComponent($_GET['craftId'], $this->node->modules[$_GET['slotId']]['module']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $d13->getLangUI($status);
			}
		}
			
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleAddUnit()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['unitId'], $_POST['quantity']) && $_POST['quantity'] > 0) {
			$status = $this->node->addUnit($_GET['unitId'], $_POST['quantity'], $_GET['slotId']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $d13->getLangUI($status);
			}
		} else {
			
			header('Location: index.php?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId']);
			exit();
		}

		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleRemoveUnit()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['unitId'], $_POST['quantity'])) {
			$status = $this->node->removeUnit($_GET['unitId'], $_POST['quantity'], $this->node->modules[$_GET['slotId']]['module'], $_GET['slotId']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $d13->getLangUI($status);
			}
		}
				
		return $tvars;

	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleCancelUnit()
	{
		global $d13;
		$tvars = array();
		
		if (isset($_GET['trainId'])) {
			$status = $this->node->cancelUnit($_GET['trainId'], $this->node->modules[$_GET['slotId']]['module']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $d13->getLangUI($status);
			}
		}
			
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
		
		$d13->templateRender($tvars['tvar_page'] , $tvars, $this->node);
		
		
	}

}

// =====================================================================================EOF

