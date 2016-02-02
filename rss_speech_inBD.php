<?
// команда для крона (в пятую минуту, раз в три часа)  5	*/3	*	*	*	cd /var/www/vhosts/probey.net/BlaBase/ ; /usr/bin/php /var/www/vhosts/probey.net/BlaBase/rss_speech_inBD.php


//Вытаскиваем последние новости в базу
header('Content-Type: text/html; charset=utf-8');//собираем всю информацию об защедщих на сайт
ini_set("max_execution_time", "160");
//set_time_limit (60); 
//htaccess :  	php_value max_execution_time 60
session_start();
include_once('startup.php');
	// Установка параметров, подключение к БД, запуск сессии.
	startup();

//	probe_ur rerty
	//Как использовать прокси в PHP функции file_get_contents?
	//http://kadomtsev.ru/kak-ispolzovat-proksi-v-php-funkcii-file_get_contents/
	

//ВАЖНО!!!!		
//прооверяем, что мы админ
	//var_dump($_SESSION['Guest_id']['id_user']);
include('variables.php');

/*
if($_SESSION['Guest_id']['id_user'] == $AdminID){  //   !!!ВАЖНО!!!
}else{die('Вы не Админ!!!');}
echo "Вы Админ продолжаем дальше";
*/
?>
находим последние речи в новостях и сохраняем в базу
<br /><a style='color:orange;' href="index.php?c=adminka">В АДМИНКУ</a><br /><br />

<br /><a style='color:orange;' href="rss_speech_inBD.php?action=ShowNewsFromBD">Показать новости в БД</a><br /><br />


<?



//Выводим последние новости из БД 
if (isset($_GET['action']) AND ($_GET['action'] =='ShowNewsFromBD')){
	if(!isset($_GET['id_news'])){
		echo "<h2>Выводим последние новости из БД </h2><br />";
		$query = "SELECT * FROM $db_news.News_foreign ORDER BY id DESC LIMIT 20 ";	//
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

$query = "SELECT * FROM $db_news.News_foreign WHERE speech = '' ORDER BY id DESC LIMIT 20  ";	//
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







	
//ищем речи в этих новостях
//$words = array(' цитирует ',' рассказал ',' рассказала ',' сказал ',' сказала ',' заявил ',' заявила ',' напомнил ',' напомнила ',' подчеркнул ',' подчеркнула ',' считает ',' подытожил ',' подытожила ',' отметил ',' отметила ',' заметил ',' заметила ',' сообщил ',' сообщила ',' добавил ',' добавила ',' указал ',' указала ',' подтвердил ',' подтвердила ',' прокомментировал ',' прокомментировала ',' продолжил ',' продолжила ');
//пробегаемся по новостям и ищем речи в них
$NewsN=0;
include_once("Get_Speech.php");//GetSpeech($text,$which){//вытаскивание речи из фрагмента речи
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
			$NewsHaveSpeech = 0; //найдена ли у новости речь				
			

			//подключаем блок поиска речей в новости
			include("Speech_In_News.php");
			
			
			
			
			
					//
					//ставим у новости speech=1, что она отработана на наличие речей
					//
					$query = "UPDATE $db_news.News_foreign SET speech = '$NewsHaveSpeech' WHERE id = '".$news['id']."' ";
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
			
			// 1.пробуем найти похожих авторов в базе			
			// 2. если находим то пробегаемся по ним, и если такой есть в новости, то сохраняем речь к нему
			//если подходящего нет, тогда сохраняем как нового автора
			
			
			$id_who = 0;
			//находим похожих авторов в базе
			$query = "SELECT * FROM  Speech_Who WHERE Who LIKE '%$Who%'"; //(в % дальнейшем % все связанное с данной таблицей можно объединить в один запрос)
			$result = mysql_query($query) or die(mysql_error());
			$n = mysql_num_rows($result);
			if($n >0){
					//$whoLike = array();
					while($row1 = mysql_fetch_assoc($result)){
						/*if(count($row1)>0){
							$whoLike[] = $row1;
						}
						*/
						//проверяем есть ли такой автор в базе
						echo "<h1>проверяем есть ли такой автор в базе ".$row1['Who']."</h1>";
						if(strpos($news['tegText'], $row1['Who'])){
							echo "<h1>в новости найден автор из базы ".$row1['Who']."</h1>";
							$id_who = $row1['id'];
							break;
						}
						
						
						//$news
					}	
			}

			
	//!!!ВАЖНО		//возможно придется востановить работу с псевдонимами
			
			if($id_who == 0){
				echo "создаем автора-";
				$query = "INSERT INTO Speech_Who (Who,Foto,descript,rukovod) VALUES ('$Who','','','0')";
				$result = mysql_query($query) or die(mysql_error());	
				$id_who = mysql_insert_id();
				echo $id_who;			
			}
			
			
			
			/*
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
			*/
		}
		

		
		//добавляем речь в БД
		$query = "INSERT INTO Speech_from_News (id_who,id_news,speech,news_title,date,rerait,url_id,link_news) VALUES ('$id_who','".$news['id']."','$speech','".$news['title']."','".$news['pubDate']."','0','0','".$news['link']."')";
		$result =  mysql_query($query) or print(mysql_error());
		if(!$result) { echo mysql_error();}
		if(mysql_insert_id()){ echo "речь добавлена";}

}



//обработка информации об авторах (сколько речей у автора, сколько не 
include('blocks/ChekAvtorsInfo.php');

?>




