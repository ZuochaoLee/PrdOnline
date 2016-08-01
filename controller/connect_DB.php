<?php

	$MENU_TABLE = "catalog";
	$GROUP_TABLE = "groupinfo";
	$DEVICE_TABLE = "topo_nodes";
	$LINK_TABLE = "t_link";
	/*<!--$HISTORY_DEVICE_TABLE='history_devices';-->*/


	include_once 'db_connector_S6.php';
	include_once('function.php');

	/*
		opertion的值的属性
		
		0：初始化menu菜单栏
		1：读nodes
		2: 读links的表
		3：用户修改链路后点击保存，对数据库update
		4：用户删除链路后点击保存，delete 表t_link
		6: 读历史记录中的时间点
		7: 读选中军区下的节点

	*/
	$operation = $_POST['operation'];

	if ($operation == "0") {
		$sql = "select * from $MENU_TABLE";
		$dbo->query($sql);
		while($rows=$dbo->read()){
			$arr[]=$rows;
		}
		echo JSON($arr);
	}

?>





