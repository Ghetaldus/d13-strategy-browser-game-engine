<script type="text/javascript">
   var timerIds=new Array();
</script>

<div class="d13-node" ><div class="card no-border large-card card-shadow">

	<div class="card-header no-border">
  		<div class="d13-heading">{{tvar_ui_subject}}: {{tvar_subject}}</div>
  		<a class="external" href="?p=node&action=list"><img class="d13-icon hvr-pulse" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
	</div>
	
	<div class="card-content">
    	
		{{tvar_body}}
		
	</div>
  
  	<div class="card-footer no-border">
		<div class="left"><a class="external" href="?p=message&action=add&messageId={{tvar_id}}">{{tvar_ui_reply}}</a></div>
		<div class="right">{{tvar_ui_from}} {{tvar_senderName}}</div>
	</div>
  
  
</div>
</div>