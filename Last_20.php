<?
//Вытаскиваем последние новости в базу
header('Content-Type: text/html; charset=utf-8');//собираем всю информацию об защедщих на сайт
ini_set("max_execution_time", "60");
//set_time_limit (60); 
//htaccess :  	php_value max_execution_time 60
session_start();
include_once('startup.php');
	// Установка параметров, подключение к БД, запуск сессии.
	startup();
?>
<script type="text/javascript" src="js/jquery.js"></script> <!--подключаем -->
<?
	//Как использовать прокси в PHP функции file_get_contents?
	//http://kadomtsev.ru/kak-ispolzovat-proksi-v-php-funkcii-file_get_contents/
	

//ВАЖНО!!!!		
//прооверяем, что мы админ
include('variables.php');
if($_SESSION['Guest_id']['id_user'] == $AdminID){ 
	include('rss_scripts.php');
}else{
echo 'Вы не Админ!!!';
die();
}

?>
<br /><a style='color:orange;' href="index.php">Главная</a><br /><br />



<?
if(!isset($_POST['ShowLast'])){
	$ShowLast = 20;
}else{
	$ShowLast = sprintf("%d",$_POST['ShowLast']);	
}
$strShOF = "<form method='post' ><input size='10' name='ShowLast' type='text' value='".$ShowLast."' /><input type='submit' value='Показать последние' /></form>";
		

//Выводим последние новости из БД 
if (isset($_GET['action']) AND ($_GET['action'] =='LastNewsWith')){
		echo "<h2>Выводим последние новости c НАЙДЕНЫМИ речами из БД </h2>";
		echo $strShOF;
		$query = "SELECT * FROM $db_news.News_foreign WHERE speech = '1' ORDER BY id DESC LIMIT $ShowLast ";	//
					die($query);
		$result = mysql_query($query) or die(mysql_error());
		if (!$result) die();
		while($row = mysql_fetch_assoc($result)){
			if(count($row)>0){
				echo "<a href='Last_20.php?id_news=".$row['id']."'>".$row['id']." | ".$row['pubDate']." | ".$row['title']."</a><hr />";
			}	
		}	
	die();
}


if (isset($_GET['action']) AND ($_GET['action'] =='LastNewsWithOut')){
		echo "<h2>Выводим последние новости c НЕ НАЙДЕНЫМИ речами из БД </h2>";
		echo $strShOF;
		$query = "SELECT * FROM $db_news.News_foreign WHERE speech = '0' ORDER BY id DESC LIMIT $ShowLast ";	//
					//die($query);
		$result = mysql_query($query) or die(mysql_error());
		if (!$result) die();
		while($row = mysql_fetch_assoc($result)){
			if(count($row)>0){
				echo "<a href='Last_20.php?id_news=".$row['id']."'>".$row['id']." | ".$row['pubDate']." | ".$row['title']."</a><hr />";
			}	
		}	
	die();
}



if (isset($_GET['action']) AND ($_GET['action'] =='LastSpeech')){
		echo "<h2>Выводим последние речи, найденные в новостях</h2>";
		echo $strShOF;
		$query = "SELECT * FROM Speech_from_News ORDER BY id DESC LIMIT $ShowLast ";	//
					//die($query);
		$result = mysql_query($query) or die(mysql_error());
		if (!$result) die();
		while($row = mysql_fetch_assoc($result)){
			if(count($row)>0){
				$st = '';
				if($row['rerait'] == '1'){ $st = "style='color:#555;'";}
				echo "<a ".$st." href='Last_20.php?id_news=".$row['id_news']."&id_speech=".$row['id']."'>".$row['id']." | ".$row['date']." | ".$row['speech']."</a><hr />";
			}	
		}	
	die();
}


//если мы открываем новость для редактирования
if (isset($_GET['id_news'])){
	/* if(isset($_GET['id_speech'])){
		 //помечаем все речи до нее как просмотренные
		 $query = "UPDATE Speech_from_News SET rerait='1' WHERE  id<= '".$_GET['id_speech']."'";
		 $result = mysql_query($query) or die(mysql_error());	
		echo " !!! РЕЧЬ ОТМЕЧЕНА КАК ПРОСМОТРЕННАЯ !!! ";
		 
	 }
	 */
	 

		 //помечаем все речи для этой новости как просмотренные
		 $query = "UPDATE Speech_from_News SET rerait='1' WHERE  id_news = '".$_GET['id_news']."'";
		 $result = mysql_query($query) or die(mysql_error());	
		echo " !!! РЕЧи ОТМЕЧЕНы КАК ПРОСМОТРЕННые !!! ";
		 
 
	 
	//вытаскиваем новость
	$query = "SELECT * FROM $db_news.News_foreign WHERE id  = '".$_GET['id_news']."' ";	//
			//die($query);
	$result = mysql_query($query) or die(mysql_error());
	if (!$result) die();
	$news = mysql_fetch_assoc($result);
	
			echo "<b>ИСТОЧНИК</b> - ".$news['fromN']."<br />";
			echo "<b>НАЗВАНИЕ</b> - ".$news['title']."<br />";
			echo "<b>ОПИСАНИЕ</b> - ".$news['description']."<br />";
			echo "<b>ДАТА РУБЛИКАЦИИ</b> - ".$news['pubDate']."<br />";
			if($news['img'] == ''){
				echo "<b>КАРТИНКА</b> - НЕТ<br />";
			}else{
				echo "<b>КАРТИНКА</b> - <img src='".$news['img']."' /><br />";
			}
			echo "<b>ВЫРЕЗАННАЯ РЕЧЬ</b> - ".$news['speech']."<br />";
			echo "<b>ТЕКСТ:</b><hr /> ";
//			echo $row['plainText']."<br />";			
			
			include_once("Get_Speech.php");//GetSpeech($text,$which){//вытаскивание речи из фрагмента речи

			//подключаем блок поиска речей в новости
			include("Speech_In_News.php");
			
			
		//				var_dump($speachArr);
		
		var_dump($speachArrWithOut);
			//выводим всю найженную речь без авторов, и авторов без речи
			foreach($speachArrWithOut as $sp){
				//выделяем найденное в тексте
				$news['tegText'] = str_replace($sp['speech'],"<b style='color:red'>".$sp['speech']."</b>", $news['tegText']);
				$news['tegText'] = str_replace($sp['Who'],"<b style='color:red'>".$sp['Who']."</b>", $news['tegText']);
			}			
		

			//выводим всю найженную речь
			foreach($speachArr as $sp){
				?>
				<!--<b>Найдена речь: </b> </b> <input size='60' id='Wh-<?=$sp['Key']?>' type='text' value='<?=$sp['Who']?>' /><br />
				<!--<b>Сама речь: </b><br /> <textarea COLS='90' ROWS='3' id='Sp-<?=$sp['Key']?>' ><?=$sp['speech']?></textarea><br />
				<!--<b>Кто такой: </b><br /> <textarea COLS='90' ROWS='1' id='De-<?=$sp['Key']?>' ></textarea><br />-->
			<?
				//выделяем найденное в тексте
				$news['tegText'] = str_replace($sp['speech'],"<b style='color:green'>".$sp['speech']."</b>", $news['tegText']);
				$news['tegText'] = str_replace($sp['Who'],"<b style='color:purple'>".$sp['Who']."</b>", $news['tegText']);
			}			
			
			
			
			//вытаскиваем из базы найденную речь
			echo "<h2>Выводим речь из базы</h2>";
			//LEFT JOIN (SELECT count(*) as count,id_who FROM Speech_from_News WHERE id_who =  )Speech_Who w ON sp.id_who=w.id
			$query = "SELECT sp.*,w.Who,w.descript FROM Speech_from_News sp 
				LEFT JOIN Speech_Who w ON sp.id_who=w.id
			
				WHERE id_news = '".$_GET['id_news']."' ";	//
						//die($query);
			$result = mysql_query($query) or die(mysql_error());
							//var_dump($result);
			if (!$result) die();
			while($row = mysql_fetch_assoc($result)){
				if(count($row)>0){
					//Вытаскиваем чьим псевдонимом мог бы быть автор
					$query = "SELECT * FROM Speech_Who WHERE Who LIKE '%".$row['Who']."%' ";	//
							//die($query);
					$result1 = mysql_query($query) or die(mysql_error());
					$whoLike = array();
					while($row1 = mysql_fetch_assoc($result1)){
						if(count($row1)>0){
							$whoLike[] = $row1;
						}
					}					
					
					//echo "<a href='Last_20.php?id_news=".$row['id_news']."'>".$row['id']." | ".$row['speech']."</a><hr />";
					?>
						<b>id речи в базе: </b> <input size='10' type='text' value='<?=$row['id']?>' /><br />
						<b>id автора в базе: </b> <input size='10' type='text' value='<?=$row['id_who']?>' /><b>Автор в базе: </b> </b> <input size='60' id='Wh-<?=$row['id']?>' type='text' value='<?=$row['Who']?>' /><button onclick="ChangeAvtor('<?=$row['id_who']?>','<?=$row['id']?>')">перезаписать автора</button><button onclick="DeleteAvtor('<?=$row['id_who']?>')">удалить автора вместе с речами</button><br />
						<b>описание автора: </b>  <input size='100' type='text' id='De-<?=$row['id']?>' value='<?=$row['descript']?>' /><button onclick="ChangeDescr('<?=$row['id_who']?>','<?=$row['id']?>')">перезаписать описание</button><br />
						<b>похожие авторы: <?=count($whoLike)?></b><?if(count($whoLike)>0){?>
							<select id='selMod-<?=$row['id']?>'  name='avtorNameCh'  >
								<?foreach($whoLike as $w){
									if($w['id'] != $row['id_who']){?>
										<option value='<?=$w['id']?>' ><?=$w['Who']?> | <?=$w['descript']?></option>
									<?}
								}?>
							</select><button onclick="SelectAvtorLike('<?=$row['id_who']?>','<?=$row['id']?>')">сделать псевдонимом</button>
							<?}else{ echo " нету ";}?>
						<br />
				        <b>Сама речь в базе: </b><br /> <textarea COLS='90' ROWS='3' id='Sp-<?=$row['id']?>' ><?=$row['speech']?></textarea><button onclick="ChangeSpeech('<?=$row['id']?>')">перезаписать речь</button><button onclick="DeleteSpeech('<?=$row['id']?>')">удалить речь</button><br /><hr /	>
						
					<?
				}	
			}
			?><script>// удаление/редактирование речей/авторов
				function SelectAvtorLike(id_avt,id_sp){//делаем автора псевдонимом выбраного
						//var NewAvt = document.getElementById('Wh-'+id_sp).value;
						var obj = document.getElementById('selMod-'+id_sp);  
						var id_act_who = obj.options[obj.selectedIndex].value;
						//alert(id_act_who);
						var res =  ManagerOfStructure(id_avt,"MakeAllias",id_act_who,'');
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
				
				
				
				

				function DeleteAvtor(id_avt){//удаление автора вместе с речами
							var colich = ConnWithPHP('ColSpAvt',id_avt,'rrr');					
						
						if (confirm("Вы уверены, что хотите удалить автора вместе сречами? У автора " +colich+ " речей")) {
							//alert("Удаляем автора! "+ id_avt);
							ConnWithPHP('DelAvt',id_avt,'rrr');
							
							
						}					
				 }
				 
				 function ChangeAvtor(id_avt,id_sp){//изменение имени автора
							alert("Редактируем автора! "+ id_avt);
							var NewAvt = document.getElementById('Wh-'+id_sp).value;
							ConnWithPHP('ChanAvt',id_avt,NewAvt);
				
				 }	

				 function DeleteSpeech(id_sp){//удаляем речь
							alert("Удаляем речь! "+ id_sp);
							ConnWithPHP('DeleteSpeech',id_sp,'rrr');
				
				 }		

				 function ChangeSpeech(id_sp){//изменение речи
							alert("Редактируем речь! "+ id_sp);
							var NewSp = document.getElementById('Sp-'+id_sp).value;
							ConnWithPHP('ChangeSpeech',id_sp,NewSp);
				
				 }
				 
				 
				 function ChangeDescr(id_avt,id_sp){//изменение описания автора
							//alert("Редактируем описание! "+ id_avt);
							var NewDe = document.getElementById('De-'+id_sp).value;
							alert("Редактируем описание! "+ id_avt+ NewDe);
							ConnWithPHP('ChangeDescr',id_avt,NewDe);
				
				 }

				
				function ConnWithPHP(Act,ids,Val){
					var _ids;
					$.ajax({
					  async: false, 
					  url: 'blocks/dinamic_scripts/Avtor_Speech.php',
					  data: {Action:Act,id:ids,Value:Val},
					  type: "POST",
					  success: function(data) {  _ids = data; }//,
					//dataType: 'json'
					 })
					
					alert(_ids);
					return	_ids;
				}
				
				
				function SaveSpeechAndAvtor(n){//сохраняем речь и автора в БД
					var sp1 = document.getElementById('Sp1-'+n).innerHTML;
					var wh1 = document.getElementById('Wh1-'+n).value;
					alert(sp1+" !!!!!!!!!!! "+wh1);
					
					var _ids;
					$.ajax({
					  async: false, 
					  url: 'blocks/dinamic_scripts/Save_Speech.php',
					  data: {Wh:wh1,Sp:sp1,NeId:'<?=$_GET['id_news']?>',NeTi:'<?=$news['title']?>',NeDa:'<?=$news['pubDate']?>',Nkey:'1',Descr_id:'',Url:'<?=$news['link']?>'},
					  type: "POST",
					  success: function(data) {  _ids = data; }//,
					//dataType: 'json'
					 })
					
					alert(_ids);
				}
			
			</script>
	<?
			
			
			
			
			echo "<hr />Чтобы искать фразы в тексте: 1.выделите часть фразы 2. нажмите ctrl+a (только в Chrome) 3. если найдет - то перекинет и выделит 4. Вернутся назад ctrl+z
			<hr /><hr /><hr /><br /><b onclick='ShoNews(\"".$NewsN."\")' >Показать текст</b><div style='display:block;' id='n-'><hr />".$news['tegText']."</div>";
			?><script>
			 function ShoNews(id){//показ скрытие текста новости
			 //alert(id);
				if(document.getElementById('n-'+id).style.display == 'block'){
					document.getElementById('n-'+id).style.display = 'none';
				}else{
					document.getElementById('n-'+id).style.display = 'block';
				}
			 }
			</script>
	<?
}

// делаем сохранение речи и автора в БД
function SaveInBD($Who,$speech,$news){}
?>


<script>
//при сочетании клавишь (ctrl была нажата a) и выделенном тексте - выделяем его на странице (работает в хроме)
var NExpText = 1;//какой по счету текст выделяется
var SelElementId; //Элемент у которого был выделен текст
document.addEventListener('keydown', function(event){//обработка сочетания клавишь
	//alert(txt);
	//alert(event.ctrlKey + "---"+event.keyCode);
	if(event.ctrlKey ){
	
		   var txt = '';//выделенный текст
		if (window.getSelection) {
			txt = window.getSelection();
		} else if (document.getSelection) {
			txt = document.getSelection();
		} else if (document.selection) {
			txt = document.selection.createRange().text;
		}


	
		if(event.keyCode ==65){ //если вместе с ctrl была нажата a, то пытаемся найти такие фразы в тексте новости и перейти к н ним
		NExpText++;
		
			//родительский элемент выделенного текста
			var SelElem = getSelection().focusNode.parentNode;
			//alert("выдел элемент - "+SelElem.innerHTML);
			SelElementId = SelElem.id;

			
			
				<?/*if($_SESSION['Guest_id']['id_user'] == $AdminID){ //если я админ	?>
					elemMenu.innerHTML += "<div id='idWh-"+id+"' onclick='ShowFotoMen(this)' style='text-decoration:underline; cursor:pointer;'>Прикрепить фото</div>";
				<?}*/?>
			
			//alert("ctrl+A----"+txt);
			//require("blocks/dinamic_scripts/EventAddMenu.js", function(){ ShowEventAddMenu(txt); });
			//ShowEventAddMenu(txt);
			
			//
			var news = document.getElementById('n-').innerHTML;
			//alert(news);
			var newTxt = news.split(txt).join("<input id='exp-"+NExpText+"' style='font-size:20px;' value='"+txt+"' />"+txt+"</b>");
			//alert(newTxt);
			document.getElementById('n-').innerHTML = newTxt;
			//
			
			//alert(document.getElementById('exp-'+NExpText).innerHTML);
			document.getElementById('exp-'+NExpText).focus();
			//document.getElementById('n-').focus();
		}
		
		
		if(event.keyCode ==90){ //если вместе с ctrl была нажата z, то пытаемся перейти назад к элементу где первоначально был выделен текст (а точнее к полю инпута автора)
			if(SelElementId){
				var id = SelElementId.split("-");
				//alert(id[0]+id[1]);
				if(id[0] == "Sp1"){//если выделение было из найденных текстов, то пытаемся перейти к инпуту автора
					document.getElementById('Wh1-'+id[1]).focus();
				}
			
			}
		}
	}
	
	

});



</script>






