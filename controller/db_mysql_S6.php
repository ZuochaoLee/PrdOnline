<?php

class ExportFile
{
  var $fp=null;

  //���캯��
  function __construct($filename, $filetype)
  {

  	$this->fp=fopen($filename,'w');
  }

  //��������
  function __destruct()
  {
 	    fclose($this->fp);
  }

  //дÿ���ֶ�
  function WriteField($field)
  {
  	//echo "<$field>";
  	fwrite($this->fp,"\"$field\",");
  }

  //д�н�����־
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
  * ��������
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
  * �������ݿ�
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
  * �ͷŲ���
  */

 function freeQuery()
 {
   if ($this->_recordset != null && !is_bool($this->_recordset))
   {
    mysql_free_result($this->_recordset);
   }
 }

 /**
  * ִ�м򵥲�ѯ
  */
 function query($Sql)
 {
    if (!$this->_connection) return FALSE;

    $this->freeQuery(); //�����ǰ��ѯ�������ͷ�һ��

    $this->_recordset = mysql_query($Sql,$this->_connection);

    return true;
 }
 function insert($Sql)
 {
    mysql_query( $Sql,$this->_connection);
 }

 /**
  * ִ���ض��������,�����������ݱ����Ѿ�����,�򷵻�1,�������ɹ�,�򷵻�0
  */
 function judgeInsert($Sql, $tablename, $fieldname,$value)
 {
  if (!$this->_connection) return FALSE;

  $this->freeQuery(); //�����ǰ��ѯ�������ͷ�һ��

  //�ȼ�����ݱ����Ƿ���ں��и�ֵ���ֶ�
  $temp_recordset = @mysql_query( "select count(*) as JUDGENUM from $tablename where $fieldname='$value' ",$this->_connection);
  if (!$temp_recordset)
  {
    die('error: '. mysql_error());
  }
  $row = mysql_fetch_array($temp_recordset);
  $existRecord = $row['JUDGENUM'];
  mysql_free_result($temp_recordset);

  //existRecordΪ1����ʾ���ݱ����Ѿ����ڣ�����1��
  if ($existRecord) return 1;

  //��ʼ�����Ĳ���
  $this->_recordset = @mysql_query( $Sql,$this->_connection);
  if (!$this->_recordset)
  {
    die('error: '. mysql_error());
  }


   return 0;
 }

  /**
  * ִ���ض��������,��������ֶ�,�����������ݱ����Ѿ�����,�򷵻�1,�������ɹ�,�򷵻�0
  */
 function judgeInsert2($Sql, $tablename, $fieldname,$value,$fieldname2,$value2)
 {
  if (!$this->_connection) return FALSE;

  $this->freeQuery(); //�����ǰ��ѯ�������ͷ�һ��

  //�ȼ�����ݱ����Ƿ���ں��и�ֵ���ֶ�
  $temp_recordset = @mysql_query( "select count(*) as JUDGENUM from $tablename where $fieldname='$value' and $fieldname2='$value2' ",$this->_connection);
  if (!$temp_recordset)
  {
    die('error: '. mysql_error());
  }
  $row = mysql_fetch_array($temp_recordset);
  $existRecord = $row['JUDGENUM'];
  mysql_free_result($temp_recordset);

  //existRecordΪ1����ʾ���ݱ����Ѿ����ڣ�����1��
  if ($existRecord) return 1;

  //��ʼ�����Ĳ���
  $this->_recordset = @mysql_query( $Sql,$this->_connection);
  if (!$this->_recordset)
  {
    die('error: '. mysql_error());
  }


   return 0;
 }

 /**
  * ִ�а󶨲����Ĳ�ѯ����������INSERT������ô洢����
  */
 function bind_query($Sql, $Bindings, $Mode = mysql_COMMIT_ON_SUCCESS)
 {
  if (!$this->_connection) return FALSE;
   $this->freeQuery(); //�����ǰ��ѯ�������ͷ�һ��
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
  * ��ȡ��һ����¼,���ַ�ʽ������:$row[0],$row['XXX']
  */
 function read()
 {
     return mysql_fetch_array($this->_recordset);
 }

 /**
  * ��ȡ��һ����¼,���ֶα��ȡ����:$row[0]���������
  */
 function readByNum()
 {
     return mysql_fetch_row($this->_recordset);
 }

 /**
  * ��ȡ��һ����¼,���ֶ���ȡ����:$row['XXX']
  */
 function readByName()
 {
     return mysql_fetch_assoc($this->_recordset);
 }

 /**
  * ��ȡȫ����¼
  */
 function read_all(&$Result)
 {
  return mysql_fetch_all($this->_recordset, $Result);
 }

 /**
  * �ڲ����ã���ʽ��������Ϣ
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
  * ִ�м򵥷�ҳ��ѯ
  */
 function query_Page($Sql, $PageSize = 10, $tablename='', $condition='', $idkey='', $orderby='')
 {
  if (!$this->_connection) return FALSE;
  //error_reporting(E_ALL);

  $this->freeQuery(); //�����ǰ��ѯ�������ͷ�һ��

  //��ȡ����,��ʼҳ��ź�ÿҳ��ʾ������
  @$this->_StartPage = $_REQUEST['StartPage'];
  if (!$this->_StartPage) $this->_StartPage = 1;
  @$size = $_REQUEST["PageSize"];
  if ($size) $this->_PageSize = $size;
  else $this->_PageSize = $PageSize;
  $StartNumber = ($this->_StartPage -1 ) * $this->_PageSize ;


  //��ȡ�������������ȹ������������SQL���
  if (empty($tablename) || !isset($tablename))
  {
      $Sqlcount = "select count(*) as ROWNUMBER from (";
      $Sqlcount .= $Sql;
      $Sqlcount .= " ) a";
      //echo $Sqlcount."<br>";

      //��ȡ����
      $this->_AllNumber = 0;

      $temp_result = mysql_query($Sqlcount, $this->_connection);
      if ($row = mysql_fetch_array($temp_result))
         $this->_AllNumber = $row['ROWNUMBER'];
      mysql_free_result($temp_result);

      //��������ҳ��,Ϊ��ҳ������Ϣ׼��
      $this->_AllPage = ceil ($this->_AllNumber / $this->_PageSize) ;

      //�����ȡ��ҳ�����ݵ�SQL���
      $SqlPage =$Sql . ' limit '.$StartNumber.','.$PageSize;
      //echo $SqlPage."<br>";
  }
  else
  {
      $Sqlcount = "select count(*) as ROWNUMBER from ".$tablename." ". $condition;
      //echo $Sqlcount;
      //��ȡ����
      $this->_AllNumber = 0;

      $temp_result = mysql_query($Sqlcount, $this->_connection);
      if ($row = mysql_fetch_array($temp_result))
         $this->_AllNumber = $row['ROWNUMBER'];
      mysql_free_result($temp_result);

      //��������ҳ��,Ϊ��ҳ������Ϣ׼��
      $this->_AllPage = ceil ($this->_AllNumber / $this->_PageSize) ;

      //�����ȡ��ҳ�����ݵ�id
      $sqlid = 'select '.$idkey.' from '.$tablename. ' '.$condition. ' '. $orderby.' limit '.$StartNumber.','.$PageSize;
	  //echo $sqlid;
      $temp_result = mysql_query($sqlid, $this->_connection);
      $strid = '';
      while($rs= mysql_fetch_array($temp_result)){
         $strid.=$rs[$idkey].',';
      }
      $strid=substr($strid,0,strlen($strid)-1); //�����id�ַ���

      //�����ȡ��ҳ�����ݵ�SQL���
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
 		echo "<table width=80% border=0 align=center ><tr><td>���м�¼ $this->_AllNumber ��.</td></tr></table>";
  		return;
  	}
  	//ȡ��ǰ��uri:
  	$uri= $_SERVER["REQUEST_URI"]; //���� "/index.php?p=222&q=biuuu"
  	//������ޣ���
  	$pos = strpos ($uri,"?");
  	$CurrentParam = null;
  	if ($pos>0) //���У���
  	{
  		//�������StartPage
  		$pos2 = strpos ($uri,"StartPage",$pos);
  		if ($pos2 > $pos) //����StartPage
  		{
  			//��StartPage�ض�
  			$CurrentParam = substr($uri,0,$pos2);
  		}
  		else 	//����StartPage������&����
  		{
  			$CurrentParam = $uri . "&";
  		}
	}
  	else //���ޣ���
  	{
  		$CurrentParam = $uri."?";
  	}
  	$CurrentParam1 = substr($CurrentParam,0,strlen($CurrentParam)-1);


  	$FontSize = "1";
  	echo "<table width=80% border=0 align=center ><tr><td  valign = \"middle\"   style=\"font-size:".$FontSize."em\" align=left>";
  	echo "���м�¼ $this->_AllNumber ��(�� $this->_AllPage ҳ), ��ǰΪ�� $this->_StartPage ҳ. ";

  	echo "</td><td  align=right style=\"font-size:".$FontSize."em\" ><table border=0 cellspacing=1 width=100%><tr><td valign = \"middle\" align=center  style=\"font-size:".$FontSize."em\">";
  	if ($this->_StartPage!=1)
  	{
  		echo "<a href=".$CurrentParam."StartPage=1&PageSize=$this->_PageSize>��һҳ</a> ";
  		$Prepage = $this->_StartPage-1;
  		echo "<a href=".$CurrentParam."StartPage=$Prepage&PageSize=$this->_PageSize>��һҳ</a> ";
  	}
  	else
  	{
  		echo "��һҳ ��һҳ ";
  	}
  	if ($this->_StartPage!=$this->_AllPage)
  	{
  		$Nextpage = $this->_StartPage+1;
  		echo "<a href=".$CurrentParam."StartPage=$Nextpage&PageSize=$this->_PageSize>��һҳ</a> ";
  		echo "<a href=".$CurrentParam."StartPage=$this->_AllPage&PageSize=$this->_PageSize>���ҳ</a> ";
  	}
  	else
  	{
  		echo "��һҳ ���ҳ ";
  	}

  	//��ʾgo��ť
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
  * ִ�м򵥵Ĳ�ѯ����ѯ���д�뵽�ļ��У��������ļ������ļ����洢����ǰĿ¼��downloadĿ¼�¡�
  * �ļ����Զ�����������Ҫ�û�����
  */
 function SQLtoFile($Sql,$filetype="csv")
 {

 	$download = "download";  //��������ļ���Ŀ¼
 	$filename = null;    //�����ļ����ļ��������ļ���
 	$currentUri = null;  //�����ļ��ľ���·��URI��/��ͷ,���ļ���
 	$currentpath = null; ////�����ļ��Ĵ��̾���Ŀ¼�������ļ���������Ŀ¼ʹ��
 	$currentfile = null; //�����ļ��Ĵ��̾���·�����������ļ������ļ���

 	$serverinfo = $_SERVER['SERVER_SOFTWARE' ]; //ȡ��ϵͳ��Ϣ
    $space = "/" ; //linux Ŀ¼�������

    if (strstr ($serverinfo,"Win")) $space = "\\"; //��ΪwindowsĿ¼�������

 	//////////////////////////////////////
 	//1.
 	//�����ļ�����$filename
	$filename=@date("YmdGis");
 	for ($a = 0; $a < 5; $a++) {  $filename .= chr(mt_rand(65, 87)); }   //����php�����
 	//�ж��ļ�����
 	if ($filetype=="xls" || $filetype=="csv" ) $filename.=".".$filetype;
 	else $filename.=".txt";

	//////////////////////////////////////
 	//2.
	//��ȡuri����·��$currentUri
 	$currentUri = $_SERVER["PHP_SELF"];
 	//�ҵ����һ��/
 	//echo $currentUri;
 	$pos = strrpos($currentUri,"/");
 	$currentUri = substr ($currentUri,0,$pos+1).$download;
 	$currentUri .="/".$filename;

 	//////////////////////////////////////
 	//3.
   	//��ȡ���̵�ǰ����Ŀ¼$currentpath
 	$currentpath=dirname(dirname(__FILE__));
 	$currentpath.=$space .$download;
 	//echo $currentpath;

 	//////////////////////////////////////
 	//4.
 	////�����ļ��Ĵ��̾���·��$currentfile
 	$currentfile = $currentpath.$space .$filename;
 	//echo $filename;

 	/////////////////////////////////////
 	//5.
 	//���Ŀ¼�����ڣ��򴴽�$currentpath
	if (!is_dir($currentpath)){ //���Ŀ¼�Ƿ����
     		if (mkdir($currentpath)){ //����Ŀ¼
      			//echo("<br>�ɹ������ļ���".$currentpath."<br>");
     		}else{
      			//echo("<br>�����ļ���ʧ��".$currentpath."<br>");
     		}
	}

 	/////////////////////////////////////
 	//6.
 	//�����ļ�
  	$eFile = new ExportFile($currentfile,$filetype);

 	/////////////////////////////////////
 	//7.
 	//��ѯ����
	$temp_recordset = @mysql_query($Sql, $this->_connection );

  	///////////////////////////////////////
  	//8.
 	//�õ��ֶ����ƣ�д�ļ�
 	$ncols = mysql_num_fields($temp_recordset);
	for ($i = 1; $i <= $ncols; $i++) {
    		$column_name  = mysql_field_name($temp_recordset, $i);
    		//д�ļ�
    		$eFile->WriteField($column_name);
	}
	$eFile->WriteEndofLine();

	////////////////////////////////////////////
	//9.
 	//�õ��������ݣ�д�ļ�
 	while ($row = mysql_fetch_row($temp_recordset))
	{
 		for ($i = 0; $i < $ncols; $i++)
 		{
 			$data=$row[$i];

 			//д�ļ�
 			$eFile->WriteField($data);
		}
		//д�ļ�
		$eFile->WriteEndofLine();
	}

	////////////////////////////////////////////
	mysql_free_result($temp_recordset);
	return $currentUri;
 }
}


?>