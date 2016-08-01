<?php

	$role = $_GET['role'];         //拓扑管理权限
	if($role==0||$role==1){
		echo "<script>var role = $role;</script>";
	}else{
		header("location: index.php");
	}
    
?>
<html>
<head>
<title>诸葛找房prd系统</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<link rel="stylesheet" type="text/css" href="css/ligerui-all.css" />
<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.10.3.custom.css" />
<link rel="stylesheet" type="text/css" href="css/demo.css" >
<link rel="stylesheet" type="text/css" href="css/zTreeStyle/zTreeStyle.css" >
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/topo_menu.css" />
<link rel="stylesheet" type="text/css" href="css/topo_index.css"/>

<script type="text/javascript" src="./js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="js/ligerui.all.js" ></script>
<script type="text/javascript" src="js/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript" src="js/jquery.easing.1.3.js" ></script>
<script type="text/javascript" src="js/jquery.ztree.all-3.5.js"></script>
<script type="text/javascript" src="js/topo_menu.js"></script>
<script type="text/javascript" src="js/topo_index.js"></script>
<script type="text/javascript" src="js/demo.js"></script>
 
</head>

<body id="all" style="width:100%;height:100%;overflow:hidden;">
	<!--header>
		<div class="hed" ><img class="had" src="images/logo.png"></div>
		<div class="t1">诸葛找房PRD系统</div>
	</header-->
	
	<!--菜单栏div-->
	<div class="content_wrap" style="width:261px; height:100%;left:0px;top:75px;position:absolute; z-index:3">
		<div class="zTreeDemoBackground noprint" style="float:left;">
			<div id="treeDemoTitle">&nbsp;&nbsp;<img class="had"src="images/logo.png" /><span>&nbsp;诸葛找房PRD系统</span></div>
			<div id="treeContent"> 
				<ul id="treeDemo" class="ztree"></ul>
			</div>
		</div>
		<div id="mh_left" class="mh_left noprint" style="float:left;"></div>
	</div>
	<?php include("topo_dialog.php")?>
	<iframe id="all" style="width:100%;height:100%;overflow:hidden;" src="./default.html"></iframe>


</body>
</html>
