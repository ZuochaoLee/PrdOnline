<?php
	date_default_timezone_set('PRC');
	include 'db_mysql_S6.php';
	include 'db_mysql_info.php';//��ȡ���ݿ�������Ϣ
	global $dbo;

	$dbo = new XBMySql();
	
	//��������
	$dbo->connect($database, $username, $password, FALSE, $serverip , $serverPort);
	$dbo->query('set names utf8');
?>