
  <script type="text/javascript">
   var timerIds=new Array();
   function setLabel(value)
   {
    document.getElementById("label").innerHTML=value;
   }
  </script>
 

    
    <form method="post" action="index.php?p=node&action=move&nodeId={{tvar_nodeID}}">
     <div><div class="cell">x</div><div class="cell"><input class="textbox numeric" type="text" name="x" value="{{tvar_nodeX}}" size="3"></div></div>
     <div><div class="cell">y</div><div class="cell"><input class="textbox numeric" type="text" name="y" value="{{tvar_nodeY}}" size="3"></div></div>
     <div><input class="button" type="submit" value="{{tvar_ui_move}}"></div>
    </form>
    <div>{{tvar_ui_cost}}: {{tvar_costData}}<div class="cell">{{tvar_ui_perSector}}</div></div>
    <div style="border-top: 1px solid black; padding-top: 5px; margin-top: 5px;"><a class="external" href="index.php?p=node&action=get&nodeId={{tvar_nodeID}}">{{tvar_nodeName}}</a></div>