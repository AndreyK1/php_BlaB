<?
/*
header('Content-Type: text/html; charset=utf-8');//собираем всю информацию об защедщих на сайт
ini_set("max_execution_time", "60");
//set_time_limit (60); 
//htaccess :  	php_value max_execution_time 60
session_start();
include_once('startup.php');
	// Установка параметров, подключение к БД, запуск сессии.
	startup();
*/
	
	//Как использовать прокси в PHP функции file_get_contents?
	//http://kadomtsev.ru/kak-ispolzovat-proksi-v-php-funkcii-file_get_contents/
	

//ВАЖНО!!!!		
//прооверяем, что мы админ
	//var_dump($_SESSION['Guest_id']['id_user']);
include('variables.php');
if($_SESSION['Guest_id']['id_user'] == $AdminID){  //   !!!ВАЖНО!!!
	?>
	"Вы Админ продолжаем дальше<br />
	 <table>
		<tr>
			<td>
				 <a style='color:orange;' href='rss_t.php'>Берем новости</a> <b style='color:green;'>STAR</b><br />
				<a style='color:orange;' href="rss_news_inBD.php">Вытаскиваем последние новости в базу</a> <b style='color:red;'>AVT</b><br />
				<a style='color:orange;' href="rss_speech_inBD.php">Вытаскиваем из новостей речи и запихиваем в БД</a> <b style='color:red;'>AVT</b><br />
			</td>
			<td>
				<a style='color:orange;' href="Last_20.php?action=LastSpeech">Показать 20 последних речей</a><br />
				<a style='color:orange;' href="Last_20.php?action=LastNewsWith">Показать 20 последних новостей с речами</a><br />
				<a style='color:orange;' href="Last_20.php?action=LastNewsWithOut">Показать 20 последних новостей без речей</a><br />
			</td>
		</tr>
	</table>
	<?
}else{}
?>
<!--<br /><a style='color:orange;' href="index.php?c=adminka">В АДМИНКУ</a><br /><br />-->








