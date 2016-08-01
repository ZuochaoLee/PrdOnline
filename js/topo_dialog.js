function topo_Dialog(id,instanceId,node_parent_id,node) {
    //console.log(instanceId);
    console.log(id);
	var self = this;
	
		var sel = new SelectorView(id,instanceId,node);
	if(instanceId==2||node_parent_id==2){
      sel.src.header = {
				did			: 'Id',
				devname 	: '设备名称',
				name		: '节点名称',
				//factory		 : '所属大单位',
				junqu		: '所属战区'
			};
			sel.dst.header = {
				did			: 'Id',
				devname 	: '设备名称',
				name		: '节点名称',
				//factory		 : '所属大单位',
				junqu		: '所属战区'
			};
      sel.src.dataKey = 'did';
		  sel.dst.dataKey = 'did';

	}else{
      sel.src.header = {
			id			: 'Id',
			name		: '节点名称',
			factory		 : '所属大单位',
			junqu		: '所属战区'
		};
		sel.dst.header = {
			id			: 'Id',
			name		: '节点名称',
			factory		 : '所属大单位',
			junqu		: '所属战区'
		};

		  sel.src.dataKey = 'id';
		  sel.dst.dataKey = 'id';
	}
    
		sel.src.title = '可选';
		sel.src.display.filter = true;
		sel.src.display.pager = true;
		sel.src.pager.size = 10;
		sel.src.pager.maxButtons = 2;

		sel.dst.title = '已选';
	//	sel.dst.display.filter = true;
		sel.dst.display.pager = true;
		sel.dst.pager.size = 10;
		sel.dst.pager.maxButtons = 2;
		sel.render();

		var arr = [];
		var arrright=[];
		var arrleft=[];
		var arrright1=[];
		var i = 0;
//		var t_url = '';
//		if (id=='tabs-2') {
//			t_url = "./controller/SelectDevices.php";
//		}
//		else if (id=='tabs-4') {
//			t_url = "./controller/selectInsDevices.php";
//		}
		$.ajax({
			type: "post",
			url: './controller/SelectDevices.php',
			dataType: 'json',
			data:{'operation':"1"},
			async:false, 
			success: function(json) {
				for (i=0; i<json.length; i++) {
					  arr.push({id:parseInt(json[i].id),did:parseInt(json[i].did),devname:json[i].devname, name:json[i].Nodename,factory:json[i].factory,junqu:json[i].junqu,JLBM:json[i].JLBM})
				}			
			}
		});
		//console.log(arr[0]);
		if(instanceId!=2&&node_parent_id!=2){
			arr_tmp=new Array();
			arr_tmp[0]=arr[0];
			for (var ii=1; ii<arr.length; ii++){
				if(arr[ii].name!=arr_tmp[(arr_tmp.length-1)].name){
					arr_tmp.push(arr[ii]);
				}
			}
			arr=arr_tmp;
		}
		//console.log(arr);
		if(id=='tabs-4'){
			var php_url = "./controller/logic_show1.php";
			var operation = "1";
			if(node_parent_id==2){
				php_url = "./controller/server_data.php";
				operation = "7";
			}
			console.log(php_url);
			$.ajax({
		     type:"post",
			 url:php_url,
			 dataType:"text",
			 data:{'operation':operation,'instance_id':instanceId},
			 async:false,
			 success:function(data){
				 console.log(data);
			 	var json = eval("("+data+")"); 
			    if(json){
			        for(var i=0;i<json.length;i++){
				        arrright.push({id:parseInt(json[i].id)});
				    }
			    }
			 }
			});
			console.log(arrright);
			console.log(arr);
			for(var i=0;i<arr.length;i++){
				var k=false;
				if(arrright.length!=0){
					for(var j=0;j<arrright.length;j++){
						if(node_parent_id==2){
							if(arr[i].did==arrright[j].id){
								arrright1.push(arr[i]);
								k=true;
								break;
							}
						}else{
							if(arr[i].id==arrright[j].id){
								arrright1.push(arr[i]);
								k=true;
								break;
							}
						}
						
					}   
				}
				if(!k){
					arrleft.push(arr[i]);   
				}
			}
			console.log(arrright1);
			console.log(arrleft);
			sel.src.addRange(arrleft);
			sel.dst.addRange(arrright1);
		}
		else
	    {
			sel.src.addRange(arr);
	    }
		this.sel = sel;
}
