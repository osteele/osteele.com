<?php
require('../../wp-blog-header.php');
/*
 created by Kae - kae@verens.com
 I can't be bothered with crappy copyright notices.
 I wrote this. Feel free to use it.
 Please retain this notice.
###########
Edits by jon abad - jonabad@gmail.com
 - Started week on sunday
 - Prevented "next month" link when current month is being displayed. 
 - Coded in a root variable to properly write links
 - put in a little spinner to provide visual confirmation that the calendar is retrieving data.
 - taught it to read the siteurl by itself.
*/
?>
//Enter your wordpress root here, this is the location that your wp-content folder is in. Do not add a trailing slash.
var siteurl="<?php echo get_settings('siteurl'); ?>";

var dateDay=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
var dateDayShort=['S','M','T','W','T','F','S'];
var dateMon=['January','February','March','April','May','June','July','August','September','October','November','December'];
var dateMonShort=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
var entries_cache=new Array();

function addEvent(el,ev,fn){
 if(el.attachEvent)el.attachEvent('on'+ev,fn);
 else if(el.addEventListener)el.addEventListener(ev,fn,false);
}

addEvent(window,'load',kcalendar_refresh);

function kcalendar_build(year,month,day){
 var today=new Date;
 shownDate=new Date(year,month,day);
 cal=document.createElement('table');
 cal.id="wp-calendar";
 caption=document.createElement('caption');
 caption.appendChild(document.createTextNode(dateMon[shownDate.getMonth()]+' '+shownDate.getFullYear()));
 cal.appendChild(caption);
// draw day headers
 cal_row=cal.insertRow(0);
 cal_row.id="calendar_daysRow";
 for(i=0;i<7;i++){
  cal_cell=cal_row.insertCell(i);
  cal_cell.appendChild(document.createTextNode(dateDayShort[i]));
  cal_cell.title=dateDay[i];
 }
 // get date of first cell
 firstcelldate=new Date(shownDate.getFullYear(),shownDate.getMonth(),1);
 cellDate=1-firstcelldate.getDay();
 if(cellDate>1)cellDate-=7;
 // draw rest of month
 days_in_last_month=kcalendar_daysInMonth(shownDate.getFullYear(),shownDate.getMonth()-1);
 for(i=0;i<6;i++){
  cal_row=cal.insertRow(1+i);
  for(j=0;j<7;j++){
   cal_cell=cal_row.insertCell(j);
   class_to_show='faded';
   if(cellDate<1){
    num_to_show='';//days_in_last_month+cellDate;
   }else if(cellDate>kcalendar_daysInMonth(shownDate.getFullYear(),shownDate.getMonth())){
    num_to_show='';//cellDate-kcalendar_daysInMonth(shownDate.getFullYear(),shownDate.getMonth());
   }else{
    num_to_show=cellDate;
    class_to_show='';
    if(shownDate.getFullYear()==today.getFullYear() && shownDate.getMonth()==today.getMonth() && cellDate==today.getDate()){
     class_to_show+=" today"
    }
   }
   cal_cell.appendChild(document.createTextNode(num_to_show));
   cal_cell.className=class_to_show;
   cal_cell.id="kcalendar_"+shownDate.getFullYear()+"_"+(shownDate.getMonth()+1)+"_"+cellDate;
   window.status=cal_cell.id;
   cellDate++;
  }
 }
 // draw navigation row
 cal_row=cal.insertRow(7);
 
 cal_cell=cal_row.insertCell(0);
 link_year=shownDate.getFullYear();
 link_month=shownDate.getMonth()-1;
 if(link_month==-1){link_month=11;link_year--;}
 link=document.createElement('a');
 link.appendChild(document.createTextNode('<' + dateMonShort[link_month] +' '+ link_year));
 link.href="javascript:kcalendar_refresh("+link_year+","+link_month+");";
 cal_cell.colSpan=3;
 cal_cell.appendChild(link);

//make spinny go now!
 cal_cell=cal_row.insertCell(1);
 cal_cell.id="calendar_spin";
 spinner = document.createElement("IMG");
 spinner.src = siteurl + "/wp-content/livecalendar/wait.gif";
 cal_cell.appendChild(spinner);
//cal_cell.appendChild(document.createTextNode(shownDate.getFullYear()+' '+dateMonShort[shownDate.getMonth()]));

//if (current month != month requested) and (current year != year requested) then do
// if dec <> jan and 2004 == 2009

if ( (today.getMonth() == shownDate.getMonth()) && (today.getFullYear() == shownDate.getFullYear()) ){	
/* cal_cell=cal_row.insertCell(2);
 link_year=shownDate.getFullYear();
 link_month=shownDate.getMonth()+1;
 if(link_month==12){link_month=0;link_year++;}
 link=document.createElement('a');
 link.appendChild(document.createTextNode(dateMonShort[link_month] +' '+ link_year + '>'));
 cal_cell.colSpan=3;
 cal_cell.appendChild(link);*/
} 
else {
 cal_cell=cal_row.insertCell(2);
 link_year=shownDate.getFullYear();
 link_month=shownDate.getMonth()+1;
 if(link_month==12){link_month=0;link_year++;}
 link=document.createElement('a');
 link.appendChild(document.createTextNode(dateMonShort[link_month] +' '+ link_year + '>'));
 link.href="javascript:kcalendar_refresh("+link_year+","+link_month+");";
 cal_cell.colSpan=3;
 cal_cell.appendChild(link);
}

// get any applicable links for the dates
 tocall='kcalendar_'+(shownDate.getFullYear())+"_"+(shownDate.getMonth());
// if(entries_cache[tocall]){
//  kcalendar_create_links(entries_cache[tocall]);
// }else{
  var req = new XMLHttpRequest();
  if (req) {
   req.onreadystatechange=function(){
    if(req.readyState==4&&req.status==200&&req.responseText!='') {
     entries_cache[tocall]=req.responseText.split(/\n/);
     kcalendar_create_links(entries_cache[tocall]);
    }
   };
   req.open('GET', siteurl + '/wp-content/livecalendar/kcalendar.php?year='+shownDate.getFullYear()+'&month='+(shownDate.getMonth()+1));
   req.send(null);
  }
// }
 return cal;
}

function kcalendar_create_links(arr){
 for(i=0;i<arr.length;i++){
  split=arr[i].split(/: /);
  id='kcalendar_'+split[0].replace(/-/g,'_');
  id=id.replace(/_0/g,'_');
  el=document.getElementById(id);
  if(el){
   text=el.childNodes[0];
   el2=document.createElement('a');
   el2.appendChild(text);
   el2.title=split[1];
   el2.href= siteurl +"/index.php?m="+split[0].replace(/-/g,'');
   el.appendChild(el2);
  }
 }
 //clear spin thingy
 el=document.getElementById('calendar_spin');
 if(el){
  els=el.childNodes;
  for(i=el.childNodes.length-1;i>-1;i--)el.removeChild(els[i]);
 }
 
//end clearing of spin thing 
}

function kcalendar_daysInMonth(year,month){
 while(month<0){month+=12;year--}
 if(month==3||month==5||month==8||month==10)return 30;
 if(month!=1)return 31;
 if(!(year%4))return 29;
 return 28;
}

function kcalendar_refresh(year,month){
var today=new Date;
 if(isNaN(year)){
  str=document.location.toString();
  if(str.match('archive')){
   str=str.replace(/.*archives\//,'');
   arr=str.split(/\//);
   year=arr[0];
   month=arr[1]-1;
  }else{
   year=today.getFullYear();
   month=today.getMonth();
  }
 }
 cal=kcalendar_build(year,month,1);
 el=document.getElementById('calendar');
 if(el){
  els=el.childNodes;
  for(i=el.childNodes.length-1;i>-1;i--)el.removeChild(els[i]);
  el.appendChild(cal);
 }

}