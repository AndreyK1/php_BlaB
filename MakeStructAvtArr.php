<?
//создание массива структуры всех авторов


//перед запуском скрипта обязательно должна быть $_GET['avtor']. Если для перестройки таблицы и записи ее в БД $_GET['avtor'] =='all'
	
session_start();
include_once('startup.php');
	// Установка параметров, подключение к БД, запуск сессии.
	startup();





	
	
	if($_GET['avtor'] !='all'){
		$_GET['avtor'] = sprintf("%d",$_GET['avtor']);
		$avtor='';
		//вытаскиваем id авторов 	
		if(isset($_GET['avtor'])){
			$arrAvt = explode('|',$_GET['avtor']);
			$arrAvt = array_diff($arrAvt, array(''));
			foreach($arrAvt as $avt){
				$id_avtor = sprintf("%d",$avt);//номер/ключ речи в новости
				$SpeechArr[] = array('id_avtor'=>$id_avtor);//7	
			}
		$avtor=$_GET['avtor'];
		}
		
		$titleStr ='';
	}else{
		$avtor=$_GET['avtor'];
	}
	
	
	
	//echo "<br /><br />avtor;".$avtor."<br /><br />";


$arrOfGlRuk = array();//массив всех главных руководителей	
	
	if($avtor == '0'){ die("неправильное id  автора!");}
	
	
	
	
	if($avtor !='all'){
		//берем SELECT руководителя автора пока его руководитель не станет 0 или -1(президент)
		function GetRuk($id_avt){
			//$arrWho = array();
			$query = "SELECT *,'ruck' as str FROM  Speech_Who WHERE id = '$id_avt'";
			//UNION SELECT *,'ruck1' as str FROM  Speech_Who WHERE id = (SELECT rukovod FROM  Speech_Who WHERE id = (SELECT rukovod FROM  Speech_Who WHERE id = '$avtor'))";  // rukovod  ='$avtor'  ORDER BY Who";
			$result = mysql_query($query) or die(mysql_error());
				$n = mysql_num_rows($result);
				if($n >0){
						$row = mysql_fetch_assoc($result);		
						return $row; //['id'];
				}	
		}

		$id_avt = $avtor;
		$id_gl_ruk ='';
		

		//ишем самого главного в цепочке
		for($i=0; $i<10 ;$i++ ){
			//echo "<br>".$i."<br>";
			$Who = GetRuk($id_avt);
			//var_dump($Who);		
			//echo "<br /><br /><br />123-3";
			if(isset($Who['id'])){
				$id_gl_ruk = $Who['id'];
				if($Who['rukovod'] > 0){
					$id_avt = $Who['rukovod'];
					continue;
				}else{///echo "shit";
				}
			}
			break;
		}
		
		$arrOfGlRuk[] = $id_gl_ruk;
		if($id_gl_ruk ==''){die("автора стаким id  нет!");}
			
	}else{//вытаскиваем всез самых главных руководителей (-1)
			$query = "SELECT * FROM  Speech_Who WHERE rukovod <1 ORDER BY  rukovod"; // '-1'";
			//UNION SELECT *,'ruck1' as str FROM  Speech_Who WHERE id = (SELECT rukovod FROM  Speech_Who WHERE id = (SELECT rukovod FROM  Speech_Who WHERE id = '$avtor'))";  // rukovod  ='$avtor'  ORDER BY Who";
			$result = mysql_query($query) or die(mysql_error());
				$n = mysql_num_rows($result);
				if($n >0){
					for ($i = 0; $i < $n; $i++)
					{
						$row = mysql_fetch_assoc($result);		
						//return $row; //['id'];
						$arrOfGlRuk[] = $row['id'];
					}
				}
	}
	
	//var_dump($arrOfGlRuk);
	
	//echo "<br /><br />id_gl_ruk;".$id_gl_ruk."<br /><br />";
	

	$arrAvtAllOb = array();//массис массивов структур подчиненности()

	
	//начинаем собирать сложные массивы
	foreach($arrOfGlRuk as $id_gl_r){
		$arrAvtId = array();//конструкция всех id	
		//вытаскиваем самого главного в массив
		$query = "SELECT * FROM  Speech_Who WHERE id = '$id_gl_r'";
			$result = mysql_query($query) or die(mysql_error());
			$n = mysql_num_rows($result);
			if($n >0){
				$row = mysql_fetch_assoc($result);
				//$arrAvtId[] = $row['id'];
				global $arrAvtAll;
				$arrAvtId[$row['id']] = array();
				$arrAvtAll[$row['id']]=$row;
				//var_dump($arrAvtId); echo"<br><br>";
			$arrAvtAllOb[] =  $row['id'];
			}/*else{
				$arrAvtAllOb[] = $id_gl_r;
			}*/
		//var_dump($arrAvtId);
	}
	
//$arrAvtAllOb[] = '7';
//echo "<br /><br /><br />123-3";
//	 var_dump($arrAvtAllOb);
	
	function GetArrOfPodch($id_ruk){	//вытаскиваем всех подчиненых
	$query = "SELECT *,'ruck' as str FROM  Speech_Who WHERE rukovod = '$id_ruk'";
	//echo $query;
	//UNION SELECT *,'podch' as str FROM  Speech_Who WHERE rukovod = '$avtor'";
	//UNION SELECT *,'ruck' as str FROM  Speech_Who WHERE id = (SELECT rukovod FROM  Speech_Who WHERE id = '$avtor')";  
		$result = mysql_query($query) or die(mysql_error());
		$n = mysql_num_rows($result);
		$arrWho = array();
		if($n >0){
			//echo " n >0 ";
			for ($i = 0; $i < $n; $i++)
			{
				$row = mysql_fetch_assoc($result);		
				$arrWho[] = $row;
				//if($row['id'] ==$SpeechArr[0]['id_avtor']){ $titleStr = $row['Who'];	}
				//return $row;
			}
		}
		return $arrWho;
	}
	
	
	//спускаемся SELECTом вниз до самого последнего подчиненого И формируем массив
	//собираем сложный массив	
	function AddArray($arrAvtId,$id_ruk){	
		//echo "<br><br>try_id_ruk_try-".$id_ruk."";
		$arrOfPodch = GetArrOfPodch($id_ruk);
		if(count($arrOfPodch)>0){
			//echo " --count(arrOfPodch)>1";
			foreach($arrOfPodch as $podch){
				//echo "<br><br>id_ruk-".$id_ruk."";
				//echo "<br>podch-".$podch['id']." <br>";
				//var_dump($podch);

				//добавляем Id автора в структуру
				$arrAvtId[$id_ruk][$podch['id']]=array();
				//добавляем саму инфу об авторе в массив
				global $arrAvtAll;
				$arrAvtAll[$podch['id']]=$podch;			
				
				//проверяем если у него есть подчиненые, то вытаскиваем их по рекурсии
				$arrAvtId[$id_ruk] =  AddArray($arrAvtId[$id_ruk],$podch['id']);
			}
		}else{
			$arrAvtId[$id_ruk] = array();
		}
		return $arrAvtId;
	}
	
	
	
	//создаем сложный массив структуры авторов
	for($i=0; $i<count($arrAvtAllOb); $i++){
	//$arrAvtId = AddArray($arrAvtId,$id_gl_ruk);
		//echo "<br><br><br>arrAvtId-<br>";
		//var_dump($arrAvtAllOb[$i])	;
		$id = $arrAvtAllOb[$i];
		$arrAvtAllOb[$i] = null;
		$arrAvtAllOb[$i] = AddArray($arrAvtAllOb[$i],$id);
		
		//echo "<br><br><br>arrAvtId-<br>";
		//var_dump($arrAvtAllOb[$i]);		
		//echo "<br><br>arrAvtAll<br>";
		//var_dump($arrAvtAll);	
	}




	
	
	if($_GET['avtor'] =='all'){
		//сохраняем в БД

		ini_set('mssql.textlimit', 2147483647);
        ini_set('mssql.textsize', 2147483647);
		
		$strBD = base64_encode(serialize($arrAvtAllOb));
		//mysql_real_escape_string()
		//$AvtorArray = base64_encode(serialize($arrAvtAll));
		$AvtorArray = serialize($arrAvtAll);
		
		//записываем в файл
			 $file = fopen ("AvtorArray.txt","r+");
			  $str = "Hello, world!";
			  if ( !$file )
			  {
				echo("Ошибка открытия файла");
			  }
			  else
			  {
				echo("Записывем в файл");
				fputs ( $file, $AvtorArray);
			  }
			  fclose ($file);
		
		//echo "Сохраняем в БД arrAvtAll - ".$AvtorArray;
		
		
		//echo $strBD;
		//$query = "UPDATE InfoTable SET AvtorStructArray = '".$strBD."' , AvtorArray = '".$AvtorArray."' ";
				$query = "UPDATE InfoTable SET AvtorStructArray = '".$strBD."' ";
			//	echo $query;
		$result = mysql_query($query) or die(mysql_error());	
	
	}

?>