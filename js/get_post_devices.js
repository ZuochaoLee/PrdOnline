var MENU_TABLE = "catalog";
var GROUP_TABLE = "groupinfo"
var DEVICE_TABLE = "devices";
var LINK_TABLE = "t_link";
var HISTORY_DEVICE_TABLE='history_devices';

//菜单栏结构及数据获取
function get_topo_menu(){
//alert(location.href);
	$.ajax({
				type: "post",
				url: "./controller/connect_DB.php",
				dataType: 'json',
				data: {"operation":"0"},
				//async:false, 
				success: function(json) {
					for (var i=0; i<json.length; i++) {
						t_id = parseInt(json[i].id);
						t_name = json[i].name;
						t_pId = parseInt(json[i].catalog_fid);
						t_isParent = parseInt(json[i].is_catalog)==0?false:true;
						topo_type = parseInt(json[i].topo_type);
						if (t_isParent) {
							zNodes.push({id:t_id, pId:t_pId, name:t_name, isParent:t_isParent, iconOpen:'images/fl2.png',iconClose:'images/fl.png',topo_type:topo_type});
						}
						else {
							zNodes.push({id:t_id, pId:t_pId, name:t_name, isParent:t_isParent, topo_type:topo_type});
						}		
					}
					init_topo_menu(zNodes);
				}
			});
	return zNodes;
}




//设备及链路数据获取,instance_id是拓扑菜单对应 的id，保存在groupinfo表里面,第2,3个参数是表名称
function get_topo_datas(instance_id,device_table,link_table,f,params){//alert(params);
	//默认GIS展示
	if (instance_id == undefined) instance_id = 4;
	var dvs = new Array();
	$.ajax({
			type: "post",
			url: "./controller/connect_DB.php",
			dataType: 'json',
			data: {"operation":"1","device_table":device_table,"instance_id":instance_id,"params":params},
			//async:false, 
			success: function(json) {
			//alert(json);
				if (json!=null) {//读取的设备不为空
					for (var i=0; i<json.length; i++) {
						var cid=new Array();
						var d_rate=new Array();
						if(json[i].jd < minLng ){
							minLng = json[i].jd;
						}
						if(json[i].jd > maxLng ){
							maxLng = json[i].jd;
						}
						if(json[i].wd < minLat ){
							minLat = json[i].wd;
						}
						if(json[i].wd > maxLat ){
							maxLat = json[i].wd;
						}
						
						////地理坐标字符串分割为double型的经度和纬度
						/*var lat_lng=json[i].g_coordinates.split(",");
						d_lat=Number(lat_lng[0].substr(1));
						d_lng=Number(lat_lng[1].substr(0,lat_lng[1].length-1));*/
						d={"d_id":json[i].id,"d_JLBM":json[i].JLBM,"d_name":json[i].dname,"d_status":json[i].status,"d_lat":json[i].wd,"d_lng":json[i].jd,"c_id":cid,"l_status":cid,"d_maintenance":json[i].maintenance,"d_contact":json[i].contact,"d_phone":json[i].phone,"d_rate":d_rate,"d_level":json[i].d_level,"d_LSDDW":json[i].LSDDW};
						dvs.push(d);
						
					}
				}else{
					if(f){//非历史回放才关闭窗口
						alert("该拓扑实例下没有节点，请在修改拓扑实例中添加");
						parent.f_closeCurrentTab();
					}
				}
				$.ajax({
					type: "post",
					url: "./controller/connect_DB.php",
					dataType: 'json',
					data: {"operation":"2","device_table":device_table,"instance_id":instance_id,"link_table":link_table,"params":params},
					//async:false, 
					success: function(json1) {//console.log(json1);
						//alert(json1);
						for(var i=0;i<dvs.length;i++){
							var cid=new Array();
							var d_rate=new Array();
							var l_status=new Array();
							var c_d; 
							
							//与设备相连的设备放入c_id中，链路的状态放入l_status中
							if(json1!=null){//设备之间有连线
								for(var j=0;j<json1.length;j++){
									if(dvs[i].d_id==json1[j].id1 || dvs[i].d_id==json1[j].id2){
										if(dvs[i].d_id==json1[j].id1 && parseInt(dvs[i].d_id)<parseInt(json1[j].id2)){    //c_id中所有设备的id应小于d_id
									//		c_d={"node_id":json1[j].id2,"d_input":json1[j].input1,"d_output":json1[j].output1,"c_input":json1[j].input2,"c_output":json1[j].output2};
										//	cid.push(c_d);
											c_d={"c_name":json1[j].name2,"d_input":json1[j].input1,"d_output":json1[j].output1,"c_input":json1[j].input2,"c_output":json1[j].output2};								
											cid.push(parseInt(json1[j].id2));
											d_rate.push(c_d);
											l_status.push(json1[j].status);
										}else if(dvs[i].d_id==json1[j].id2 && parseInt(dvs[i].d_id)<parseInt(json1[j].id1)){
									
											c_d={"c_name":json1[j].name2,"d_input":json1[j].input2,"d_output":json1[j].output2,"c_input":json1[j].input1,"c_output":json1[j].output1};						
											cid.push(parseInt(json1[j].id1));
											d_rate.push(c_d);
											l_status.push(json1[j].status);
										}
									}
								}
								
								dvs[i].c_id=cid;
								dvs[i].d_rate=d_rate;
								dvs[i].l_status=l_status;
							}
						}
						
						if(params==""){//非历史回放状态
							draw_device(f,dvs);
							devices = dvs;
							// console.log(dvs);
							// console.log(devices);
							setTimeout(function(){get_refresh_data(topo_instance_id,device_table,link_table,f,params);}, 30000);//节点添加好之后开启自动刷新30s
						}else{//历史回放状态
							for(var i=0;i<markers1.length;i++){
								markers1[i].setMap();
							}		
							for(var i=0;i<dvs.length;i++){
								var city=new google.maps.LatLng(dvs[i].d_lat,dvs[i].d_lng);
								   markers1[i]=addDevice(city,i,0,dvs);
							}
							devices = dvs;
						}
					}
				});			
			}
		});

	
	//return dvs;
}

//修改设备后插入数据
//idTemp代表增加的链路信息，filtered_devices代表改变的设备位置

// function save_topo_change(filtered_devices,timestamp){
	// //alert(timestamp);
	// $.ajax({
				// type:"post",
				// url:"./controller/connect_DB.php",
				// dataType:'text',
				// data:{"operation":"3","filtered_devices":filtered_devices,"timestamp":timestamp},
				// async:false, 
				// success:function(msg){
					// //alert(msg);
					// //alert(11);
					// //传递成功后清除数据
					// filtered_devices.length = 0;
					// changed_position_devices.length = 0;
					// is_changed = false;
				// }	
			// });
	// /*if ( 0 != idTemp.length){
		    // $.ajax({
				 // type:"post",
                 // url:"connect_DB.php",
				 // dataType:'text',
				 // data:{operation:"0",idTemp:idTemp,l_id:l_id+1,"tablename":LINK_TABLE},
				 // success:function(){
     				 // alert("新添加连线已经保存到数据库");
					 // idTemp=[];	
                     // //alert(idTemp+"miao");					 
				     // }	
			     // });
		// }*/
// }

//取得所选日期当天 历史表中的时间点
function get_day_changetime(h_now,instance_id){
	var temp=new Date(h_now*1000);
	var table_name='table'+formatDate(temp);
	var time_changed = new Array();
	
	$.ajax({
			type:"post",
			url:"./controller/connect_DB.php",
			dataType:'json',
			data:{"operation":"6","table_name":table_name,"instance_id":instance_id},
			async:false, 
			success:function(json){//alert(json.length);
			   if (json!=null){
					for(var i=0;i<json.length;i++){
						time_changed.push(json[i].times_changed);
					}
				}
			}
	});
	//alert(time_changed);
	return time_changed;
}
//将获取到的时间戳h_now转换为yyyymmdd的日期
function formatDate(h_now){
	var year = h_now.getFullYear();
	var month = h_now.getMonth()+1;
	var day = h_now.getDate();
	if(month<10){
		month='0'+month;
	}else{
		month = month.toString();  //如果不将其转换成字符串，当月份为10,11,12时，其类型是int，return中会做数字相加，而不是字符串拼接
	}
	if(day<10){
		day='0'+day;
	}else{
		day = day.toString();
	}
	
	return (year+month+day);
}
