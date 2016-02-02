<?
	header('Content-Type: text/html; charset=utf-8');//собираем всю информацию об защедщих на сайт
	
session_start();
include_once('startup.php');
	// Установка параметров, подключение к БД, запуск сессии.
	startup();

//прооверяем, что мы админ
include('variables.php');
if($_SESSION['Guest_id']['id_user'] == $AdminID){ 
	include('rss_scripts.php');
}else{
//echo 'Вы не Админ!!!';
}

echo "<br />";


//в дальнейшем бы надо закешировать структуры


//$_GET['avtor']='all';
//$_GET['avtor']='7';

	$avtor='';
	if(isset($_POST['avtor'])){
		$_GET['avtor'] = $_POST['avtor'];
	}

	
	//		include('MakeStructAvtArr.php');	
	if($_GET['avtor'] !='all'){//если мы выводим структуру какого либо определенного автора, то просчитываем ее
		include('MakeStructAvtArr.php');		
	}else{//если всех авторов, то вытаскиваем структуру из БД, куда ее запихиваем при всех изменениях авторов
		$arrWho = array();
		//вытаскиваем из БД
		$query = "SELECT * FROM  InfoTable "; 
		$result = mysql_query($query) or die(mysql_error());
		$n = mysql_num_rows($result);
		if($n >0){
			$row = mysql_fetch_assoc($result);
			//echo $row['AvtorStructArray'];
			$arrAvtAllOb = unserialize( base64_decode( $row['AvtorStructArray']));
			$arrAvtAll = unserialize( base64_decode( $row['AvtorArray']));				
		}
	}




		
		

	
	
	
//echo "<br /><br /><br />123-4";
//	 var_dump($arrAvtAllOb);	
	$titleStr = $arrAvtAll[$avtor]['Who'];

	
	//вытаскиваем всех авторов (если в дальнейшем будет висеть, то сделать при клике на ссылку)
	$query = "SELECT * FROM  Speech_Who ORDER BY Who";
	$result = mysql_query($query) or die(mysql_error());
		$n = mysql_num_rows($result);
		if($n >0){
			$arrWho = array();
			for ($i = 0; $i < $n; $i++)
			{
				$row = mysql_fetch_assoc($result);		
				$arrWho[] = $row;
				if($row['id'] ==$SpeechArr[0]['id_avtor']){ $titleStr = $row['Who'];	}
			}
		}
		

		//
?>	
<html>
<head>
	<title><?=$titleStr?> в структкре мирового порядка (положение, должность, подчиненные, начальство)</title>
</head>	
<body style="position:relative;" >
<script type="text/javascript" src="js/jquery.js"></script> <!--подключаем -->
<style>
.AvtTD{
	padding:0px 10px 0 10px;
	border-right:1px solid #ccc;
}
.AvtTD:hover{
	background:#ddd;
	border:1px solid green;
}
#SpeechFilterForm, .vdiskus{ 
	background: -webkit-gradient(linear, left top, right  top, from(#EAEAEA), to(#BBBBBB)); background: -moz-linear-gradient(left top, #EAEAEA, #BBBBBB);  
}
</style>
<!--[if IE]>
<style>
	#SpeechFilterForm{ 
		height:200px; 
		filter:progid:DXImageTransform.Microsoft.Gradient(
			startColorStr='#EAEAEA', endColorStr='#BBBBBB', gradientType='0'
		);
	}
	.vdiskus{
		background:#BBBBBB;
	}
</style>
<![endif]-->

<?	//echo "Автор - ".$avtor;
	//отрисовываем таблицу подчиненности авторов
	?>
	<a class='vdiskus' href="index.php" title="Главная страница сети о Российских и мировых новостях"><< Главная страница!</a> |
	<!--<a class='vdiskus' href="index.php?c=soobsh" title="Новости в России и мире " > Последние новости</a> |-->
	<a class='vdiskus' href="SpeechWind.php" title="Речи, высказывания, диалоги, обращения знаменитых людей (политиков, историков, аналитиков)" > Высказывания известных людей</a> |
	<!--<a class='vdiskus' href="EventsDate.php" title="Хроника событий, новостей и происшествий в России и мире по времени" > Хроника событий по времени</a>-->
	
	
		<!--Форма фильтра -->
		<div id="SpeechFilterForm" style='padding:8px;' >
			<form id='AvtorForm' method='post' action='PersonsStruct.php' >
					<fieldset style='float:left;'>
						<legend><b>Выберите персону для просмотра</b></legend>
							<input   onkeyup='ChangeAvtors(this)' name='avtorName' id='avtorNameId' /> <b>или выберите</b>
							<select id='selectMod'  name='avtorNameCh' onChange='SelectAvtor(this)' >
								<?foreach($arrWho as $w){?>
								<option value='<?=$w['id']?>' ><?=$w['Who']?></option>
								<?}?>
							</select>
							<br />
							<input type='submit' value='выбрать' />
							<br /><a href='PersonsStruct.php?avtor=all'>Показать всех!</a><br />
					</fieldset>
			</form>
			<div style="clear:both;"></div>
		</div><br />
	<script>
		function SelectAvtor(obj){//отправка формы с id автора
			//alert(obj.options[obj.selectedIndex].value);
			document.location.href = 'PersonsStruct.php?avtor='+obj.options[obj.selectedIndex].value;
		}
		 
		function ChangeAvtors(obj){//функция поиска в БД авторов по буквам
			if (obj.value.length > 2){
			//alert("rrrr");
				//вытаскиваем всех авторов у которых один из имен начинается на эти буквы
				var arr;
				$.ajax({
				  async: false, 
				  url: 'blocks/dinamic_scripts/Find_Avtors.php',
				  data: {name:obj.value},
				  type: "POST",
				  success: function(data) {  arr = data; },
				dataType: 'json'
				 })
		
		//alert(arr);
				if(arr){
					var Sel = document.getElementById('selectMod');
					Sel.innerHTML= '';
					Sel.multiple = true;
					for(var i=0; i<arr.length; i++){
						var arr1 = arr[i].split('|');
						//alert(arr1[0]+"---"+arr1[1]);
						var opt = document.createElement('option');
						opt.value = arr1[0];
						opt.innerHTML = arr1[1];
						Sel.appendChild(opt);
					}
				}
			}
		}
	</script>		
		
		

	<?	
$arrAvtId;

//строим таблицы
function BuiltTable($arr,$pos =''){
	//var_dump($arr);
	//if(!$arr){ $arr[] }
		foreach($arr as $k=>$v){
		//echo "<br><br>werwe-".$k;
		//var_dump($v);
			$onclick = " onclick='ShowAdminMenu(this,event)' ";
			
			//заполняем автора (члена констукции)
			global $arrAvtAll;
			$val = $arrAvtAll[$k];//берем значения из второго массива
			//if($val['Foto'] ==''){ $ff = 'FotoAvtors/No_Foto.jpg';}else{$ff = ''.$val['Foto'];}  //src='".$ff."' style='width:100px;' 
			if($val['Foto'] ==''){ $ff = " src='FotoAvtors/No_Foto.jpg' style='width:50px;' ";}else{$ff = " src='".$val['Foto']."'  style='width:150px;' ";} 
			//если автор которого искали
			global $avtor;
			$stlNeedAvt = '';
			if($val['id'] == $avtor){ $stlNeedAvt = "style='background:#ccc; border:2px solid green;'"; }			
			$descrip=''; if($val['descript'] !=''){$descrip ="<b style='color:#999;'>".$val['descript']."</b><br />";}
			//данные о проверенных речах
			$allSp = '';
			$spCheckArr = explode('|',$val['LastSpeechInfo']);
			//if(count($spCheckArr) >2){
				$allSp = " <b style='color:green; font-size:11px;'> ".$spCheckArr['0']." речей </b>";
				if($_SESSION['Guest_id']['id_user'] != $AdminID){$allSp .= "<b style='color:red; font-size:15px;'>".$spCheckArr['2']." | ".$spCheckArr['1']."</b>";}
			//}
			$avtorStr = "<div class='AvtTD' align='center' ".$stlNeedAvt." ".$onclick." id='WhI-".$k."'>
							<b style='font-size:19px;'><a href='SpeechWind.php?avtor=".$val['id']."' target='_blank' title='".$val['descript'].", все высказывания, речи, выступления, обращения,  разговоры, диалоги и дискуссии' >".$val['Who']."</a>".$allSp."</b><br />".$descrip."<img ".$ff."  />
						</div>";

				$style='';
				if($pos == 'first'){ $style = "style='height:40px; margin-left:50%;  background:url(img/struct.png) no-repeat; '"; //border:1px solid red;
				}elseif($pos == 'last'){ $style = "style='height:40px; margin-right:50%; background:url(img/struct.png) no-repeat;  background:url(img/struct.png) no-repeat; background-position:100% 0px;'";
				}elseif($pos == 'mid'){ $style = "style='height:40px; background:url(img/struct.png) no-repeat; background-position:50% 0px; '";
				}elseif($pos == 'edinstv'){ $style = "style='height:20px;  background-position:50% 0px;'";  //background:url(img/struct1.png) no-repeat;
				}		


				
			if(count($v)>0){	
				?>
				<table align="center"  cellspacing="0" style="border:1px solid #ccc;"  ><!--border="1" bordercolor="red;" cellpadding="15" bordercolor="#eee;"-->
					<tr>
						<?
						//global $arrAvtAll;
						//$val = $arrAvtAll[$k];//берем значения из второго массива

							//var_dump($val);
							$colspan='';
							if(count($v)>0){$colspan="colspan='".count($v)."'";}?>
							<td    <?=$colspan?> >
								<!--background:url(img/fon.png);-->
									<div <?=$style?>  ></div><!--margin-left: 50%; margin-top:-40px;-->
									<div class='AvtTD' align='center'  >

										<!--<div <?=$stlNeedAvt?>  <?=$onclick?> >
											<?=$pos?> <br>12
											<?=$val['Who']?>
											<br><?=$val['descript']?>-->
											<?=$avtorStr?>
										<!--</div>-->
										<div style='position: relative;'>
										<div class="IeMar" style='position:absolute; top:-5px; right:50%; background:url(img/struct2.png) no-repeat; width:40px; height:40px; background-position:50% 0%;'><!--стрелка вниз-->

										</div></div>
									</div>
							</td> 
					</tr>
					<?if(count($v)>0){?>
					<tr>
							<?$m = 1;
							foreach($v as $k1 => $v1){ 						
									$pos1='';
									
									if(count($v)!=1){
										if($m==1){ $pos1 = "first"; //.count($v)."<br>"; 
										}elseif($m == count($v)){ $pos1= "last"; // .count($v)."<br>";
										}else{$pos1= "mid"; //.count($v)."<br>";  
										}
									}else{ $pos1= "edinstv"; //.count($v)."<br>"; 
									}
									echo "<td valign='top' >";	
										if(count($v1)>0){// echo "перенос на др стр<br>";
										}else{?>
												<?//=$pos1?>
												<?// echo $m." из ".count($v)." след ".count($v1)."<br>";?>
										<?}
										BuiltTable(array($k1=>$v1),$pos1);
									echo "</td>";
									$m++;
									//$val = $arrAvtAll[$k];//берем значения из второго массива
								}
							?>
					</tr>
					<?}?>
				</table>
				<?
			}else{  

			//style='position: relative; overflow:hidden; border:1px solid green;'
				echo  "<div ".$style." ></div>";
						//<div class='AvtTD' align='center' ".$stlNeedAvt." ".$onclick." >";
				//echo "444".$pos."<br>111";
				echo $avtorStr;
				//echo $val['Who']."<br>".$val['descript'];
				//echo "</div>"; 
			}//background:url(img/fon.png);
		}
}

//BuiltTable($arrAvtId,'beg');
//var_dump($arrAvtAllOb);

?><table align="center"  cellspacing="0" style="border:1px solid #ccc;"  ><tr><?
foreach($arrAvtAllOb as $ke=>$arrAvtId){
	//echo "<br>121<br>"; var_dump($arrAvtId);
	echo "<td valign='top'>";
		/*if(!$arrAvtId){
		echo "rr ".$ke." rr";
			global $arrAvtAll;
			$val = $arrAvtAll[$ke];
			$arrAvtId[$ke] =$val;
		}*/
		BuiltTable($arrAvtId,'beg');
	echo "</td>";
}
?></tr></table><?

?>
	<script type="text/javascript" src="blocks/dinamic_scripts/CreateElementOnScreen.js"></script><!--подключаем для создания плавающих окон -->
	<script>//вывод меню персоны
			var IdWho = '';//id автора речи
			
			var PreviosMenu;//предыдущее открытое меню
			
			function ShowAdminMenu(obj,event){//выводим меню к персоне
				if(PreviosMenu != null){
					//alert(PreviosMenu.id);
					PreviosMenu.style.display = 'none';
					PreviosMenu.parentNode.removeChild(PreviosMenu);
				} //else{alert("lllll");}
				PreviosMenu = null;
				
				//alert(obj.id+'ShowAdminMenu');
				var id = obj.id.split("-")[1];
				IdWho = id;
				
				//проверяем не создан ли еще
				//if(document.getElementById('Menu-'+id)){return;}
			//	alert(IdWho);
				var opt = new Array(); //параметры
				opt.IndexZ = 4 //IndexZ
				//opt.fixed = 'fix'; //означает, что она зафиксирована экране
				var elemMenu = CreateElementOnScreen(260,event,opt);
				elemMenu.innerHTML = "<span onclick='deletElem(this)' id='delme' style='cursor:pointer; float:right; margin: -10px -5px 0 0;' > <span style='color:black;'>_</span><b>x</b><span style='color:black;'>_ </span></span><br />";  //закрывание этого списка
				elemMenu.id = 'Menu-'+id;
				
				
				<?if($_SESSION['Guest_id']['id_user'] == $AdminID){ //если я админ	?>
					//elemMenu.innerHTML += "<div id='idWh-"+id+"' onclick='ShowFotoMen(this)' style='text-decoration:underline; cursor:pointer;'>Прикрепить фото</div>";
					elemMenu.innerHTML +="<b>Админское:</b><br /> id автора - "+id+"<br />";
					elemMenu.innerHTML +="<div id='FA-"+id+"'  style='text-decoration:underline; cursor:pointer;'><span onclick='InsertFormAllias(this)' >сделать его псевдонимом</span></div>";
					elemMenu.innerHTML +="<div id='FP-"+id+"'  style='text-decoration:underline; cursor:pointer;'><span onclick='InsertFormPodch(this)' >подчинить другому автору</span></div>";
				elemMenu.innerHTML +="<div id='FD-"+id+"'  style='text-decoration:underline; cursor:pointer;'><span onclick='InsertFormDescr(this)' >поменять описание</span></div>";
					elemMenu.innerHTML +="<div onclick='MakePresident(\""+id+"\")' style='text-decoration:underline; cursor:pointer;'>сделать его президентом</div>";
					elemMenu.innerHTML +="<div onclick='DelAutor(\""+id+"\")' style='text-decoration:underline; cursor:pointer;'>удалить вместе с речами</div>";
					elemMenu.innerHTML +="<br />";
				<?}?>
				elemMenu.innerHTML += "<div id='idStruc-"+id+"' onclick='ShowSpeechOfPerson(this)' style='text-decoration:underline; cursor:pointer;'>Показать речь и высказывания</div>";
				elemMenu.innerHTML += "<div onclick='ShoseCtructure(\""+id+"\")' style='text-decoration:underline; cursor:pointer;'>Показать подчиненность</div>";
				
				document.body.appendChild(elemMenu);	
				PreviosMenu = elemMenu;
			}
			
			function deletElem(obj){//удаляем элемент меню
			//var elem = document.getElementById('CalendarDiv');
			obj.parentNode.style.display = 'none';
			obj.parentNode.parentNode.removeChild(obj.parentNode);
			PreviosMenu = null;
			}
			
			function ShowSpeechOfPerson(obj){//открываем новое окно с речью и высказываниями
				var id_avt = obj.id.split('-')[1];
				var addr = 'SpeechWind.php?avtor='+id_avt;
				var popupWin = window.open(addr,"","menubar=yes,width=900,height=600,location=no,toolbar=no,menubar=no,status=no,scrollbars=yes,resizable=yes'");
				popupWin.focus();
			} 
			
			function ShoseCtructure(id){//отображаем только структуру с выбранным автором
				document.location.href = 'PersonsStruct.php?avtor='+id;
			}
			function DelAutor(id){//удаляем автора и все его высказывания
				if (confirm('Точно удаляем автора с id-'+id+' и все его высказывания?')) { 
					//alert("удаляем автора "+id);
					var res =  ManagerOfStructure(id,"delete",'','');
					alert(res);
				}
			}
			function MakePresident(id){//поставить автору признак президента -1
					//alert("удаляем автора "+id);
					var res =  ManagerOfStructure(id,"makePresident",'','');
					alert(res);
			}
			
			function InsertFormPodch(obj){//поставить автору признак президента -1
					//alert("InsertFormPodch ");
					id = obj.parentNode.id.split('-')[1];
					obj.parentNode.innerHTML = "<span style='color:green;'>введите id кому хотите подчинить</span><input type='text' size='2' value='' /><button id='idP-"+id+"' onclick='MakePodch(this)' >ок</button>";
			}
			
			function InsertFormDescr(obj){//поставить автору признак президента -1
					//alert("InsertFormPodch ");
					id = obj.parentNode.id.split('-')[1];
					obj.parentNode.innerHTML = "<span style='color:green;'>описание автора</span><br /><input type='text' size='20' value='' /><button id='idD-"+id+"' onclick='MakeDescr(this)' >ок</button>";
			}
			function MakePodch(obj){//поставить автору признак президента -1
					//alert("MakePodch ");
					var id_who = obj.id.split('-')[1];
					var obj1 = obj.previousSibling;
					var id_act_who = obj1.value;
					//alert("id_who-"+id_who+" id_act_who-"+id_act_who);
					var res =  ManagerOfStructure(id_who,"MakePodch",id_act_who,'');
					alert(res);
			}
			function MakeDescr(obj){//добавить автору описание
					//alert("MakePodch ");
					var id_who = obj.id.split('-')[1];
					var obj1 = obj.previousSibling; //obj1 = obj1.previousSibling;
					var descr = obj1.value;
					//alert("id_who-"+id_who+" id_act_who-"+id_act_who);
					alert("описание-"+descr);
					var res =  ManagerOfStructure(id_who,"MakeDescr",'',descr);
					//alert(res);
			}
			
			function InsertFormAllias(obj){//поставить автору признак президента -1
					//alert("InsertFormPodch ");
					id = obj.parentNode.id.split('-')[1];
					obj.parentNode.innerHTML = "<span style='color:green;'>введите id автора чей это псевдоним</span><input type='text' size='2' value='' /><button id='idP-"+id+"' onclick='MakeAllias(this)' >ок</button>";
			}
			function MakeAllias(obj){//поставить автору признак президента -1
					//alert("MakePodch ");
					var id_who = obj.id.split('-')[1];
					var obj1 = obj.previousSibling;
					var id_act_who = obj1.value;
					//alert("id_who-"+id_who+" id_act_who-"+id_act_who);
					var res =  ManagerOfStructure(id_who,"MakeAllias",id_act_who,'');
					alert(res);
			}			
			
			
			function ManagerOfStructure(id_who,action,id_act_who,value){
				var _ids;
				$.ajax({
				  async: false, 
				  url: 'blocks/dinamic_scripts/ManagerOfStructure.php',
				  data: {id_who:id_who,action:action,id_act_who:id_act_who,value:value},
				  type: "POST",
				  success: function(data) {  _ids = data; }//,
				//dataType: 'json'
				 })
				
				return _ids;
			
			}
	</script>



<?include('checker.php');?>

</body>
</html>