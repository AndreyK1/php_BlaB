<?
//Вытаскиваем последние новости в базу
header('Content-Type: text/html; charset=utf-8');//собираем всю информацию об защедщих на сайт
ini_set("max_execution_time", "60");
//set_time_limit (60); 
//htaccess :  	php_value max_execution_time 60
session_start();
include_once('startup.php');
	// Установка параметров, подключение к БД, запуск сессии.
	startup();

	// команда для крона (в первую минуту, раз в три часа)  1	*/3	*	*	*	cd /var/www/vhosts/probey.net/BlaBase/ ; /usr/bin/php /var/www/vhosts/probey.net/BlaBase/rss_news_inBD.php
	
	
	//Как использовать прокси в PHP функции file_get_contents?
	//http://kadomtsev.ru/kak-ispolzovat-proksi-v-php-funkcii-file_get_contents/
	

//ВАЖНО!!!!		
//прооверяем, что мы админ
	//var_dump($_SESSION['Guest_id']['id_user']);
include('variables.php');


		//echo "\n m \n a \n i \nl";

/*
if($_SESSION['Guest_id']['id_user'] == $AdminID){  //   !!!ВАЖНО!!!
}else{die('Вы не Админ!!!');}
echo "Вы Админ продолжаем дальше";
*/
?>
Вытаскиваем последние новости в базу

<br /><a style='color:orange;' href="index.php?c=adminka">В АДМИНКУ</a><br /><br />

<?


	// подключаем библиотеку simple_html_dom
	require_once 'simple_html_dom.php'; 

	//парсинг RSS в Массивы
	function ParseRSSInMassiv($url,$from){
		$aqrrOfNewsIn = Array();	
		if($url !=''){
		//echo $url;
			/*$xml = xml_parser_create();     //создаёт XML-разборщик
			xml_parser_set_option($xml, XML_OPTION_SKIP_WHITE, 1);  //устанавливает опции XML-разборщика
			xml_parse_into_struct($xml, file_get_contents($url), $element, $index); //разбирает XML-данные в структуру массива, все передается в массив $index
			xml_parser_free($xml);  //освобождает XML-разборщик
			*/
		
			//достаем текст
			//$data = file_get_html($url);
			$str = file_get_html($url);
			//echo $str;
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
						$arr['fromN']= $from;
						
						
						$aqrrOfNewsIn[] = $arr;
				}
			}
			$data->clear(); // очишаем
			unset($data);
			
		}
		return $aqrrOfNewsIn;
	}





	//парсинг в Массивы новостей из источников поочереди
	function ParseInMassivFrom($from){

		
		//создаем массив, куда будем складывать новости
		$aqrrOfNews = Array();
		
		//массив новостей которых еще нет в базе
		$aqrrOfNewsNeed = Array();
		
		//проверяем с какого сайта забираем
		$url ='';
		if($from=='ria'){
			echo "Забираем РИА новости в БД<br />";
			$url ='http://ria.ru/export/rss2/politics/index.xml';
		}elseif($from=='itar-tass'){
			echo "Забираем ИТАР-ТАСС новости в БД<br />";
			$url ='http://itar-tass.com/rss/v2.xml';
		}
		
		
		
		//парсим RSS в Массивы
		$aqrrOfNews = ParseRSSInMassiv($url,$from);
		//var_dump($aqrrOfNews);
		
		//вытаскиваем последнюю дату новости из базы для этого источника
		global $db_news;
		$query = "SELECT pubDate FROM $db_news.News_foreign WHERE fromN = '".$from."' ORDER BY pubDate DESC LIMIT 1 ";	//
		//die($query);
			$result = mysql_query($query) or die(mysql_error());
			if($result){
				$row = mysql_fetch_assoc($result);
				echo "последняя дата в базе ".$row['pubDate'];
			}
		 if(count($aqrrOfNews)>0){
		 
			//отсекаем те новости, которые уже есть в базе
			/*for($i=0; $i<count($aqrrOfNews);$i++){
				if($aqrrOfNews[$i]['pubDate'] <= $row['pubDate']){
					unset($aqrrOfNews[$i]);
				}
			}*/
		 

			//выводим новости в форму для выбора
			?>		
				<form  method="post">
					<input type="hidden" value='' name='ChooseNews' />
					<b>Выберите новости для добавления в базу</b><input type="submit" value="Отправить"><br />
					<?$Num=0;
					foreach($aqrrOfNews as $news){
						$style = '';
						if($news['pubDate'] <= $row['pubDate']){
							$style = "style='color:#ccc;'";
							
						}else{
							//отсекаем те новости, которые уже есть в базе
							$aqrrOfNewsNeed[] =	$news;		
						}
					?>
						<input type="checkbox" name="option<?=$Num++?>" value="<?=$news['pubDate']?>"><span <?=$style?> title="<?=$news['description']?>"><?=$news['pubDate']?> | <?=$news['title']?></span><br />
					<?

					
					}?>
				</form>
			<?
						
				//var_dump($aqrrOfNewsNeed);
				
		 }else{
			echo "<br />!!!Нету новостей для выбора!!!<br /><br />";
		 }
		
		return $aqrrOfNewsNeed;
	}



//массив новостей которых еще нет в базе
$arr1 = ParseInMassivFrom('ria');
$arr2 = ParseInMassivFrom('itar-tass');
$aqrrOfChosenNews = array_merge($arr1,$arr2); 
//var_dump($aqrrOfChosenNews);


echo "\n\n\n\n";

	//забор новости с сайта
	function grabbNewsToBD($link,$from){
		if($link){
		//достаем текст
			$data = file_get_html($link);
			if($data->innertext!=''){// and count($data->find('title'))){
				$text = Array();
				if($from == 'ria'){
					foreach($data->find('#article_full_text') as $a){
						//echo "<br />11-".$a->plaintext."-11<br />";
						$text['plaintext'] = $a->plaintext;
						$text['innertext'] = $a->innertext;
					}			
				}
				if($from == 'itar-tass'){
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
	
	
	
	
	//если есть выбранные массивы
	if(count($aqrrOfChosenNews)>0){
		//переворачиваем массив
		$aqrrOfChosenNews = array_reverse($aqrrOfChosenNews);
		$aqrrOfNews = $aqrrOfChosenNews;
		
		//вытаскиваем последнюю новость из базы для этого источника
		/*$query = "SELECT * FROM $db_news.News_foreign WHERE fromN = '".$_GET['from']."' ORDER BY id DESC LIMIT 1 ";	//
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
		*/
		
		//пробегаемся по массиву, и те которые позже закидываем в БД вместе с текстом из источника
echo "\n\n";
		$new=0;
		for($i=0; $i<count($aqrrOfNews); $i++){
			//if(($last =='') or ($last < $aqrrOfNews[$i]['pubDate'])){
				$new++;
				//записываем новость в БД
				echo "\n<br />записываем в бд новость из ".$aqrrOfNews[$i]['fromN']." - ".$aqrrOfNews[$i]['title']."<br />";
				
				//вытаскиваем текс новости с сайта
				//$aqrrOfNews[$i]['plainText'] = grabbNewsToBD($aqrrOfNews[$i]['link']);
				$t = grabbNewsToBD($aqrrOfNews[$i]['link'],$aqrrOfNews[$i]['fromN']);
				$aqrrOfNews[$i]['plainText'] = $t['plaintext'];
				$aqrrOfNews[$i]['innertext'] = $t['innertext'];
				
				$aqrrOfNews[$i]['title'] =  mysql_real_escape_string($aqrrOfNews[$i]['title']);
				$aqrrOfNews[$i]['link'] =  mysql_real_escape_string($aqrrOfNews[$i]['link']);
				$aqrrOfNews[$i]['pubDate'] =  mysql_real_escape_string($aqrrOfNews[$i]['pubDate']);
				$aqrrOfNews[$i]['img'] =  mysql_real_escape_string($aqrrOfNews[$i]['img']);
				$aqrrOfNews[$i]['description'] =  mysql_real_escape_string($aqrrOfNews[$i]['description']);
				$aqrrOfNews[$i]['plainText'] =  mysql_real_escape_string($aqrrOfNews[$i]['plainText']);
				$aqrrOfNews[$i]['innertext'] =  mysql_real_escape_string($aqrrOfNews[$i]['innertext']);

				$query = "INSERT INTO $db_news.News_foreign (title,fromN,link,description,pubDate,img,plainText,tegText,speech) VALUES ('".$aqrrOfNews[$i]['title']."','".$aqrrOfNews[$i]['fromN']."','".$aqrrOfNews[$i]['link']."','".$aqrrOfNews[$i]['description']."','".$aqrrOfNews[$i]['pubDate']."','".$aqrrOfNews[$i]['img']."','".$aqrrOfNews[$i]['plainText']."','".$aqrrOfNews[$i]['innertext']."','')";
				//echo $query;
				$result = mysql_query($query);
				
			//}
		}
		if($new==0){
			echo "\n<br />!!!Небыло выбрано новостей!!!<br /><br />";
		}else{
			echo "\n<br />!!!Были добавлены новые новости!!!<br /><br />";
		}
	}


?>





