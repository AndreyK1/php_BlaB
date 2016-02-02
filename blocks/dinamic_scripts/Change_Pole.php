<?php  //изменение одного параметра в одной таблице
header('Content-Type: text/html; charset=utf-8');//собираем всю информацию об защедщих на сайт
//отмечаем что пользователь все еще на сайте
	session_start();
	include_once('../../startup.php');
	// Установка параметров, подключение к БД, запуск сессии.
	startup();
	
	echo $_POST['Table']."_____".$_POST['PoleVal']."_____".$_POST['Value']."_____".$_POST['PoleWhere']."_____".$_POST['Where'];

	$Table =  mysql_real_escape_string($_POST['Table']);
	$PoleVal =  mysql_real_escape_string($_POST['PoleVal']);
	$Value =  mysql_real_escape_string($_POST['Value']);
	$PoleWhere =  mysql_real_escape_string($_POST['PoleWhere']);
	$Where =  mysql_real_escape_string($_POST['Where']);


	if(($Table =='') OR ($PoleVal =='') OR ($Value =='') OR ($PoleWhere =='') OR ($Where =='')){
		die('какя-то из POST переменных пустая!!!');
	}


	
	
	//Меняем параметр
	$query = "UPDATE $Table SET $PoleVal = '$Value'  WHERE $PoleWhere = '$Where'"; 
	$result = mysql_query($query) or die(mysql_error());
	
	
	


?>