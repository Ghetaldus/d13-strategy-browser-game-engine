
  <script type="text/javascript">
   var timerIds=new Array();
   function setLabel(value)
   {
    document.getElementById("label").innerHTML=value;
   }
  </script>
 

<div>{{tvar_ui_areYouSure}}</div><div><a class="external" href="index.php?p=node&action=remove&nodeId={{tvar_nodeID}}&go=1">{{tvar_ui_yes}}</a> | <a class="external" href="index.php?p=node&action=get&nodeId={{tvar_nodeID}}">{{tvar_ui_no}}</a></div>
  