<script type="text/javascript">
   var timerIds=new Array();
</script>

<div class="d13-node" >
	<div class="card no-border large-card card-shadow">
		<form class="pure-form" method="post" action="?p=message&action=remove" id="messageList">
		
		<div class="card-header no-border">
			<div class="d13-heading">{{tvar_ui_messages}} {{tvar_ui_list}}</div>
			<a class="external" href="?p=node&action=list"><img class="d13-icon hvr-pulse" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
		</div>
  
		<div class="card-content">
			<div class="card-content-inner">
				<div class="list-block">
					<ul>
					{{tvar_messages}}
					</ul>
				</div>
			</div>
		</div>

		<div class="card-footer no-border">
			
			<div class="left">
			<div class="buttons-row">
				{{tvar_remove}}
				{{tvar_removeAll}}
				</form>
				<a class="button external" href="?p=message&action=add">{{tvar_ui_send}}</a>
			
			
			<form class="pure-form" method="post" action="?p=message&action=list">
				{{tvar_filterSelect}}
			</form>
			</div>
				</div>
				
			
			
			<div class="right">
				<div class="buttons-row">
				{{tvar_controls}}
				</div>
			</div>
			
		</div>
	
	</div>
	

</div>