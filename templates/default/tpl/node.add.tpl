
  <script type="text/javascript">
   var timerIds=new Array();
   function setLabel(value)
   {
    document.getElementById("label").innerHTML=value;
   }
  </script>
 
    <form method="post" action="index.php?p=node&action=add">
     <div><div class="cell">{{tvar_ui_faction}}</div><div class="cell"><select class="dropdown" name="faction" id="faction" onChange="changeFaction()">';
   		{{tvar_factionOptions}}
     </select></div></div>
     <div><div class="cell"></div><div class="cell" id="factionDescription">{{tvar_factionText}}</div></div>
     <div><div class="cell">{{tvar_ui_name}}</div><div class="cell"><input class="textbox" type="text" name="name"></div></div>
     <div><div class="cell">{{tvar_ui_location}}</div><div class="cell">{{tvar_ui_x}}<input class="textbox" type="text" name="x" size="2">{{tvar_ui_y}}<input class="textbox" type="text" name="y" size="2"></div></div>
     <div><input class="button" type="submit" value="{{tvar_ui_add}}"></div>
    </form>
    
    <script type="text/javascript">
     var factions=new Array({{tvar_factionDescriptions}});
     function changeFaction()
     {
      	document.getElementById("factionDescription").innerHTML=factions[document.getElementById("faction").selectedIndex];
     }
    </script>