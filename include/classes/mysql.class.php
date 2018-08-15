<?php
/*
| ----------------------------
| 	文件功能：定义数据库操作类(终极版)
|	程序作者：王海滨（技术部）
|	编写时间：2014-08-06
| ----------------------------
*/

define('CLIENT_MULTI_RESULTS', 131072); //允许客户端执行存储过程（非常重要）
/*
/*
| --------------------------------------------------------
| 	以下区域定义数据库操作类
| --------------------------------------------------------
*/
class Mysql
{
	private  $conn;
	
	//函数功能：构造函数
	function __construct(){
		$this->connect();
		$this->selectdb();
	}
	
	//函数功能：析构函数
	function __destruct(){		
			$this->close();
	}
	
	//函数功能：连接服务器
	function connect(){
		//$this->conn=(DB_PCONNECT)?@mysql_pconnect(DB_HOST,DB_USER,DB_PWD):@mysql_connect(DB_HOST,DB_USER,DB_PWD,1,CLIENT_MULTI_RESULTS);
		$this->conn=mysql_connect(DB_HOST,DB_USER,DB_PWD,1,CLIENT_MULTI_RESULTS);		
		if($this->conn == FALSE){
			die('数据库连接错误'.mysql_error());
		}	
		mysql_query("SET NAMES '".DB_CHARSET."'");//让MYSQL支持中文	
		mysql_query("set sql_mode=''");//去除新版本的严格字段检查
	}
	//函数功能：关闭服务器连接
	function close(){
		//因为__destruct中已经关闭连接，故不需要二次关闭,暂时屏蔽
		//@mysql_close($this->conn);
	}	
	//函数功能：选择数据库
	function selectdb(){
		if(mysql_select_db(DB_NAME,$this->conn) == FALSE){
			die('数据库'.DB_NAME.'不存在');
		}			
	}	
	//函数功能：得到增，删，改操作所影响的行数，某些特殊情况会用到
	function get_affected_rows(){
		return mysql_affected_rows($this->conn);
	}
		
	//////////////////////////////////////////////////////////////////////////
	////////// 以下功能函数需要封装在ThinkPHP等第三方框架中 //////////
	//////////////////////////////////////////////////////////////////////////
	
	/*
	| ----------------------------------
	| 	函数功能：得到记录集的第1行，第1列
	|	传入形参：1个SQL语句
	|	返回结果：1个字符串
	| ----------------------------------
	*/		
	function get_one_bysql($sqlstr){		
		$temp=$this->do_execute($sqlstr);	
		$array=mysql_fetch_array($temp);//此处返回的是ARRAY		
		$result=$array[0];
		return trim($result);
	}
		
	/*
	| ----------------------------------
	| 	函数功能：复杂连表批量查询
	|	传入形参：一个SQL语句
	|	返回结果：返回一个关联数组（注意不是数字数组）
	| ----------------------------------
	*/
	function get_list_bysql($sqlstr){			
		$result=$this->do_execute($sqlstr);	
		$sql_array = NULL;	
		//返回二维关联数组（带key键）
		while($rows=mysql_fetch_assoc($result)){
			$sql_array[]=$rows;
		}		
		return $sql_array;
	}
		
	//函数功能：得到最后一次插入的id，某些特殊情况会用到
	function get_insert_id(){
		return mysql_insert_id($this->conn);
	}	
	/*
	| ----------------------------------
	| 	函数功能：执行增删改差操作的中间函数
	|	传入形参：$sqlstr:代表1条SQL语句
	|	返回结果：与mysql_query函数等同，返回布尔或Resouce ID值
	| ----------------------------------
	*/
	function do_execute($sqlstr){				
		if(!$result=mysql_query($sqlstr,$this->conn)){
			if(SYS_DEBUG_MODE)
			{
				$this->close();//关闭连接
				die("<br><br>系统报错：Mysql->do_execute函数执行错误！<br>具体信息：".mysql_error()."<br>相关语句：$sqlstr<br><br>");
			}				
			else
				$result=false;//die('系统报错：Mysql->do_execute函数执行错误！请联系管理员。');
		}
		return $result;
	}
	/*
	| ----------------------------------
	| 	函数功能：执行增删改差操作的中间函数
	|	传入形参：$sqlstr:代表1条SQL语句
	|	返回结果：与mysql_query函数等同，返回布尔或Resouce ID值
	| ----------------------------------
	*/	
	function do_update_count($updatetype,$count,$table,$field,$id)
	{
		if($updatetype==1)
			$sqlstr="update $table set $field=$field+$count where id=$id";
		else
			$sqlstr="update $table set $field=if($field-$count<0,0,$field-$count)  where id=$id";
			
		return $this->do_execute($sqlstr);
	}
	/*
	| ----------------------------------
	| 	函数功能：事务处理
	|	传入形参：$sqlstr_array,一个包含多条SQL语句的数组
	|	返回结果：布尔值，表示
	| ----------------------------------
	*/
	function do_transaction($sqlstr_array){
		//注意：此处不能换成this->do_execute();
		mysql_query("BEGIN",$this->conn); //或者mysql_query("START TRANSACTION");
		$result = true;
		foreach($sqlstr_array as $sqlstr)
		{
			sys_log("sqlstr=".$sqlstr);
			$result = $result && mysql_query($sqlstr,$this->conn);	
		}		
		if($result){
			mysql_query("COMMIT",$this->conn);			
		}else{
			mysql_query("ROLLBACK",$this->conn);			
		}		
		mysql_query("END",$this->conn);
		return $result; 		
	}
}
?>