<?php

class ExportFile
{
  var $fp=null;

  //构造函数
  function __construct($filename, $filetype)
  {

  	$this->fp=fopen($filename,'w');
  }

  //析构函数
  function __destruct()
  {
 	    fclose($this->fp);
  }

  //写每个字段
  function WriteField($field)
  {
  	//echo "<$field>";
  	fwrite($this->fp,"\"$field\",");
  }

  //写行结束标志
  function WriteEndofLine()
  {
  	fwrite($this->fp,"\n");
  }

}

class XBMySql
{
 var $_connection = null;
 var $_recordset = null;
 var $_AllNumber = null;
 var $_AllPage = null;
 var $_StartPage = null;
 var $_PageSize = null;

 /**
  * 析构函数
  */

 function __destruct()
 {
  if ($this->_connection != null)
  {
   if ($this->_recordset != null && !is_bool($this->_recordset))
   {
    mysql_free_result($this->_recordset);
   }
   mysql_close($this->_connection);
  }
 }


 /**
  * 连接数据库
  */

//function connect($Sid = 'buptyq', $User = 'root', $Password='pris410', $Permanent = FALSE, $Ip = '59.64.139.50', $port = '33060')
//function connect($Sid = 'pris_mobile', $User = 'root', $Password='pris410', $Permanent = FALSE, $Ip = '59.64.137.6', $port = '3306')
//function connect($Sid = 'pris_public_info', $User = 'root', $Password='pris410', $Permanent = FALSE, $Ip = '10.103.13.212', $port = '3306')
function connect($Sid = 'wsnms', $User = 'ipcc', $Password='root', $Permanent = FALSE, $Ip = '10.103.14.63', $port = '3306')
 {
 if ($this->_connection != null)
  {
   if ($this->_recordset != null)
   {
    mysql_free_result($this->_recordset);
   }
   mysql_close($this->_connection);
  }
  $this->_connection = mysql_connect($Ip.':'.$port,$User,$Password);

  if (!$this->_connection)
  {
   die('failed to connect the mysql database.'. mysql_error());
  }
  $db_selected = mysql_select_db($Sid, $this->_connection);

  if (!$db_selected)
  {
  die ("Can\'t use ".$Sid." : " . mysql_error());
  }


 }

 /**
  * 释放操作
  */

 function freeQuery()
 {
   if ($this->_recordset != null && !is_bool($this->_recordset))
   {
    mysql_free_result($this->_recordset);
   }
 }

 /**
  * 执行简单查询
  */
 function query($Sql)
 {
    if (!$this->_connection) return FALSE;

    $this->freeQuery(); //如果以前查询过，先释放一下

    $this->_recordset = mysql_query($Sql,$this->_connection);

    return true;
 }
 function insert($Sql)
 {
    mysql_query( $Sql,$this->_connection);
 }

 /**
  * 执行特定插入操作,如果插入的数据表中已经存在,则返回1,如果插入成功,则返回0
  */
 function judgeInsert($Sql, $tablename, $fieldname,$value)
 {
  if (!$this->_connection) return FALSE;

  $this->freeQuery(); //如果以前查询过，先释放一下

  //先检查数据表中是否存在含有该值的字段
  $temp_recordset = @mysql_query( "select count(*) as JUDGENUM from $tablename where $fieldname='$value' ",$this->_connection);
  if (!$temp_recordset)
  {
    die('error: '. mysql_error());
  }
  $row = mysql_fetch_array($temp_recordset);
  $existRecord = $row['JUDGENUM'];
  mysql_free_result($temp_recordset);

  //existRecord为1，表示数据表中已经存在，返回1。
  if ($existRecord) return 1;

  //开始真正的插入
  $this->_recordset = @mysql_query( $Sql,$this->_connection);
  if (!$this->_recordset)
  {
    die('error: '. mysql_error());
  }


   return 0;
 }

  /**
  * 执行特定插入操作,检查两个字段,如果插入的数据表中已经存在,则返回1,如果插入成功,则返回0
  */
 function judgeInsert2($Sql, $tablename, $fieldname,$value,$fieldname2,$value2)
 {
  if (!$this->_connection) return FALSE;

  $this->freeQuery(); //如果以前查询过，先释放一下

  //先检查数据表中是否存在含有该值的字段
  $temp_recordset = @mysql_query( "select count(*) as JUDGENUM from $tablename where $fieldname='$value' and $fieldname2='$value2' ",$this->_connection);
  if (!$temp_recordset)
  {
    die('error: '. mysql_error());
  }
  $row = mysql_fetch_array($temp_recordset);
  $existRecord = $row['JUDGENUM'];
  mysql_free_result($temp_recordset);

  //existRecord为1，表示数据表中已经存在，返回1。
  if ($existRecord) return 1;

  //开始真正的插入
  $this->_recordset = @mysql_query( $Sql,$this->_connection);
  if (!$this->_recordset)
  {
    die('error: '. mysql_error());
  }


   return 0;
 }

 /**
  * 执行绑定参数的查询，例如批量INSERT，或调用存储过程
  */
 function bind_query($Sql, $Bindings, $Mode = mysql_COMMIT_ON_SUCCESS)
 {
  if (!$this->_connection) return FALSE;
   $this->freeQuery(); //如果以前查询过，先释放一下
  $this->_recordset = @mysql_parse($this->_connection, $Sql);
  if (!$this->_recordset)
  {
    die('error: '. mysql_error());
  }

  while(list($key, $variable) = each($Bindings))
  {
   if (!@mysql_bind_by_name($this->_recordset, $key, $Bindings[$key],1000))
   {
    die($this->format_error_msg(mysql_error($this->_recordset)));
   }
  }

  return mysql_execute($this->_recordset, $Mode);
 }

 /**
  * 读取下一条记录,两种方式都可以:$row[0],$row['XXX']
  */
 function read()
 {
     return mysql_fetch_array($this->_recordset);
 }

 /**
  * 读取下一条记录,按字段编号取数据:$row[0]，操作最快
  */
 function readByNum()
 {
     return mysql_fetch_row($this->_recordset);
 }

 /**
  * 读取下一条记录,按字段名取数据:$row['XXX']
  */
 function readByName()
 {
     return mysql_fetch_assoc($this->_recordset);
 }

 /**
  * 读取全部记录
  */
 function read_all(&$Result)
 {
  return mysql_fetch_all($this->_recordset, $Result);
 }

 /**
  * 内部调用，格式化错误信息
  */
 function format_error_msg($Error)
 {
  $err = "error!<br>";
  if ( isset($Error['code']) )
   $err .= 'code: ' . $Error['code'] . "<br>";
  if ( isset($Error['message']) )
   $err .= 'message: ' . $Error['message'] . "<br>";
  if ( isset($Error['offset']) )
   $err .= 'offset: ' . $Error['offset'] . "<br>";
  if ( isset($Error['sqltext']) )
   $err .= 'sqltext: ' . $Error['sqltext'];
  $err .= "<br>";
  return $err;
 }

  /**
  * 执行简单分页查询
  */
 function query_Page($Sql, $PageSize = 10, $tablename='', $condition='', $idkey='', $orderby='')
 {
  if (!$this->_connection) return FALSE;
  //error_reporting(E_ALL);

  $this->freeQuery(); //如果以前查询过，先释放一下

  //获取参数,起始页面号和每页显示多少行
  @$this->_StartPage = $_REQUEST['StartPage'];
  if (!$this->_StartPage) $this->_StartPage = 1;
  @$size = $_REQUEST["PageSize"];
  if ($size) $this->_PageSize = $size;
  else $this->_PageSize = $PageSize;
  $StartNumber = ($this->_StartPage -1 ) * $this->_PageSize ;


  //获取所有行数，首先构造计算行数的SQL语句
  if (empty($tablename) || !isset($tablename))
  {
      $Sqlcount = "select count(*) as ROWNUMBER from (";
      $Sqlcount .= $Sql;
      $Sqlcount .= " ) a";
      //echo $Sqlcount."<br>";

      //获取行数
      $this->_AllNumber = 0;

      $temp_result = mysql_query($Sqlcount, $this->_connection);
      if ($row = mysql_fetch_array($temp_result))
         $this->_AllNumber = $row['ROWNUMBER'];
      mysql_free_result($temp_result);

      //计算所有页数,为分页连接信息准备
      $this->_AllPage = ceil ($this->_AllNumber / $this->_PageSize) ;

      //构造获取该页面数据的SQL语句
      $SqlPage =$Sql . ' limit '.$StartNumber.','.$PageSize;
      //echo $SqlPage."<br>";
  }
  else
  {
      $Sqlcount = "select count(*) as ROWNUMBER from ".$tablename." ". $condition;
      //echo $Sqlcount;
      //获取行数
      $this->_AllNumber = 0;

      $temp_result = mysql_query($Sqlcount, $this->_connection);
      if ($row = mysql_fetch_array($temp_result))
         $this->_AllNumber = $row['ROWNUMBER'];
      mysql_free_result($temp_result);

      //计算所有页数,为分页连接信息准备
      $this->_AllPage = ceil ($this->_AllNumber / $this->_PageSize) ;

      //构造获取该页面数据的id
      $sqlid = 'select '.$idkey.' from '.$tablename. ' '.$condition. ' '. $orderby.' limit '.$StartNumber.','.$PageSize;
	  //echo $sqlid;
      $temp_result = mysql_query($sqlid, $this->_connection);
      $strid = '';
      while($rs= mysql_fetch_array($temp_result)){
         $strid.=$rs[$idkey].',';
      }
      $strid=substr($strid,0,strlen($strid)-1); //构造出id字符串

      //构造获取该页面数据的SQL语句
      $newsql = strstr(strtolower($Sql), ' where ', true);

      if ($newsql =='') $newsql = $Sql;
	  $xp = strpos($condition,"where ");
	  //echo "<br>".$xp."<br>";
	  $sss = "";
	  if ($xp!=false) $sss= "and " . substr($condition,$xp+6);
	  if($strid!=""){
		$SqlPage =$newsql . " where $tablename.$idkey in( $strid ) ". $sss . $orderby;
	  }
	  else{
		$SqlPage =$newsql .$condition . $orderby;
	  }
      //echo $SqlPage."<br>";

  }


  $this->_recordset = @mysql_query( $SqlPage,$this->_connection);
  if (!$this->_recordset)
  {
   die('error: '. mysql_error());
  }

  return true;
 }

 function show_LinkPage()
 {
 	if( $this->_AllPage <=1 )
 	{
 		echo "<table width=80% border=0 align=center ><tr><td>共有记录 $this->_AllNumber 行.</td></tr></table>";
  		return;
  	}
  	//取当前的uri:
  	$uri= $_SERVER["REQUEST_URI"]; //形如 "/index.php?p=222&q=biuuu"
  	//检查有无？号
  	$pos = strpos ($uri,"?");
  	$CurrentParam = null;
  	if ($pos>0) //若有？号
  	{
  		//检查有无StartPage
  		$pos2 = strpos ($uri,"StartPage",$pos);
  		if ($pos2 > $pos) //若有StartPage
  		{
  			//从StartPage截断
  			$CurrentParam = substr($uri,0,$pos2);
  		}
  		else 	//若无StartPage，接上&符号
  		{
  			$CurrentParam = $uri . "&";
  		}
	}
  	else //若无？号
  	{
  		$CurrentParam = $uri."?";
  	}
  	$CurrentParam1 = substr($CurrentParam,0,strlen($CurrentParam)-1);


  	$FontSize = "1";
  	echo "<table width=80% border=0 align=center ><tr><td  valign = \"middle\"   style=\"font-size:".$FontSize."em\" align=left>";
  	echo "共有记录 $this->_AllNumber 行(共 $this->_AllPage 页), 当前为第 $this->_StartPage 页. ";

  	echo "</td><td  align=right style=\"font-size:".$FontSize."em\" ><table border=0 cellspacing=1 width=100%><tr><td valign = \"middle\" align=center  style=\"font-size:".$FontSize."em\">";
  	if ($this->_StartPage!=1)
  	{
  		echo "<a href=".$CurrentParam."StartPage=1&PageSize=$this->_PageSize>第一页</a> ";
  		$Prepage = $this->_StartPage-1;
  		echo "<a href=".$CurrentParam."StartPage=$Prepage&PageSize=$this->_PageSize>上一页</a> ";
  	}
  	else
  	{
  		echo "第一页 上一页 ";
  	}
  	if ($this->_StartPage!=$this->_AllPage)
  	{
  		$Nextpage = $this->_StartPage+1;
  		echo "<a href=".$CurrentParam."StartPage=$Nextpage&PageSize=$this->_PageSize>下一页</a> ";
  		echo "<a href=".$CurrentParam."StartPage=$this->_AllPage&PageSize=$this->_PageSize>最后页</a> ";
  	}
  	else
  	{
  		echo "下一页 最后页 ";
  	}

  	//显示go按钮
  	echo "</td><td width=5%> </td><td  valign = \"middle\" align=center style=\"font-size:".$FontSize."em\"><br>";
  	echo "<form action='".$CurrentParam1."' method=post> <select name=StartPage>";
    if ($this->_AllPage<100){
      	for ($i=1;$i<=$this->_AllPage;$i++)
      	{
      		if ($i==$this->_StartPage) echo "<option value=$i selected>$i</option>";
        		else echo "<option value=$i>$i</option>";
       	}
    }
    else
    {
        $k=1;
        $np = $this->_AllPage ;
        while ($np>100){ $np = (int)($np / 10) ; $k *= 10;}
      	for ($i=1;$i<=$np;$i++)
      	{
      	    $ip = $i * $k;
      		if ($ip<=$this->_StartPage && $ip + $k > $this->_StartPage ) echo "<option value=$ip selected>$ip</option>";
        		else echo "<option value=$ip>$ip</option>";
       	}
    }
  	echo "</select> <input type=hidden value=$this->_PageSize name=PageSize> <input type=submit value='go'> </form>";
  	echo "</td></tr></table>";
  	echo "</td></tr></table>";

  	return;
  }

  /**
  * 执行简单的查询，查询结果写入到文件中，并返回文件名，文件名存储到当前目录的download目录下。
  * 文件名自动产生，不需要用户设置
  */
 function SQLtoFile($Sql,$filetype="csv")
 {

 	$download = "download";  //存放下载文件的目录
 	$filename = null;    //下载文件的文件名，仅文件名
 	$currentUri = null;  //下载文件的绝对路径URI，/开头,带文件名
 	$currentpath = null; ////下载文件的磁盘绝对目录，不带文件名，创建目录使用
 	$currentfile = null; //下载文件的磁盘绝对路径，创建该文件，带文件名

 	$serverinfo = $_SERVER['SERVER_SOFTWARE' ]; //取得系统信息
    $space = "/" ; //linux 目录间隔符号

    if (strstr ($serverinfo,"Win")) $space = "\\"; //变为windows目录间隔符号

 	//////////////////////////////////////
 	//1.
 	//构造文件名称$filename
	$filename=@date("YmdGis");
 	for ($a = 0; $a < 5; $a++) {  $filename .= chr(mt_rand(65, 87)); }   //生成php随机数
 	//判断文件类型
 	if ($filetype=="xls" || $filetype=="csv" ) $filename.=".".$filetype;
 	else $filename.=".txt";

	//////////////////////////////////////
 	//2.
	//获取uri绝对路径$currentUri
 	$currentUri = $_SERVER["PHP_SELF"];
 	//找到最后一个/
 	//echo $currentUri;
 	$pos = strrpos($currentUri,"/");
 	$currentUri = substr ($currentUri,0,$pos+1).$download;
 	$currentUri .="/".$filename;

 	//////////////////////////////////////
 	//3.
   	//获取磁盘当前绝对目录$currentpath
 	$currentpath=dirname(dirname(__FILE__));
 	$currentpath.=$space .$download;
 	//echo $currentpath;

 	//////////////////////////////////////
 	//4.
 	////下载文件的磁盘绝对路径$currentfile
 	$currentfile = $currentpath.$space .$filename;
 	//echo $filename;

 	/////////////////////////////////////
 	//5.
 	//如果目录不存在，则创建$currentpath
	if (!is_dir($currentpath)){ //检查目录是否存在
     		if (mkdir($currentpath)){ //创建目录
      			//echo("<br>成功创建文件夹".$currentpath."<br>");
     		}else{
      			//echo("<br>创建文件夹失败".$currentpath."<br>");
     		}
	}

 	/////////////////////////////////////
 	//6.
 	//建立文件
  	$eFile = new ExportFile($currentfile,$filetype);

 	/////////////////////////////////////
 	//7.
 	//查询操作
	$temp_recordset = @mysql_query($Sql, $this->_connection );

  	///////////////////////////////////////
  	//8.
 	//得到字段名称，写文件
 	$ncols = mysql_num_fields($temp_recordset);
	for ($i = 1; $i <= $ncols; $i++) {
    		$column_name  = mysql_field_name($temp_recordset, $i);
    		//写文件
    		$eFile->WriteField($column_name);
	}
	$eFile->WriteEndofLine();

	////////////////////////////////////////////
	//9.
 	//得到所有数据，写文件
 	while ($row = mysql_fetch_row($temp_recordset))
	{
 		for ($i = 0; $i < $ncols; $i++)
 		{
 			$data=$row[$i];

 			//写文件
 			$eFile->WriteField($data);
		}
		//写文件
		$eFile->WriteEndofLine();
	}

	////////////////////////////////////////////
	mysql_free_result($temp_recordset);
	return $currentUri;
 }
}


?>