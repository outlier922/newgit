<?php

/**
 * 总平台首页
 */
class IndexAction extends BaseAction
{
    //首页
    public function index(){
        if(UID){
            $user = session('auth');
			$menu_list = $this -> _cascade_get(0,'sys_shopmenu');
            $component_data = array(
                '_parser'=>'common/index',
                'menu_list' => $menu_list,
                'user' => $user
            );
            _display($component_data);
        }
        else{
            $this->redirect('Login/index');
        }
    }
    //我的桌面
    public function welcome(){
        //获取用户信息
        $user_r = $this -> get_list_bysql("select login,lastloginip,lastlogintime from sys_admin where id=".UID);
        //系统信息
        if (function_exists('gd_info')) {
            $gd = gd_info();
            $gd = $gd['GD Version'];
        }else {
            $gd = '不支持';
        }
        $sys_info = array(
            '操作系统' => PHP_OS,
            '主机名IP端口' => $_SERVER['SERVER_NAME'].'('.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].')',
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式' => php_sapi_name(),
            '程序目录' => str_replace("\\", '/', SYS_ROOT_PATH),
            'MYSQL版本' => function_exists("mysql_close") ? mysql_get_client_info() : '不支持',
            'GD库版本' => $gd,
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time')."秒",
            '剩余空间' => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',
            '服务器时间' => date("Y年n月j日 H:i:s"),
            '北京时间' => gmdate("Y年n月j日 H:i:s", time() + 8 * 3600),
            '采集函数检测' => ini_get('allow_url_fopen') ? '支持' : '不支持',
            'register_globals' => get_cfg_var("register_globals") == "1" ? "ON" : "OFF",
            'magic_quotes_gpc' => (1 === get_magic_quotes_gpc()) ? 'YES' : 'NO',
            'magic_quotes_runtime' => (1 === get_magic_quotes_runtime()) ? 'YES' : 'NO',
        );
        $component_data = array(
            '_parser'=>'common/welcome',
            'user_info'=>$user_r[0],
            'sys_info'=>$sys_info
        );
        _display($component_data);
    }
    //修改密码
    public function password_save(){
        if(!UID) layer_out_fail("非法操作");
        if(IS_POST){
            $password = _POST('password');
            $password_again = _POST('password_again');
            if($password != $password_again) sys_out_fail("两次输入密码不一致");
            $password=md5($password);
            $sqlstr = "update sys_shop set password='$password' where id=".UID;
            $result = $this -> do_execute($sqlstr);
            if($result === false) sys_out_fail();
            sys_out_success();
        }
        else{
            $form_items = array(
                array('_parser'=>'form_item/form/input','type'=>'password',
                    'name'=>'password','label'=>'新密码','placeholder'=>'请输入密码，不能低于6位',
                    '_validation'=>array(
                        'minlength'=>array(6,'密码最少6位')
                    ),
                ),
                array('_parser'=>'form_item/form/input','type'=>'password',
                    'name'=>'password_again','label'=>'确认密码','placeholder'=>'再次输入密码',
                    '_validation'=>array(
                        'equalTo'=>array('#password','输入不一致')
                    ),
                ),
            );
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            $component_data = array('_parser'=>'form/default',
                'action' => U(MODULE_NAME.'/'.ACTION_NAME),'_children'=>$form_items,
                'rules' => $rules,'messages' => $messages,
            );
            _display($component_data);
        }
    }
    
}