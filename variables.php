<?php //файл определения различных переменных

//какой id пользователя является админом, для доступа к парсингу новостей
$AdminID = "557"; //если на локалке socset/ иначе пусто

$maxRSSNews = 12; //максимальное кол-во последних новостей в RSS для парсинга
	
$UrlOfServer = 'http://localhost/blabase';

$db_news = "BlaBase_news";//база с таблицей с новостями

$num = 10; //речей на странице
//если я админ то здесь ставим
//$_SESSION['Guest_id']['id_user'] = '557';

//unset($_SESSION['Guest_id']);




/*

//кол-во записей (коментариев) на странице
$numof = 10;
//urlAdressServera


//определяет надо ли после $_SERVER['SERVER_NAME'] вставлять socset или нет, использ-ся в m_mail И c_registration
$hostddr = "socset/"; //если на локалке socset/ иначе пусто



//для подключения к базе рерайта
$hostnameRer = 'localhost'; 
$usernameRer = 'probeyuser'; 
$passwordRer = '12345';
$dbNameRer = 'rer_forNet3d';
*/

?>