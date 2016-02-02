<?php
function startup()
{
	// Настройки подключения к БД.
	$hostname = 'localhost'; 
	$username = 'probeyuser'; 
	$password = '12345';
	$dbName = 'BlaBase';
	
	// Языковая настройка.
	setlocale(LC_ALL, 'ru_RU.utf8');	
	
	// Подключение к БД.
	mysql_connect($hostname, $username, $password) or die('No connect with data base'); 
	mysql_query('SET NAMES utf8');
	mysql_select_db($dbName) or die('No data base');

	// Открытие сессии.
	ini_set('session.cookie_httponly',1);
	session_start();
	//error_reporting(0);
		
}
