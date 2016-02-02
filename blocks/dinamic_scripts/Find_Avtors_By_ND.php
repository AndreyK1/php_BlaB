<?php  //поиск автора в БД по имени или описанию
header('Content-Type: text/html; charset=utf-8');

	session_start();
	include_once('../../startup.php');
	// Установка параметров, подключение к БД, запуск сессии.
	startup();

	$name =  mysql_real_escape_string($_POST['name']);	
	$by =  mysql_real_escape_string($_POST['by']);	
	//echo $name;


	if(($name =='') or ($by=='')){
		die('какя-то из POST переменных пустая!!!');
	}

if($by == "name"){
	//вытаскиваем всех авторов
	$query = "SELECT * FROM  Speech_Who WHERE Who LIKE '%$name%' ORDER BY Who";
}elseif($by == "descr"){
	$query = "SELECT * FROM  Speech_Who WHERE descript LIKE '%$name%' ORDER BY Who";
}else{
	DIE("[херня");
}
	//echo $query;
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
				$arrWho[$i]['descript'] = addslashes($arrWho[$i]['descript']);
			
				$arrWho[$i] = $arrWho[$i]['id']."|".$arrWho[$i]['Who']."|".$arrWho[$i]['descript'];
				$arrWho[$i] = '"'.$arrWho[$i].'"';
			}
			$js = implode(",",$arrWho);
			$js = "[".$js."]";
			echo $js;
		}


?>