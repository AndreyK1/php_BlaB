<?php  //добавление речи из новости в БД
header('Content-Type: text/html; charset=utf-8');//собираем всю информацию об защедщих на сайт
//отмечаем что пользователь все еще на сайте
	session_start();
	include_once('../../startup.php');
	// Установка параметров, подключение к БД, запуск сессии.
	startup();
	
	echo $_POST['Wh']."_____".$_POST['Sp']."_____".$_POST['NeId']."_____".$_POST['NeTi']."_____".$_POST['NeDa']."_____".$_POST['Descr_id']."_____".$_POST['Url'];

	$Wh =  mysql_real_escape_string($_POST['Wh']); $Wh = trim($Wh);
	$Sp =  mysql_real_escape_string($_POST['Sp']);
	$NeId = sprintf("%d",$_POST['NeId']);
	$NeTi =  mysql_real_escape_string($_POST['NeTi']);
	$NeDa =  mysql_real_escape_string($_POST['NeDa']);
	$Descr =  mysql_real_escape_string($_POST['Descr_id']);	
	$Url =  mysql_real_escape_string($_POST['Url']);	
	$Nkey = sprintf("%d",$_POST['Nkey']);//номер/ключ речи в новости

	if(($Wh =='') OR ($Sp =='') OR ($NeId =='') OR ($NeTi =='') OR ($NeDa =='')){
		die('какя-то из POST переменных пустая!!!');
	}


	//Вытаскиваем или автора или псевдоним или создаем автора речи
	$query = "SELECT * FROM  Speech_Who WHERE Who = '$Wh'"; //(в % дальнейшем % все связанное с данной таблицей можно объединить в один запрос)
	$result = mysql_query($query) or die(mysql_error());
	$n = mysql_num_rows($result);
	if($n >0){
		echo "вытаскиваем id автора-";
		$row = mysql_fetch_assoc($result);
		$id_who = $row['id'];
		echo "id найденого автора -".$id_who;
	}else{
		//пробуем найти псевдоним автора
		$query = "SELECT * FROM  Speech_Allias_Who WHERE Allias = '$Wh'"; //(в % дальнейшем % все связанное с данной таблицей можно объединить в один запрос)
		$result = mysql_query($query) or die(mysql_error());
		$n = mysql_num_rows($result);
		if($n >0){
			echo "вытаскиваем id автора-";
			$row = mysql_fetch_assoc($result);
			$id_who = $row['id_who'];
			echo "id найденого автора ".$id_who." по псевдониму -".$row['Allias'];
		}else{
			echo "создаем автора-";
			$query = "INSERT INTO Speech_Who (Who,Foto,descript,rukovod,LastSpeechInfo) VALUES ('$Wh','','".$Descr."','0','')";
			echo $query;
			$result = mysql_query($query) or die(mysql_error());	
			$id_who = mysql_insert_id();
			echo $id_who;
			
			
				//пересчитываем структуру авторов и закидываем в БД
				$_GET['avtor'] ='all';
				include_once('../../MakeStructAvtArr.php');	
		}
	}
	
	
	
	//die();
include('../../variables.php');
	
	//добавляем речь в БД
	$query = "INSERT INTO Speech_from_News (id_who,id_news,speech,news_title,date,rerait,link_news) VALUES ('$id_who','$NeId','$Sp','$NeTi','$NeDa','0','$Url')";
	$result = mysql_query($query);
	if(!$result) { echo mysql_error();}
	if(mysql_insert_id()){ echo "речь добавлена";}

	
	
	//вытаскиваем в новости поле ключи речей и добавляем туда этот ключ
	$query = "UPDATE $db_news.News_foreign SET speech = CONCAT(speech,'|$Nkey') WHERE id = '$NeId' ";
	$result = mysql_query($query) or die(mysql_error());	
	
	


?>