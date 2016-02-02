<?php  //все действия со структурой авторов в БД
header('Content-Type: text/html; charset=utf-8');//собираем всю информацию об защедщих на сайт
//отмечаем что пользователь все еще на сайте
	session_start();
	include_once('../../startup.php');
	// Установка параметров, подключение к БД, запуск сессии.
	startup();
	
	echo $_POST['id_who']."_____".$_POST['action']."_____".$_POST['id_act_who'];

	$action =  mysql_real_escape_string($_POST['action']);
	$id_who = sprintf("%d",$_POST['id_who']);//в отношении кого эти действия
	$id_act_who = sprintf("%d",$_POST['id_act_who']);//в привязке к кому эти действия

	$value =  mysql_real_escape_string($_POST['value']); //если передавалось какое значение
		$value = htmlspecialchars ($value);

	
	if($id_act_who !='0'){ 
		//проверяем, что такой пользователь вообще есть
		echo "id_act_who-".$id_act_who; 
		$query = "SELECT * FROM  Speech_Who WHERE id = '$id_act_who'"; //(в % дальнейшем % все связанное с данной таблицей можно объединить в один запрос)
		$result = mysql_query($query) or die(mysql_error());
		$n = mysql_num_rows($result);
		if($n >0){
			echo "  ок id автора есть  ";
		}else{ die("    id автора (2) к которому применяется действие не существует!  ");}
	}


	if($id_who == '0'){die("    id автора (1) не существует!  ");}
	
	if($action == 'makePresident'){
		//делаем его президентом
		$query = "UPDATE Speech_Who SET rukovod='-1' WHERE id = '$id_who'";
		$result = mysql_query($query) or die(mysql_error());
		echo " сделан президентом! ";
	}elseif($action == 'delete'){
		//удаляем автора и его речи
		$query = "DELETE FROM Speech_Who WHERE id = '$id_who'" ;
		$result = mysql_query($query) or die(mysql_error());
		
		$query = "DELETE FROM Speech_from_News WHERE id_who	 = '$id_who'" ;
		$result = mysql_query($query) or die(mysql_error());
		echo $query;
		echo " удалено! ";
	}elseif($action == 'MakePodch'){
		//переподчиняем автора другому автору
		if($id_act_who !='0'){
			$query = "UPDATE Speech_Who SET rukovod='$id_act_who' WHERE id = '$id_who'";
			$result = mysql_query($query) or die(mysql_error());
			echo " переподчинен! ";
		}
	}elseif($action == 'MakeAllias'){
		//делаем автора псевдонимом (переносим его в другую таблицу, а его речи присваиваем владельцу псевдонима)
		if($id_act_who !='0'){
			//вытаскиваем его имя
			$query = "SELECT * FROM  Speech_Who WHERE id = '$id_who'"; //(в % дальнейшем % все связанное с данной таблицей можно объединить в один запрос)
			$result = mysql_query($query) or die(mysql_error());
			$n = mysql_num_rows($result);
			if($n >0){
				$row = mysql_fetch_assoc($result);
				echo "имя -".$row['Who'];
			}else{die(" косяк-1 ");}
			
			//переносим его речи 
			$query = "UPDATE Speech_from_News SET id_who='$id_act_who' WHERE  id_who= '$id_who'";
			//echo $query;
			$result = mysql_query($query) or die(mysql_error());	

			//создаем псевдоним
			$query = "INSERT INTO Speech_Allias_Who (Allias,id_who) VALUES ('".$row['Who']."','$id_act_who')";
			$result = mysql_query($query) or die(mysql_error());

			//удаляем из таблицы авторов			
			$query = "DELETE FROM Speech_Who WHERE id = '$id_who'" ;
			$result = mysql_query($query) or die(mysql_error());			
			
			//$query = "UPDATE Speech_Who SET rukovod='$id_act_who' WHERE id = '$id_who'";
			//$result = mysql_query($query) or die(mysql_error());
			echo " сделан псевдонимом! ";
		}
	}elseif($action == 'MakeDescr'){
		//меняем описание автора
		if($id_who !='0'){
			$query = "UPDATE Speech_Who SET descript='$value' WHERE id = '$id_who'";
			echo $query;
			$result = mysql_query($query) or die(mysql_error());
			echo " переподчинен! ";
		}
	}
	
	//пересчитываем структуру авторов и закидываем в БД
	$_GET['avtor'] ='all';
	include_once('../../MakeStructAvtArr.php');	



	
	die();
	

	
	

	
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
			$query = "INSERT INTO Speech_Who (Who,Foto,descript,rukovod,LastSpeechInfo) VALUES ('$Wh','','','0','')";
			$result = mysql_query($query) or die(mysql_error());	
			$id_who = mysql_insert_id();
			echo $id_who;
		}
	}
	
	
	
	//die();
include('../../variables.php');
	
	//добавляем речь в БД
	$query = "INSERT INTO Speech_from_News (id_who,id_news,speech,news_title,date,rerait) VALUES ('$id_who','$NeId','$Sp','$NeTi','$NeDa','0')";
	$result = mysql_query($query);
	if(!$result) { echo mysql_error();}
	if(mysql_insert_id()){ echo "речь добавлена";}
	

	
	//вытаскиваем в новости поле ключи речей и добавляем туда этот ключ
	$query = "UPDATE $db_news.News_foreign SET speech = CONCAT(speech,'|$Nkey') WHERE id = '$NeId' ";
	$result = mysql_query($query) or die(mysql_error());	
	
	


?>