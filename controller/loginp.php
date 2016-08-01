<?php
	include_once 'db_connector_S6.php';
	include_once('function.php');
	$username=$_GET["username"];
	$passwd=md5($_GET["p"]);
	$sql="select count(*),`role` from user where username='$username' and password='$passwd'";
	$dbo->query($sql);
	$row = $dbo->read();
	//print_r($row);
	if($row[0]==0){
		echo header("location: ../index.php?err=1");
	}else{
		echo header("location: ../main.php?role=".$row[1]);
	}
?>