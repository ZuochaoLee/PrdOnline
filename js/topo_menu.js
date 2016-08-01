$(document).ready(function(){
	// $('.tools').draggable({ 
	// 	axis:'y',
	// 	containment:"parent"
	// });
	// $('.content_wrap').draggable({
	// 	axis:'y',
	// 	containment:"parent"
	// });
	var left = $('.content_wrap').width() * (-1) + 27;
	// $('.content_wrap').css({
	// 	'left'	:	left
	// });
	var tmp = false;
	var $arrow = $('#mh_left');
	var $content_wrap = $('.content_wrap');
	$arrow.bind('click',function(){
		var $this 	= $(this);
		if (tmp) { 
			left = $('.content_wrap').width() * (-1) + 27;
			$content_wrap.stop().animate({'left': left},1000);
			$arrow.removeClass('mh_left').addClass('mh_right');
			tmp = false;
		}
		else {
			$content_wrap.stop().animate({'left': '0px'},1000);
			$arrow.removeClass('mh_right').addClass('mh_left');
			tmp = true;
		} 
	});
	/*工具栏收缩*/	
	var right = $('.tools').width() * (-1) + 27;
	$('.tools').css({
		'right'	:	right
	});
	var tmp1 = false;
	var $arrow1 = $('.out');
	var $tools = $('.tools');
	$arrow1.bind('click',function(){
		var $this 	= $(this);
		if (tmp1) { 
			right = $('.tools').width() * (-1) + 27;
			$tools.stop().animate({'right': right},1000);
			$arrow1.removeClass('in').addClass('out');
			tmp1 = false;
		}
		else {
			$tools.stop().animate({'right': '0px'},1000);
			$arrow1.removeClass('out').addClass('in');
			tmp1 = true;
		} 
	});
	get_topo_menu();//init_topo_menu();在get_topo_menu()de sucess中被调用
});
var setting = {
	view: {
		addHoverDom: addHoverDom,
		removeHoverDom: removeHoverDom,
		selectedMulti: false
	},
	edit: {
		drag:{
			isCopy: true,
			isMove: false,
			prev : true,
			inner : true,
			next : true
		},
		enable: true,
		editNameSelectAll: true,
		showRemoveBtn: showRemoveBtn,
		showRenameBtn: showRenameBtn
	},
	data: {
		simpleData: {
			enable: true
		}
	},
	callback: {
		beforeDrag: beforeDrag,
		beforeEditName: beforeEditName,
		beforeRemove: beforeRemove,
		onRemove: onRemove,
		onClick: onClick,
		beforeClick: beforeClick,
		beforeDblClick:beforeDblClick,
		onDblClick:onDblClick,
		beforeDrag: beforeDrag,
		beforeDrop: beforeDrop

	}
};

var zNodes = [];
var actionNode = null;
function init_topo_menu(zNodes){
	$.fn.zTree.init($("#treeDemo"), setting, zNodes);
	var zTree = $.fn.zTree.getZTreeObj("treeDemo");
	var node = zTree.getNodeByParam('id', 1, null);
	if(node.children){
		drawSubNode(node,'images/综合拓扑.png','images/自定义.png');
		drawSubNode(node.getNextNode(),'images/综合拓扑.png','images/自定义.png');
		//drawSubNode(node.getNextNode().getNextNode().getNextNode(),'images/综合拓扑.png','images/自定义.png');
		myDialog(null,'add',null);
		myDialog(null,'edit', null);
	}else{
	}
	zTree.expandAll(true);
}

function drawSubNode(node,text1,text2){
	if(node.children){
		for(var i=0;i<node.children.length;i++){
			if (!node.children[i].isParent) {
				if(node.children[i].topo_type==0){
					node.children[i].icon =text1 ;
					}
				else{
					node.children[i].icon=text2;
				}
			}
			else{//如果这个节点为父节点，则他下面的子节点也要画图片
				drawSubNode(node.children[i],text1,text2);
			}
		}
	}
}

//菜单栏结构及数据获取
function get_topo_menu(){
	$.post("./controller/connect_DB.php",{"operation":"0"},function(json) {
		json=$.parseJSON(json);
		for (var i=0; i<json.length; i++) {
			t_id = parseInt(json[i].id);
			t_name = json[i].name;
			t_url=json[i].url;
			t_pId = parseInt(json[i].catalog_fid);
			t_isParent = parseInt(json[i].is_catalog)==0?false:true;
			topo_type = parseInt(json[i].topo_type);
			if (t_isParent) {
				zNodes.push({id:t_id, pId:t_pId, name:t_name,url:t_url, isParent:t_isParent, iconOpen:'images/fl2.png',iconClose:'images/fl.png',topo_type:topo_type});
			}
			else {
				zNodes.push({id:t_id, pId:t_pId, name:t_name,url:t_url, isParent:t_isParent, topo_type:topo_type});
			}		
		}
		init_topo_menu(zNodes);
	});
	return zNodes;
}

function beforeDblClick(treeId, treeNode) {
	return !treeNode.isParent;
}
function onDblClick(event, treeId, treeNode) {
	 $.fn.zTree.getZTreeObj("treeDemo").editName(treeNode);
}

function beforeClick(treeId, treeNode, clickFlag) {
	return (!treeNode.isParent||treeNode.topo_type==3);
}

function onClick(event, treeId, treeNode) {
	if(treeNode.url == ""){
		$("iframe").attr("src","./default.html")
	}
	else{
		$("iframe").attr("src",treeNode.url)
	}

}
var log, className = "dark";
function beforeDrag(treeId, treeNodes) {
	return false;
}
function beforeDrop(treeId, treeNode){
	return false;
}
function beforeEditName(treeId, treeNode) {
	className = (className === "dark" ? "":"dark");
	var zTree = $.fn.zTree.getZTreeObj("treeDemo");
	zTree.selectNode(treeNode);
	actionNode = treeNode;
	$('#dialog_edit').dialog('open');
	if(actionNode.isParent){
		$('#mytoponame_3').val(actionNode.name);
	}else{
		$('#mytoponame_4').val(actionNode.name);
		$('#mytoponame_42').val(actionNode.url);
	}
	return true;
}
function beforeRemove(treeId, treeNode) {
	className = (className === "dark" ? "":"dark");
	var zTree = $.fn.zTree.getZTreeObj("treeDemo");
	zTree.selectNode(treeNode);//console.log(treeNode);
	var flag = true;
	if (confirm("确认删除 PRD-- " + treeNode.name + " 吗？")) {
		$.post("./controller/menu_delete.php",{"id":treeNode.id,"isParent":treeNode.isParent,"name":treeNode.name},function(json){
			flag = true;
		});
		if (flag) {//todo----这个地方关闭对应打开的tab页面
			zTree.removeNode(treeNode);
		}
	}
	return flag;
}
function onRemove(e, treeId, treeNode) {
	$.ligerDialog.warn('删除成功！');
}

function showRemoveBtn(treeId, treeNode) {
	if(role != 0)
		return treeNode.level && treeNode.name!='PRD展示' /*&& treeNode.name!="逻辑展示"*/&& treeNode.name!="视频指挥拓扑"&& treeNode.topo_type!=3;
}
function showRenameBtn(treeId, treeNode) {
	if(role != 0)
		return treeNode.level && treeNode.name!='PRD展示'/* && treeNode.name!="逻辑展示"*/&& treeNode.name!="视频指挥拓扑"&& treeNode.topo_type!=3;
}
function showLog(str) {
	if (!log) log = $("#log");
	log.append("<li class='"+className+"'>"+str+"</li>");
	if(log.children("li").length > 8) {
		log.get(0).removeChild(log.children("li")[0]);
	}
}
function getTime() {
	var now= new Date(),
	h=now.getHours(),
	m=now.getMinutes(),
	s=now.getSeconds(),
	ms=now.getMilliseconds();
	return (h+":"+m+":"+s+ " " +ms);
}

var newCount = 1;
var node_parent_id = 0;
function addHoverDom(treeId, treeNode) {
	if (role==0) return;
	instanceid=treeNode.id; 
    // if(treeNode.getParentNode()!=undefined) 
    	// node_parent_id = treeNode.getParentNode().id; 
		
	if(treeNode.getParentNode())
		var rootNode = treeNode.getParentNode();
	else
		var rootNode = treeNode;
	while(rootNode.pId)
		rootNode = rootNode.getParentNode();
	node_parent_id = rootNode.id;//追溯到最上层的目录
	var sObj = $("#" + treeNode.tId + "_span");
	if (treeNode.editNameFlag || $("#addBtn_"+treeNode.tId).length>0 || !treeNode.isParent|| treeNode.name=='业务拓扑'||treeNode.name=='视频指挥拓扑'||treeNode.topo_type==3) return;
	var addStr = "<span class='button add' id='addBtn_" + treeNode.tId
		+ "' title='添加' onfocus='this.blur();'></span>";
	sObj.after(addStr);
	var btn = $("#addBtn_"+treeNode.tId);
	if (btn) btn.bind("click", function(){
		var zTree = $.fn.zTree.getZTreeObj("treeDemo");
		actionNode = treeNode;
		$('#dialog_add').dialog('open');
		return false;
	});
	
};
function removeHoverDom(treeId, treeNode) {
	$("#addBtn_"+treeNode.tId).unbind().remove();
};
function selectAll() {
	var zTree = $.fn.zTree.getZTreeObj("treeDemo");
	zTree.setting.edit.editNameSelectAll =  $("#selectAll").attr("checked");
}

//ids是一个数组 返回结果数组 treeNode是选中的节点
function getChildren(ids, treeNode) {
	ids.push(treeNode);
	if (treeNode.isParent) {
		for (var obj in treeNode.children) {
			getChildren(ids, treeNode.children[obj]);
		}
	}
	return ids;
}

function jsonAjax(v_type, v_url, v_dataType, v_data, v_async, index, name, actionNode) {
	$.ajax({
		type: v_type,
		url: v_url,
		dataType: v_dataType,
		data: v_data,
		async: v_async,
		success: function(json) {//alert(json);
			if (index==1) successAction_1(json, name, actionNode);
			if (index==2) successAction_2(json, name, actionNode);
			if (index==3) successAction_3(json, name, actionNode);
			if (index==4) successAction_4(json, name, actionNode);
			
		}
	});
}

function successAction_1(json,name,actionNode) {
	if (json.co=="0") {
		//alert('插入目录成功 id:'+json.id);
		$.ligerDialog.success('添加目录成功！');
		$.fn.zTree.getZTreeObj("treeDemo").addNodes(actionNode, {id:json.id, pId:actionNode.id, name:name.toString(), isParent:true, iconOpen:'images/fl2.png',iconClose:'images/fl.png'});
		$('#dialog_add').dialog("close");
	}
	else {
		//alert('目录与其它目录重名');
		$.ligerDialog.warn('目录与其它目录重名');
	}
}

function successAction_2(json,name,actionNode) {
	console.log(json);
	json=JSON.parse(json)
	if (json.co=="0") {
		//alert('插入实例成功');
		$.ligerDialog.success('添加文档成功');
		iconImage='images/综合拓扑.png';
		iconImage='images/自定义.png';
		console.log(json);
		console.log(actionNode);
		var url=$('#mytoponame_22').val();
		$.fn.zTree.getZTreeObj("treeDemo").addNodes(actionNode, {id:json.id[0], pId:actionNode.id, name:name, isParent:false,topo_type:json.topo_type, icon:iconImage,url:url});
		$('#dialog_add').dialog("close");
	}
	else {
		//alert('实例名与其它实例名重名');
		$.ligerDialog.warn('实例名与其它文档名重名');
	}
}

function successAction_3(json, name,actionNode) {
	console.log(json);
	console.log(typeof(json));
	console.log(JSON.parse(json).co);
	if (JSON.parse(json).co =="0") {
		$.ligerDialog.success('修改目录成功！');
		var node = $.fn.zTree.getZTreeObj("treeDemo").getNodeByTId(actionNode.tId);
		node.name = name;
		$.fn.zTree.getZTreeObj("treeDemo").updateNode(node);
		$('#dialog_edit').dialog('close');
	}
	else {
		$.ligerDialog.success('目录与其它目录重名');
	}
}

function successAction_4(json, name, actionNode) {
	console.log(json);
	console.log(typeof(json));
	if (JSON.parse(json).co=="0") {
		$.ligerDialog.success('修改文档成功！');
		actionNode.name = name;
		actionNode.url =$('#mytoponame_42').val();
		$.fn.zTree.getZTreeObj("treeDemo").updateNode(actionNode);
		$('#dialog_edit').dialog("close");
	}
	else {
		alert('实例名与其它文档名重名');
	}
}
//添加、修改对话框,obj为对话框对象，text为按钮名称
//funC为预留                                                                                                                                                                                                                                                                                                                     
function myDialog(obj, text,funC) {
	var t = (text=='add')?'添加':'修改';
	$( "#tabs_"+text ).tabs({
		select: function(event,ui){
			if (ui.index == 0) {
				debugger;
			}
		}
	});
	$('#dialog_'+text).dialog(
		{
			show: {
				effect: "fold",
				duration: 500
			},
			hide: {
				effect: "fold",
				duration: 500
			},
			width: $(window).width()*0.65, 
			height:$(window).height()*0.4, 
			modal: true, 
			autoOpen:false,
			open:function(event) {  //打开对话框初始化
			 	for (var i=1; i<=4; i++) {
				 	$("#mytoponame_"+i).val("");
			 	}
			 	if (text == 'edit') {
				 	//topo_dialog_edit = new topo_Dialog('tabs-4',instanceid,node_parent_id,actionNode);
				 	$('#tabs_edit').tabs('disable');
				 	if (actionNode.isParent) {
					 	$('#tabs_edit').tabs('enable',0);
					 	$('#tabs_edit').tabs('option', 'active', 0);
				 	}
				 	else {
					 	$('#tabs_edit').tabs('enable',1);
					 	$('#tabs_edit').tabs('option', 'active', 1);
		             	//lele
					 	//topo_dialog_edit.sel.SetName("mytoponame_4",actionNode.name);
				 	}
			 	}
			 	else if(text == 'add') {
				 //topo_dialog = new topo_Dialog('tabs-2',instanceid,node_parent_id,actionNode);
				 	$('#tabs_add').tabs('option','active', 0);
				 	$('#tabs_add').bind("tabsselect",function(event,tab){alert("tabs-1");});
			 	}
			},
			buttons: [
				{
					text: t,
					click: function() {
						var active = $("#tabs_"+text ).tabs('option','active');
						if (text == 'add') {  //"添加"对话框
							if (active == 0)  { //"添加目录"
								var name = $('#mytoponame_1').val();
								if (name.toString() == '') {
									$.ligerDialog.warn('目录名称不能为空！');
								}
								else {
									data = 'action='+active+'&fid='+actionNode.id+'&name='+name.toString()+'&url=NULL';
									jsonAjax('post', './controller/menu_add.php', 'json',data,false,1, name.toString(), actionNode);
								}
							}
							else if (active == 1) {  //'添加实例' 
								var name = $('#mytoponame_2').val();
								var url = $('#mytoponame_22').val();
								if (name == '') {
									$.ligerDialog.warn('修改的目录名称不能为空!');
								}
								else {
									data = {'action':active,'fid':actionNode.id,'name':name.toString(),'url':encodeURI(url),'id':actionNode.id};
									console.log(data);
									jsonAjax('post', './controller/menu_add.php', 'text',data,false,2, name.toString(), actionNode);
								}	
							}
						}
						else if (text == 'edit') {  //"修改“对话框
							var name = $('#mytoponame_3').val();
							var fid = 0;
							if (actionNode.getParentNode()) {
								fid = actionNode.getParentNode().id;
							}
							if (active == 0) {  //"修改目录”
								var name = $('#mytoponame_3').val();
								if (name == '') {
									$.ligerDialog.warn('修改的目录名称不能为空!');
								}
								else {
									data = 'action='+active+'&fid='+fid+'&name='+name.toString()+'&id='+actionNode.id+'&url=NULL';
									jsonAjax('post', './controller/menu_edit.php', 'text',data,false,3, name.toString(), actionNode);
								}
							}
							else if(active==1) {  //'修改实例'
								var name = $('#mytoponame_4').val();
								var url = $('#mytoponame_42').val();
								if (name == '') {
									$.ligerDialog.warn('修改的目录名称不能为空!');
								}
								else {
									data = {'action':active,'fid':fid,'name':name.toString(),'id':actionNode.id,'url':encodeURI(url)};
									jsonAjax('post', './controller/menu_edit.php', 'text',data,false,4, name.toString(), actionNode);
								}
							}
						}
					}
				},
				{
					text: "取消",
					click: function() {
						if (confirm('确定取消?')) {
							$(this).dialog( "close" );

						}
					}
				}
			]
		}
	);
}
