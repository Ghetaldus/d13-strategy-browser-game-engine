  <script type="text/javascript">
   var timerIds=new Array();
   function setLabel(value)
   {
    document.getElementById("label").innerHTML=value;
   }
  </script>
 
    <form method="post" action="index.php?p=node&action=set&nodeId={{tvar_nodeID}}">
     <div><div class="cell">{{tvar_ui_name}}</div><div class="cell"><input class="textbox" type="text" name="name" value="{{tvar_nodeName}}"></div></div>
     <div>
      <div class="cell">{{tvar_ui_focus}}</div>
      <div class="cell">
       <select class="dropdown" name="focus">
        <option value="hp"{{tvar_selFocusHP}}>{{tvar_ui_hp}}</option>
        <option value="damage"{{tvar_selFocusDamage}}>{{tvar_ui_damage}}</option>
        <option value="armor"{{tvar_selFocusArmor}}>{{tvar_ui_armor}}</option>
       </select>
      </div>
     </div>
     <div><input class="button" type="submit" value="{{tvar_ui_set}}"></div>
    </form>
    <div>{{tvar_ui_cost}}: {{tvar_costData}}</div>
    <div style="border-top: 1px solid black; padding-top: 5px; margin-top: 5px;"><a class="external" href="index.php?p=node&action=get&nodeId={{tvar_nodeID}}">{{tvar_nodeName}}</a></div>