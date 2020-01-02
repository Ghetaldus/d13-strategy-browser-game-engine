<div class="right {{tvar_sliderTooltip}}">
<form class="pure-form" method="post" action="{{tvar_formAction}}">
	
		<input type="image" class="d13-icon hvr-pulse" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/submit.png" type="submit" value="{{tvar_ui_set}}" {{tvar_disableData}}>
		<input type="range" name="input" id="input{{tvar_sliderID}}" min="{{tvar_sliderMin}}" max="{{tvar_sliderMax}}" value="{{tvar_sliderValue}}" step="1" onMouseDown="mySwiper.lockSwipes()" onMouseUp="mySwiper.unlockSwipes()" onInput="showInputValue('sliderRange{{tvar_sliderID}}', this.value)" ondblclick="showInputMax('input{{tvar_sliderID}}', 'sliderRange{{tvar_sliderID}}')" onChange="showInputValue('sliderRange{{tvar_sliderID}}', this.value)">
		<input type="number" size="3" maxlength="3" class="pure-input" min="{{tvar_sliderMin}}" max="{{tvar_sliderMax}}" id="sliderRange{{tvar_sliderID}}" value="{{tvar_sliderValue}}" onInput="showInputValue('input{{tvar_sliderID}}', this.value)" onChange="showInputValue('input{{tvar_sliderID}}', this.value)">

</form>
</div>