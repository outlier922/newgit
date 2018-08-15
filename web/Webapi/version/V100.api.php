<?php
/**
 * API右侧内容配制说明
 *
 * title：接口名称
 * req_params：客户端需传参数
 *     └── POST参数：例如token
 *            ├── title : 参数说明
 *            ├── memo : 备注
 *            ├── newflag : 为1时表示，该行加红显示
 *            └── default : 模拟测试时默认的测试值
 * req_desc : 特别说明部分
 * ret（默认为0）：0表示服务接口只响应成功或失败(一般用于操作、保存、删除接口); 1表示服务接口响应单条结果集(一般用于用户、帖子详情); 2表示服务接口响应列表集(一般用于列表)
 * ret_level（默认为1）： 1表示返回列表集中没有子列表; 2表示返回列表集中有子列表(一般用于帖子列表中嵌套图片列表)
 * ret_infor：服务器返回参数信息
 *     └── 参数名：例如id
 *            ├── title : 参数说明
 *            ├── memo : 备注
 *            └── newflag : 为1时表示，该行加红显示
 * special：其他相关说明
 *      ├── title : 说明标题
 *      └── memo : 备注
 */

$api_phpfiles = array(
//-------------系统相关---begin
    'init'=>array
    (
        'title' => '（此方法应该在登录之前调用）获取系统初始化信息',
        'req_params' => array
        (
            'lastloginversion'=>array('title'=>'登陆所用的系统版本号','memo'=>'记录用户的登录版本，便于日后维护统计，默认1.0.0版本登录。','default'=>'1.0.0'),
            'device_sn'=>array('title'=>'客户端硬件串号','memo'=>'苹果和安卓均需要传递','default'=>''),
            'device_mac'=>array('title'=>'客户端MAC地址','memo'=>'苹果专用，安卓无需传递','default'=>''),
        ),
        'ret'=>'1',
        'ret_level'=>'1',
        'ret_infor'=>array
        (
            'sys_web_service'=>array('title'=>'后台服务根路径(含版本号)','memo'=>''),
            'sys_plugins'=>array('title'=>'第三方插件根路径','memo'=>''),
            'sys_show_iospay'=>array('title'=>'苹果是否显示在线支付功能','memo'=>'苹果商店审核前置为0，审核通过需置为1$客户端根据此标记来决定是否进入在线支付模块(0：弹窗提醒"暂未开放"1：进入支付模块)$$专门应对苹果审核$安卓无需处理此标记'),
            'start_img'=>array('title'=>'启动页图片地址','memo'=>''),
            'android_must_update'=>array('title'=>'安卓强制更新标记','memo'=>'0：不强制 1：强制$（当软件架构进行了较大变动，客户端必须强制用户升级到最新版本）'),
            'android_last_version'=>array('title'=>'安卓最新版本号','memo'=>'将该信息与安卓本机版本号比对，如果不相等，则提醒在线升级'),
            'iphone_must_update'=>array('title'=>'苹果强制更新标记','memo'=>'0：不强制 1：强制$（当软件架构进行了较大变动，客户端必须强制用户升级到最新版本）'),
            'iphone_last_version'=>array('title'=>'苹果最新版本号','memo'=>'将该信息与苹果本机版本号比对，如果不相等，则提醒在线升级'),
            'sys_chat_ip'=>array('title'=>'聊天服务器IP地址','memo'=>'形如：124.128.23.74'),
            'sys_chat_port'=>array('title'=>'聊天服务器端口号','memo'=>'形如：5222（一个整数）'),
            'sys_pagesize'=>array('title'=>'系统规定单页记录数','memo'=>'此参数在系统列表分页时需要用到，默认：20'),
            'sys_service_phone'=>array('title'=>'我公司统一客服电话','memo'=>'前台客服解疑释惑专用，目前是"0531-67804172"'),
            'android_update_url'=>array('title'=>'安卓软件更新地址','memo'=>''),
            'iphone_update_url'=>array('title'=>'苹果软件更新地址','memo'=>''),
            'iphone_comment_url'=>array('title'=>'苹果软件评论地址','memo'=>''),
            'msg_invite'=>array('title'=>'邀请下载短信内容','memo'=>''),
        ),
        'special'=>array
        (
            array('title'=>'特殊说明一','memo'=>'
（1）客户端通过init接口后获取到sys_web_service值后，后面的所有接口(除去第三方插件)一定要根据此字段值来组装具体地址，服务器根据客户端传递lastloginversion参数来动态配置此值，以便兼容不同版本的客户端访问。<br />
（2）客户端通过init接口后获取到sys_plugins值后，所有第三方插件接口一定要根据此字段值来组装具体地址。<br />
（3）无论任何项目，任何带默认性质的图像（包括但不限于默认头像，个人主页顶部默认背景图等），此类图片客户端都应当配置到本缓存中，当发现服务器
返回数据为空或服务器图片异常丢失时，即需要用此类默认图片填充图像显示控件。'),
        )
    ),

);

function get_class_all_methods($class){
    $r = new reflectionclass($class);
    foreach($r->getmethods() as $key=>$methodobj){
        if ($methodobj->class=="BaseAction" || $methodobj->class==$class) {
            if ($methodobj->name == "menu_root") continue;
            if ($methodobj->name == "push_add") continue;
            if ($methodobj->isprivate()) {
                //$methods[$key]['type'] = 'private';
                continue;
            } elseif ($methodobj->isprotected()) {
                //$methods[$key]['type'] = 'protected';
                continue;
            } else
                $methods[$key]['type'] = 'public';
            $methods[$key]['name'] = $methodobj->name;
            $methods[$key]['class'] = $methodobj->class;
        }
    }
    return $methods;
}

$path = dirname(dirname(dirname(__FILE__)));

include_once $path.'/Webservice/Action/V100Action.class.php';
$className='V100Action';
$methods = get_class_all_methods($className);
//var_dump(json_encode($methods));
//die();

foreach ($methods as $m)
{
    $method_name=$m['name'];
    $rMethod = new ReflectionMethod($className, $method_name);
    $docComment = $rMethod->getDocComment();
    $docCommentArr = explode("\n", $docComment);
    $title="";
    $req_params_array=NULL;
    $req_desc="";
    $ret=0;
    $ret_level=1;
    $ret_infor_array=NULL;
    $special_array="";
    $special_array_i=0;
    foreach ($docCommentArr as $comment) {
        $comment = trim($comment);
        if (strpos($comment, '@') === false && strpos($comment, '/') === false) {
            $title = substr($comment, strpos($comment, '*') + 1);
        }

        $pos = stripos($comment, '@req_params');
        if ($pos !== false) {
            $req_params = substr($comment, $pos + 12);
            $tmp_req_params_array=explode(" ",$req_params);
            $req_params_name=$tmp_req_params_array[0];
            $req_params_title=$tmp_req_params_array[1]!==NULL ? $tmp_req_params_array[1] : "";
            $req_params_memo=$tmp_req_params_array[2]!==NULL ? $tmp_req_params_array[2] : "";
            $req_params_default=$tmp_req_params_array[3]!==NULL ? $tmp_req_params_array[3] : "";
            $req_params_newflag=$tmp_req_params_array[4]!==NULL ? $tmp_req_params_array[4] : 0;

            $temp_array=NULL;
            $temp_array['title']=$req_params_title;
            $temp_array['memo']=$req_params_memo;
            $temp_array['default']=$req_params_default;
            $temp_array['newflag']=$req_params_newflag;

            $req_params_array[$req_params_name]=$temp_array;
        }

        $pos = stripos($comment, '@req_desc');
        if ($pos !== false) {
            $req_desc = substr($comment, $pos + 10);
        }

        $pos = stripos($comment, '@ret');
        if ($pos !== false) {
            $ret = substr($comment, $pos + 5);
        }

        $pos = stripos($comment, '@ret_level');
        if ($pos !== false) {
            $ret = substr($comment, $pos + 5);
        }

        $pos = stripos($comment, '@ret_infor');
        if ($pos !== false) {
            $ret_infor=substr($comment, $pos + 11);
            $tmp_ret_infor_array=explode(" ",$ret_infor);
            $ret_infor_name=$tmp_ret_infor_array[0]!==NULL ? $tmp_ret_infor_array[0] : "";
            $ret_infor_title=$tmp_ret_infor_array[1]!==NULL ? $tmp_ret_infor_array[1] : "";
            $ret_infor_memo=$tmp_ret_infor_array[2]!==NULL ? $tmp_ret_infor_array[2] : "";
            $ret_infor_newflag=$tmp_ret_infor_array[3]!==NULL ? $tmp_ret_infor_array[3] : 0;

            $temp_array=NULL;
            $temp_array['title']=$ret_infor_title;
            $temp_array['memo']=$ret_infor_memo;
            $temp_array['newflag']=$ret_infor_newflag;

            $ret_infor_array[$ret_infor_name]=$temp_array;
        }

        $pos = stripos($comment, '@special');
        if ($pos !== false) {
            $special = substr($comment, $pos + 9);
            $tmp_special_array=explode(" ", $special);
            $special_title=$tmp_special_array[0]!==NULL ? $tmp_special_array[0] : "";
            $special_memo=$tmp_special_array[1]!==NULL ? $tmp_special_array[1] : "";
            $temp_array=NULL;
            $temp_array['title']=$special_title;
            $temp_array['memo']=$special_memo;
            $special_array[$special_array_i]=$temp_array;
            $special_array_i++;
        }
    }
    $api_phpfiles[$method_name]['title']=$title;
    $api_phpfiles[$method_name]['req_params']=$req_params_array;
    $api_phpfiles[$method_name]['req_desc']=$req_desc;
    $api_phpfiles[$method_name]['ret']=$ret;
    $api_phpfiles[$method_name]['ret_level']=$ret_level;
    $api_phpfiles[$method_name]['ret_infor']=$ret_infor_array;
    $api_phpfiles[$method_name]['special']=$special_array;
}

