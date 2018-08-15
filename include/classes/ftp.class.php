<?php  
/*
| ----------------------------
| 	文件功能：FTP操作类(用于简单DFS分布式存储)
|	程序作者：王海滨（技术部）
|	编写时间：2014-10-08
| ----------------------------
*/
class WhbFtp  
{  
    public $result;                       // 返回操作状态(成功/失败)  
    public $conn_id;                      // FTP连接  
  
    /** 
    * 方法：FTP连接 
    * @FTP_HOST -- FTP主机 
    * @FTP_PORT -- 端口 
    * @FTP_USER -- 用户名 
    * @FTP_PASS -- 密码 
    */  
    function __construct($FTP_HOST,$FTP_PORT,$FTP_USER,$FTP_PASS)  
    {  
        $this->conn_id = @ftp_connect($FTP_HOST,$FTP_PORT) or die("DFS分布式服务器连接失败");  
        @ftp_login($this->conn_id,$FTP_USER,$FTP_PASS) or die("DFS分布式服务器登录失败");  
        @ftp_pasv($this->conn_id,1); // 打开被动模拟  
    }  
  
    /** 
    * 方法：上传文件 
    * @path    -- 本地路径 
    * @newpath -- 上传路径 
    * @type    -- 若目标目录不存在则新建 
    */  
    function upload_file($path,$newpath,$type=true)  
    {  
        if($type) $this->dir_mkdirs($newpath);
        $this->result = @ftp_put($this->conn_id,$newpath,$path,FTP_BINARY);
		return $this->result;
    }  
  
    /** 
    * 方法：移动文件 
    * @path    -- 原路径 
    * @newpath -- 新路径 
    * @type    -- 若目标目录不存在则新建 
    */  
    function move_file($path,$newpath,$type=true)  
    {  
        if($type) $this->dir_mkdirs($newpath);  
        $this->result = @ftp_rename($this->conn_id,$path,$newpath);
		return $this->result; 
    }  
  
    /** 
    * 方法：复制文件 
    * 说明：由于FTP无复制命令,本方法变通操作为：下载后再上传到新的路径 
    * @path    -- 原路径 
    * @newpath -- 新路径 
    * @type    -- 若目标目录不存在则新建 
    */  
    function copy_file($path,$newpath,$type=true)  
    {  
        $downpath = "c:/tmp.dat";  
        $this->result = @ftp_get($this->conn_id,$downpath,$path,FTP_BINARY);// 下载  
		return $this->result; 
        $this->upload_file($downpath,$newpath,$type);  
    }  
  
    /** 
    * 方法：删除文件 
    * @path -- 路径 
    */  
    function remove_file($path)  
    {  
        $this->result = @ftp_delete($this->conn_id,$path);  
		return $this->result; 
    }  
  
    /** 
    * 方法：生成目录（仅支持linux系统） 
    * @path -- 路径 
    */  
    function dir_mkdirs($path)  
    {  
        $path_arr  = explode('/',$path);              // 取目录数组  
        $file_name = array_pop($path_arr);            // 弹出文件名  
        $path_div  = count($path_arr);                // 取层数  
  
        foreach($path_arr as $val)                    // 创建目录  
        {  
            if(@ftp_chdir($this->conn_id,$val) == FALSE)  
            {  
                $tmp = @ftp_mkdir($this->conn_id,$val);  
                if($tmp == FALSE)  
                {  
                    echo "$val 目录创建失败,请检查权限及路径是否正确！";  
                    exit;  
                }  
                @ftp_chdir($this->conn_id,$val);  
            }  
        }  
          
        for($i=1;$i<=$path_div;$i++)                  // 回退到根  
        {  
            @ftp_cdup($this->conn_id);  
        }  
    }  
  
    /** 
    * 方法：关闭FTP连接 
    */  
    function close()  
    {  
        @ftp_close($this->conn_id);  
    }  
}  
// class class_ftp end  
  
  
  
  
/************************************** 测试 *********************************** 
$ftp = new WhbFtp('127.0.0.1',21,'whbftp','whbftp');          // 打开FTP连接 
$ftp->upload_file('aa.wav','test/13548957217/bb.wav');         // 上传文件 
//$ftp->move_file('aaa/aaa.php','aaa.php');                // 移动文件 
//$ftp->copy_file('aaa.php','aaa/aaa.php');                // 复制文件 
//$ftp->remove_file('aaa.php');                               // 删除文件 
$ftp->close();                                             // 关闭FTP连接 
//******************************************************************************/  