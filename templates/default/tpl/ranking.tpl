<script type="text/javascript">
   var timerIds=new Array();
</script>

<div class="d13-node" >
<div class="card no-border large-card card-shadow">

	<div class="card-header no-border">
  		<div class="d13-heading">{{tvar_ui_user}} {{tvar_ui_ranking}}</div>
  		<a class="external" href="?p=node&action=list"><img class="d13-icon hvr-pulse" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
	</div>
  
  
	<div class="card-content">
		<div class="card-content-inner">

		<div class="list-block no-hairlines-between">
			<ul>

				{{tvar_userRankings}}
	
			</ul>
			
		</div>

	</div>
	</div>

	<div class="card-footer no-border">
		<div class="left"></div>
		
		<div class="buttons-row">
			{{tvar_controls}}
		</div>
	</div>
	
</div>