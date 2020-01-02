<div class="d13-node" >
<div class="card no-border large-card card-shadow">

  <div class="card-header no-border">
  	{{tvar_ui_alliance}}
  	<a class="external" href="?p=node&action=list"><img class="d13-icon hvr-pulse" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
  </div>
  
  <div class="card-content">
    <div class="card-content-inner">
    
    
    </div>
  </div>
  
</div>
</div>

{{tvar_tpl_allianceMenu}}

<div>
	<form method="post" class="pure-form" action="?p=alliance&action=addInvitation">
	<label>{{tvar_ui_username}}</label>
	<input class="textbox" type="text" name="name" value="">
	<input class="button" type="submit" value="{{tvar_ui_invite}}">
	</form>
</div>