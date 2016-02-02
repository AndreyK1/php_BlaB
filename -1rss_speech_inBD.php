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

	
	//Как использовать прокси в PHP функции file_get_contents?
	//http://kadomtsev.ru/kak-ispolzovat-proksi-v-php-funkcii-file_get_contents/
	

//ВАЖНО!!!!		
//прооверяем, что мы админ
	//var_dump($_SESSION['Guest_id']['id_user']);
include('variables.php');
if($_SESSION['Guest_id']['id_user'] == $AdminID){  //   !!!ВАЖНО!!!
}else{die('Вы не Админ!!!');}
echo "Вы Админ продолжаем дальше";

?>
<br /><a style='color:orange;' href="index.php?c=adminka">В АДМИНКУ</a><br /><br />

<br /><a style='color:orange;' href="rss_speech_inBD.php?action=ShowNewsFromBD">Показать новости в БД</a><br /><br />


<?
//слова по которым ведется поиск речей
$words = array(' цитирует ',' рассказал ',' рассказала ',' сказал ',' сказала ',' заявил ',' заявила ',' напомнил ',' напомнила ',' подчеркнул ',' подчеркнула ',' считает ',' подытожил ',' подытожила ',' отметил ',' отметила ',' заметил ',' заметила ',' сообщил ',' сообщила ',' добавил ',' добавила ',' указал ',' указала ',' подтвердил ',' подтвердила ',' прокомментировал ',' прокомментировала ',' продолжил ',' продолжила ');



//Выводим последние новости из БД 
if (isset($_GET['action']) AND ($_GET['action'] =='ShowNewsFromBD')){
	if(!isset($_GET['id_news'])){
		echo "<h2>Выводим последние новости из БД </h2><br />";
		$query = "SELECT * FROM News_foreign ORDER BY id DESC LIMIT 20 ";	//
					//die($query);
		$result = mysql_query($query) or die(mysql_error());
		if (!$result) die();
		while($row = mysql_fetch_assoc($result)){
			$sps = '';
			if($row['speech'] !=''){ $sps ="<b title='речь для новости в базе есть' style='color:red;'>*</b>";  }
			if(count($row)>0){
				echo "<a href='rss_t.php?action=ShowNewsFromBD&id_news=".$row['id']."'>".$row['title']."</a>".$sps."<hr />";
				
			}	
		}	
	}
	die();
	
}


//берем из базы последние 20 новостей, у которых не искалась речь
echo "<h2>Выводим последние 20 новостей, у которых не искалась речь</h2><br />";
$aqrrOfNews = Array(); //список новостей

$query = "SELECT * FROM News_foreign WHERE speech = '' ORDER BY id DESC LIMIT 20  ";	//
			//die($query);
$result = mysql_query($query) or die(mysql_error());
if (!$result) die();
while($row = mysql_fetch_assoc($result)){
		$aqrrOfNews[] = $row;
		echo "<a href='rss_t.php?action=ShowNewsFromBD&id_news=".$row['id']."'>".$row['title']."</a>".$sps."<hr />";
}

if(count($aqrrOfNews) <1){
	echo "<p>таких новостей в базе нет!</p>";
}	
	

//var_dump($aqrrOfNews);





function GetSpeech($text,$which){//вытаскивание речи из фрагмента речи
	//$which - какой член массива нам нужен
	$arr = explode('"',$text);
	$need = '';
	$arrZnak = array('.','!','?');
	
	if(count($arr>2)){
		if($which == 'left'){
			//проверяем наличие окончаний предложения
			$zamen = str_replace($arrZnak,'ĀQÐ', $arr[count($arr)-1]);//2
			if($zamen == $arr[count($arr)-1]){//если между ковычками и словом не было таких знаков
				$need = $arr[count($arr)-2];
			}
		}elseif($which == 'right'){
			//проверяем наличие окончаний предложения
			$zamen = str_replace($arrZnak,'ĀQÐ', $arr['0']);//2
			if($zamen == $arr['0']){//если между ковычками и словом не было таких знаков
				$need = $arr['1'];
			}
		}
	}
 return $need;
}

	
//ищем речи в этих новостях
//$words = array(' цитирует ',' рассказал ',' рассказала ',' сказал ',' сказала ',' заявил ',' заявила ',' напомнил ',' напомнила ',' подчеркнул ',' подчеркнула ',' считает ',' подытожил ',' подытожила ',' отметил ',' отметила ',' заметил ',' заметила ',' сообщил ',' сообщила ',' добавил ',' добавила ',' указал ',' указала ',' подтвердил ',' подтвердила ',' прокомментировал ',' прокомментировала ',' продолжил ',' продолжила ');
//пробегаемся по новостям и ищем речи в них
$NewsN=0;
foreach($aqrrOfNews as $news){
	$NewsN++;
		if(count($news)>0){		
			//описываем новость
		//	echo "<a href='rss_t.php?action=ShowNewsFromBD&id_news=".$news['id']."&findSpeach=1'>Найти речь</a><hr />";
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
//			echo "<hr /> ";
			//echo $row['tegText']."<br />";
			
			
			//достаем речь
			$speachArr = array();
			//$who = array();
			
			//избавляемся от длинного тире
			$ttte = str_replace('&mdash;','-', $news['plainText']);
			//избавляемся от запятых
			$ttte = str_replace(',',' Êµ ', $ttte);
			//echo $ttte;
			$ttte = str_replace($words,'ĀÐ', $ttte);//2
			$arr= explode('ĀÐ',$ttte); //2
			
			//var_dump($arr);
	
			$NewsHaveSpeech = 0; //найдена ли у новости речь	
			
			for($z=1; $z<count($arr);$z++){
				//ставим запятые назад
				$arrBack = array(' Êµ ','Êµ ',' Êµ');
				$arr[$z-1] = str_replace($arrBack,',', $arr[$z-1]);//2
				$arr[$z] = str_replace($arrBack,',', $arr[$z]);//2
				//if(count($arr)>1){//значит слово было//2
				
				//
				//ищем речь
				//
				$speech = '';//чье высказывание/речь
				//ищем ближайщие кавычки
				//echo "PART_Left------".$arr[$z-1]."<br />";
				//echo "PART_Right-----".$arr[$z]."<br />";
				$arrLeft = explode('"',$arr[$z-1]);
				$arrRight = explode('"',$arr[$z]);
				
				//сравниваем до каких кавычек ближе
						$n = $n1 = $n2 = '';
						if(count($arrLeft) > 1){
//							echo "arrLeft-".$arrLeft[count($arrLeft)-1];
							$n1 = mb_strlen($arrLeft[count($arrLeft)-1],'UTF-8');
//							echo "-n1-:".$n1;
							if($n1 > 50){ $n1='';}
						}
						if(count($arrRight) > 1){
//							echo "<br />arrRight-".$arrRight['0'];
							$n2 = mb_strlen($arrRight['0'],'UTF-8');
//							echo "-n2-:".$n2;
							if($n2 > 50){ $n2='';}
						}
						
						
						if((($n1 < $n2) AND ($n1!=='')) OR ($n2==='')){
						//if((($n1 < $n2) AND ($n2 >=0) AND ($n1 >=0)) or ($n2 < 0)){//ищем где скобки ближе
							//if($n1 > $n2){//ищем где скобки ближе
//								echo "<br />----LLLLLLLLLLLLL----------";
							$n = $n2;
							$speech = GetSpeech($arr[$z-1],'left');
						}else{
//								echo "<br />----rrrrrrrrr----------";
							$n = $n1;
							$speech = GetSpeech($arr[$z],'right');
						}
						
						if(mb_strlen($speech,'UTF-8')<30){//отсеивание речи менее стольки-то символов
							$speech = '';
						}
						
					//	
					//ищем Автора
					//
					$Who = '';				
					//ищем где ближе заглавня буква
					$Le = $arr[$z-1]; 
					$Ri = $arr[$z]; 
					//заменяем ограничивающие знаки
					$arrRep = array('.','!','?','"');
					$Le = str_replace($arrRep,'ØÙ', $Le);
					$Ri = str_replace($arrRep,'ØÙ', $Ri);
					
					//ограничивае текст
					$Le = explode('ØÙ',$Le); $Le=$Le[count($Le)-1];
					$Ri = explode('ØÙ',$Ri); $Ri=$Ri[0];
					//раззбиваем на слова
					$Le = str_replace(',','', $Le);
					$Ri = str_replace(',','', $Ri);

					$Le = explode(' ',$Le);
					$Ri = explode(' ',$Ri);
//					echo "Ri-".var_dump($Ri)."-=";
//					echo "Le-".var_dump($Le)."-=";				
						//переворачиваем массив справа
						$Le = array_reverse($Le);
					//и ишем с какой строны быстрее появится слово С заглавной буквы
					$n = $nR = $nL = '';
					for($i=0;$i<count($Ri);$i++){
						if((bool)preg_match('/^[А-Я]{1}[а-я]{3,20}$/u',$Ri[$i])){
							$nR = $i; break;
						}
					}
					for($i=0;$i<count($Le);$i++){
					
						if((bool)preg_match('/^[А-Я]{1}[а-я]{3,20}$/u',$Le[$i])){
//							echo "ffff-".$Le[$i];
							$nL = $i; break;
						}
					}
					
//					echo "<br />nL-".$nL." nR-".$nR."<br />";
					if(min($nL,$nR) > 5){//проверяем, чтобы было не слишком далеко
						//$n = -1;
					}else{
						//nL- !nR-1
						//nL- !nR-0
						if((($nL < $nR) AND ($nL!=='')) OR ($nR==='')){
						//if((($nR > $nL) AND ($nR!=0)) or ($nL == 0)){//ищем где скобки ближе | если справа дальше но не первое слово или первое слово слева, то
//							echo "lll";
							$Who = $Le[$nL];
							if((bool)preg_match('/^[А-Я]{1}[а-я]{3,20}$/u',$Le[$nL+1])){//если след-ее слово тоже заглавное, то берем и его
								$Who += $Le[$nL+1];
								//echo $nL;
							}
						}else{
							$Who = $Ri[$nR];
//						echo "rrr".$Who;	
							if((bool)preg_match('/^[А-Я]{1}[а-я]{3,20}$/u',$Ri[$nR+1])){//если след-ее слово тоже заглавное, то берем и его
								$Who = $Who." ".$Ri[$nR+1];
								//echo $nR;
								//echo "rrr".$Who;
							}
						}
					}
					
					
					//
					//если и речь и автор найден
					//
echo  "<b style='color:red;'>Speech</b>- ".$speech." <br /><b style='color:red;'>Who</b>- ".$Who."<br /><br />";					
					

					
					if(($speech != '') AND ($Who !='')){
						$ArrA['speech'] = $speech;
						$ArrA['Who'] = $Who;
						$ArrA['Key'] = $z;
						$speachArr[] = $ArrA;
						
						$NewsHaveSpeech = 1; 
						SaveInBD($Who,$speech,$news);	
							
							
							
							//вытаскиваем в новости поле ключи речей и добавляем туда этот ключ
						//	$query = "UPDATE News_foreign SET speech = CONCAT(speech,'|$Nkey') WHERE id = '$NeId' ";
						//	$result = mysql_query($query) or die(mysql_error());
						
						
					}


			
			}
					//
					//ставим у новости speech=1, что она отработана на наличие речей
					//
					$query = "UPDATE News_foreign SET speech = '$NewsHaveSpeech' WHERE id = '".$news['id']."' ";
					$result =  mysql_query($query) or print(mysql_error());			
			
			//создаем массив речей, которые уже бобавлены в таблицу
			$arrSp = explode('|',$news['speech']);

			var_dump($speachArr);

			//выводим всю найженную речь
			foreach($speachArr as $sp){
				?>
				<b>Найдена речь: </b> </b> <input size='60' id='Wh-<?=$sp['Key']?>' type='text' value='<?=$sp['Who']?>' /><br />
				<b>Сама речь: </b><br /> <textarea COLS='90' ROWS='3' id='Sp-<?=$sp['Key']?>' ><?=$sp['speech']?></textarea><br />
				<!--<b>Кто такой: </b><br /> <textarea COLS='90' ROWS='1' id='De-<?=$sp['Key']?>' ></textarea><br />-->
			<?
				//выделяем найденное в тексте
				$news['tegText'] = str_replace($sp['speech'],"<b style='color:green'>".$sp['speech']."</b>", $news['tegText']);
				$news['tegText'] = str_replace($sp['Who'],"<b style='color:red'>".$sp['Who']."</b>", $news['tegText']);
			}			
			
			
			echo "<b onclick='ShoNews(\"".$NewsN."\")' >Показать текст</b><div style='display:none;' id='n-".$NewsN."'><hr />".$news['tegText']."</div>";
			?><script>
			 function ShoNews(id){//показ скрытие текста новости
			 //alert(id);
				if(document.getElementById('n-'+id).style.display == 'block'){
					document.getElementById('n-'+id).style.display = 'none';
				}else{
					document.getElementById('n-'+id).style.display = 'block';
				}
			 }
			</script><?
			
			echo "<hr style='color:red' /><hr style='color:red' /><hr style='color:red' /><br /> ";
		}
}

// делаем сохранение речи и автора в БД
function SaveInBD($Who,$speech,$news){
	//
	//делаем сохранение речи в БД
	//
		//Вытаскиваем или автора или псевдоним или создаем автора речи
		$query = "SELECT * FROM  Speech_Who WHERE Who = '$Who'"; //(в % дальнейшем % все связанное с данной таблицей можно объединить в один запрос)
		$result = mysql_query($query) or die(mysql_error());
		$n = mysql_num_rows($result);
		if($n >0){
			echo "вытаскиваем id автора-";
			$row = mysql_fetch_assoc($result);
			$id_who = $row['id'];
			echo "id найденого автора -".$id_who;
		}else{
			//пробуем найти псевдоним автора
			$query = "SELECT * FROM  Speech_Allias_Who WHERE Allias = '$Who'"; //(в % дальнейшем % все связанное с данной таблицей можно объединить в один запрос)
			$result = mysql_query($query) or die(mysql_error());
			$n = mysql_num_rows($result);
			if($n >0){
				echo "вытаскиваем id автора-";
				$row = mysql_fetch_assoc($result);
				$id_who = $row['id_who'];
				echo "id найденого автора ".$id_who." по псевдониму -".$row['Allias'];
			}else{
				echo "создаем автора-";
				$query = "INSERT INTO Speech_Who (Who,Foto,descript,rukovod) VALUES ('$Who','','','0')";
				$result = mysql_query($query) or die(mysql_error());	
				$id_who = mysql_insert_id();
				echo $id_who;
			}
		}
		

		
		//добавляем речь в БД
		$query = "INSERT INTO Speech_from_News (id_who,id_news,speech,news_title,date,rerait,url_id,link_news) VALUES ('$id_who','".$news['id']."','$speech','".$news['title']."','".$news['pubDate']."','0','0','".$news['link']."')";
		$result =  mysql_query($query) or print(mysql_error());
		if(!$result) { echo mysql_error();}
		if(mysql_insert_id()){ echo "речь добавлена";}

}


?>




