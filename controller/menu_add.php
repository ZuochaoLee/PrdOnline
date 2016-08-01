<?php
	include_once 'db_connector_S6.php';
	include_once('function.php');
	$CATALOG_TABLE = "catalog";
	$name = $_REQUEST['name'];
	$active = $_REQUEST['action'];
	$fid = $_REQUEST['fid'];
	$url = $_REQUEST['url'];
	if ($active==0) {  //添加文件夹
		$sql="select count(*) from catalog where catalog_fid=$fid and name='$name'";
		$dbo->query($sql);
		$row = $dbo->read();

		if($row[0]==0){
			$sql="insert into catalog(name,is_catalog,catalog_fid,url) values('$name',1,$fid,'$url')";
			if( $dbo->query($sql) ){
				$sql="select 0 ,@@IDENTITY as id";
				$dbo->query($sql);
				$row = $dbo->read();
				$v = array(
					'co' => $row[0],
					'id' => $row[1],
				);
				echo JSON($v);
			}
		}else{
			$v = array(
				'co'=>$row[0]
			);
			echo JSON($v);
		}
	}
	else if ($active == 1) {  //添加实例
	    $sql="select count(*) from catalog where catalog_fid=$fid and name='$name'";
	    $dbo->query($sql);
	    $row = $dbo->read();
	    
	    if($row[0]==0){
	        $sql="insert into catalog(name,is_catalog,catalog_fid,url) values('$name',0,$fid,'$url')";
	        if( $dbo->query($sql) ){
	            $sql="select 0 ,@@IDENTITY as id";
	            $dbo->query($sql);
	            $row = $dbo->read();
	            $v = array(
	                'co' => $row[0],
	                'id' => $row[1],
	            );
	            echo JSON($v);
	        }
	    }else{
	        $v = array(
	            'co'=>$row[0]
	        );
	        echo JSON($v);
	    }
		
	}
?>