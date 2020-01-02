<div class="d13-node" >
<div class="card no-border large-card card-shadow">

  <div class="card-header no-border">
  	{{tvar_ui_account}}
  	<a class="external" href="?p=node&action=list"><img class="d13-icon hvr-pulse" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
  </div>
  
  <div class="card-content">
    <div class="card-content-inner">

    <div class="buttons-row">
      <a href="#tab1" class="tab-link active button">{{tvar_ui_misc}}</a>
      <a href="#tab2" class="tab-link button">{{tvar_ui_preferences}}</a>
      <a href="#tab3" class="tab-link button">{{tvar_ui_blocklist}}</a>
      <a href="#tab4" class="tab-link button">{{tvar_ui_password}}</a>
      <a href="#tab5" class="tab-link button">{{tvar_ui_remove}}</a>
    </div>
    


<div class="tabs-animated-wrap">
<div class="tabs">

<div id="tab1" class="tab active">
  <div class="content-block">
	<p>
	
	<form method="post" action="?p=account&action=misc">
     <div><div class="cell">{{tvar_ui_email}}</div><div class="cell"><input class="textbox" type="text" name="email" maxlength="32" value="{{tvar_user_email}}"></div></div>
     <div><div class="cell">{{tvar_ui_sitter}}</div><div class="cell"><input class="textbox" type="text" name="sitter" maxlength="32" value="{{tvar_user_sitter}}"></div></div>
     <div><div class="cell">{{tvar_ui_locale}}</div><div class="cell"><select class="dropdown" type="text" name="locale">{{tvar_locales}}</select></div></div>
     <div><div class="cell">{{tvar_ui_template}}</div><div class="cell"><select class="dropdown" type="text" name="template">{{tvar_templates}}</select></div></div>
     <div><div class="cell">{{tvar_ui_color}}</div><div class="cell"><select class="dropdown" type="text" name="color">{{tvar_colors}}</select></div></div>
     <div><div class="cell">{{tvar_ui_password}}</div><div class="cell"><input class="textbox" type="password" name="password"></div></div>
     <div><div class="cell"><input class="button" type="submit" value="{{tvar_ui_edit}}"></div></div>
    </form>
	
	</p>
  </div>
</div>

<div id="tab2" class="tab">
  <div class="content-block">
	<p>
	
	<form method="post" action="?p=account&action=preferences">
     <div><div class="cell">{{tvar_ui_name}}</div><div class="cell"><select class="dropdown" name="name" id="preferenceName" onChange="changePreference()">{{tvar_preferenceNames}}</select></div></div>
     <div><div class="cell">{{tvar_ui_value}}</div><div class="cell"><input class="textbox" type="text" name="value" id="preferenceValue" maxlength="64" value="{{tvar_user_preferences}}"></div></div>
     <div><div class="cell">{{tvar_ui_password}}</div><div class="cell"><input class="textbox" type="password" name="password"></div></div>
     <div><div class="cell"><input class="button" type="submit" value="{{tvar_ui_edit}}"></div></div>
    </form>
	
	</p>
  </div>
</div>

<div id="tab3" class="tab">
  <div class="content-block">
	<p>
	
	<form method="post" action="?p=account&action=blocklist">
     <div><div class="cell">{{tvar_ui_username}}</div><div class="cell"><input class="textbox" type="text" name="name"></div></div>
     <div><div class="cell">{{tvar_ui_password}}</div><div class="cell"><input class="textbox" type="password" name="password"></div></div>
     <div><div class="cell"><input class="button" type="submit" value="{{tvar_ui_go}}"></div></div>
    </form>
	
	</p>
  </div>
</div>

<div id="tab4" class="tab">
  <div class="content-block">
	<p>
	
	<form method="post" action="?p=account&action=password">
     <div><div class="cell">{{tvar_ui_oldPassword}}</div><div class="cell"><input class="textbox" type="password" name="password"></div></div>
     <div><div class="cell">{{tvar_ui_newPassword}}</div><div class="cell"><input class="textbox" type="password" name="newPassword" id="newPassword" onChange="check("newPassword")"></div></div>
     <div><div class="cell">{{tvar_ui_retypePassword}}</div><div class="cell"><input class="textbox" type="password" name="rePassword"></div></div>
     <div><div class="cell"><input class="button" type="submit" value="{{tvar_ui_edit}}"></div></div>
    </form>
	
	</p>
  </div>
</div>

<div id="tab5" class="tab">
  <div class="content-block">
	<p>
	
	 <form method="post" action="?p=account&action=remove">
     <div><div class="cell">{{tvar_ui_password}}</div><div class="cell"><input class="textbox" type="password" name="password"></div></div>
     <div><div class="cell"><input class="button" type="submit" value="{{tvar_ui_edit}}"></div></div>
    </form>
	
	</p>
  </div>
</div>

</div>
</div>

</div>

</div>
</div>