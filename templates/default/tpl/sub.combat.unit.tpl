<div class="swiper-slide swiper-slide-list">
	
	<div class="card no-border tiny-card">
	
    	<div class="card-header no-border">
    		<div class="left">{{tvar_unitName}} ({{tvar_unitAmount}})</div>
    		<div class="right"><a href="#" class="open-popup" data-popup=".popup-unit-{{tvar_unitId}}"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/information.png"></a></div>
    	</div>
    	
    	<div class="card-content">
       		<div class="card-content-inner">
       			<img class="d13-unit" src="templates/{{tvar_global_template}}/images/units/{{tvar_nodeFaction}}/{{tvar_unitImage}}" title="{{tvar_unitName}}">
    		</div>
    	</div>
    	
    	<div class="card-footer no-border">
			<div>
				<input type="range" name="attackerGroups[]" id="quantity{{tvar_unitId}}" min="0" max="{{tvar_unitAmount}}" value="0" step="1" onMouseDown="mySwiper.lockSwipes()" onMouseUp="mySwiper.unlockSwipes()" ondblclick="showInputMax('quantity{{tvar_unitId}}', 'sliderRange{{tvar_unitId}}')" onInput="armyValue('sliderRange{{tvar_unitId}}', this.value)" onChange="armyValue('sliderRange{{tvar_unitId}}', this.value, {{tvar_unitId}})">
				<input type="number" size="3" maxlength="3" class="pure-input" min="0" max="{{tvar_unitAmount}}" id="sliderRange{{tvar_unitId}}" value="0" onInput="showInputValue('quantity{{tvar_unitId}}', this.value)" onChange="showInputValue('quantity{{tvar_unitId}}', this.value)">
			</div>

			<input type="hidden" name="attackerGroupUnitIds[]" value="{{tvar_unitId}}">
			<input type="hidden" id="attackerAmount_sliderRange{{tvar_unitId}}" name="unitAmount[]" value="0">
			<input type="hidden" name="unitFuel[]" value="{{tvar_unitFuel}}">
			<input type="hidden" name="unitType[]" value="{{tvar_unitType}}">
			<input type="hidden" name="unitUnique[]" value="{{tvar_unitUnique}}">
			
			<input type="hidden" name="unitDamage[]" value="{{tvar_unitDamage}}">
			<input type="hidden" name="unitSpeed[]" value="{{tvar_unitSpeed}}">
			<input type="hidden" name="unitStealth[]" value="{{tvar_unitStealth}}">
			<input type="hidden" name="unitArmor[]" value="{{tvar_unitArmor}}">
			<input type="hidden" name="unitHP[]" value="{{tvar_unitHP}}">
			<input type="hidden" name="unitCritical[]" value="{{tvar_unitCritical}}">
			<input type="hidden" name="unitCapacity[]" value="{{tvar_unitCapacity}}">

			<input type="hidden" name="armyModDamage[]" value="{{tvar_armyModDamage}}">
			<input type="hidden" name="armyModSpeed[]" value="{{tvar_armyModSpeed}}">
			<input type="hidden" name="armyModStealth[]" value="{{tvar_armyModStealth}}">
			<input type="hidden" name="armyModArmor[]" value="{{tvar_armyModArmor}}">
			<input type="hidden" name="armyModHP[]" value="{{tvar_armyModhp}}">
			<input type="hidden" name="armyModCritical[]" value="{{tvar_armyModCritical}}">
			<input type="hidden" name="armyModCapacity[]" value="{{tvar_armyModCapacity}}">
			
    	</div>
	</div> 
	
</div>  