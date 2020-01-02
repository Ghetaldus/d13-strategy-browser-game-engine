 <div class="container">
  <div class="content">

   <div style="display: inline-block;">
    <div>
     <div class="cell">{{tvar_ui_edit}}</div>
     <div class="cell">
      <select class="dropdown" id="action" onChange="selectAction()">
       <option value="vars">{{tvar_ui_vars}}</option>
       <option value="bans">{{tvar_ui_bans}}</option>
       <option value="accounts">{{tvar_ui_accounts}}</option>
       <option value="username">{{tvar_ui_username}}</option>
       <option value="blacklist">{{tvar_ui_blacklist}}</option>
      </select>
     </div>
    </div>
   </div>
   <div id="vars">
    <form method="post" action="?p=admin&action=vars">
     <div><div class="cell">{{tvar_ui_name}}</div><div class="cell"><select class="dropdown" name="name" id="varName" onChange="selectFlag()">{{tvar_flagNames}}</select></div></div>
     <div><div class="cell">{{tvar_ui_value}}</div><div class="cell"><input class="textbox" type="text" name="value" id="varValue" maxlength="64" value="{{tvar_flagsValue}}"></div></div>
     <div><div class="cell">{{tvar_ui_password}}</div><div class="cell"><input class="textbox" type="password" name="password"></div></div>
     <div><div class="cell"><input class="button" type="submit" value="{{tvar_ui_edit}}"></div></div>
    </form>
   </div>
   <div id="bans">
    <form method="post" action="?p=admin&action=bans">
     <div><div class="cell">{{tvar_ui_name}}</div><div class="cell"><input type="text" class="textbox" name="name"></div></div>
     <div><div class="cell">{{tvar_ui_level}}</div><div class="cell"><input class="textbox numeric" type="text" name="level" maxlength="2" size="1"></div></div>
     <div><div class="cell">{{tvar_ui_password}}</div><div class="cell"><input class="textbox" type="password" name="password"></div></div>
     <div><div class="cell"><input class="button" type="submit" value="{{tvar_ui_edit}}"></div></div>
    </form>
   </div>
   <div id="accounts">
    <form method="post" action="?p=admin&action=accounts">
     <div><div class="cell">{{tvar_ui_maxIdleTime}}</div><div class="cell"><input class="textbox numeric" type="text" name="maxIdleTime" size="1"></div></div>
     <div><div class="cell">{{tvar_ui_password}}</div><div class="cell"><input class="textbox" type="password" name="password"></div></div>
     <div><div class="cell"><input class="button" type="submit" value="{{tvar_ui_remove}}"></div></div>
    </form>
   </div>
   <div id="username">
    <form method="post" action="?p=admin&action=username">
     <div><div class="cell">{{tvar_ui_username}}</div><div class="cell"><input class="textbox" type="text" name="name"></div></div>
     <div><div class="cell">{{tvar_ui_password}}</div><div class="cell"><input class="textbox" type="password" name="password"></div></div>
     <div><div class="cell"><input class="button" type="submit" value="{{tvar_ui_get}}"></div></div>
    </form>
   </div>
   <div id="blacklist">
    {{tvar_ui_action}} <select class="dropdown" id="blacklistAction" onChange="selectBlacklistAction()"><option value="add">{{tvar_ui_add}}</option><option value="remove">{{tvar_ui_remove}}</option></select>
    <form method="post" action="?p=admin&action=blacklist&blacklistAction=add" id="blacklistAdd">
     <div><div class="cell">{{tvar_ui_type}}</div><div class="cell"><select class="dropdown" name="type" id="blacklist_add_type"><option value="ip">{{tvar_ui_ip}}</option><option value="email">{{tvar_ui_email}}</option></select></div></div>
     <div><div class="cell">{{tvar_ui_value}}</div><div class="cell"><input class="textbox" type="text" name="value"></div></div>
     <div><div class="cell">{{tvar_ui_password}}</div><div class="cell"><input class="textbox" type="password" name="password"></div></div>
     <div><div class="cell"><input class="button" type="submit" value="{{tvar_ui_add}}"></div></div>
    </form>
    <form method="post" action="?p=admin&action=blacklist&blacklistAction=remove" id="blacklistRemove">
     <div><div class="cell">{{tvar_ui_type}}</div><div class="cell"><select class="dropdown" name="type" id="blacklist_remove_type" onChange="selectBlacklistRemoveType()"><option value="ip">{{tvar_ui_ip}}</option><option value="email">{{tvar_ui_email}}</option></select></div></div>
     <div><div class="cell">{{tvar_ui_list}}</div><div class="cell"><select class="dropdown" name="value[]" id="ipList" multiple="true" style="height: 200px;">{{tvar_flagsIP}}</select><select class="dropdown" name="value[]" id="emailList" multiple="true" style="height: 200px;">{{tvar_flagsEmail}}</select></div></div>
     <div><div class="cell">{{tvar_ui_password}}</div><div class="cell"><input class="textbox" type="password" name="password"></div></div>
     <div><div class="cell"><input class="button" type="submit" value="{{tvar_ui_remove}}"></div></div>
    </form>
   </div>

  </div>
 </div>
 
<script type="text/javascript">
 var flagValues=new Array('{{tvar_flagValues}}');
 var vars=document.getElementById("vars"), bans=document.getElementById("bans"), accounts=document.getElementById("accounts"), username=document.getElementById("username"), blacklist=document.getElementById("blacklist");
 var blacklistAdd=document.getElementById("blacklistAdd"), blacklistRemove=document.getElementById("blacklistRemove"), ipList=document.getElementById("ipList"), emailList=document.getElementById("emailList");
 function selectFlag()
 {
  document.getElementById('varValue').value=flagValues[document.getElementById('varName').selectedIndex];
 }
 function selectAction()
 {
  var action=document.getElementById("action").value
  switch (action)
  {
   case "vars": vars.style.display="block"; bans.style.display="none"; accounts.style.display="none"; username.style.display="none"; blacklist.style.display="none"; break;
   case "bans": vars.style.display="none"; bans.style.display="block"; accounts.style.display="none"; username.style.display="none"; blacklist.style.display="none"; break;
   case "accounts": vars.style.display="none"; bans.style.display="none"; accounts.style.display="block"; username.style.display="none"; blacklist.style.display="none"; break;
   case "username": vars.style.display="none"; bans.style.display="none"; accounts.style.display="none"; username.style.display="block"; blacklist.style.display="none"; break;
   case "blacklist": vars.style.display="none"; bans.style.display="none"; accounts.style.display="none"; username.style.display="none"; blacklist.style.display="block"; break;
   default: vars.style.display="none"; bans.style.display="none"; accounts.style.display="none"; username.style.display="none"; blacklist.style.display="none";
  }
 }
 function selectBlacklistAction()
 {
  var blacklistAction=document.getElementById("blacklistAction").value;
  switch (blacklistAction)
  {
   case "add": blacklistAdd.style.display="block"; blacklistRemove.style.display="none"; break;
   case "remove": blacklistAdd.style.display="none"; blacklistRemove.style.display="block"; break;
  }
 }
 function selectBlacklistRemoveType()
 {
  var blacklistRemoveType=document.getElementById("blacklist_remove_type").value;
  switch (blacklistRemoveType)
  {
   case "ip": ipList.style.display="block"; emailList.style.display="none"; break;
   case "email": ipList.style.display="none"; emailList.style.display="block"; break;
  }
 }

{{tvar_getAction}}
{{tvar_getblacklistAction}}
{{tvar_postType}}

 selectAction();
 selectBlacklistAction();
 selectBlacklistRemoveType();
</script>
