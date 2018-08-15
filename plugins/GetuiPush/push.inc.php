<?php
//个推配制
//IOS的
define('HOST','http://sdk.open.api.igexin.com/apiex.htm');
define('IOS_APPID','8fvsJsc2GP7ibzPcXiCHuA');
define('IOS_APPKEY','g79HrpT7kc8tSBfYyAfD7A');
define('IOS_MASTERSECRET','CnhqSLC6dj7qKWZc0ILq71');
//安卓的
define('ANDROID_APPID','TobMME8jnL6P3rWsARurT7');
define('ANDROID_APPKEY','cCJS2P4v6OA1s0kFDUeV1A');
define('ANDROID_MASTERSECRET','Ek3YBDMrqU5YXENUK03gyA');
//引入个推推送
require_once(dirname(__FILE__) . '/' . 'IGt.Push.php');
require_once(dirname(__FILE__) . '/' . 'igetui/IGt.AppMessage.php');
require_once(dirname(__FILE__) . '/' . 'igetui/IGt.APNPayload.php');
require_once(dirname(__FILE__) . '/' . 'igetui/template/IGt.BaseTemplate.php');
require_once(dirname(__FILE__) . '/' . 'igetui/utils/AppConditions.php');

function sys_push($sql_helper,$deviceType,$clientID,$msgContent,$keyType=0,$keyID=0,$nickname="",$avatar="",$msgtype=1){
    //sys_log("sys_push");
    pushMessageToSingle($deviceType,$clientID,$msgContent,$keyType,$keyID,$nickname,$avatar);
}

//推送到用户列表
function sys_list_push($cid_list,$msgContent,$keyType=0,$keyID=0,$msgtype=1){
    //sys_log("sys_list_push");
    pushMessageToList($cid_list,$msgContent,$keyType,$keyID);
}

//推送到整个app
function sys_app_push($msgContent,$keyType=0,$keyID=0,$msgtype=1){
    //sys_log("sys_app_push");
    pushMessageToApp($msgContent,$keyType,$keyID);
}

//单推接口
function pushMessageToSingle($deviceType,$clientID,$msgContent,$keyType,$keyId,$nickname,$avatar){
    $host = HOST;
    if($deviceType == 1){//苹果
        $appid = IOS_APPID;
        $appkey = IOS_APPKEY;
        $mastersecret = IOS_MASTERSECRET;
    }
    else{//安卓
        $appid = ANDROID_APPID;
        $appkey = ANDROID_APPKEY;
        $mastersecret = ANDROID_MASTERSECRET;
    }
    $igt = new IGeTui($host,$appkey,$mastersecret);
    //消息模版：透传功能模板
    $template = IGtTransmissionTemplateDemo($appid,$appkey,$msgContent,$keyType,$keyId,$nickname,$avatar);
    //个推信息体
    $message = new IGtSingleMessage();

    $message->set_isOffline(true);//是否离线
    $message->set_offlineExpireTime(3600*12*1000);//离线时间
    $message->set_data($template);//设置推送消息类型
    //	$message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
    //接收方
    $target = new IGtTarget();
    $target->set_appId($appid);
    $target->set_clientId($clientID);
    //    $target->set_alias(Alias);

    try {
        $rep = $igt->pushMessageToSingle($message, $target);
        var_dump($rep);
        echo ("<br><br>");
    }catch(RequestException $e){
        $requstId =e.getRequestId();
        $rep = $igt->pushMessageToSingle($message, $target,$requstId);
        var_dump($rep);
        echo ("<br><br>");
    }
}

//多推接口案例
function pushMessageToList($cid_list,$msgContent,$keyType,$keyId){
    putenv("needDetails=true");
    $deviceType = 2;
    $host = HOST;
    if($deviceType == 1){//苹果
        $appid = IOS_APPID;
        $appkey = IOS_APPKEY;
        $mastersecret = IOS_MASTERSECRET;
    }
    else{//安卓
        $appid = ANDROID_APPID;
        $appkey = ANDROID_APPKEY;
        $mastersecret = ANDROID_MASTERSECRET;
    }
    $igt = new IGeTui($host,$appkey,$mastersecret);
    //消息模版：透传功能模板
    $template = IGtTransmissionTemplateDemo($appid,$appkey,$msgContent,$keyType,$keyId);
    //个推信息体
    $message = new IGtListMessage();
    $message->set_isOffline(true);//是否离线
    $message->set_offlineExpireTime(3600*12*1000);//离线时间
    $message->set_data($template);//设置推送消息类型
    //$message->set_PushNetWorkType(1);//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
    $contentId = $igt->getContentId($message);
    //接收方1
    $cid_list_a = explode(",",$cid_list);
    $targetList = array();
    foreach ($cid_list_a as $cid){
        $target1 = new IGtTarget();
        $target1->set_appId($appid);
        $target1->set_clientId($cid);
        $targetList[] = $target1;
    }

    $rep = $igt->pushMessageToList($contentId, $targetList);
//    var_dump($rep);
//    echo ("<br><br>");
}

//推送到整个APP
function pushMessageToApp($msgContent,$keyType,$keyId){
    $deviceType = 2;
    $host = HOST;
    if($deviceType == 1){//苹果
        $appid = IOS_APPID;
        $appkey = IOS_APPKEY;
        $mastersecret = IOS_MASTERSECRET;
    }
    else{//安卓
        $appid = ANDROID_APPID;
        $appkey = ANDROID_APPKEY;
        $mastersecret = ANDROID_MASTERSECRET;
    }
    $igt = new IGeTui($host,$appkey,$mastersecret);
    //消息模版：透传功能模板
    $template = IGtTransmissionTemplateDemo($appid,$appkey,$msgContent,$keyType,$keyId);

    //个推信息体
    //基于应用消息体
    $message = new IGtAppMessage();
    $message->set_isOffline(true);
    $message->set_offlineExpireTime(3600*12*1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
    $message->set_data($template);
    //$message->set_PushNetWorkType(1);//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
    $message->set_speed(100);// 设置群推接口的推送速度，单位为条/秒，例如填写100，则为100条/秒。仅对指定应用群推接口有效。
    $message->set_appIdList(array($appid));
    //$message->set_phoneTypeList(array('ANDROID'));
//  $message->set_provinceList(array('浙江','北京','河南'));
//  $message->set_tagList(array('开心'));
    $rep = $igt->pushMessageToApp($message);
//    var_dump($rep);
//    echo ("<br><br>");
}


function IGtTransmissionTemplateDemo($appid,$appkey,$msgContent,$keyType,$keyId,$nickname="",$avatar=""){
    $msg = array(
        'keyType' => $keyType,
        'keyId' => $keyId,
        'msg' => $msgContent,
        'nickname' => $nickname,
        'avatar' => $avatar
    );
    $msg = json_encode($msg);
    $template =  new IGtTransmissionTemplate();
    $template->set_appId($appid);//应用appid
    $template->set_appkey($appkey);//应用appkey
    $template->set_transmissionType(2);//透传消息类型
    $template->set_transmissionContent($msg);//透传内容
    //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
    //APN简单推送
    //        $template = new IGtAPNTemplate();
    //        $apn = new IGtAPNPayload();
    //        $alertmsg=new SimpleAlertMsg();
    //        $alertmsg->alertMsg="";
    //        $apn->alertMsg=$alertmsg;
    ////        $apn->badge=2;
    ////        $apn->sound="";
    //        $apn->add_customMsg("payload","payload");
    //        $apn->contentAvailable=1;
    //        $apn->category="ACTIONABLE";
    //        $template->set_apnInfo($apn);
    //        $message = new IGtSingleMessage();

    //APN高级推送
    $apn = new IGtAPNPayload();
    $alertmsg=new DictionaryAlertMsg();
    $alertmsg->body=$msgContent;
    $alertmsg->actionLocKey="ActionLockey";
    $alertmsg->locKey=$msgContent;
    $alertmsg->locArgs=array("locargs");
    $alertmsg->launchImage="launchimage";
    //        IOS8.2 支持
    $alertmsg->title=SYS_ZH_NAME;
    $alertmsg->titleLocKey=SYS_ZH_NAME;
    $alertmsg->titleLocArgs=array("TitleLocArg");

    $apn->alertMsg=$alertmsg;
    $apn->badge=1;
    $apn->sound="default";
    $apn->add_customMsg("payload","payload");
    $apn->add_customMsg("keyType",$keyType);
    $apn->add_customMsg("keyId",$keyId);
    $apn->add_customMsg("nickname",$nickname);
    $apn->add_customMsg("avatar",$avatar);
    $apn->add_customMsg("msg",$msgContent);
    $apn->contentAvailable=1;
    $apn->category="ACTIONABLE";
    $template->set_apnInfo($apn);

    //PushApn老方式传参
    //    $template = new IGtAPNTemplate();
    //          $template->set_pushInfo("", 10, "", "com.gexin.ios.silence", "", "", "", "");

    return $template;
}

//


?>
