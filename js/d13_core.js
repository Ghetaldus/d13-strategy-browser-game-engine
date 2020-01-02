/* -- D13 JS -------------------------------------------------------------------*/

function showInputValue(id, newValue)
{
	document.getElementById(id).value = newValue;
}

function showInputMax(id, spanid)
{
	obj = document.getElementById(id);
	newValue = obj.max
	obj.value = newValue;
	showInputValue(spanid, newValue);
}

function set_maximum(id, value) {
    document.getElementById(id).value = value;
    document.getElementById(id).max = value;
}

function change_maximum(id, value, form, formvalue) {
    set_maximum(id, value);
    document.getElementById(form).action= formvalue;
}

/* -- D13 JS -------------------------------------------------------------------*/

function armyCheck()
{

	var armyDisabled	= false;
	var totalAmount 	= 0;
	var uniqueAmount	= {};
	
	var leaderRequired 	= document.getElementById('leaderRequired');
	
	var unitAmount 		= document.getElementsByName('unitAmount[]');
	var unitType		= document.getElementsByName('unitType[]');
	var unitUnique		= document.getElementsByName('unitUnique[]');
	var availableRes	= document.getElementsByName('availableRes[]');
	var totalRes		= document.getElementsByName('totalRes[]');
	
	for (key=0; key < unitAmount.length; key++)  {	
	
		if (unitUnique[key].value > 0) {
			index = unitType[key].value;
			if (uniqueAmount[index] === undefined) {
				uniqueAmount[index] = parseInt(unitAmount[key].value);
			} else {
				uniqueAmount[index] += parseInt(unitAmount[key].value);
			}
		}
		
		if (leaderRequired.value > 0) {
			if (unitType[key].value != "leader") {
				armyDisabled = true;
			}
		}
		totalAmount += (unitAmount[key].value);
	}
	
	if (totalAmount <= 0) {
		armyDisabled = true;
	}
	
	for (key=0; key < unitAmount.length; key++)  {	
	
		if (unitUnique[key].value > 0) {
			index = unitType[key].value;
			if (uniqueAmount[index] > unitUnique[key].value) {
				armyDisabled = true;
			}
		}
		
	}
	
	for (key=0; key < availableRes.length; key++)  {	
		if (parseInt(availableRes[key].value) < parseInt(totalRes[key].innerHTML)) {
			armyDisabled = true;
		}
	}
	
	document.getElementById('startCombat').disabled = armyDisabled;

}

/* -- D13 JS -------------------------------------------------------------------*/

function armyValue(id, newValue)
{
	
	document.getElementById(id).value = newValue;
	document.getElementById('attackerAmount_'+id).value = newValue;
	
	var fuelFactor		= document.getElementById('fuelFactor').value;
	
	var totalFuel 		= 0;
	var grandAmount		= 0;
	
	var totalSpeed 		= 999999999;
	var totalAmount 	= 0;
	var totalDamage 	= 0;
	var totalStealth 	= 0;
	var totalArmor		= 0;
	var totalHealth		= 0;
	var totalCritical	= 0;
	var totalCapacity	= 0;

	var modDamage 		= 0.0;
	var modSpeed 		= 0.0;
	var modStealth 		= 0.0;
	var modArmor		= 0.0;
	var modHealth		= 0.0;
	var modCritical		= 0.0;
	var modCapacity		= 0.0;

	var unitAmount 		= document.getElementsByName('unitAmount[]');
	var unitFuel 		= document.getElementsByName('unitFuel[]');
	
	var unitDamage 		= document.getElementsByName('unitDamage[]');
	var unitSpeed 		= document.getElementsByName('unitSpeed[]');
	var unitStealth 	= document.getElementsByName('unitStealth[]');
	var unitArmor 		= document.getElementsByName('unitArmor[]');
	var unitHealth 		= document.getElementsByName('unitHP[]');
	var unitCritical	= document.getElementsByName('unitCritical[]');
	var unitCapacity	= document.getElementsByName('unitCapacity[]');

	var armyModDamage 	= document.getElementsByName('armyModDamage[]');
	var armyModSpeed 	= document.getElementsByName('armyModSpeed[]');
	var armyModStealth  = document.getElementsByName('armyModStealth[]');
	var armyModArmor	= document.getElementsByName('armyModArmor[]');
	var armyModHealth	= document.getElementsByName('armyModHP[]');
	var armyModCritical	= document.getElementsByName('armyModCritical[]');
	var armyModCapacity	= document.getElementsByName('armyModCapacity[]');

	for(key=0; key < unitAmount.length; key++)  {
		totalAmount = parseInt(unitAmount[key].value);
		
		if (parseInt(unitAmount[key].value) > 0 && parseInt(unitSpeed[key].value) < totalSpeed) {
		totalSpeed 		= parseInt(unitSpeed[key].value);
		}
		
    	totalDamage 	+= parseInt(unitDamage[key].value) * totalAmount;
    	totalStealth 	+= parseInt(unitStealth[key].value) * totalAmount;
    	totalArmor 		+= parseInt(unitArmor[key].value) * totalAmount;
    	totalHealth 	+= parseInt(unitHealth[key].value) * totalAmount;
    	totalCritical 	+= parseInt(unitCritical[key].value) * totalAmount;
    	totalCapacity 	+= parseInt(unitCapacity[key].value) * totalAmount;
    	
    	tmpAmount = totalAmount!=0?1:0;
    	
    	modDamage 	+= Math.floor(totalDamage 		* (parseFloat(armyModDamage[key].value) * tmpAmount));
   		modSpeed 	+= Math.floor(totalSpeed 		* (parseFloat(armyModSpeed[key].value) * tmpAmount));
    	modStealth 	+= Math.floor(totalStealth 		* (parseFloat(armyModStealth[key].value) * tmpAmount));
    	modArmor 	+= Math.floor(totalArmor 		* (parseFloat(armyModArmor[key].value) * tmpAmount));
    	modHealth 	+= Math.floor(totalHealth 		* (parseFloat(armyModHealth[key].value) * tmpAmount));
    	modCritical += Math.floor(totalCritical 	* (parseFloat(armyModCritical[key].value) * tmpAmount));
    	modCapacity += Math.floor(totalCapacity		* (parseFloat(armyModCapacity[key].value) * tmpAmount));
    	
    	totalFuel += parseInt(unitFuel[key].value) * totalAmount * fuelFactor;
    	grandAmount += totalAmount;
	}
	
	if (totalSpeed >= 999999999) {
		totalSpeed = 0;
	}
	
	document.getElementById('totalAmount').innerHTML 	= grandAmount;
	document.getElementById('totalFuel').innerHTML 		= totalFuel;
	
	document.getElementById('totalDamage').innerHTML 	= totalDamage 	+ ' [+' + modDamage 	+ ']';
	document.getElementById('totalSpeed').innerHTML 	= totalSpeed 	+ ' [+' + modSpeed 		+ ']';
	document.getElementById('totalStealth').innerHTML 	= totalStealth 	+ ' [+' + modStealth 	+ ']';
	document.getElementById('totalArmor').innerHTML 	= totalArmor 	+ ' [+' + modArmor 		+ ']';
	document.getElementById('totalHealth').innerHTML 	= totalHealth 	+ ' [+' + modHealth 	+ ']';
	document.getElementById('totalCritical').innerHTML 	= totalCritical + ' [+' + modCritical 	+ ']';
	document.getElementById('totalCapacity').innerHTML 	= totalCapacity + ' [+' + modCapacity 	+ ']';
	
	armyCheck();
	
}

/* -- D13 JS -------------------------------------------------------------------*/

function timedJump(objectId, url)
{
	object=document.getElementById(objectId);
	var time=(object.innerHTML).split(":");
 	var done=0;
	if (time[2] > 0) time[2]--;
	else
	{
		time[2]=59;
		if (time[1] > 0) time[1]--;
		else
		{
			time[1]=59;
			if (time[0] > 0) time[0]--;
			else
   			{
    			clearTimeout(timerIds[objectId]);
   				window.location.href=url;
   				done=1;
   			}
		}
	}
	if (!done)
	{
  	if (String(time[1]).length==1) time[1]="0"+String(time[1]);
  	if (String(time[2]).length==1) time[2]="0"+String(time[2]);
		object.innerHTML=time[0]+":"+time[1]+":"+time[2];
		timerIds[objectId]=setTimeout("timedJump('"+objectId+"', '"+url+"')", 1000);
	}
}

/* -- D13 JS -------------------------------------------------------------------*/

function isset(variable)
{
 if ((typeof(variable)!="undefined")&&(variable!==null)) return true;
 else return false;
}

function indexOfSelectValue(object, value)
{
 var index=-1;
 for (var i=0, done=false; ((i<object.length)&&(!done)); i++)
  if (object.options[i].value==value)
  {
   index=i;
   done=true;
  }
 return index;
}

function jumpToSector()
{
 var x=document.getElementById("x").value, y=document.getElementById("y").value;
 fetch("/server/index.php?p=getGrid", "x="+x+"&y="+y);
}
function setSectorData(descriptionText, playerText, allianceText)
{
 var description=document.getElementById("description"), player=document.getElementById("player"), alliance=document.getElementById("alliance");
 description.innerHTML=descriptionText; player.innerHTML=playerText; alliance.innerHTML=allianceText;
}
function fetch(link, vars)
{
 var xmlHttp;
 try
 {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
 }
 catch (e)
 {
  // Internet Explorer
  try
  {
   xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
  }
  catch (e)
  {
   try
   {
    xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
   }
   catch (e)
   {
    alert("Your browser does not support AJAX.");
    return false;
   }
  }
 }
 contentData=document.getElementById("content");
 contentData.innerHTML="<img src=\"templates/loading.gif\">"+contentData.innerHTML;
 xmlHttp.onreadystatechange=function()
 {
  if(xmlHttp.readyState==4)
  {
   contentData.innerHTML="";
   data=xmlHttp.responseText;
   if (data.indexOf("<script type='text/javascript'>")>-1)
    for (start=data.indexOf("<script type='text/javascript'>"); start>-1; start=data.indexOf("<script type='text/javascript'>"))
    {
     end=data.indexOf("</script>");
     script=document.createElement("script");
     script.type="text/javascript";
     script.text=data.substring(start+31, end-1);
     contentData.innerHTML+=data.substring(0, start-1);
     contentData.appendChild(script);
     if (data.length>end+9) data=data.substring(end+9);
     else data="";
    }
   contentData.innerHTML+=data;
  }
 }
 if (vars)
 {
  xmlHttp.open("POST", link, true);
  xmlHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
  xmlHttp.send(vars);
 }
 else
 {
  xmlHttp.open("GET", link, true);
  xmlHttp.send(null);
 }
}

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
   
/* -------------------------------------------------------------------------------------*/
