<script type="text/javascript">
	var timerIds=new Array();
</script>

<div class="d13-node" >

	<div class="card no-border large-card card-shadow">

		<div class="card-header no-border">
			<div class="d13-heading">{{tvar_name}} {{tvar_levelLabel}}</div>
			<a class="external" href="?p=node&action=get&nodeId={{tvar_nodeID}}"><img class="d13-icon hvr-pulse" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
		</div>

		<div class="card-content">
	<div class="card-content-inner">

		<div class="row">
	
			<div class="col-40">
				<div class="d13-module-detail" style="background-image: url('{{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/moduleBackground.png'); background-repeat: repeat;">
				<img class="d13-module-image" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/{{tvar_image}}">
				</div>
				<p class="d13-italic"> {{tvar_moduleDescription}}</p>
				{{tvar_linkData}}
			</div>
			
			
			
			<div class="col-60">
			
				<div class="list-block no-hairlines-between">
				<ul>
					
				<li class="item-content">
					<div class="item-media"><img class="d13-icon" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_moduleInput}}.png" title="{{tvar_moduleInputName}}"></div>
					<div class="item-inner">
					<div class="item-title">{{tvar_moduleInputName}}</div>
				
					<div class="item-after">{{tvar_inputSlider}}</div>
				
					</div>
				</li>	
				

				{{tvar_queue}}

				</ul>
				</div>
			</div>
		</div>

	</div>
	</div>

		<div class="card-footer no-border">
			<div class="left">{{tvar_demolishLink}}</div><div class="right">{{tvar_inventoryLink}}</div>
		</div>

	</div>

</div>