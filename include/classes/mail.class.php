<?php
/*
| ----------------------------
| 	文件功能：定义邮件发送类(终极版)
|	程序作者：王海滨（技术部）
|	编写时间：2013-04-20
| ----------------------------
*/

class WhbMail { 
	var $smtp = MAIL_SMTP;
	//您的email帐号名称 
	var $username =MAIL_USER;
	//您的email密码 
	var $password =MAIL_PWD;
	//real_from 必需是发信服务器上真实的email
	var $real_from = MAIL_USER;
	
	//函数功能：构造函数
	function __construct(){
	}
	
	//函数功能：析构函数
	function __destruct(){
	}	
	
	/* 
	* 功能：发送邮件 
	* $to 目标邮箱 
	* $show_from 来源邮箱(可以伪造) 
	* $subject 邮件标题 
	* $message 邮件内容 
	*/ 
	function send($to,$show_from,$subject,$message) {
		//首先判断to是否为合法邮箱地址
		if(!(ereg("^([a-za-z0-9_-])+@([a-za-z0-9_-])+(\.[a-za-z0-9_-])+",$to)))
		{
			//echo "对方邮箱地址错误".__LINE__; 
			return false;
		}
		
		//连接服务器 
		$fp = fsockopen ( $this->smtp, 25, $errno, $errstr, 60); 
		if (!$fp )
		{
			//echo "连接服务器失败".__LINE__; 
			return false;
		} 
		set_socket_blocking($fp, true ); 
		$lastmessage=fgets($fp,512); 
		if (substr($lastmessage,0,3) != 220 )
		{
			//echo "SMTP服务器错误信息1:$lastmessage".__LINE__; 
			return false;
		}  
		//HELO 
		$yourname = "YOURNAME"; 
		$lastact="EHLO ".$yourname."\r\n"; 
		fputs($fp, $lastact); 
		$lastmessage == fgets($fp,512); 
		if (substr($lastmessage,0,3) != 220 )
		{
			//echo "SMTP服务器错误信息2:$lastmessage".__LINE__; 
			return false;
		} 		
		while (true) { 
			$lastmessage = fgets($fp,512); 
			if ( (substr($lastmessage,3,1) != "-") or (empty($lastmessage)) ) 
				break; 
		} 
		//验证开始 
		$lastact="AUTH LOGIN"."\r\n"; 
		fputs( $fp, $lastact); 
		$lastmessage = fgets ($fp,512); 
		if (substr($lastmessage,0,3) != 334) 
		{
			//echo "SMTP服务器错误信息3:$lastmessage".__LINE__; 
			return false;
		}
	
		//用户姓名 
		$lastact=base64_encode($this->username)."\r\n"; 
		fputs( $fp, $lastact); 
		$lastmessage = fgets ($fp,512); 
		if (substr($lastmessage,0,3) != 334)
		{
			//echo "SMTP服务器错误信息4:$lastmessage".__LINE__; 
			return false;
		}		
		//用户密码 
		$lastact=base64_encode($this->password)."\r\n"; 
		fputs( $fp, $lastact); 
		$lastmessage = fgets ($fp,512); 
		if (substr($lastmessage,0,3) != "235")
		{
			//echo "SMTP服务器错误信息5:$lastmessage".__LINE__; 
			return false;
		}	
		//FROM: 
		$lastact="MAIL FROM: <". $this->real_from . ">\r\n"; 
		fputs( $fp, $lastact); 
		$lastmessage = fgets ($fp,512); 
		if (substr($lastmessage,0,3) != 250)
		{
			//echo "SMTP服务器错误信息6:$lastmessage".__LINE__; 
			return false;
		}
		
		//TO: 
		$lastact="RCPT TO: <". $to ."> \r\n"; 
		fputs( $fp, $lastact); 
		$lastmessage = fgets ($fp,512); 
		if (substr($lastmessage,0,3) != 250) 
		{
			//echo "SMTP服务器错误信息7:$lastmessage".__LINE__; 
			return false;
		}			
		//DATA 
		$lastact="DATA\r\n"; 
		fputs($fp, $lastact); 
		$lastmessage = fgets ($fp,512); 
		if (substr($lastmessage,0,3) != 354)
		{
			//echo "SMTP服务器错误信息8:$lastmessage".__LINE__; 
			return false;
		}			
	 
		//处理Subject头 
		$head="Subject: $subject\r\n"; 
		$message = $head."\r\n".$message; 
	 
		//处理From头 
		$head="From: $show_from\r\n"; 
		$message = $head.$message; 
		//处理To头 
		$head="To: $to\r\n"; 
		$message = $head.$message; 
	 
		//加上结束串 
		$message .= "\r\n.\r\n"; 
		//发送信息 
		fputs($fp, $message); 
		//$lastact="QUIT\r\n"; 
		fclose($fp); 
		return true;
	} 
}
?>