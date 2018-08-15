<?php
class SysClientModel extends Model {
	//定义静态验证规则
	protected $_validate = array(
			array('username','','登录账号已经存在！',0,'unique',0), // //第四个参数1非常重要，表示存在字段时才验证（用于兼容add和save）
			/*array('username','require',"用户名必须填写",1), //第四个参数1非常重要，表示必须存在字段且不为空（TP默认是存在才验证，不存在就直接通过）
			array('password','require','密码必须填写！',1), //默认情况下用正则进行验证		
			array('devicetype','require','devicetype必须填写！',1), //默认情况下用正则进行验证			
			array('devicetype',array(1,2),'devicetype取值范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
			array('name','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
			array('value',array(1,2,3),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
			array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
			array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式*/
		);
	//定义自动完成:(callback是调用本类内的函数，function是调用system.core.php中的函数)
	protected $_auto = array(       
			array('regdate','sys_get_time',1,'function'),	//第三个参数说明：1新增时填充 2修改时填充 3任意情况都填充
	);	
}
?>