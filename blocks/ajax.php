<?php  header('Content-Type: text/html; charset=utf-8');
    //ini_set('session.cookie_httponly',1);
	session_start();
	

	
	//if($_POST['login']=='leroy' and $_POST['password']=='password'){ 
     //   $auth = true; 
    //die('ggggggggggg');

	if(isset($_POST['SearchWords'])){ //очишаем массив словосоч-ий найденных по поисковой фразе
		if($_POST['SearchWords'] =="!null!"){
			unset($_SESSION['SearchWords']);
		}
	}
	
	if(isset($_POST['textarea'])){
		$_SESSION['textarea'] = addslashes($_POST['textarea']);
		$_SESSION['id_soobsh'] = $_POST['id_soobsh'];
		$_SESSION['id_obsuj'] = $_POST['id_obsuj'];
	}
	
	if(isset($_POST['size'])){	//изменение размеров поля коментариев
		if($_SESSION['textareaRows'] == 3)
			$_SESSION['textareaRows'] = 20;
		else	
			$_SESSION['textareaRows'] =3;
	}
	
	
	if(isset($_POST['ShAllFor'])){	//изменение списка форумов в теме
		if($_POST['ShAllFor'] == 'yes')
			$_SESSION['ShAllF'] = 1;
		elseif($_POST['ShAllFor'] == 'no')	
			$_SESSION['ShAllF'] = 0;
	}
	
	if(isset($_POST['ShAllSimFor'])){	//изменение списка похожих форумов в теме
		if($_POST['ShAllSimFor'] == 'yes')
			$_SESSION['ShAllSimF'] = 1;
		elseif($_POST['ShAllSimFor'] == 'no')	
			$_SESSION['ShAllSimF'] = 0;
	}
	
	if(isset($_POST['ShowAttached'])){	//показывать ли все закрепленные сообщения
		if($_POST['ShowAttached'] == 'yes')
			$_SESSION['ShowAttached'] = 1;
		elseif($_POST['ShowAttached'] == 'no')	
			$_SESSION['ShowAttached'] = 0;
	}

	if(isset($_POST['FullCollMem'])){	//показывать шесть или 18 членов группы
		if($_POST['FullCollMem'] == 'yes')
			$_SESSION['FullCollMem'] = 1;
		elseif($_POST['FullCollMem'] == 'no')	
			$_SESSION['FullCollMem'] = 0;
	}

	if(isset($_POST['SearchArray'])){	//указывает, что нам можно уже показывать пользователю фразы по запросу из поисковика
		if($_POST['SearchArray'] =='nu'){
			$_SESSION['SearchArray'] = '';
		}else{
			$_SESSION['SearchArray'] = 1;
		}
	}
	
	if(isset($_POST['WindowScl'])){	//указывает, что нам нужно пометить чтобы 1-окно фразы из поисковика не открывалось 
		if($_POST['WindowScl'] =='yes')
			$_SESSION['WindowScl'] = 'yes';
	}


/*
	if(isset($_POST['NotShowGuestList'])){	//указывает, что нам нужно спрятать списки гостей и их переписку
		if($_POST['NotShowGuestList'] =='0'){
			$_SESSION['NotShowGuestList'] = 0;
		}elseif($_POST['NotShowGuestList'] =='1'){
			$_SESSION['NotShowGuestList'] = 1;
		}
	}
*/

	if(isset($_POST['NotShowTegs'])){	//указывает, что нам нужно спрятать списки гостей и их переписку
		if(!isset($_SESSION['Elements'])){$_SESSION['Elements'] = array();}
		if($_POST['NotShowTegs'] =='0'){
			$_SESSION['Elements']['NotShowTegs'] = 0;
		}elseif($_POST['NotShowTegs'] =='1'){
			$_SESSION['Elements']['NotShowTegs'] = 1;
		}
	}

	if(isset($_POST['NotShowChatPa'])){	//указывает, что нам нужно спрятать чат страницы
		if(!isset($_SESSION['Elements'])){$_SESSION['Elements'] = array();}
		if($_POST['NotShowChatPa'] =='0'){
			$_SESSION['Elements']['NotShowChatPa'] = 0;
		}elseif($_POST['NotShowChatPa'] =='1'){
			$_SESSION['Elements']['NotShowChatPa'] = 1;
		}
	}
	if(isset($_POST['NotShowChat'])){	//указывает, что нам нужно спрятать общий чат
		if(!isset($_SESSION['Elements'])){$_SESSION['Elements'] = array();}
		if($_POST['NotShowChat'] =='0'){
			$_SESSION['Elements']['NotShowChat'] = 0;
		}elseif($_POST['NotShowChat'] =='1'){
			$_SESSION['Elements']['NotShowChat'] = 1;
		}
	}
	if(isset($_POST['NotShowEvent'])){	//указывает, что нам нужно спрятать общий чат
		if(!isset($_SESSION['Elements'])){$_SESSION['Elements'] = array();}
		if($_POST['NotShowEvent'] =='0'){
			$_SESSION['Elements']['NotShowEvent'] = 0;
		}elseif($_POST['NotShowEvent'] =='1'){
			$_SESSION['Elements']['NotShowEvent'] = 1;
		}
	}
	
	/*
		//удаляем подстроку в переменной для проверки спана
	if(isset($_POST['spanText'])){
		$_POST['spanText'] = str_replace('<span class="attachedImg"></span>', "", $_POST['spanText']);
		$_POST['spanText'] = str_replace('<SPAN class=attachedImg></SPAN>', "", $_POST['spanText']);//для ие
		$_POST['spanText'] = trim($_POST['spanText']);
	}*/
	//echo $_POST['spanText'];
	//die();
	
	
	
	//записываем словосоч в сессию
	if(isset($_POST['words']) && isset($_POST['url']) && isset($_POST['id_span']) && isset($_POST['where'])){
		$_SESSION['wordsToLink'] = addslashes($_POST['words']);
		$_SESSION['UrlToLink'] = addslashes($_POST['url']);
		$_SESSION['SpanToLink'] = addslashes($_POST['id_span']);
		$_SESSION['whereTab'] = sprintf("%d", $_POST['where']);
		//$_SESSION['spanText'] = $_POST['spanText'];
	//echo '["'.$_SESSION['wordsToLink'].'","'.$_SESSION['UrlToLink'].'"]';
	//echo '["пппп","рррооо"]';
	}
	
	
	//записываем положения окон (блоков) в куки
	if(isset($_POST['WhatWin']) ){
		if(($_POST['WhatWin'] == 'ChatPaDiv') or ($_POST['WhatWin'] == 'ChatDiv') or ($_POST['WhatWin'] == 'EventDiv') ){
			if(isset($_POST['polXY']) ){
				
				$_POST['polXY'] = addslashes($_POST['polXY']);
				
				
				if($_POST['WhatWin'] == 'ChatDiv'){
					$_SESSION['win_Place']['ChatDiv'] = addslashes($_POST['polXY']);
				}
				if($_POST['WhatWin'] == 'ChatPaDiv'){
					$_SESSION['win_Place']['ChatPaDiv'] = addslashes($_POST['polXY']);
				}
				if($_POST['WhatWin'] == 'EventDiv'){
					$_SESSION['win_Place']['EventDiv'] = addslashes($_POST['polXY']);
				}
				
				
				//echo $_POST['WhatWin'];
				/*
				$expire = time() + 3600 * 24 * 100;
				setcookie('ChatDiv',$_POST['polXY'], $expire,"","","",true);
				echo $_POST['polXY'];*/
			}
		}
	}
	
	
	//die($_SESSION['spanText']);
//$_SESSION['textarea'] = $_GET['text'];


	//setcookie('textarea',$_POST['textarea']); 
    //}else $auth = false; 
   // die( json_encode(array('auth'=>$auth)) ); 
?>