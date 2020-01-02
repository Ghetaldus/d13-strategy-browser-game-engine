<div class="swiper-slide">
	<div>
	<form method="post" action="index.php?p=node&action=random">
    	<p class="d13-bold">{{tvar_factionName}}</p>
    	<p class="d13-italic">{{tvar_factionText}}</p>
    	<input type="image" class="d13-module-image" name="submit" src="templates/{{tvar_global_template}}/images/gui/{{tvar_factionID}}.png">
    	
    	
    	<input type="hidden" name="faction" id="faction" value="{{tvar_factionID}}">
    	
    </form>
    </div>
</div>  