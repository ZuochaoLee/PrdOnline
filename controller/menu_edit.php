<?php
	include_once 'db_connector_S6.php';
	include_once('function.php');
	$CATALOG_TABLE = "catalog";
	$name = $_REQUEST['name'];
	$active = $_REQUEST['action'];
	$fid = $_REQUEST['fid'];
	$url = $_REQUEST['url'];
	$id = $_REQUEST['id'];
	if ($active==0) {  //更新文件夹
		$sql="update catalog set name='$name' where id=$id";
		if( $dbo->query($sql) ){
			$sql="select 0 ,@@IDENTITY as id";
			$dbo->query($sql);
			$row = $dbo->read();
			$v = array(
				'co' => $row[0],
				'id' => $row[1]
			);
			echo JSON($v);
		}
	}
	else if ($active == 1) {  //添加实例
        $sql="update catalog set name='$name',url='$url' where id=$id";
        if( $dbo->query($sql) ){
            $sql="select 0 ,@@IDENTITY as id";
            $dbo->query($sql);
            $row = $dbo->read();
            $v = array(
                'co' => $row[0],
                'id' => $row[1]
            );
            echo JSON($v);
        }
	}
?>