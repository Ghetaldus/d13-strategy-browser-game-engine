<div class="d13-node" >
	<div class="card no-border large-card card-shadow">
	
	<form class="pure-form" method="post" id="combatForm" action="?p=combat&action=add&nodeId={{tvar_nodeID}}&type={{tvar_combatType}}&slotId={{tvar_slotID}}" id="combatForm">
			
  		<div class="card-header no-border">
  			<div class="left">{{tvar_ui_combat}}: {{tvar_combatType}}</div>
  			<div class="right"><a class="external" href="?p=node&action=get&nodeId={{tvar_nodeID}}"><img class="d13-icon hvr-pulse" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a></div>
  		</div>
  
  		<div class="card-content">
    		
				<div class="swiper-container">
					<div class="swiper-wrapper">
						 {{tvar_unitsHTML}}
					</div>
					<div class="swiper-button-prev"></div>
					<div class="swiper-button-next"></div>
					<div class="swiper-pagination"></div>
					<div class="swiper-scrollbar"></div>
				</div>
				
				<input type="hidden" id="leaderRequired" value="{{tvar_leader}}">
				<input type="hidden" name="type" value="{{tvar_type}}">
				<input type="hidden" name="fuelFactor" id="fuelFactor" value="{{tvar_fuelfactor}}">
				<input type="hidden" name="fuelResource" id="fuelResource" value="{{tvar_fuelResource}}">
				{{tvar_resources}}
		</div>
		
		<div class="card-footer no-border">
  			
			<div class="content-block">
				
				
  				<span class="badge">
					<span id="totalAmount">0</span>
				</span>
		
				<span class="badge" data-tooltip="{{tvar_ui_army}} {{tvar_ui_speed}}">
					<img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_speed.png"><span id="totalSpeed">0</span>
				</span>
		
				<span class="badge" data-tooltip="{{tvar_ui_army}} {{tvar_ui_damage}}">
					<img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_damage.png"><span id="totalDamage">0</span>
				</span>
		
				<span class="badge" data-tooltip="{{tvar_ui_army}} {{tvar_ui_critical}}">
					<img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_critical.png"><span id="totalCritical">0</span>
				</span>
		
				<span class="badge" data-tooltip="{{tvar_ui_army}} {{tvar_ui_armor}}">
					<img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_armor.png"><span id="totalArmor">0</span>
				</span>
		
				<span class="badge" data-tooltip="{{tvar_ui_army}} {{tvar_ui_hp}}">
					<img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_hp.png"><span id="totalHealth">0</span>
				</span>
		
				<span class="badge" data-tooltip="{{tvar_ui_army}} {{tvar_ui_stealth}}">
					<img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_stealth.png"><span id="totalStealth">0</span>
				</span>
				
				<span class="badge" data-tooltip="{{tvar_ui_army}} {{tvar_ui_capacity}}">
					<img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_capacity.png"><span id="totalCapacity">0</span>
				</span>
				
  			</div>
  			
  			<div class="content-block">
  				
  				<select name="id">{{tvar_nodeList}}</select> {{tvar_costData}} {{tvar_leaderRequired}} {{tvar_wipeoutRequired}} <input id="startCombat" class="pure-button" type="submit" value="{{tvar_combatType}}!" disabled>
				
			</div>
			
  		</div>

	</form>

	</div>
</div>