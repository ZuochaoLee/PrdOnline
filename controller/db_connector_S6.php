<?php
	date_default_timezone_set('PRC');
	include 'db_mysql_S6.php';
	include 'db_mysql_info.php';//获取数据库连接信息
	global $dbo;

	$dbo = new XBMySql();
	
	//建立连接
	$dbo->connect($database, $username, $password, FALSE, $serverip , $serverPort);
	$dbo->query('set names utf8');
?>