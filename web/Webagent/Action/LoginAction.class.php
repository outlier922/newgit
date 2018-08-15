<?php
/**
 * 登录控制器
 */
class LoginAction extends AdminAction{
    //登录
    public function index(){
        if(IS_POST){
            $username = _POST('username');
            $password = _POST('password');
            $password = md5($password);
            $verify = _POST('verify');
            $regdate = sys_get_time();
            $ip = get_client_ip(0,true);
            //检测验证码
            if(session('verify') != md5($verify)) sys_out_fail("验证码错误");
            //查询数据
            $admin_r = $this -> get_list_bysql("select * from sys_admin where account='$username' ");
            //验证账号
            if(!$admin_r) sys_out_fail("账号不存在");
            if($password != $admin_r[0]['password']) sys_out_fail("密码不正确");
            if($admin_r[0]['islogin'] != 1)  sys_out_fail("账号已被冻结");
            //更新登录信息
            $this -> do_execute("update sys_admin set login=login+1,lastlogintime='$regdate',lastloginip='$ip' where id=".$admin_r[0]['id']);
            //保存session值
            $auth = array(
                'uid' => $admin_r[0]['id'],
                'role_id' => $admin_r[0]['roleid'],
                'username' => $admin_r[0]['account'],
                'realname' => $admin_r[0]['realname'],
                'lastlogintime' => $admin_r[0]['lastlogintime'],
            );
            session('auth', $auth);

            $temp_array['url'] = U('Index/index');
            sys_out_success(0,$temp_array);
        }
        else{
            if(is_login()){
                $this->redirect('Index/index');
            }else{	            				
                include SYS_UI_COMPONENT.'common/login.php';
            }
        }
    }
    //退出登录
    public function logout(){
        if(is_login()){
            session('auth', null);
            $this->success('退出成功！', U('Login/index'));
        }
        else {
            $this->redirect('Login/index');
        }
    }
    //生成验证码
    public function verify(){
        import('ORG.Util.Image');
        ob_end_clean();
        Image::buildImageVerify();
    }
}