<?
header('Content-Type: text/html; charset=utf-8');//собираем всю информацию об защедщих на сайт
ini_set("max_execution_time", "60");
//set_time_limit (60); 
//htaccess :  	php_value max_execution_time 60
session_start();
include_once('startup.php');
	// Установка параметров, подключение к БД, запуск сессии.
	startup();

	
	//Как использовать прокси в PHP функции file_get_contents?
	//http://kadomtsev.ru/kak-ispolzovat-proksi-v-php-funkcii-file_get_contents/
	

//ВАЖНО!!!!		
//прооверяем, что мы админ
	//var_dump($_SESSION['Guest_id']['id_user']);
include('variables.php');
if($_SESSION['Guest_id']['id_user'] == $AdminID){  //   !!!ВАЖНО!!!
}else{die('Вы не Админ!!!');}
echo "Вы Админ продолжаем дальше";

?>
<br /><a style='color:orange;' href="index.php?c=adminka">В АДМИНКУ</a><br /><br />
<?

?>
<br />
<a href="rss_t.php">В НАЧАЛО</a>
<br /><br />
<b>Забрать новости с сайтов к себе в БД</b>
<div style="border:2px solid green;">
	<a href="rss_t.php?action=grabbNewsToBD&from=ria">С РИА НОВОСТИ</a></br>
	<a href="rss_t.php?action=grabbNewsToBD&from=itar-tass">С ИТАР-ТАСС</a></br>
</div>


<br />
<b>Просмотреть все последние новости из БД</b>
<div style="border:2px solid green;">
	<a href="rss_t.php?action=ShowNewsFromBD">просмотреть</a></br>
</div>
<hr /><hr /><hr /><hr /><hr />
<?
$maxRSSNews = 15; //максимальное кол-во последних новостей в RSS для парсинга


//парсинг RSS в Массивы
function ParseRSSInMassiv($url){
	$aqrrOfNewsIn = Array();	
	if($url !=''){
		/*$xml = xml_parser_create();     //создаёт XML-разборщик
		xml_parser_set_option($xml, XML_OPTION_SKIP_WHITE, 1);  //устанавливает опции XML-разборщика
		xml_parse_into_struct($xml, file_get_contents($url), $element, $index); //разбирает XML-данные в структуру массива, все передается в массив $index
		xml_parser_free($xml);  //освобождает XML-разборщик
		*/
	
		//достаем текст
		//$data = file_get_html($url);
		$str = file_get_html($url);
		$str = str_replace('<![CDATA[','', $str);
		$str = str_replace(']]>','', $str);
		$data = str_get_html($str);
		if($data->innertext!=''){// and count($data->find('title'))){
			$text ='';
			$k = 0;			
			foreach($data->find('item') as $a){
				//$this->remove_noise("'<![CDATA[(.*?)]]>'is", true);

				//"<b>".$a->plaintext;
				//echo "<br />11-".$a->plaintext."-11<br />";
					//$data->find('#article_full_text');
					//$b = $a->innertext;
					if($_GET['from']=='itar-tass'){//отсеиваем не политику 
						$pol=false;
						foreach ($a->find('category') as $c){
						//	echo "<br />+0-".$c->innertext."-0";
							if($c->innertext == 'Политика'){ $pol=true;}
							if($c->innertext == 'Кризис на Украине'){ $pol=true;}
							if($c->innertext == 'Международная панорама'){ $pol=true;}
						}
						//echo "<br />";
						if(!$pol){ continue;}
						$pol = false;		
										
					}
		//ВАЖНО!!!!					
					$k++;
					global $maxRSSNews;
					if($k>$maxRSSNews){ break;} //ограничиваем число до семи последних новостей
					
					foreach ($a->find('title') as $c){
						$arr['title']=$c->innertext;
						//echo "<br />+1-".$c->innertext."-1<br />";
					}
					foreach ($a->find('guid') as $c){
						$arr['link']=$c->innertext;
						//echo "<br />+2-".$c->plaintext."-2<br />";
					}
					foreach ($a->find('description') as $c){
						$arr['description']=$c->innertext;
						//echo "<br />+3-".$c->plaintext."-3<br />";
					}
					foreach ($a->find('pubDate') as $c){
						$d1 = strtotime($c->innertext); // переводит из строки в дату
						$arr['pubDate'] = $pubDate = date("Y-m-d H:i:s", $d1); 
						//echo "<br />+4-".$c->plaintext."-4<br />";
					}
					foreach ($a->find('ENCLOSURE') as $c){
						$arr['img']=$c->url;
						//echo "<br />+5-<img src='".$c->url."' />-5<br />";
					}
					//echo "<br /><br />";
					
					$aqrrOfNewsIn[] = $arr;
			}
		}
		$data->clear(); // очишаем
		unset($data);
		
	}
	return $aqrrOfNewsIn;
}




//Если мы забираем новости в БД
if (isset($_GET['action']) AND isset($_GET['from']) AND ($_GET['action'] =='grabbNewsToBD')){
	// подключаем библиотеку simple_html_dom
	require_once 'simple_html_dom.php'; 
	
	//создаем массив, куда будем складывать новости
	$aqrrOfNews = Array();
	
	//проверяем с какого сайта забираем
	$url ='';
	if($_GET['from']=='ria'){
		echo "Забираем РИА новости в БД<br />";
		$url ='http://ria.ru/export/rss2/politics/index.xml';
	}elseif($_GET['from']=='itar-tass'){
		echo "Забираем ИТАР-ТАСС новости в БД<br />";
		$url ='http://itar-tass.com/rss/v2.xml';
	}
	
	//парсим RSS в Массивы
	$aqrrOfNews = ParseRSSInMassiv($url);
	//var_dump($aqrrOfNews);
	
	//вытаскиваем последнюю дату новости из базы для этого источника
	$query = "SELECT pubDate FROM $db_news.News_foreign WHERE fromN = '".$_GET['from']."' ORDER BY pubDate DESC LIMIT 1 ";	//
	//die($query);
		$result = mysql_query($query) or die(mysql_error());
		if($result){
			$row = mysql_fetch_assoc($result);
			echo "последняя дата в базе ".$row['pubDate'];
		}
	 if(count($aqrrOfNews)>0){
		//выводим новости в форму для выбора
		?>
			<form  method="post">
				<input type="hidden" value='' name='ChooseNews' />
				<b>Выберите новости для добавления в базу</b><input type="submit" value="Отправить"><br />
				<?$Num=0;
				foreach($aqrrOfNews as $news){
				$style = '';
				if($news['pubDate'] <= $row['pubDate']){$style = "style='color:#ccc;'";}
				?>
					<input type="checkbox" name="option<?=$Num++?>" value="<?=$news['pubDate']?>"><span <?=$style?> title="<?=$news['description']?>"><?=$news['pubDate']?> | <?=$news['title']?></span><br />
				<?}?>
			</form>
		<?
	 }else{
		echo "<br />!!!Нету новостей для выбора!!!<br /><br />";
	 }
	
	//создаем массив выбранных из RSS новостей
	$aqrrOfChosenNews = Array();

	//если были выбраны новости для добавления в БД
	if(isset($_POST['ChooseNews'])){
		echo "были выбраны новости для добавления в БД!!!";
		//пробегаемся по перемееным и смотрим, какие новости выбраны для добавления в БД
		for($i=0; $i<$maxRSSNews; $i++){
			$varName = 'option'.$i;
			if(isset($_POST[$varName])){
				//echo $_POST[$varName]."<br />";
				//$aqrrOfChosenNews/
				//выбираем из последних новостей выбранные новости
				foreach($aqrrOfNews as $news){
					if($news['pubDate'] == $_POST[$varName]){
						$aqrrOfChosenNews[] = $news;
					}
				}
			}
		}
	 //var_dump($aqrrOfChosenNews);
	}
	
	
	//пробегаемся по массиву разборшика xml и создаем свой массив новостей -  выводим на экран
	//for ($i=0; $i < count($index);$i++){
/*	for ($i=1; $i < 3;$i++){  													//!!!ВАЖНО!!!
		$arr['title'] = $title=$element[$index["TITLE"][$i]]["value"]; // 
		$arr['link'] = $link = $element[$index["LINK"][$i]]["value"]; // 
		$arr['description'] = $description=$element[$index["DESCRIPTION"][$i]]["value"]; // 
			$pubDate=$element[$index["PUBDATE"][$i-1]]["value"]; 
			$d1 = strtotime($pubDate); // переводит из строки в дату
		$arr['pubDate'] = $pubDate = date("Y-m-d H:i:s", $d1); 
		$arr['img'] = $img= $element[$index["ENCLOSURE"][$i-1]]["attributes"]["URL"];
		//var_dump($element[$index["ENCLOSURE"][$i]]);
		
		echo "<a href=\"$link\">$title</center></a><br/>$description --- $pubDate  $img<br/><img src='$img' /><br /><br /><br />";
		
		$aqrrOfNews[] = $arr;
 	}
*/

	
	
	//если есть выбранные массивы
	if(count($aqrrOfChosenNews)>0){
		//переворачиваем массив
		$aqrrOfChosenNews = array_reverse($aqrrOfChosenNews);
		$aqrrOfNews = $aqrrOfChosenNews;
		
		//вытаскиваем последнюю новость из базы для этого источника
		$query = "SELECT * FROM $db_news.News_foreign WHERE fromN = '".$_GET['from']."' ORDER BY id DESC LIMIT 1 ";	//
					//die($query);
		$result = mysql_query($query) or die(mysql_error());
		if (!$result) die();
		$row = mysql_fetch_assoc($result);		
		
		//echo "99-99-".$row['id'];
		$last = '';
		if(count($row)>0){
			$last = $row['pubDate'];
			//echo "pubDate = ".$last;
		}
		
		//пробегаемся по массиву, и те которые позже закидываем в БД вместе с текстом из источника
		$new=0;
		for($i=0; $i<count($aqrrOfNews); $i++){
			if(($last =='') or ($last < $aqrrOfNews[$i]['pubDate'])){
				$new++;
				//записываем новость в БД
				echo "<br />записываем в бд новость - ".$aqrrOfNews[$i]['title']."<br />";
				
				//вытаскиваем текс новости с сайта
				//$aqrrOfNews[$i]['plainText'] = grabbNewsToBD($aqrrOfNews[$i]['link']);
				$t = grabbNewsToBD($aqrrOfNews[$i]['link']);
				$aqrrOfNews[$i]['plainText'] = $t['plaintext'];
				$aqrrOfNews[$i]['innertext'] = $t['innertext'];
				
				$aqrrOfNews[$i]['title'] =  mysql_real_escape_string($aqrrOfNews[$i]['title']);
				$aqrrOfNews[$i]['link'] =  mysql_real_escape_string($aqrrOfNews[$i]['link']);
				$aqrrOfNews[$i]['pubDate'] =  mysql_real_escape_string($aqrrOfNews[$i]['pubDate']);
				$aqrrOfNews[$i]['img'] =  mysql_real_escape_string($aqrrOfNews[$i]['img']);
				$aqrrOfNews[$i]['description'] =  mysql_real_escape_string($aqrrOfNews[$i]['description']);
				$aqrrOfNews[$i]['plainText'] =  mysql_real_escape_string($aqrrOfNews[$i]['plainText']);
				$aqrrOfNews[$i]['innertext'] =  mysql_real_escape_string($aqrrOfNews[$i]['innertext']);

				$query = "INSERT INTO $db_news.News_foreign (title,fromN,link,description,pubDate,img,plainText,tegText,speech) VALUES ('".$aqrrOfNews[$i]['title']."','".$_GET['from']."','".$aqrrOfNews[$i]['link']."','".$aqrrOfNews[$i]['description']."','".$aqrrOfNews[$i]['pubDate']."','".$aqrrOfNews[$i]['img']."','".$aqrrOfNews[$i]['plainText']."','".$aqrrOfNews[$i]['innertext']."','')";
				//echo $query;
				$result = mysql_query($query);
				
			}
		}
		if($new==0){
			echo "<br />!!!Небыло выбрано новостей!!!<br /><br />";
		}else{
			echo "<br />!!!Были добавлены новые новости!!!<br /><br />";
		}
	}
	
}

function grabbNewsToBD($link){
	if($link){
	//достаем текст
		$data = file_get_html($link);
		if($data->innertext!=''){// and count($data->find('title'))){
			$text = Array();
			if($_GET['from'] == 'ria'){
				foreach($data->find('#article_full_text') as $a){
					//echo "<br />11-".$a->plaintext."-11<br />";
					$text['plaintext'] = $a->plaintext;
					$text['innertext'] = $a->innertext;
				}			
			}
			if($_GET['from'] == 'itar-tass'){
				foreach($data->find('.b-material-text__l') as $a){
					//echo "<br />11-".$a->plaintext."-11<br />";
					$text['plaintext'] = $a->plaintext;
					$text['innertext'] = $a->innertext;
				}			
			}
		}
		$data->clear(); // РїРѕРґС‡РёС€Р°РµРј
		unset($data);
		return $text;
	}
}





//Если надо вывести все новости из БД
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
	}else{
		echo "<h2>Редактирование Новости </h2><br />";
		
		function GetSpeech($text,$which){//вытаскивание речи из фрагмента речи
			//$which - какой член массива нам нужен
			$arr = explode('"',$text);
			$need = '';
			$arrZnak = array('.','!','?');
			
			if(count($arr>2)){
				if($which == 'left'){
					//проверяем наличие окончаний предложения
					$zamen = str_replace($arrZnak,'ĀQÐ', $arr[count($arr)-1]);//2
					if($zamen == $arr[count($arr)-1]){//если между ковычками и словом не было таких знаков
						$need = $arr[count($arr)-2];
					}
				}elseif($which == 'right'){
					//проверяем наличие окончаний предложения
					$zamen = str_replace($arrZnak,'ĀQÐ', $arr['0']);//2
					if($zamen == $arr['0']){//если между ковычками и словом не было таких знаков
						$need = $arr['1'];
					}
				}
			}
		 return $need;
		}
		
		
		
		
		
		
		$id_news = sprintf("%d", $_GET['id_news']);
		//вытаскиваем  новость из базы 
		$query = "SELECT * FROM $db_news.News_foreign WHERE id = '$id_news' ";	//
					//die($query);
		$result = mysql_query($query) or die(mysql_error());
		if (!$result) die();
		$row = mysql_fetch_assoc($result);
		if(count($row)>0){		
			echo "<a href='rss_t.php?action=ShowNewsFromBD&id_news=".$row['id']."&findSpeach=1'>Найти речь</a><hr />";
			echo "<b>ИСТОЧНИК</b> - ".$row['fromN']."<br />";
			echo "<b>НАЗВАНИЕ</b> - ".$row['title']."<br />";
			echo "<b>ОПИСАНИЕ</b> - ".$row['description']."<br />";
			echo "<b>ДАТА РУБЛИКАЦИИ</b> - ".$row['pubDate']."<br />";
			if($row['img'] == ''){
				echo "<b>КАРТИНКА</b> - НЕТ<br />";
			}else{
				echo "<b>КАРТИНКА</b> - <img src='".$row['img']."' /><br />";
			}
			echo "<b>ВЫРЕЗАННАЯ РЕЧЬ</b> - ".$row['speech']."<br />";
			echo "<b>ТЕКСТ:</b><hr /> ";
//			echo $row['plainText']."<br />";
//			echo "<hr /> ";
			//echo $row['tegText']."<br />";
			echo "<hr /> ";
		}
		if(isset($_GET['findSpeach'])){

			$speachArr = array();
			//$who = array();
			
			//избавляемся от длинного тире
			$ttte = str_replace('&mdash;','-', $row['plainText']);
			//избавляемся от запятых
			$ttte = str_replace(',',' Êµ ', $ttte);
			
			//echo $ttte;
			
			$words = array(' рассказал ',' рассказала ',' сказал ',' сказала ',' заявил ',' заявила ',' напомнил ',' напомнила ',' подчеркнул ',' подчеркнула ',' считает ',' подытожил ',' подытожила ',' отметил ',' отметила ',' заметил ',' заметила ',' сообщил ',' сообщила ',' добавил ',' добавила ',' указал ',' указала ',' подтвердил ',' подтвердила ',' прокомментировал ',' прокомментировала ',' продолжил ',' продолжила ');
			$ttte = str_replace($words,'ĀÐ', $ttte);//2
			$arr= explode('ĀÐ',$ttte); //2
//			echo "---777---<br/>";
/*			foreach($arr as $key=>$rer){
				echo $key."]".$rer."<hr />";
			}
*/			
			//var_dump($arr);
//			echo "---777---<br/>";
			//foreach($words as $w){ //2
			for($z=1; $z<count($arr);$z++){
				 
//				echo "<br /><br />___HHHHHHHHHHНННННННН_".$z."__<br />";
				
				//$arr= explode($w,$ttte); //2
				
				//ставим запятые назад
				$arrBack = array(' Êµ ','Êµ ',' Êµ');
				$arr[$z-1] = str_replace($arrBack,',', $arr[$z-1]);//2
				$arr[$z] = str_replace($arrBack,',', $arr[$z]);//2
				//if(count($arr)>1){//значит слово было//2
						
						$speech = '';//чье высказывание/речь	
						//ищем ближайщие кавычки
//						echo "PART_Left------".$arr[$z-1]."<br />";
//						echo "PART_Right-----".$arr[$z]."<br />";
						$arrLeft = explode('"',$arr[$z-1]);
						$arrRight = explode('"',$arr[$z]);
//						echo "<br />VAR_DLeft-";
//						var_dump($arrLeft);
//						echo "<br />VAR_DRight-";
//						var_dump($arrRight);
//						echo "<br /><br />";
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
						
						/*if(($n1 == '') AND ($n2 == '')){
						echo "ffff===";
						}else{*/
							//n1-14  n2--1
							//n1--1 !n2-6
								//if((($nR > $nL) AND ($nR!=0)) or ($nL == 0)){
							//if((($n1 !='') AND ($n2 =='')) OR ($n1 < $n2) ){
							
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
						//}

//						echo "<br />n1-".$n1." n2-".$n2."<br />";
//						echo "NSPCH-".mb_strlen($speech,'UTF-8')."<br />";
						if(mb_strlen($speech,'UTF-8')<30){//отсеивание речи менее стольки-то символов
							$speech = '';
						}
//						echo "speech-".$speech."<br />";
					
					//ищем Автора
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
					
					
					//echo "<br /><br />----";
//					echo "Who-".$Who;
					/*	echo "<br /><br />";
					echo $Le;*/
					//	echo preg_replace('![А-Я]!','-RRR-',$arr['0']);
						
					//preg_match_all('/^\p{Lu}\pL+$/u', $arr['0'],$matches);
					
					//	var_dump((bool)preg_match('/^[А-Я]+$/', 'Впмаха'));
					//var_dump((bool)preg_match('/^\p{Lu}\pL+$/u', 'Nпмаха'));
						//preg_match_all('/\p{Lu}\pL+/u', $arr['0'],$matches);
						//var_dump($matches);
				//}//2
				
				if(($speech != '') AND ($Who !='')){
					$ArrA['speech'] = $speech;
					$ArrA['Who'] = $Who;
					$ArrA['Key'] = $z;
					$speachArr[] = $ArrA;
				}
			}

			
			echo "<hr /><hr />";
			//var_dump($speachArr);
			
			//создаем массив речей, которые уже бобавлены в таблицу
			$arrSp = explode('|',$row['speech']);
			
			//добавляем одну нулевую речь, чтобы можно было просто самому добавить речь из тектста
			$arrNull = array('Key'=>0,'Who'=>'','speech'=>'');
			$speachArr[] = $arrNull;
			?>
			
			<b style='color:green; cursor:pointer; text-decoration:underline' onclick="return document.getElementById('DivForSpech').style.display = 'block';" >Добавить речь самому</b><br/>
			
			<?
			//выводим всю найженную речь
			foreach($speachArr as $sp){
				
				
				?>
				<br /><br />
			<div <?if($sp['Key'] =='0'){ echo " id='DivForSpech' style='display:none;' "; }?>>	
				<b>Найдена речь: </b> </b> <input size='60' id='Wh-<?=$sp['Key']?>' type='text' value='<?=$sp['Who']?>' /><br />
				<b>Сама речь: </b><br /> <textarea COLS='90' ROWS='3' id='Sp-<?=$sp['Key']?>' ><?=$sp['speech']?></textarea><br />
				<b>Кто такой: </b><br /> <textarea COLS='90' ROWS='1' id='De-<?=$sp['Key']?>' ></textarea><br />
				<input  type='hidden' id='NeId-<?=$sp['Key']?>' value='<?=$_GET['id_news']?>' />
				<input  type='hidden' id='NeTi-<?=$sp['Key']?>' value='<?=$row['title']?>' />
				<input  type='hidden' id='NeDa-<?=$sp['Key']?>' value='<?=$row['pubDate']?>' />
				<input  type='hidden' value='<?=$row['speech']?>' />

				<button onclick="SaveSpeechBD('<?=$sp['Key']?>')" >Сохранить речь</button><br />
							<span id="picHead-<?=$sp['Key']?>" onclick='ShowFotoMen(this);' style="cursor:pointer; text-decoration:underline; font-size:9px;" title="Добавить картинку">Картинка из вне</span>
							<br /><span id="MakePic-<?=$sp['Key']?>" onclick='MakePicture(this);' style="cursor:pointer; text-decoration:underline; font-size:9px;" title="Переделать картинку новости">Переделать картинку из новостие</span>
							<img src ='' id='makedPicture-<?=$sp['Key']?>' style='display:none;'  />

			<br />
				
				<span id='YNSp-<?=$sp['Key']?>' style='color:green;'><? if (in_array($sp['Key'], $arrSp)) { echo $sp['Key'].'-добалена в БД';} ?></span><br />
				<?
				echo 'ключ - '.$sp['Key'].'<br />';
				?>
			</div>
				<?				
				//выделяем найденное в тексте
				$row['tegText'] = str_replace($sp['speech'],"<b style='color:green'>".$sp['speech']."</b>", $row['tegText']);
				$row['tegText'] = str_replace($sp['Who'],"<b style='color:green'>".$sp['Who']."</b>", $row['tegText']);
				//$row['tegText']
				

				
			}

			
			$speachArr = array_unique($speachArr);	
			//echo "<br /><br />!!!!! speachArr-"; var_dump($speachArr);			
		
		}
	}

?>
<iframe  name="h_iframe" width="700" height="100" style="display: none;"></iframe><!--фрейм для загрузки страницы  onchange="document.forms['img_upload'].submit();"  -->
	 <div id="picoBody" style="display:none">
		<form  id="linkForm2" method="post" action="blocks/dinamic_scripts/loadPicture.php?path=FotoAvtors&size=90"  name="img_upload" enctype="multipart/form-data" target="h_iframe">
			<div id="imageId">
				 
				  <img src="img/loadinfo1.gif" style="display:none;" />
			 </div>
			 <div id="image_upload_status"></div>	
			 <p><input id="showfiles1" type="file" name="userfileComment"  /></p>
			 <input id="srcFoto" type="text" name="srcFoto"   />
			 <input id="TextArea1" type="hidden" name="srcFoto"  />
		</form>
	</div>	
<?	
	
echo "<hr /><hr />".$row['tegText'];


		
	
}
//http://xdan.ru/Uchimsya-parsit-saity-s-bibliotekoi-PHP-Simple-HTML-DOM-Parser.html
//Maximum execution time of 30
?>

<script type="text/javascript" src="js/jquery.js"></script> <!--подключаем -->
<script type="text/javascript">//работа с фотографией
var keyF = '';
function ShowFotoMen(obj){
		var t = obj.id.split('-');
		t = t[1];
		keyF = t;
	if(document.getElementById('YNSp-'+t).innerHTML !=''){

		//alert(t);
		var DivFoto = document.getElementById('picoBody');
		
		DivFoto.style.display = 'block';	
		//var pp= document.createElement('span'); pp.innerHTML = 'jjjjjjjjjjjjjjjjjjjjjjjjjjjjjj';
		//alert(obj.parentNode.innerHTML);
		//document.getElementById('picHead-'+t).parentNode.appendChild(DivFoto);
		obj.parentNode.appendChild(DivFoto);
		
	}else{
		alert('речь не добавлена в БД, значит и картинку прикрепить нельзя');
	}

	
}

$(document).ready(function() { 
	//on('input propertychange');
	/*$('#srcFoto').bind('input',function(){ 
		alert('shit');
	
	});	
	*/
	$('#showfiles1').change(function(){ 
			//document.getElementById('srcFoto').value = ''; //очишаем
			//alert($('#showfiles').val());
			//loadPicture($('#showfiles').val());
			document.forms['linkForm2'].submit();
			
			//сохраняем к автору картинку
			//alert('ggg');
			setTimeout(function(){//откладываем событие на пол секунды
				var srcFoto = document.getElementById('srcFoto').value;
				if(srcFoto !=''){
					//alert(srcFoto);
					var WhoF = document.getElementById('Wh-'+keyF).value;
					var _ids;
						$.ajax({
						  async: false, 
						  url: 'blocks/dinamic_scripts/Change_Pole.php',
						  data: {Table:'Speech_Who',PoleVal:'Foto',Value:srcFoto,PoleWhere:'Who',Where:WhoF},
						  type: "POST",
						  success: function(data) {  _ids = data; }//,
						//dataType: 'json'
						 })
						alert(_ids);
					
				}
			}, 1000);


	});	
});




//переделывание под фото картинку с новости
var img = '<?=$row['img']?>';
//var SrcPic = '';
var ImgPackInText = '';
//$(document).ready(function() { 
	function MakePicture(obj){//переделываем картинку 
	
			var key = obj.id.split('-');
				var key = key[1];	
		
		if(document.getElementById('YNSp-'+key).innerHTML !=''){		

				if(img !=''){
//alert(document.getElementById('YNSp-'+key).innerHTML);				
					var _ids;
						$.ajax({
						  async: false, 
						  url: 'blocks/dinamic_scripts/PictureForNews.php',
						  data: {img:img,whereFol:'FotoAvtors',size:'90'},
						  type: "POST",
						  success: function(data) {  _ids = data; }//,
						//dataType: 'json'
						 })
						if(_ids !=''){
							alert(_ids);
							//SrcPic = _ids;
							var arr = _ids.split('FotoAvtors');
							var src = 'FotoAvtors' + arr[1]; 
							
							var el = document.getElementById('makedPicture-'+key);
							//makedPicture
							el.src = src;
							el.style.display = 'block';
							
							//сохраняем к автору картинку	
							var WhoF = document.getElementById('Wh-'+key).value;
							alert("WhoF- "+WhoF);
							var _ids1;
							$.ajax({
							  async: false, 
							  url: 'blocks/dinamic_scripts/Change_Pole.php',
							  data: {Table:'Speech_Who',PoleVal:'Foto',Value:src,PoleWhere:'Who',Where:WhoF},
							  type: "POST",
							  success: function(data) {  _ids1 = data; }//,
							//dataType: 'json'
							 })
							alert(_ids1);
							
						
						}
					
				}else{ alert("нет у новости картинки!")}
		}else{
			alert('речь не добавлена в БД, значит и картинку прикрепить нельзя');
		}
	}

</script>



<script type="text/javascript">

	function SaveSpeechBD(key){//запись речи в БД
		
		var Wh_id = 'Wh-'+key;
		Wh_id = document.getElementById(Wh_id).value;
		var Sp_id ='Sp-'+key;
		Sp_id = document.getElementById(Sp_id).value;
		var NeId_id = 'NeId-'+key;
		NeId_id= document.getElementById(NeId_id).value;
		var NeTi_id = 'NeTi-'+key;
		NeTi_id= document.getElementById(NeTi_id).value;	
		var NeDa_id = 'NeDa-'+key;
		NeDa_id= document.getElementById(NeDa_id).value;
		var Descr_id = 'De-'+key;
		Descr_id= document.getElementById(Descr_id).value;		
		
		

		//alert("речь -" +Sp_id);
		//alert(Wh_id+"___"+Sp_id+"___"+NeId_id+"___"+NeTi_id+"___"+NeDa_id);
		

		var _ids;
		$.ajax({
		  async: false, 
		  url: 'blocks/dinamic_scripts/Save_Speech.php',
		  data: {Wh:Wh_id,Sp:Sp_id,NeId:NeId_id,NeTi:NeTi_id,NeDa:NeDa_id,Nkey:key,Descr_id:Descr_id},
		  type: "POST",
		  success: function(data) {  _ids = data; }//,
		//dataType: 'json'
		 })
		
		alert(_ids);
		
		var YNSp = 'YNSp-'+key;
		document.getElementById(YNSp).innerHTML = 'добалена в БД';
		

	}

</script>

