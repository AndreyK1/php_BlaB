<?php  
//меняем размеры окон
//chatUserWin  GlobalUserWin
header('Content-Type: text/html; charset=utf-8');//собираем всю информацию об защедщих на сайт	



/*
		if (is_uploaded_file($_FILES['upload_photo']['tmp_name']))
		{
			 // загружаем изображение на сервер, если оно соответствует требованиям (формат: gif/jpeg/png и размер файла ~ 500 kB)
			if ( ($_FILES['upload_photo']['type'] == 'image/gif' || $_FILES['upload_photo']['type'] == 'image/jpeg' || $_FILES['upload_photo']['type'] == 'image/png') && $_FILES['upload_photo']['size'] <= 512000 )
			{
				  $upload_photo= $_FILES['upload_photo']['name'];
				  copy($_FILES['upload_photo']['tmp_name'],"img/".$upload_photo);
				  // уведомляем об успешной загрузке и обновляем ссылку на изображение
				  echo "<script type=\"text/javascript\">parent.document.getElementById(\"imageId\").innerHTML = '<img src=\"img/{$upload_photo}\">'; parent.document.getElementById(\"image_upload_status\").innerHTML = '<p class=\"image_success\">Изображение успешно загружено</p>';</script>";
			 }

			 // уведомление об ошибке
			 else if (($_FILES['upload_photo']['type'] != 'image/gif' && $_FILES['upload_photo']['type'] != 'image/jpeg' && $_FILES['upload_photo']['type'] != 'image/png'))
			 {
				  echo "<script type=\"text/javascript\">parent.document.getElementById(\"image_upload_status\").innerHTML = '<p class=\"image_error\">Недопустимый тип файла</p>';</script>";
			 }
			 else if ($_FILES['upload_photo']['size'] > 512000)
			 {
				  echo "<script type=\"text/javascript\">parent.document.getElementById(\"image_upload_status\").innerHTML = '<p class=\"image_error\">Недопустимый размер файла</p>';</script>";
			 }
			 else
			 {
				  echo "<script type=\"text/javascript\">parent.document.getElementById(\"image_upload_status\").innerHTML = '<p class=\"image_error\">Произошла ошибка при загрузке файла</p>';</script>";
			 }
		}	
	
*/	




	
	session_start(); 
	
				include_once('../../M/M_Users.php');
				include_once('../../M/MSQL.php');		
				include_once('../../M/M_Foto.php');
				include_once("../../M/img_resize.php");
	$ee = explode("blocks",__DIR__);
	
	$GLOBALS['dir'] = substr($ee['0'], 0, -1) ;
	
	//var_dump($GLOBALS['dir']);
	
	//echo "666666666666666666666666666666666".$GLOBALS['dir']."hhh";
	//echo "<script type=\"text/javascript\">parent.document.getElementById(\"image_upload_status\").innerHTML = '<p class=\"image_error\">Фотография НЕ добавлена</p>';</script>";



	
		if($_FILES['userfileComment']['name'] !=''){	
				
			//заменяем ссылку на изображение
				echo "<script type=\"text/javascript\">parent.document.getElementById(\"imageId\").innerHTML = '<img src=\"img/loadinfo1.gif\">'; </script>";	
				
				//подключаем модуль работы с фотографиями
				$m_Foto = M_Foto::Instance	(); // *
				
				
				$path = 'FotoAvtors'; $size = '';
				if(isset($_GET['size'])){
					$size = addslashes($_GET['size']);
				}
				if(isset($_GET['path'])){
					$path = addslashes($_GET['path']);
				}
				
				
				$add = $m_Foto->AddFoto2comment(0,0,$path,$size);//добавлени фоток
				//var_dump($add);
				if($add === false){
							 echo "<script type=\"text/javascript\">parent.document.getElementById(\"image_upload_status\").style['display'] = 'block';</script>";
							//echo "<script type=\"text/javascript\">parent.document.getElementById(\"image_upload_status\").innerHTML = '<p class=\"image_error\">Фотография НЕ добавлена</p>';</script>";
							//style="display:none;"
							
							/*
							$msg = 'Фотография НЕ добавлена!';			
							$path = 'index.php?'.$_SERVER['QUERY_STRING'];
							include_once('blocks/post.php');	
							die();*/
				}else{	
						if((int)$add == 55){
							echo "<script type=\"text/javascript\">parent.document.getElementById(\"image_upload_status\").innerHTML = '<p class=\"image_error\">Тип файла не поддерживается (только jpeg или gif)!</p>';</script>";
							/*
							$msg = 'Тип файла не поддерживается (только jpeg или gif)!';			
							$path = 'index.php?'.$_SERVER['QUERY_STRING'];
							include_once('blocks/post.php');	
							die();*/
						}else{
							
							echo "<script type=\"text/javascript\">parent.document.getElementById(\"image_upload_status\").innerHTML = '<p class=\"image_error\">Фотография добавлена!".$add."</p>'; parent.document.getElementById(\"srcFoto\").value = '".$add."'; </script>";
							
							$a=explode(".",$add);
//							$_SESSION['textarea'].= " [image]".$a['0']."|			
//document.getElementById('TextArea1').value = document.getElementById('TextArea1').value +'[b][/b]';
							echo "<script type=\"text/javascript\">parent.document.getElementById(\"TextArea1\").value = parent.document.getElementById(\"TextArea1\").value + '[image]".$a['0']."|';</script>";
							
							/*
							$msg = 'Фотография добавлена!';			
							$path = 'index.php?'.$_SERVER['QUERY_STRING'];
							//добавляем ее в текстовое поле
 ";
 							include_once('blocks/post.php');	
							die();*/

						}
				}
					echo "<script type=\"text/javascript\">parent.document.getElementById(\"imageId\").style['display'] = 'none'</script>";
					//echo "<script type=\"text/javascript\">parent.document.getElementById(\"imageId\").innerHTML = '<img src=\"img/cif1.jpg\">'; </script>";	
					//style['display'] = 'block'
							
		}
	
	
	/*
	
	if(isset($_POST['id'])){
		$id = addslashes($_POST['id']);
		echo 'yes'.$id;
				include_once('../../M/M_Users.php');
				include_once('../../M/MSQL.php');		
				include_once('../../M/M_Foto.php');
				
				$m_Foto = M_Foto::Instance	(); // *
				$add = $m_Foto->AddFoto2comment();//добавлени фоток			
		
		
		
		
		
		
		

		
	}else{
		echo 'shit';
	}	
	*/
	
	
	
/*

*/				
/*	
	if(!isset($_SESSION['win_Size'])){
		$_SESSION['win_Size'] = array();
	}
	
	
	//var_dump($_POST);
	
	if($_POST['id'] =='chatWin'){
		//echo 'sdfsdf'.$_SESSION['win_Size']['chatWin']."---";
		if($_SESSION['win_Size']['chatWin'] == '1'){
			$_SESSION['win_Size']['chatWin'] = '0';
			echo '0';
		}else{
			$_SESSION['win_Size']['chatWin'] = '1';
			echo '1';		
		}
	}
	
	
	if($_POST['id'] =='chatWinOb'){
		//echo 'sdfsdf'.$_SESSION['win_Size']['chatWinOb']."---";
		if($_SESSION['win_Size']['chatWinOb'] == '1'){
			$_SESSION['win_Size']['chatWinOb'] = '0';
			echo '0';
		}else{
			$_SESSION['win_Size']['chatWinOb'] = '1';
			echo '1';		
		}
	}	
	
	
	if($_POST['id'] =='chatUserWin'){
		//echo 'sdfsdf'.$_SESSION['win_Size']['chatWin']."---";
		if($_SESSION['win_Size']['chatUserWin'] == '1'){
			$_SESSION['win_Size']['chatUserWin'] = '0';
			echo '0';
		}else{
			$_SESSION['win_Size']['chatUserWin'] = '1';
			echo '1';		
		}
	}
	
	if($_POST['id'] =='GlobalUserWin'){
		//echo 'sdfsdf'.$_SESSION['win_Size']['chatWin']."---";
		if($_SESSION['win_Size']['GlobalUserWin'] == '1'){
			$_SESSION['win_Size']['GlobalUserWin'] = '0';
			echo '0';
		}else{
			$_SESSION['win_Size']['GlobalUserWin'] = '1';
			echo '1';		
		}
	}
	
*/	

?>