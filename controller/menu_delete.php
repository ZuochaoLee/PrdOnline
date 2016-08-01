<?php
	include_once 'db_connector_S6.php';
	include_once('function.php');
	$id = $_REQUEST['id'];
	$isParent = $_REQUEST['isParent'];
	$name = $_REQUEST['name'];
	$sql="delete from catalog where id=$id or catalog_fid=$id";
	//echo $sql;
	$re=$dbo->query($sql);
	///return $re;
	echo JSON($name);
?>