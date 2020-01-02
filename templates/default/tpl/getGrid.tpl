 {{tvar_ui_jumpTo}}:
 <input class="textbox" type="text" id="x" size="2" value="{{tvar_x}}">
 <input class="textbox" type="text" id="y" size="2" value="{{tvar_y}}">
 <input class="button" type="button" onClick="jumpToSector()" value="{{tvar_ui_go}}">
 
 <div style="position: relative; top: 30px; left: 0px;">
	{{tvar_mapDiv_1}}
	{{tvar_mapDiv_2}}
 </div>
 
 <div style="position: relative; top: 0px; left: 15px;">
 {{tvar_mapDiv_3}}
  <img src="{{tvar_global_basepath}}template/{{tvar_global_template}}/images/grid/arrows.png" border="0" usemap="#grid" style="position: absolute; left: 0px; top: 29px; width: 560px; height: 291px;">
  <map name="grid">
	{{tvar_mapDiv_4}}
   	<area shape="circle" coords="482,38,15" 	href="javascript: fetch('{{tvar_global_basepath}}index.php?p=getGrid', '{{tvar_mapControls_North}}')" 	title="{{tvar_ui_North}}">
   	<area shape="circle" coords="77,241,15" 	href="javascript: fetch('{{tvar_global_basepath}}index.php?p=getGrid', '{{tvar_mapControls_South}}')" 	title="{{tvar_ui_South}}">
   	<area shape="circle" coords="482,241,15" 	href="javascript: fetch('{{tvar_global_basepath}}index.php?p=getGrid', '{{tvar_mapControls_East}}')" 	title="{{tvar_ui_East}}">
   	<area shape="circle" coords="77,38,15" 		href="javascript: fetch('{{tvar_global_basepath}}index.php?p=getGrid', '{{tvar_mapControls_West}}')" 	title="{{tvar_ui_West}}">
  </map>
 </div>
 
 <div style="display: inline-block; position: relative; top:-25px; left:350px;">
  <table style="border-collapse: collapse; border-style: none;" width="250">
   <tr>
    <td align="center" id="description" style="border-style: none;">{{tvar_ui_description}}</td>
   </tr>
   <tr>
    <td width="117" align="center" id="player" style="border-style: none;">{{tvar_ui_player}}</td>
   </tr>
   <tr>
    <td width="117" align="center" id="alliance" style="border-style: none;">{{tvar_ui_alliance}}</td>
   </tr>
  </table>
 </div>
 
<script type='text/javascript'>
 setSector({{tvar_x}}, {{tvar_y}});
</script>