<?//поиск речей в новости 
		//(нужен массив новости $news)
		// необходима реализация функции SaveInBD (можно и пустую)
		// можно  реализовать функцию SaveSpeechAndAvtor в JS 
		// необходимо подключение include_once("Get_Speech.php");//GetSpeech($text,$which){//вытаскивание речи из фрагмента речи
		// на выходе имеем массив авторов и речей $speachArr
		
		
//слова по которым ведется поиск речей
$words = array(' цитирует ',' рассказал ',' рассказала ',' сказал ',' сказала ',' заявил ',' заявила ',' напомнил ',' напомнила ',' подчеркнул ',' подчеркнула ',' считает ',' подытожил ',' подытожила ',' отметил ',' отметила ',' заметил ',' заметила ',' сообщил ',' сообщила ',' добавил ',' добавила ',' указал ',' указала ',' подтвердил ',' подтвердила ',' прокомментировал ',' прокомментировала ',' продолжил ',' продолжила ');
		



		
//echo "bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb";
		
			//достаем речь
			$speachArr = array();//массив речей с авторами
			//$who = array();
	
			$speachArrWithOut = array();//массив речей без авторов
			
			//избавляемся от длинного тире
			$ttte = str_replace('&mdash;','-', $news['plainText']);
			//избавляемся от запятых
			$ttte = str_replace(',',' Êµ ', $ttte);
			//echo $ttte;
			$ttte = str_replace($words,'ĀÐ', $ttte);//2
			$arr= explode('ĀÐ',$ttte); //2
			
			//var_dump($arr);
	

			
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
echo  "<b style='color:red;' >Speech</b>- <span id='Sp1-".$z."'>".$speech."</span>";
echo "<br /><b style='color:red;'>Who</b>- ".$Who;
if($speech !='' AND $Who ==''){
	echo "<input size='10' type='text' value=''  id='Wh1-".$z."'/><button id='Bt-".$z."' onclick='SaveSpeechAndAvtor(\"".$z."\")'>сохранить</button>";
}
echo "<br /><br />";					
					

					
					if(($speech != '') AND ($Who !='')){
						$ArrA['speech'] = $speech;
						$ArrA['Who'] = $Who;
						$ArrA['Key'] = $z;
						$speachArr[] = $ArrA;
						
						$NewsHaveSpeech = 1; 
						SaveInBD($Who,$speech,$news);	
							
							
							
							//вытаскиваем в новости поле ключи речей и добавляем туда этот ключ
						//	$query = "UPDATE $db_news.News_foreign SET speech = CONCAT(speech,'|$Nkey') WHERE id = '$NeId' ";
						//	$result = mysql_query($query) or die(mysql_error());
						
						
					}else{
						$ArrA = Array();
						if($speech != ''){
							$ArrA['speech'] = $speech;
						}elseif($Who !=''){
							$ArrA['Who'] = $Who;
						}
						
						$speachArrWithOut[] = $ArrA;
					
					}


			
			}
?>