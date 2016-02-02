<?php  //добавление речи из новости в БД
header('Content-Type: text/html; charset=utf-8');//собираем всю информацию об защедщих на сайт
//отмечаем что пользователь все еще на сайте
	session_start();
	include_once('../../startup.php');
	// Установка параметров, подключение к БД, запуск сессии.
	startup();

	$name =  mysql_real_escape_string($_POST['name']);	
	//echo $name;


	if($name ==''){
		die('какя-то из POST переменных пустая!!!');
	}


	//вытаскиваем всех авторов
	$query = "SELECT * FROM  Speech_Who WHERE Who LIKE '$name%' OR Who LIKE '% $name%'  ORDER BY Who";
	$result = mysql_query($query) or die(mysql_error());
		$n = mysql_num_rows($result);
		if($n >0){
			$arrWho = array();
			for ($i = 0; $i < $n; $i++)
			{
				$row = mysql_fetch_assoc($result);		
				$arrWho[] = $row;
			}
		}
	
	//var_dump($arrWho);
	

		if($arrWho){
			//создаем  json строку
			//$js = "";
			for($i=0;$i<count($arrWho);$i++){
				$arrWho[$i] = $arrWho[$i]['id']."|".$arrWho[$i]['Who'];
				$arrWho[$i] = '"'.$arrWho[$i].'"';
			}
			$js = implode(",",$arrWho);
			$js = "[".$js."]";
			echo $js;
		}


?>