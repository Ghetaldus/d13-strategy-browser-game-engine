<script type="text/javascript">
   var timerIds=new Array();
</script>

<script src="{{tvar_global_directory}}/plugins/ckeditor/ckeditor.js"></script>

<div class="d13-node" >
<div class="card no-border large-card card-shadow">

	<form method="post" class="pure-form" action="?p=message&action=add">

	<div class="card-header no-border">
  		<div class="d13-heading">{{tvar_ui_messages}} {{tvar_ui_send}}</div>
  		<a class="external" href="?p=node&action=list"><img class="d13-icon hvr-pulse" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
	</div>
	
	<div class="card-content">
    	<div class="card-content-inner">
		
				
				
				<div class="cell">{{tvar_ui_recipient}}: <input class="textbox" placeholder="{{tvar_ui_recipient}}" type="text" name="recipient" value="{{tvar_recipient}}" style="width: 200px;"></div><div class="cell"></div>
				<div class="cell">{{tvar_ui_subject}}: <input class="textbox" placeholder="{{tvar_ui_subject}}" type="text" name="subject" value="{{tvar_subject}}" style="width: 200px;"></div><div class="cell"></div>
			
				<textarea class="textbox" name="msgbody">{{tvar_body}}</textarea>
				<script>
                CKEDITOR.replace( 'msgbody' );
            	</script>
				
			
		
		</div>
	</div>
  
  	<div class="card-footer no-border">
		<div class="right"><input class="button" type="submit" value="{{tvar_ui_send}}"></div>
	</div>
  	
  	</form>
  
</div>
</div>