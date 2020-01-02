<?php

// ========================================================================================
//
// COMBAT.CLASS
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//
// ABOUT CLASSES:
//
// Represents the lowest layer, next to the database. All logic checks must be performed
// by a controller beforehand. Any class function calls directly access the database. 
// 
// NOTES:
//
// Responsible for all calculations regarding a battle between two armies. Requires army
// data of attacker/defender. Not responsible for movement and/or fuel costs. Handles all
// aspects including damage, casualties, scouting, loot and sending combat reports to the
// participants.
//
// ========================================================================================

class d13_combat

{
	
	private $attackerNode, $defenderNode, $attackerUser, $defenderUser;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @ Creates a new combat instances and immediately starts the war using the provided data
	// ----------------------------------------------------------------------------------------
	public

	function __construct($data)
	{
		return $this->doCombat($data);
	}

	// ----------------------------------------------------------------------------------------
	// doCombat
	// Processes the whole combat including its results and effects
	// Important: Function call order must NOT change!
	// ----------------------------------------------------------------------------------------

	public

	function doCombat($data)
	{
	
		global $d13;
		
		$data = $this->doCalculateAttackerStats($data);
		$data = $this->doCalculateDefenderStats($data);
		$data = $this->doCalculateCombatSetup($data);
		$data = $this->doScoutCheck($data);
		$data = $this->doCalculateAttackerCasualties($data);
		$data = $this->doCalculateDefenderCasualties($data);
		$data = $this->doCalculateWinner($data);
		$data = $this->doAdjustLeague($data);
		$data = $this->doApplyCasualties($data);
		$data = $this->doCalculateAftermath($data);
		
		return $data;
	}


	// ----------------------------------------------------------------------------------------
	// doCalculateAttackerStats
	// ----------------------------------------------------------------------------------------

	private
	
	function doCalculateAttackerStats($data)

	{
		
		global $d13;
		
		$data['output']['attacker']['groups'] = array();
		
		foreach($d13->getGeneral('classes') as $key => $class) {
			$data['classes']['attacker'][$key] = 0;
		}

		foreach($d13->getGeneral('stats') as $stat) {
			$data['input']['attacker'][$stat] = 0;
		}
		
		$this->attackerUser = new d13_user();
		$status = $this->attackerUser->get('id', $data['input']['attacker']['userId']);
		
		$this->attackerNode = new d13_node();
		$status = $this->attackerNode->get('id', $data['input']['attacker']['nodeId']);
		
		shuffle($data['input']['attacker']['groups']);
		
		$armyBonus = array();	
		foreach($data['input']['attacker']['groups'] as $key => $group) {
			if ($group['quantity'] > 0) {

				$args = array();
				$args['supertype'] 	= 'unit';
				$args['obj_id'] 	= $group['unitId'];
				$args['node'] 		= $this->attackerNode;
				
				$unit = new d13_object_unit($args);
				
				if (!empty($unit->data['armyAttackModifier'])) {
					foreach ($unit->data['armyAttackModifier'] as $modifier) {
						$armyBonus[] = $modifier;
					}
				}
			}
		}
		
		foreach($data['input']['attacker']['groups'] as $key => $group) {
			if ($group['quantity'] > 0) {
			
				$args = array();
				$args['supertype'] 	= 'unit';
				$args['obj_id'] 	= $group['unitId'];
				$args['node'] 		= $this->attackerNode;
				
				$unit = new d13_object_unit($args);
				
				$stats = $unit->getStats();
				$upgrades = $unit->getUpgrades();
			
				foreach($d13->getGeneral('stats') as $stat) {
			
					$data['input']['attacker']['groups'][$key][$stat] = ($stats[$stat] + $upgrades[$stat]) * $group['quantity'];
					
					if (!empty($armyBonus)) {
						foreach ($armyBonus as $modifier) {
							if ($modifier['stat'] == $stat) {
								$data['input']['attacker']['groups'][$key][$stat] += floor($stats[$stat] * $modifier['value']);
							}
						}
					}
				
					if (!empty($unit->data['attackModifier'])) {
						foreach ($unit->data['attackModifier'] as $modifier) {
							if ($modifier['stat'] == $stat) {
								$data['input']['attacker']['groups'][$key][$stat] += floor($stats[$stat] * $modifier['value']);
							}
						}
					}
				
					$data['input']['attacker'][$stat] += $data['input']['attacker']['groups'][$key][$stat];
				
				}
			
				$data['input']['attacker']['groups'][$key]['critdmg'] = 0;
				$crit = $data['input']['attacker']['groups'][$key]['critical'];
							
				if ($crit > 100) { $crit = 100; }
				if (rand(1,100) <= $crit) {
					$data['input']['attacker']['groups'][$key]['critdmg'] += $data['input']['attacker']['groups'][$key]['damage'] * $d13->getGeneral('factors', 'critical');
					$data['input']['attacker']['damage'] += $data['input']['attacker']['groups'][$key]['damage'] * $d13->getGeneral('factors', 'critical');
				}
				
				//- - - - - Add to Class List
				$class = $d13->getUnit($data['input']['attacker']['faction'],$group['unitId'],'class');
				foreach ($d13->getGeneral('classes', $class) as $classKey => $classMod) {
					$data['classes']['attacker'][$classKey] += $classMod;
				}
		
			}
		}
		
		
		
		return $data;

	}
	
	// ----------------------------------------------------------------------------------------
	// doCalculateDefenderStats
	// ----------------------------------------------------------------------------------------

	private
	
	function doCalculateDefenderStats($data)

	{
		global $d13;
		
		$data['output']['defender']['groups'] = array();
		
		foreach($d13->getGeneral('classes') as $key => $class) {
			$data['classes']['defender'][$key] = 0;
		}

		foreach($d13->getGeneral('stats') as $stat) {
			$data['input']['defender'][$stat] = 0;
		}
		
		$this->defenderUser = new d13_user();
		$status = $this->defenderUser->get('id', $data['input']['defender']['userId']);

		$this->defenderNode = new d13_node();
		$status = $this->defenderNode->get('id', $data['input']['defender']['nodeId']);
		
		if ($data['input']['defender']['groups']) {
		
			shuffle($data['input']['defender']['groups']);

			$armyBonus = array();	
			foreach($data['input']['defender']['groups'] as $key => $group) {
				if (isset($group['quantity']) && $group['quantity'] > 0) {
			
					$args = array();
					$args['supertype'] 	= 'unit';
					$args['obj_id'] 	= $group['unitId'];
					$args['node'] 		= $this->defenderNode;
				
					$unit = new d13_object_unit($args);
			
					if (!empty($unit->data['armyDefenseModifier'])) {
						foreach ($unit->data['armyDefenseModifier'] as $modifier) {
							$armyBonus[] = $modifier;
						}
					}
				}
			}

			foreach($data['input']['defender']['groups'] as $key => $group) {

				// - - - - - UNITS

				if (isset($group['quantity']) && $group['type'] == 'unit' && $group['quantity'] > 0) {
				
					$args = array();
					$args['supertype'] 	= 'unit';
					$args['obj_id'] 	= $group['unitId'];
					$args['node'] 		= $this->defenderNode;
				
					$unit = new d13_object_unit($args);
				
					$stats = $unit->getStats();
					$upgrades = $unit->getUpgrades();
				
					foreach($d13->getGeneral('stats') as $stat) {
				
						$data['input']['defender']['groups'][$key][$stat] = ($stats[$stat] + $upgrades[$stat]) * $group['quantity'];
					
						if (!empty($armyBonus)) {
							foreach ($armyBonus as $modifier) {
								if ($modifier['stat'] == $stat) {
									$data['input']['defender']['groups'][$key][$stat] += floor($stats[$stat] * $modifier['value']);
								}
							}
						}
					
						if (!empty($unit->data['defenseModifier'])) {
							foreach ($unit->data['defenseModifier'] as $modifier) {
								if ($modifier['stat'] == $stat) {
									$data['input']['defender']['groups'][$key][$stat] += floor($stats[$stat] * $modifier['value']);
								}
							}
						}
				
						$data['input']['defender'][$stat] += $data['input']['defender']['groups'][$key][$stat];
					
					}
				
					$data['input']['defender']['groups'][$key]['critdmg'] = 0;
					$crit = $data['input']['defender']['groups'][$key]['critical'];
					if ($crit > 100) { $crit = 100; }
					if (rand(1,100) <= $crit) {
						$data['input']['defender']['groups'][$key]['critdmg'] += $data['input']['defender']['groups'][$key]['damage'] * $d13->getGeneral('factors', 'critical');
						$data['input']['defender']['damage'] += $data['input']['defender']['groups'][$key]['damage'] * $d13->getGeneral('factors', 'critical');
					}

					//- - - - - Add to Class List
					$class = $d13->getUnit($data['input']['defender']['faction'],$group['unitId'],'class');
					foreach ($d13->getGeneral('classes', $class) as $classKey => $classMod) {
						$data['classes']['defender'][$classKey] += $classMod;
					}
				
				// - - - - - MODULES

				} else if ($group['type'] == 'module' && $group['input'] > 0) {
							
					$args = array();
					$args['supertype'] 	= 'turret';
					$args['obj_id'] 	= $group['moduleId'];
					$args['level'] 		= $group['level'];
					$args['input'] 		= $group['input'];
					$args['unitId'] 	= $group['unitId'];
					$args['node'] 		= $this->defenderNode;
				
					$turret = new d13_object_turret($args);
				
					$stats = $turret->getStats();
					$upgrades = $turret->getUpgrades();
				
					foreach($d13->getGeneral('stats') as $stat) {
				
						$data['input']['defender']['groups'][$key][$stat] = ($stats[$stat] + $upgrades[$stat]) * $group['level'];
					
						foreach ($armyBonus as $modifier) {
							if ($modifier['stat'] == $stat) {
								$data['input']['defender']['groups'][$key][$stat] += floor($stats[$stat] * $modifier['value']);
							}
						}
				
						if (!empty($unit->data['defenseModifier'])) {
							foreach ($unit->data['defenseModifier'] as $modifier) {
								if ($modifier['stat'] == $stat) {
									$data['input']['defender']['groups'][$key][$stat] += floor($stats[$stat] * $modifier['value']);
								}
							}
						}

						$data['input']['defender'][$stat]+= $data['input']['defender']['groups'][$key][$stat];

					}

					// - - - - - Special rule for Module HP

					if ($d13->getGeneral('options', 'defensiveModuleDamage')) {
						$data['input']['defender']['groups'][$key]['hp'] = ($data['input']['defender']['groups'][$key]['input'] / $data['input']['defender']['groups'][$key]['maxInput']) * $data['input']['defender']['groups'][$key]['hp'];
					}
				
					$data['input']['defender']['groups'][$key]['critdmg'] = 0;
					$crit = $data['input']['defender']['groups'][$key]['critical'];
					if ($crit > 100) { $crit = 100; }
					if (rand(1,100) <= $crit) {
						$data['input']['defender']['groups'][$key]['critdmg'] += $data['input']['defender']['groups'][$key]['damage'] * $d13->getGeneral('factors', 'critical');
						$data['input']['defender']['damage'] += $data['input']['defender']['groups'][$key]['damage'] * $d13->getGeneral('factors', 'critical');
					}
				
					//- - - - - Add to Class List
					$class = $d13->getUnit($data['input']['defender']['faction'],$group['unitId'],'class');
					foreach ($d13->getGeneral('classes', $class) as $classKey => $classMod) {
						$data['classes']['defender'][$classKey] += $classMod;
					}

				}
			}
		
			
		
		}
		
		return $data;

	}	

	// ----------------------------------------------------------------------------------------
	// doCalculateCombatSetup
	// ----------------------------------------------------------------------------------------
	private
	
	function doCalculateCombatSetup($data)
	{
	
		global $d13;
		
		$percentCap = 400;
		
		$attackerRatio = 0;
		$defenderRatio = 0;
		
		$data['output']['attacker']['trueDamage'] = 0;
		$data['output']['defender']['trueDamage'] = 0;
		
		$data['input']['attacker']['damage']++;
		$data['input']['defender']['damage']++;
		$data['input']['attacker']['armor']++;
		$data['input']['defender']['armor']++;
		
		$attackerRatio = sqrt($data['input']['attacker']['damage'] / $data['input']['defender']['armor']);
		$defenderRatio = sqrt($data['input']['defender']['damage'] / $data['input']['attacker']['armor']);
		
		$attackerDamage = $data['input']['attacker']['damage'] * $attackerRatio;
		$defenderDamage = $data['input']['defender']['damage'] * $defenderRatio;
		
		$data['input']['attacker']['trueDamage'] = floor($attackerDamage);
		$data['input']['defender']['trueDamage'] = floor($defenderDamage);
		
		$data['output']['attacker']['totalDamage'] = 0;
		$data['output']['defender']['totalDamage'] = 0;
	
		return $data;
	
	}

	// ----------------------------------------------------------------------------------------
	// doCalculateAttackerCasualties
	// ----------------------------------------------------------------------------------------
	private
	
	function doCalculateAttackerCasualties($data)
	{
		
		global $d13;
				
		foreach($data['input']['attacker']['groups'] as $key => $group) {
			if (isset($group['quantity']) && $group['quantity'] > 0) {
	
				$baseDamage = 0;
				$damage = 0;
				$casualties = 0;
				$bonusDamage = 0;
						
				// - - - Ignore if Scout was successful
				if (!$data['combat']['requiresScoutCheck'] || ($data['combat']['requiresScoutCheck'] && $data['output']['attacker']['scoutCheck'] <= 0)) {
					$baseDamage = $data['input']['defender']['trueDamage'];
				}
						
				if ($baseDamage > 0) {
						
					$class = $d13->getUnit($data['input']['attacker']['faction'], $group['unitId'], 'class');
					foreach ($data['classes']['defender'] as $classKey => $damageMod) {
						if ($classKey == $class) {
							$bonusDamage += floor($baseDamage * $damageMod);
						}
					}
				
					$damage = 1 + $baseDamage + $bonusDamage;
					$casualties = floor($damage / ($group['hp']/$group['quantity']));
					$data['input']['defender']['trueDamage'] -= $casualties * $group['hp'];
				
					if ($casualties < 0) { $casualties = 0; }
				
					$group['quantity'] -= $casualties;
				
					if ($group['quantity'] < 0) { $group['quantity'] = 0; }
						
				}
						
				$data['output']['attacker']['groups'][$key] = $group;
				$data['output']['defender']['totalDamage'] += $damage;
			
			}
		
		}
		
		return $data;
		
	}

	// ----------------------------------------------------------------------------------------
	// doCalculateDefenderCasualties
	// ----------------------------------------------------------------------------------------
	private
	
	function doCalculateDefenderCasualties($data)
	{
	
		global $d13;
		
		if ($data['input']['defender']['groups']) {
		
			foreach($data['input']['defender']['groups'] as $key => $group) {
				
				
					$baseDamage = 0;
					$damage = 0;
					$casualties = 0;
					$bonusDamage = 0;
					
					// - - - Ignore if Scout was successful
					if (!$data['combat']['requiresScoutCheck'] || ($data['combat']['requiresScoutCheck'] && $data['output']['attacker']['scoutCheck'] <= 0)) {
						$baseDamage = $data['input']['attacker']['trueDamage'];	
					}
					
					if ($baseDamage > 0) {
					
						$class = $d13->getUnit($data['input']['defender']['faction'], $group['unitId'], 'class');
						foreach ($data['classes']['attacker'] as $classKey => $damageMod) {
							if ($classKey == $class) {
								$bonusDamage += floor($baseDamage * $damageMod);
							}
						}
				
						$damage = 1 + $baseDamage + $bonusDamage;
						
						//-- ignore if enemy group is empty
						if (isset($group['quantity']) && $group['quantity'] > 0) {
							$casualties = floor($damage / ($group['hp']/$group['quantity']));
							$data['input']['attacker']['trueDamage'] -= $casualties * $group['hp'];
						}
					
						if ($casualties < 0) { $casualties = 0; }
				
						if ($group['type'] == 'unit') {
							$group['quantity'] -= $casualties;
							if ($group['quantity'] < 0) { $group['quantity'] = 0; }
						} else if ($group['type'] == 'module') {
							$group['input'] -= $casualties;
							if ($group['input'] < 0) { $group['input'] = 0; }
						}
					
					}
					
					//-- ignore if enemy group is empty
					if (isset($group['quantity']) && $group['quantity'] > 0) {
						$data['output']['defender']['groups'][$key] = $group;
					}
					
					//-- important for victory calculation
					$data['output']['attacker']['totalDamage']	+= $damage;

			}
		
		}
		
		return $data;
	
	}

	// ----------------------------------------------------------------------------------------
	// doCalculateWinner
	// ----------------------------------------------------------------------------------------
	private
	
	function doCalculateWinner($data)
	{
			
		global $d13;
		
		// - - - - - - - - - - Requires Scout Check, winner is winner of scout
		if ($data['combat']['requiresScoutCheck']) {
		
			if ($data['output']['attacker']['scoutCheck'] > 0) {
				$data['output']['attacker']['winner'] = 1;
				$data['output']['defender']['winner'] = 0;
			} else {
				$data['output']['attacker']['winner'] = 0;
				$data['output']['defender']['winner'] = 1;
			}
		
		// - - - - - - - - - - Regular Combat, winner is winner of combat
		} else {
		
			if ($data['output']['defender']['totalDamage'] >= $data['output']['attacker']['totalDamage']) {
				$data['output']['attacker']['winner'] = 0;
				$data['output']['defender']['winner'] = 1;
			} else {
				$data['output']['attacker']['winner'] = 1;
				$data['output']['defender']['winner'] = 0;
			}

			/*
			if ((!$data['input']['attacker']['hp']) && (!$data['input']['defender']['hp'])) {
				$data['output']['attacker']['winner'] = 0;
				$data['output']['defender']['winner'] = 1;
			} else if (($data['input']['attacker']['hp']) && (!$data['input']['defender']['hp'])) {
				$data['output']['attacker']['winner'] = 1;
				$data['output']['defender']['winner'] = 0;
			}
			*/
			
			
			
		
		}
		
		return $data;
			
	}

	// ----------------------------------------------------------------------------------------
	// doScoutCheck
	// ----------------------------------------------------------------------------------------
	
	private
	
	function doScoutCheck($data)
	{
	
		global $d13;
		
		$data['output']['attacker']['scoutValue'] = 0;

		if ($data['combat']['requiresScoutCheck']) {

			$attacker = $data['input']['attacker']['stealth'];
			$defender = $data['input']['defender']['vision'];
		
			if ($attacker > $defender) {
		
				$value = d13_misc::percent_difference($attacker, $defender);
				
				if ($value > 100) {
					$value = 100;
				} else if ($value < 1) {
					$value = 1;
				}
						
				$data['output']['attacker']['scoutValue'] = $value;
			
			} else {
				$data['output']['attacker']['scoutValue'] = 0;
			}

			if ($attacker > $defender) {
				$data['output']['attacker']['scoutCheck'] = 1;
			} else {
				$data['output']['attacker']['scoutCheck'] = 0;
			}
		
		}
		
		return $data;
		
	}
	
	// ----------------------------------------------------------------------------------------
	// doScout
	// @ lookup all individual scout results and add them to data, then return
	// ----------------------------------------------------------------------------------------
	private
	
	function doScout($data)
	{
			
		$data = $this->doScoutTechnology($data);
		$data = $this->doScoutResources($data);
		$data = $this->doScoutModules($data);
		$data = $this->doScoutUnits($data);
		
		return $data;
	
	}
	
	// ----------------------------------------------------------------------------------------
	// doScoutUnits
	// @ returns a list of stationed units sorted by their amount
	//   units with stealth attributte have a slight chance to be missed
	// ----------------------------------------------------------------------------------------
	private
	
	function doScoutUnits($data)
	{
	
		global $d13;
	
		$value = $data['output']['attacker']['scoutValue'] - $d13->getGeneral("spionage", "unit");
		
		if ($value > 0 ) {
		
			$tmp_result = array();
				
			$this->defenderNode->getUnits();
			$tmp_units = $this->defenderNode->units;
	
			foreach ($tmp_units as $unit) {
				$tmp_unit = $d13->getUnit($this->defenderNode->data['faction'], $unit['id']);
				if ($tmp_unit['active'] && $unit['value'] > 0 && $tmp_unit['stealth'] < $data['output']['attacker']['scoutValue']) {
					$tmp_result[]= array('type'=>'scout_units','unit'=>$unit['id'], 'value'=>$unit['value']);
				}
			}
			
			// limit amount of objects by scout value and threshold
			$value = max(floor($value/$d13->getGeneral("spionage", "threshold")), 1);
			shuffle($tmp_result);
			$tmp_result = array_slice($tmp_result, 0, $value);
			
			$tmp_result = d13_misc::record_sort($tmp_result, 'value', true);

			$data['output']['attacker']['results']['scout_units'] = $tmp_result;
		
		}
		
		return $data;
	
	}

	// ----------------------------------------------------------------------------------------
	// doScoutTechnology
	// @ returns a list of technologies, sorted by level
	// ----------------------------------------------------------------------------------------
	private
	
	function doScoutTechnology($data)
	{
	
		global $d13;
		
		$value = $data['output']['attacker']['scoutValue'] - $d13->getGeneral("spionage", "technology");
		
		if ($value > 0 ) {
		
			$tmp_result = array();
				
			$this->defenderNode->getTechnologies();
			$tmp_technology = $this->defenderNode->technologies;
	
			foreach ($tmp_technology as $technology) {
				$tmp_tech = $d13->getTechnology($this->defenderNode->data['faction'], $technology['id']);
				if ($tmp_tech['active'] && $technology['level'] > 0) {
					$tmp_result[]= array('type'=>'scout_technologies','technology'=>$technology['id'], 'level'=>$technology['level']);
				}
			}
			
			
			// limit amount of objects by scout value and threshold
			$value = max(floor($value/$d13->getGeneral("spionage", "threshold")), 1);
			shuffle($tmp_result);
			$tmp_result = array_slice($tmp_result, 0, $value);
			
			$tmp_result = d13_misc::record_sort($tmp_result, 'level', true);
		
			$data['output']['attacker']['results']['scout_technologies'] = $tmp_result;
		
			return $data;
		
		}
	
	}
	
	// ----------------------------------------------------------------------------------------
	// doScoutModules
	// @ returns a list of modules sorted by level, defensive modules have priority
	// ----------------------------------------------------------------------------------------
	private
	
	function doScoutModules($data)
	{
	
		global $d13;
		
		$value = $data['output']['attacker']['scoutValue'] - $d13->getGeneral("spionage", "modules");
		
		if ($value > 0 ) {
		
			$tmp_result = array();
			$tmp_result2 = array();
		
			$this->defenderNode->getModules();
			$tmp_modules = $this->defenderNode->modules;
	
			foreach ($tmp_modules as $module) {
				$tmp_mod = $d13->getModule($this->defenderNode->data['faction'], $module['module']);
				if ($tmp_mod['active'] && $tmp_mod['type'] == 'defense' && $module['level'] > 0) {
					$tmp_result[]= array('type'=>'scout_modules', 'module'=>$module['module'], 'level'=>$module['level']);
				} else if ($tmp_mod['active'] && $tmp_mod['type'] != 'defense' && $module['level'] > 0) {
					$tmp_result2[]= array('type'=>'scout_modules', 'module'=>$module['module'], 'level'=>$module['level']);
				}
			}
			
			
			// limit amount of objects by scout value and threshold
			$value = max(floor($value/$d13->getGeneral("spionage", "threshold")), 1);
			shuffle($tmp_result);
			shuffle($tmp_result2);
			$tmp_result = array_slice($tmp_result, 0, $value);
			$tmp_result2 = array_slice($tmp_result2, 0, $value);
			
			$tmp_result = array_merge($tmp_result, $tmp_result2);
			$tmp_result = d13_misc::record_sort($tmp_result, 'level', true);
		
			$data['output']['attacker']['results']['scout_modules'] = $tmp_result;
		
		}
		
		return $data;
	
	}
	
	// ----------------------------------------------------------------------------------------
	// doScoutResources
	// @ returns a list of resources, sorted by rarity
	// ----------------------------------------------------------------------------------------
	private
	
	function doScoutResources($data)
	{
	
		global $d13;
		
		$value = $data['output']['attacker']['scoutValue'] - $d13->getGeneral("spionage", "resources");
		
		if ($value > 0 ) {
		
			$tmp_result = array();
			
			$this->defenderNode->getResources();
			$tmp_resources = $this->defenderNode->resources;
	
			foreach ($tmp_resources as $resource) {
				$tmp_res = $d13->getResource($resource['id']);
				if ($tmp_res['active'] && $tmp_res['carryable'] && $resource['value'] > 0) {
					$tmp_result[]= array('type'=>'scout_resources', 'rarity'=>$tmp_res['rarity'], 'resource'=>$resource['id'], 'value'=>$resource['value']);
				}
			}
			
			// limit amount of objects by scout value and threshold
			$value = max(floor($value/$d13->getGeneral("spionage", "threshold")), 1);
			shuffle($tmp_result);
			$tmp_result = array_slice($tmp_result, 0, $value);
				
			$tmp_result = d13_misc::record_sort($tmp_result, 'rarity', true);
		
			$data['output']['attacker']['results']['scout_resources'] = $tmp_result;
		
		}
		
		return $data;
	
	}	
	
	// ----------------------------------------------------------------------------------------
	// doSabotage
	// ----------------------------------------------------------------------------------------
	
	private
	
	function doSabotage($data)
	{
	
		global $d13;
		
		#$data['output']['attacker']['scoutValue']
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	
	}
	
	// ----------------------------------------------------------------------------------------
	// doAdjustLeague
	// ----------------------------------------------------------------------------------------
	private
	
	function doAdjustLeague($data)
	{
		global $d13;
		
		// - - - Ignore if Scout was successful
		if (!$data['combat']['requiresScoutCheck'] || ($data['combat']['requiresScoutCheck'] && $data['output']['attacker']['scoutCheck'] <= 0)) {
		
			$exp_value = 0;
			$attackerTrophies = $d13->getGeneral('factors', 'experience') + count($d13->getLeague());
			$defenderTrophies = $d13->getGeneral('factors', 'experience') + count($d13->getLeague());
		
			$a = $this->attackerUser->data['trophies'] +1;
			$b = $this->defenderUser->data['trophies'] +1;
			$difference		= sqrt($a / $b);
		
			$exp_value 		  = 1+ floor(($difference * $attackerTrophies)  / $d13->getGeneral('factors', 'experience'));
			$attackerTrophies = 1 + floor(($difference * $attackerTrophies)  / $d13->getGeneral('factors', 'trophies'));
			$defenderTrophies = 1 + floor(($difference * $defenderTrophies)  / $d13->getGeneral('factors', 'trophies'));
		
			if ($data['output']['attacker']['winner'] > 0) {
				$attackerTrophies = abs($attackerTrophies);
				$defenderTrophies = $defenderTrophies * -1;
			} else {
				$attackerTrophies = $attackerTrophies * -1;
				$defenderTrophies = abs($defenderTrophies);
			}

			// - - - - - Attacker Stats
			$status = $this->attackerUser->addStat('experience', $exp_value);
			$status = $this->attackerUser->addStat('trophies', $attackerTrophies);
		
			// - - - - - Defender Stats
			$status = $this->defenderUser->addStat('trophies', $defenderTrophies);
		
			$data['output']['attacker']['userstat']['experience'] 	= $exp_value;
			$data['output']['attacker']['userstat']['trophies'] 	= $attackerTrophies;
			$data['output']['defender']['userstat']['trophies'] 	= $defenderTrophies;
		
		}
			
		return $data;
		
	}
	
	// ----------------------------------------------------------------------------------------
	// doStealResources
	// ----------------------------------------------------------------------------------------
	private
	
	function doStealResources($data)
	{
	
		global $d13;
		
		// ============================== Calculate Ratio according to Leagues
		
		$resourceRatio 	= 20; 								// TODO move to data files later
		$minRatio 		= 1;								// TODO move to data files later
		$maxRatio 		= 60;								// TODO move to data files later
		
		$this->attackerUser = new d13_user();
		$status = $this->attackerUser->get('id', $data['input']['attacker']['userId']);
		
		$this->defenderUser = new d13_user();
		$status = $this->defenderUser->get('id', $data['input']['defender']['userId']);
		
		if ($status == 'done') {
		
			$attackerLeague = $d13->getLeague(d13_misc::getLeague($this->attackerUser->data['level'], $this->attackerUser->data['trophies']));
			$defenderLeague = $d13->getLeague(d13_misc::getLeague($this->defenderUser->data['level'], $this->defenderUser->data['trophies']));
			
			$attackerLeague['id']++;
			$defenderLeague['id']++;
			
			if ($attackerLeague['id'] >= $defenderLeague['id']) {
				$resourceRatio -= floor($resourceRatio * ($attackerLeague['id']-$defenderLeague['id'])/$attackerLeague['id']);
			} else {
				$resourceRatio += floor($resourceRatio * ($defenderLeague['id']-$attackerLeague['id'])/$defenderLeague['id']);
			}
		
			if ($resourceRatio < $minRatio) {
				$resourceRatio = $minRatio;
			} else if ($resourceRatio > $maxRatio) {
				$resourceRatio = $maxRatio;
			}
		
		}
		
		// ============================== Calculate total Capacity and Ratio
		
		$totalResCapacity	= 0;
		$totalResAvailable	= 0;
		$resList 			= array();
		$realResList		= array();
		
		$data['output']['attacker']['results'] = array();
		$data['output']['defender']['results'] = array();
		
		// - Attacker Capacity

		if ($status == 'done') {
		
			foreach($data['input']['attacker']['groups'] as $key => $group) {
				
				$args = array();
				$args['supertype'] 	= 'unit';
				$args['obj_id'] 	= $group['unitId'];
				$args['node'] 		= $this->defenderNode;
				
				$unit = new d13_object_unit($args);
				
				$stats = $unit->getStats();
				$upgrades = $unit->getUpgrades();
			
				foreach($d13->getGeneral('stats') as $stat) {
					$data['input']['attacker']['groups'][$key][$stat] = ($stats[$stat] + $upgrades[$stat]) * $group['quantity'];
					$data['input']['attacker'][$stat]+= $data['input']['attacker']['groups'][$key][$stat];
				}

				$totalResCapacity += $data['input']['attacker']['groups'][$key]['capacity'];
			}
		
		}
		
		// - Defender Availability

		if ($status == 'done') {
			$this->defenderNode->getResources();

			foreach ($this->defenderNode->resources as $resource) {
			
				$tmp_res = $d13->getResource($resource['id']);
			
				if ($tmp_res['active'] && $tmp_res['type'] == 'dynamic' && $tmp_res['carryable'] && $resource['value'] > 0) {
					
						$resAvailable = $resource['value'] * ($resourceRatio/100);
						$totalResAvailable += $resAvailable;
						$resList[] = array('resource'=>$resource['id'], 'value'=>$resAvailable);
						$realResList[] = array('type'=>'raid_resources', 'resource'=>$resource['id'], 'value'=>0);
					
				}
			}
		
		}
		
		$totalResCapacity = floor($totalResCapacity);
		$totalResAvailable = floor($totalResAvailable);

		// ============================== Check existing Loot
		$continue = true;
		
		if (!empty($resList)) {
			while ($continue) {
			
				// - select random from available resources
				$key = array_rand($resList);	
			
				// - choose 1/10 from available resources
				//   (to give the other resources a chance as well)
				$value = floor($resList[$key]['value']/10);
				
				if ($value > $totalResCapacity) {
					$value = $totalResCapacity;
				}
				
				$totalResCapacity -= $value;
				$totalResAvailable -= $value;
				$resList[$key]['value'] -= $value;
				$realResList[$key]['value'] += $value;
				
				// - stop when either capacity or resources are depleted
				if ($totalResCapacity <= 0 || $totalResAvailable <= 0) {
					$continue = false;
				}

			}
		}
		
		// ============================== Check virtual Loot
		if ($d13->getGeneral('options', 'bonusLoot')) {
				// TODO
				// add randomized bonus loot out of 'thin air'
				// - this feature is still questionable -
		}
				
		// ============================== Process Data
		$d13->dbQuery('start transaction');
		
		$data['output']['defender']['results']['raid_resources'] = $realResList;
		$ok = $this->defenderNode->setResources($realResList);
		
		$data['output']['attacker']['results']['raid_resources'] = $realResList;
		$ok = $this->attackerNode->setResources($realResList, 1);

		if ($ok) {
			$d13->dbQuery('commit');
		} else {
			$d13->dbQuery('rollback');
		}
		
		// ============================== Return Data
		
		return $data;
	
	}

	// ----------------------------------------------------------------------------------------
	// doApplyCasualties
	// ----------------------------------------------------------------------------------------
	private
	
	function doApplyCasualties($data)
	
	{
		
		global $d13;
		
		$data['combat']['overkill'] = true;
		
		$this->defenderNode->getResources();

		// - - - - - Defender Groups

		foreach($data['output']['defender']['groups'] as $key => $group) {

			// - - - - - Units

			if ($group['type'] == 'unit') {
				$d13->dbQuery('update units set value="' . $group['quantity'] . '" where node="' . $this->defenderNode->data['id'] . '" and id="' . $group['unitId'] . '"');
				if ($d13->dbAffectedRows() == - 1) {
					$ok = 0;
				}

				$lostCount = $data['input']['defender']['groups'][$key]['quantity'] - $group['quantity'];
				if ($lostCount > 0) {
					$upkeepResource = $d13->getUnit($this->defenderNode->data['faction'], $group['unitId'], 'upkeepResource');
					$upkeep = $d13->getUnit($this->defenderNode->data['faction'], $group['unitId'], 'upkeep');
					$this->defenderNode->resources[$upkeepResource]['value']+= $upkeep * $lostCount;
					$d13->dbQuery('update resources set value="' . $this->defenderNode->resources[$upkeepResource]['value'] . '" where node="' . $this->defenderNode->data['id'] . '" and id="' . $upkeepResource . '"');
					if ($d13->dbAffectedRows() == - 1) {
						$ok = 0;
					}
				}

				if ($group['quantity']) {
					$data['combat']['overkill'] = false;
				}

				// - - - - - Modules

			}
			else
			if ($group['type'] == 'module') {
				$d13->dbQuery('update modules set input="' . $group['input'] . '" where node="' . $this->defenderNode->data['id'] . '" and module="' . $group['moduleId'] . '"');
				if ($d13->dbAffectedRows() == - 1) {
					$ok = 0;
				}

				// no lost count for modules

				if ($group['input']) {
					$data['combat']['overkill'] = false;
				}
			}
		}

		// - - - - - Attacker Groups

		foreach($data['output']['attacker']['groups'] as $key => $group) {
			$d13->dbQuery('update combat_units set value="' . $group['quantity'] . '" where combat="' . $data['combat']['cid'] . '" and id="' . $group['unitId'] . '"');
			if ($d13->dbAffectedRows() == - 1) {
				$ok = 0;
			}
		}

		// - - - - - Check Battle Result

		$start = strftime('%Y-%m-%d %H:%M:%S', $data['combat']['end']);
		$d13->dbQuery('update combat set stage=1, start="' . $start . '" where id="' . $data['combat']['cid'] . '"');
		if ($d13->dbAffectedRows() == - 1) {
			$ok = 0;
		}

		return $data;

	}
	
	// ----------------------------------------------------------------------------------------
	// doCalculateAftermath
	// ----------------------------------------------------------------------------------------
	private
	
	function doCalculateAftermath($data)
	
	{

		global $d13;

		// - - - - - ScoutReport
		if ($data['combat']['scoutReport']) {
			if (!$data['combat']['requiresScoutCheck'] || ($data['combat']['requiresScoutCheck'] && $data['output']['attacker']['scoutCheck'] > 0)) {
			$data = $this->doScout($data);
			}
		}
		
		// - - - - - lootAttacker
		if ($data['output']['attacker']['winner'] && $data['combat']['lootAttacker']) {
			$data = $this->doStealResources($data);
		}
		
		// - - - - - nodeDefenderConquer
		if ($data['output']['attacker']['winner'] && $data['combat']['nodeDefenderConquer']) {
			if ($data['combat']['requiresWipeout'] == false || $data['combat']['requiresWipeout'] && $overkill) {
				$d13->dbQuery('update nodes set user="' . $attackerNode->data['user'] . '" where id="' . $defenderNode->data['id'] . '"');
			}
		}
		
		// - - - - - nodeDefenderRemove
		if ($data['output']['attacker']['winner'] && $data['combat']['nodeDefenderRemove']) {
			if ($data['combat']['requiresWipeout'] == false || $data['combat']['requiresWipeout'] && $overkill) {
				$this->remove($defenderNode->data['id']);
			}
		}
		
		// - - - - - sabotageDefender
		if ($data['combat']['sabotageDefender']) {
			if (!$data['combat']['requiresScoutCheck'] || ($data['combat']['requiresScoutCheck'] && $data['output']['attacker']['scoutCheck'] > 0)) {
				$data = $this->doSabotage($data);
			}
		}
		
		if ($d13->dbAffectedRows() == - 1) {
			$ok = 0;
		}

		// - - - - - Attacker Report
		$this->attackerUser->getPreferences('name');
		if ($this->attackerUser->preferences['combatReports']) {
			$msg = new d13_message();
			$msg->data['sender'] = $this->attackerUser->data['name'];
			$msg->data['recipient'] = $this->attackerUser->data['name'];
			$msg->data['subject'] = $d13->getLangUI($data['combat']['string']) . ' ' . $d13->getLangUI("report") . ' vs ' . $this->defenderNode->data['name'];
			$msg->data['body'] = $this->assembleReport($data);
			$msg->data['type'] = 'attack';
			$msg->data['viewed'] = 0;
			$msg->add();
		}
		
		// - - - Ignore if Scout was successful
		if ($data['combat']['requiresScoutCheck'] && $data['output']['attacker']['scoutCheck'] <= 0) {

			// - - - - - Defender Report
			$this->defenderUser->getPreferences('name');
			if ($this->defenderUser->preferences['combatReports']) {
				$msg = new d13_message();
				$msg->data['sender'] = $this->defenderUser->data['name'];
				$msg->data['recipient'] = $this->defenderUser->data['name'];
				$msg->data['subject'] = $d13->getLangUI($data['combat']['string']) . ' ' . $d13->getLangUI("report") . ' vs ' . $this->attackerNode->data['name'];
				$msg->data['body'] = $this->assembleReport($data, true);
				$msg->data['type'] = 'defense';
				$msg->data['viewed'] = 0;
				$msg->add();
			}
		
		}
		
		return $data;
	
	}
	
	// ----------------------------------------------------------------------------------------
	// assembleRaidResources
	// ----------------------------------------------------------------------------------------
	private
	
	function assembleRaidResources($data)
	{
		
		global $d13;
	
		$html = '';
		$open = false;
		$limit = 7;
		$i = 0;
		
		if (isset($data['output']['attacker']['results']['raid_resources'])) {
			foreach ($data['output']['attacker']['results']['raid_resources'] as $result) {
					
				if ($i == 0) {
					$html .= '<div class="row">';
					$open = true;
				}
					
				$vars = array();
				$vars['tvar_listImage'] 	= CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $d13->getResource($result['resource'], 'image');
				$vars['tvar_listLabel'] 	= $d13->getLangGL('resources', $result['resource'], 'name');
				$vars['tvar_listAmount'] 	= '+'.$result['value'];
			
				$html .= $d13->templateSubpage("msg.combat.resource", $vars);
				
				if ($i == $limit) {
					$html_result .= '</div>';
					$i = -1;
					$open = false;
				}
				
				$i++;
			
			}
				
			if ($open) {
				$html .= '</div>';
			}
		}
		
		return $html;
	
	}
	
	// ----------------------------------------------------------------------------------------
	// assembleScoutResources
	// ----------------------------------------------------------------------------------------
	private
	
	function assembleScoutResources($data)
	{
		
		global $d13;
		
		$html = '';
		$open = false;
		$limit = 7;
		$i = 0;
		
		foreach ($data['output']['attacker']['results']['scout_resources'] as $result) {
			
			if ($i == 0) {
				$html .= '<div class="row">';
				$open = true;
			}
					
			$vars = array();
			$vars['tvar_listImage'] 	= CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $d13->getResource($result['resource'], 'image');
			$vars['tvar_listLabel'] 	= $d13->getLangGL('resources', $result['resource'], 'name');
			$vars['tvar_listAmount'] 	= floor($result['value']);
		
			$html .= $d13->templateSubpage("msg.combat.resource", $vars);
				
			if ($i == $limit) {
				$html .= '</div>';
				$i = -1;
				$open = false;
			}
				
			$i++;
			
		}
				
		if ($open) {
			$html .= '</div>';
		}
			
		return $html;
	
	}
	
	// ----------------------------------------------------------------------------------------
	// assembleScoutModules
	// ----------------------------------------------------------------------------------------
	private
	
	function assembleScoutModules($data)
	{
		
		global $d13;
		
		$html = '';
		$open = false;
		$limit = 7;
		$i = 0;
		
		foreach ($data['output']['attacker']['results']['scout_modules'] as $result) {
					
			if ($i == 0) {
				$html .= '<div class="row">';
				$open = true;
			}

			$tmp_module = $d13->getModule($this->defenderNode->data['faction'], $result['module']);
			$image = $tmp_module['images'][1]['image'];
			
			$vars = array();
			$vars['tvar_listImage'] 	= CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/modules/' . $this->defenderNode->data['faction'] . "/" . $image;
			$vars['tvar_listLabel'] 	= $d13->getLangGL('modules', $this->defenderNode->data['faction'], $result['module'], 'name');
			$vars['tvar_listAmount'] 	= 'L'.$result['level'];
		
			$html .= $d13->templateSubpage("msg.combat.resource", $vars);
				
			if ($i == $limit) {
				$html .= '</div>';
				$i = -1;
				$open = false;
			}
				
			$i++;
			
		}
				
		if ($open) {
			$html .= '</div>';
		}
			
		return $html;

	}

	// ----------------------------------------------------------------------------------------
	// assembleScoutTechnologies
	// ----------------------------------------------------------------------------------------
	private
	
	function assembleScoutTechnologies($data)
	{

		global $d13;
		
		$html = '';
		$open = false;
		$limit = 7;
		$i = 0;
		
		foreach ($data['output']['attacker']['results']['scout_technologies'] as $result) {
					
			if ($i == 0) {
				$html .= '<div class="row">';
				$open = true;
			}
			
			$tmp_technology = $d13->getTechnology($this->defenderNode->data['faction'], $result['technology']);
			$image = $tmp_technology['images'][0]['image'];

			$vars = array();
			$vars['tvar_listImage'] 	= CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/technologies/' . $this->defenderNode->data['faction'] . "/" . $image;
			$vars['tvar_listLabel'] 	= $d13->getLangGL('technologies', $this->defenderNode->data['faction'], $result['technology'], 'name');
			$vars['tvar_listAmount'] 	= 'L'.$result['level'];			
		
			$html .= $d13->templateSubpage("msg.combat.resource", $vars);
				
			if ($i == $limit) {
				$html .= '</div>';
				$i = -1;
				$open = false;
			}
				
			$i++;
			
		}
				
		if ($open) {
			$html .= '</div>';
		}
			
		return $html;

	}

	// ----------------------------------------------------------------------------------------
	// assembleScoutUnits
	// ----------------------------------------------------------------------------------------
	private
	
	function assembleScoutUnits($data)
	{
	
		global $d13;
	
		$html = '';
		$open = false;
		$limit = 7;
		$i = 0;
		
		foreach ($data['output']['attacker']['results']['scout_units'] as $result) {
					
			if ($i == 0) {
				$html .= '<div class="row">';
				$open = true;
			}
			
			$tmp_unit = $d13->getUnit($this->defenderNode->data['faction'], $result['unit']);
			$image = $tmp_unit['images'][0]['image'];

			$vars = array();
			$vars['tvar_listImage'] 	= CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->defenderNode->data['faction'] . "/" . $image;
			$vars['tvar_listLabel'] 	= $d13->getLangGL('units', $this->defenderNode->data['faction'], $result['unit'], 'name');
			$vars['tvar_listAmount'] 	= $result['value'];
		
			$html .= $d13->templateSubpage("msg.combat.resource", $vars);
				
			if ($i == $limit) {
				$html .= '</div>';
				$i = -1;
				$open = false;
			}
				
			$i++;
			
		}
				
		if ($open) {
			$html .= '</div>';
		}
			
		return $html;

	}
	
	// ----------------------------------------------------------------------------------------
	// assembleReport
	// ----------------------------------------------------------------------------------------
	private
	
	function assembleReport($data, $other=false)
	{
		
		global $d13;
		
		$html = '';
		$tvars = array();
		
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Report Header

		if (!$other) {
			if ($data['output']['attacker']['winner']) {
				$tvars['tvar_msgHeader'] = $d13->getLangUI($data['combat']['string']) . ' ' . $d13->getLangUI("won");
			}
			else {
				$tvars['tvar_msgHeader'] = $d13->getLangUI($data['combat']['string']) . ' ' . $d13->getLangUI("lost");
			}
		} else {
			if ($data['output']['defender']['winner']) {
				$tvars['tvar_msgHeader'] = $d13->getLangUI($data['combat']['string']) . ' ' . $d13->getLangUI("won");
			}
			else {
				$tvars['tvar_msgHeader'] = $d13->getLangUI($data['combat']['string']) . ' ' . $d13->getLangUI("lost");
			}
		}

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  Report Part Attacker
		$html = '';
		$totalAmount 	= 0;
		$leftAmount 	= 0;
		$totalSpeed 	= 0;
		$totalDamage 	= 0;
		$totalArmor 	= 0;
		$totalVision 	= 0;
		$totalStealth 	= 0;
		$totalCritical 	= 0;
		$totalCapacity 	= 0;
		$i = 0;
		$limit = 7;
		$open = false;
		
		// - - - - - 
		foreach($data['output']['attacker']['groups'] as $key => $group) {
			
			if ($data['input']['attacker']['groups'][$key]['quantity']) {
				
				$totalAmount 	+= $data['input']['attacker']['groups'][$key]['quantity'];
				$leftAmount		+= $group['quantity'];
				
				$totalSpeed 	+= $data['input']['attacker']['groups'][$key]['speed'];
				$totalArmor 	+= $data['input']['attacker']['groups'][$key]['armor'];
				$totalVision	+= $data['input']['attacker']['groups'][$key]['vision'];
				$totalStealth 	+= $data['input']['attacker']['groups'][$key]['stealth'];
				$totalDamage 	+= $data['input']['attacker']['groups'][$key]['damage'];
				$totalCritical 	+= $data['input']['attacker']['groups'][$key]['critdmg'];
				$totalCapacity 	+= $data['input']['attacker']['groups'][$key]['capacity'];
				
				$args = array();
				$args['supertype'] 	= 'unit';
				$args['obj_id'] 	= $group['unitId'];
				$args['node'] 		= $this->attackerNode;
				
				$unit = new d13_object_unit($args);
				
				$name 			= $unit->data['name'];
				$label 			= $group['quantity']. "/" .$data['input']['attacker']['groups'][$key]['quantity'];
				$image 			= CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->attackerNode->data['faction'] . '/' . $unit->data['image'];
				
				$vars = array();
				$vars['tvar_listImage'] = $image;
				$vars['tvar_listLabel'] = $name;
				$vars['tvar_listAmount'] = $label;
				
				if ($i == 0) {
					$html .= '<div class="row">';
					$open = true;
				}
		
				$html .= $d13->templateSubpage("msg.combat.entry", $vars);
				
				if ($i == $limit) {
					$html .= '</div>';
					$open = false;
				}
				
				$i++;
				
			}
		}
		
		if ($open) {
			$html .= '</div>';
		}
		
		if (empty($html)) {
			$html = '<div class="row"><div class="col-auto">' . $d13->getLangUI('none') . '</div></div>';
		}
		
		
		// - - - - - Army Stats
		
		$vars = array();
		$vars['tvar_totalAmount'] 	= $totalAmount;
		$vars['tvar_leftAmount'] 	= $leftAmount;
		$vars['tvar_totalSpeed'] 	= $totalSpeed;
		$vars['tvar_totalDamage'] 	= $totalDamage;
		$vars['tvar_totalArmor'] 	= $totalArmor;
		$vars['tvar_totalVision'] 	= $totalVision;
		$vars['tvar_totalStealth'] 	= $totalStealth;
		$vars['tvar_totalCritical']	= $totalCritical;
		$vars['tvar_totalCapacity'] = $totalCapacity;
		
		if (!$other) {
			$tvars['tvar_msgSelfStats'] 	= $d13->templateSubpage("msg.combat.attackerstats", $vars);
			$tvars['tvar_msgSelfRowName'] 	= $this->attackerNode->data['name']."'s " . $d13->getLangUI('army');
			$tvars['tvar_msgSelfRow'] 		= $html;
		} else {
			$tvars['tvar_msgOtherStats'] 	= $d13->templateSubpage("msg.combat.attackerstats", $vars);
			$tvars['tvar_msgOtherRowName'] 	= $this->attackerNode->data['name']."'s " . $d13->getLangUI('army');
			$tvars['tvar_msgOtherRow'] 		= $html;
		}

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Report Part Defender
		$html = '';
		$totalAmount 	= 0;
		$leftAmount 	= 0;
		$totalSpeed 	= 0;
		$totalDamage 	= 0;
		$totalArmor 	= 0;
		$totalVision 	= 0;
		$totalStealth 	= 0;
		$totalCritical 	= 0;
		$i = 0;
		$open = false;
		
		// - - - Ignore if Scout was successful
		if (!$data['combat']['requiresScoutCheck'] || ($data['combat']['requiresScoutCheck'] && $data['output']['attacker']['scoutCheck'] <= 0)) {
		
			// - - - - - 
			foreach($data['output']['defender']['groups'] as $key => $group) {

			if ($group['type'] == 'unit') {
				// - - - - - Unit
				if ($data['input']['defender']['groups'][$key]['quantity']) {

					$totalAmount 	+= $data['input']['defender']['groups'][$key]['quantity'];
					$leftAmount		+= $group['quantity'];
					
					$totalSpeed 	+= $data['input']['defender']['groups'][$key]['speed'];
					$totalArmor 	+= $data['input']['defender']['groups'][$key]['armor'];
					$totalVision	+= $data['input']['defender']['groups'][$key]['vision'];
					$totalStealth 	+= $data['input']['defender']['groups'][$key]['stealth'];
					$totalDamage 	+= $data['input']['defender']['groups'][$key]['damage'];
					$totalCritical 	+= $data['input']['defender']['groups'][$key]['critdmg'];
					$totalCapacity 	+= $data['input']['defender']['groups'][$key]['capacity'];
					
					$args = array();
					$args['supertype'] 	= 'unit';
					$args['obj_id'] 	= $group['unitId'];
					$args['node'] 		= $this->defenderNode;
				
					$unit = new d13_object_unit($args);
					
					$name 			= $unit->data['name'];
					$label 			= $group['quantity']. "/" .$data['input']['defender']['groups'][$key]['quantity'];
					$image 			= CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->defenderNode->data['faction'] . '/' . $unit->data['image'];
					
					$vars = array();
					$vars['tvar_listImage'] = $image;
					$vars['tvar_listLabel'] = $name;
					$vars['tvar_listAmount'] = $label;
					
					if ($i == 0) {
						$html .= '<div class="row">';
						$open = true;
					}
					
					$html .= $d13->templateSubpage("msg.combat.entry", $vars);
					
					if ($i == $limit) {
						$html .= '</div>';
						$open = false;
					}
					
					$i++;
				
				}

			} else if ($group['type'] == 'module') {
				// - - - - - Module
				if ($data['input']['defender']['groups'][$key]['input']) {

					$totalAmount 	+= $data['input']['defender']['groups'][$key]['input'];
					$leftAmount		+= $group['input'];
					
					$totalSpeed 	+= $data['input']['defender']['groups'][$key]['speed'];
					$totalArmor 	+= $data['input']['defender']['groups'][$key]['armor'];
					$totalVision	+= $data['input']['defender']['groups'][$key]['vision'];
					$totalStealth 	+= $data['input']['defender']['groups'][$key]['stealth'];
					$totalDamage 	+= $data['input']['defender']['groups'][$key]['damage'];
					$totalCritical 	+= $data['input']['defender']['groups'][$key]['critdmg'];
					
					$args = array();
					$args['supertype'] 	= 'turret';
					$args['obj_id'] 	= $group['unitId'];
					$args['node'] 		= $this->defenderNode;
				
					$unit = new d13_object_unit($args);
					
					$name 			= $unit->data['name'];
					$label 			= $group['quantity']. "/" .$data['input']['defender']['groups'][$key]['quantity'];
					$image 			= CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->defenderNode->data['faction'] . '/' . $unit->data['image'];
							
					$vars = array();
					$vars['tvar_listImage'] = $image;
					$vars['tvar_listLabel'] = $name;
					$vars['tvar_listAmount'] = $label;
					
					if ($i == 0) {
						$html .= '<div class="row">';
						$open = true;
					}
					
					$html .= $d13->templateSubpage("msg.combat.entry", $vars);
					
					if ($i == $limit) {
						$html .= '</div>';
						$open = false;
					}
					
					$i++;
				
				}
			}
		}
		
			if ($open) {
				$html .= '</div>';
			}

			if (empty($html)) {
				$html = '<div class="row"><div class="col-auto">' . $d13->getLangUI('none') . '</div></div>';
			}
		
		}
		// - - - - - Army Stats
		$vars = array();
		$vars['tvar_totalAmount'] 	= $totalAmount;
		$vars['tvar_leftAmount'] 	= $leftAmount;
		$vars['tvar_totalSpeed'] 	= $totalSpeed;
		$vars['tvar_totalDamage'] 	= $totalDamage;
		$vars['tvar_totalArmor'] 	= $totalArmor;
		$vars['tvar_totalVision'] 	= $totalVision;
		$vars['tvar_totalStealth'] 	= $totalStealth;
		$vars['tvar_totalCritical'] = $totalCritical;
		$vars['tvar_totalCapacity'] = $totalCapacity;
		
		if (!$other) {
			$tvars['tvar_msgOtherStats'] 	= $d13->templateSubpage("msg.combat.defenderstats", $vars);
			$tvars['tvar_msgOtherRowName'] 	= $this->defenderNode->data['name']."'s " . $d13->getLangUI('army');
			$tvars['tvar_msgOtherRow'] 		= $html;
		} else {
			$tvars['tvar_msgSelfStats'] 	= $d13->templateSubpage("msg.combat.defenderstats", $vars);
			$tvars['tvar_msgSelfRowName'] 	= $this->defenderNode->data['name']."'s " . $d13->getLangUI('army');
			$tvars['tvar_msgSelfRow'] 		= $html;
		}

		// - - - - Report Results etc.
		
		$tvars['tvar_msgSelfResRowName'] = $d13->getLangUI("combat") . ' ' . $d13->getLangUI("results");
		$tvars['tvar_msgSelfResRow'] = '';
		$html = '';
		$i = 0;
		
		if (!$other) {
			
			// - - - - - Trophies & Experience
			$this->attackerUser = new d13_user();
			$status = $this->attackerUser->get('id', $this->attackerNode->data['user']);
			if ($status == 'done') {
				$html = '';
				$html .= '<div class="row">';
			
				foreach($d13->getGeneral("userstats") as $stat) {
					if ($stat['active'] && isset($data['output']['attacker']['userstat'][$stat['name']])) {
						$vars = array();
						$vars['tvar_listImage'] 	= CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $stat['image'];
						$vars['tvar_listLabel'] 	= $d13->getLangUI($stat['name']);
						$vars['tvar_listAmount'] 	= $data['output']['attacker']['userstat'][$stat['name']];
						$html .= $d13->templateSubpage("msg.combat.resource", $vars);
					}
				}
				
				$html .= '</div>';
				
			}

			// - - - - - Raid Results
			$html .= $this->assembleRaidResources($data);
			
			// - - - - - Scout Results
			if ($data['combat']['requiresScoutCheck'] && ($data['combat']['requiresScoutCheck'] && $data['output']['attacker']['scoutCheck'] > 0)) {
			
				$html_scout_resources 		= $this->assembleScoutResources($data);
				$html_scout_technologies 	= $this->assembleScoutTechnologies($data);
				$html_scout_modules 	 	= $this->assembleScoutModules($data);
				$html_scout_units	 		= $this->assembleScoutUnits($data);
			
			}	

		} else {
			
			// - - - - - Trophies & Experience
			$this->defenderUser = new d13_user();
			$status = $this->defenderUser->get('id', $this->defenderNode->data['user']);
			if ($status == 'done') {
				$html = '';
				$html .= '<div class="row">';
			
				foreach($d13->getGeneral("userstats") as $stat) {
					if ($stat['active'] && isset($data['output']['defender']['userstat'][$stat['name']])) {
						$vars = array();
						$vars['tvar_listImage'] 	= CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $stat['image'];
						$vars['tvar_listLabel'] 	= $d13->getLangUI($stat['name']);
						$vars['tvar_listAmount'] 	= $data['output']['defender']['userstat'][$stat['name']];
						$html .= $d13->templateSubpage("msg.combat.resource", $vars);
					}
				}
				
				$html .= '</div>';
				
			}
			
			// - - - - - Results (Resources, Scout, Sabotage etc.)
			foreach ($data['output']['defender']['results']['raid_resources'] as $result) {
								
				$vars = array();
				$vars['tvar_listImage'] = CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $d13->getResource($result['resource'], 'image');
				$vars['tvar_listLabel'] = $d13->getLangGL('resources', $result['resource'], 'name');
				$vars['tvar_listAmount'] = '-'.$result['value'];
			
				if ($i == 0) {
					$html .= '<div class="row">';
					$open = true;
				}
	
				$html .= $d13->templateSubpage("msg.combat.resource", $vars);
			
				if ($i == $limit) {
					$html .= '</div>';
					$i = 0;
					$open = false;
				}
			
				$i++;
			
			}
			
			if ($open) {
				$html .= '</div>';
			}
	
		}
		
		// - - - - - Return Report
		
		// - - - Scout Template if Scout was succesfull
		if (!$data['combat']['requiresScoutCheck'] || ($data['combat']['requiresScoutCheck'] && $data['output']['attacker']['scoutCheck'] <= 0)) {
			$tvars['tvar_msgSelfResRow'] = $html;
			$html = $d13->templateSubpage("msg.combat", $tvars);
		// - - - Combat Template otherwise
		} else {
			$tvars['tvar_msgOtherRowName1'] = $d13->getLangUI("resource") . " " .$d13->getLangUI("report");
			$tvars['tvar_msgOtherRow1'] 	= $html_scout_resources;
			$tvars['tvar_msgOtherRowName2'] = $d13->getLangUI("module") . " " .$d13->getLangUI("report");
			$tvars['tvar_msgOtherRow2'] 	= $html_scout_modules;
			$tvars['tvar_msgOtherRowName3'] = $d13->getLangUI("technology") . " " .$d13->getLangUI("report");
			$tvars['tvar_msgOtherRow3'] 	= $html_scout_technologies;
			$tvars['tvar_msgOtherRowName4'] = $d13->getLangUI("unit") . " " .$d13->getLangUI("report");
			$tvars['tvar_msgOtherRow4'] 	= $html_scout_units;
			$html = $d13->templateSubpage("msg.combat.scout", $tvars);
		}

		return $html;
	}

}

// =====================================================================================EOF