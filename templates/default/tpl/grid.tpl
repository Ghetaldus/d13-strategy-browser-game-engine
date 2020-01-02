
<script type="text/javascript"> 
	var labels=new Array("{{tvar_ui_water}}", "{{tvar_ui_land}}");
   	var position=new Array(0, 0);
   	
   function setSector(x, y)
   {
    var sector;
    sector=document.getElementById("sector_"+(position[0]-3)+"_"+(position[1]-3));
    if (isset(sector)) sector.style.border="";
    sector=document.getElementById("sector_"+(position[0]-3)+"_"+(position[1]+3));
    if (isset(sector)) sector.style.border="";
    sector=document.getElementById("sector_"+(position[0]+3)+"_"+(position[1]+3));
    if (isset(sector)) sector.style.border="";
    sector=document.getElementById("sector_"+(position[0]+3)+"_"+(position[1]-3));
    if (isset(sector)) sector.style.border="";
    sector=document.getElementById("sector_"+(x-3)+"_"+(y-3));
    if (isset(sector))
    {
     sector.style.borderLeft="1px solid white";
     sector.style.borderBottom="1px solid white";
    }
    sector=document.getElementById("sector_"+(x-3)+"_"+(y+3));
    if (isset(sector))
    {
     sector.style.borderLeft="1px solid white";
     sector.style.borderTop="1px solid white";
    }
    sector=document.getElementById("sector_"+(x+3)+"_"+(y+3));
    if (isset(sector))
    {
     sector.style.borderRight="1px solid white";
     sector.style.borderTop="1px solid white";
    }
    sector=document.getElementById("sector_"+(x+3)+"_"+(y-3));
    if (isset(sector))
    {
     sector.style.borderRight="1px solid white";
     sector.style.borderBottom="1px solid white";
    }
    position=new Array(x, y);
   }
  </script>
  <style>
   div.sector
   {
    display: inline-block;
    cursor: pointer;
    width: 5px;
    height: 5px;
    border: 1px solid transparent;
   }
   div.sector:hover
   {
    border: 1px solid black;
   }
  </style>
  
<div class="container">
	<div class="cell" style="float: left;"><div class="content" id="content" style="width: 600px; height: 370px; text-align: left;"></div></div>
	
	<div class="cell" style="float: right;"><div class="content" style="z-index: 10; height: 370px;">
		{{tvar_gridHTML}}
	</div></div><div class="clear"></div>
		
</div>

<script type="text/javascript">fetch("{{tvar_global_basepath}}index.php?p=getGrid", {{tvar_vars}})</script>
