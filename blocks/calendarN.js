//var args = window.dialogArguments;


var WeekTitles=new Array('П','В','С','Ч','П','С','В')
function showallweektitles(){
	var i, answer="  <tr>\n"
	
	for(i=0;i<WeekTitles.length;i++){
		style=''; if((i==5) || (i==6)){style=" bgcolor=\"#FFDAB9 \" ";}
		answer+="    <th CLASS=\"calendar\" "+style+" >"+WeekTitles[i]+"</th>\n";
	}
	
	answer+="  </tr>\n"
	return answer
}
function calendar_show(d, m, y){//вывод календаря на показ
var MonthLet = new Array('','январь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь');
//alert('2222');
	var sdate= new Date(m+'/1/'+y);
	//var mdate= new Date(m+'/'+d+'/'+y);
	var todaydate= new Date();
	var days=dayonmonth(m,y)
	var iday=0, day, answer=''
	answer+='<table border=0 CLASS="calendar">'
	answer+='  <tr CLASS="calendar">'
	answer+='  	<th CLASS="calendar" colspan=7>'
	answer+='		<table width="100%" border=0 cellpadding=0 cellspacing=0 height="100%">'
	answer+='  			<tr CLASS="calendar">'
	answer+='    			<td align="left"><b></b></td>'//Календарь
	answer+='    			<td align="right"> <b style="font-size:12px">'+MonthLet[m]+' '+y+' </b></td>' //<td align="right">'+d+'/'+MonthLet[m]+'/'+y+'</td>
	answer+='  			</tr>'
	answer+='		</table>'
	answer+='	</th>'
	answer+='  </tr>'
	answer+='  <tr CLASS="calendar">'
	answer+='  	<th colspan=7>'
	answer+='<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%">'
	answer+='  <tr CLASS="calendar">'
	answer+='    <td width="25%"><table onClick="calendar_move(\'-\',\'y\','+m+','+d+','+y+')" CLASS="calendar_button_0_1" onMouseOver="this.className=\'calendar_button_1_1\'" onMouseOut="this.className=\'calendar_button_0_1\'" border=0 cellpadding=0 cellspacing=0 width="100%" height="100%"><tr CLASS="calendar"><td><<</td></tr></table></td>'  //calendar_move(\'-\',\'y\','+m+','+d+','+y+')
	answer+='    <td width="25%"><table onClick="calendar_move(\'-\',\'m\','+m+','+d+','+y+')" CLASS="calendar_button_0_2" onMouseOver="this.className=\'calendar_button_1_2\'" onMouseOut="this.className=\'calendar_button_0_2\'" border=0 cellpadding=0 cellspacing=0 width="100%" height="100%"><tr CLASS="calendar"><td><</td></tr></table></td>'
	answer+='    <td width="25%"><table onClick="calendar_move(\'+\',\'m\','+m+','+d+','+y+')" CLASS="calendar_button_0_2" onMouseOver="this.className=\'calendar_button_1_2\'" onMouseOut="this.className=\'calendar_button_0_2\'" border=0 cellpadding=0 cellspacing=0 width="100%" height="100%"><tr CLASS="calendar"><td>></td></tr></table></td>'
	answer+='    <td width="25%"><table onClick="calendar_move(\'+\',\'y\','+m+','+d+','+y+')" CLASS="calendar_button_0_1" onMouseOver="this.className=\'calendar_button_1_1\'" onMouseOut="this.className=\'calendar_button_0_1\'" border=0 cellpadding=0 cellspacing=0 width="100%" height="100%"><tr CLASS="calendar"><td>>></td></tr></table></td>'
	answer+='  </tr>'
	answer+='</table>'
	answer+='	</th>'
	answer+='  </tr>'
	answer+='  <tr>'
	answer+=showallweektitles()
	
	var dayNeed = sdate.getDay()-1; if(dayNeed == -1){dayNeed = 6;} //передвигаем одинь день под наш календарь
	//alert(dayNeed);
	for(day=0;day<(dayNeed);day++){
		iday++
		answer+="    <td></td>"
	}
	for(day=1;day<=days;day++){//вывод дней в календаре  поставил 0, чтобы передвинуть понедельник
		iday++
		
		var style='';  if((iday==6) || (iday==(7))){  style=" bgcolor=\"#FFDAB9 \" ";}
		if((todaydate.getMonth()+1)==m && todaydate.getDate()==day && todaydate.getFullYear()==y)
			answer+="    <td onClick=\"InsertDate('"+day+"-"+m+"-"+y+"'); \" CLASS=\"calendar_today\"><table onMouseOver=\"this.className='calendar_active'\" onMouseOut=\"this.className=''\" border=0 cellpadding=0 cellspacing=0 width=\"100%\" height=\"100%\"><tr CLASS=\"calendar\"><td "+style+" >"+day+"</td></tr></table></td>"  //onClick=\"window.returnValue='"+m+"/"+day+"/"+y+"';window.close()\"
		else
			answer+="    <td onClick=\"InsertDate('"+day+"-"+m+"-"+y+"');\" CLASS=\"calendar\"><table onMouseOver=\"this.className='calendar_active'\" onMouseOut=\"this.className=''\" border=0 cellpadding=0 cellspacing=0 width=\"100%\" height=\"100%\"><tr CLASS=\"calendar\"><td "+style+" >"+day+"</td></tr></table></td>"
		if(iday==7){
			answer+="</tr><tr>"
			iday=0
		}
	}
	answer+="  </tr>"
	answer+='</table>'
	//self.calendar.innerHTML=answer
	document.all.calendar.innerHTML=answer
}
function dayonmonth(m, y){ // Функция, для определения количества дней в месяце
	var answer
	if(m!=2){ // Если не "Февраль"
		var date1=new Date(m+'/31/'+y)
		var mm=(m<12)?(m+1):1;
		var yy=(m<12)?y:(y+1);
		var date2=new Date(mm+'/1/'+yy)
		answer=(date1.getDay()==date2.getDay())?'30':'31';
	}else{
		var date1=new Date(m+'/29/'+y)
		var mm=(m<12)?(m+1):1;
		var yy=(m<12)?y:(y+1);
		var date2=new Date(mm+'/1/'+yy)
		answer=(date1.getDay()==date2.getDay())?'28':'29';
	}
	return answer
}



function calendar_move(to, index, m, d, y){//передвижение календаря 
	switch(to){
		case('-'):
			switch(index){
				case('m'):
					if(m>1){
						m--
					}else{
						m=1
						y--
					}
					break
				case('d'):
					var mm=(m>1)?--m:1
					var yy=(m>1)?y:--y
					var days=dayonmonth(mm, yy)
					if(d>1){
						d--
					}else{
						d=days
						m=mm
						y=yy
					}
					break
				case('y'):
					y--
					break
			}
			break
		case('+'):
			switch(index){
				case('m'):
					if(m<12){
						m++
					}else{
						m=1
						y++
					}
					break
				case('d'):
					var mm=(m<12)?++m:1
					var yy=(m<12)?y:++y
					var days=dayonmonth(m, y)
					if(d<days){
						d++
					}else{
						d=1
						m=mm
						y=yy
					}
					break
				case('y'):
					y++
					break
				}
			break
	}
	calendar_show(d, m, y)
}
