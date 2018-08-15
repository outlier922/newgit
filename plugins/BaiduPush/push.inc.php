<?php 
//引入百度推送
require_once dirname(__FILE__).'/sdk.php';
//3.0.0版已将 deviceID 换成 channelId
function sys_push($sql_helper,$deviceType,$deviceID,$msgContent,$keyType=0,$keyID=0,$msgtype=1)
{	
	global $default_devicetype;
	if($deviceType==1) //苹果
	{
		$default_devicetype = 4;
		// 创建SDK对象.
		$sdk = new PushSDK(BAIDU_PUSH_CONFIG::iphone_apiKey,BAIDU_PUSH_CONFIG::iphone_secretkey);
		$msgtype = 1; //1：通知
	}
	else if($deviceType==2) //安卓
	{
		$default_devicetype = 3;
		// 创建SDK对象.
		$sdk = new PushSDK(BAIDU_PUSH_CONFIG::android_apiKey,BAIDU_PUSH_CONFIG::android_secretkey);
		$msgtype = 0; //0：透传消息
	}	
	else
		return;
	sys_pushMsgToSingleDevice($sdk,$deviceID,$msgContent,$keyType,$keyID,$msgtype,$deviceType);
}
/**
 * 根据channel_id，向单个设备推送消息。
 */
function sys_pushMsgToSingleDevice($sdk,$channelId,$msgContent,$keyType=0,$keyID=0,$msgtype=1,$deviceType)
{
	/*// message content.
	$message = array (
	    // 消息的标题.
	    'title' => SYS_ZH_NAME,
	    // 消息内容 
	    'description' => $msgContent,
	    'custom_content' => array(
	    	'keyType' => $keyType,
	    	'keyID' => $keyID
	    )
	);*/
	 if($deviceType == 1)//IOS
    {
        $message = array (
            // 消息的标题.
            'title' => SYS_ZH_NAME,
            // 消息内容
            'description' => $msgContent,
            'custom_content' => array(
                'keyType' => $keyType,
                'keyId' => $keyID,
            ),
            "aps" => array(
                "alert" => $msgContent,
                "badge" => '1',
                "sound" => "default" // 提示音，需要在Xcode工程中添加同名的音频资源
            )
        );
    }
    else if($deviceType == 2)
    {
        $message = array (
            // 消息的标题.
            'title' => SYS_ZH_NAME,
            // 消息内容
            'description' => $msgContent,
            'custom_content' => array(
                'keyType' => $keyType,
                'keyId' => $keyID,
            )
        );
    }
    else 
    {
        return false;
    }
	// 设置消息类型为 通知类型.
	$opts = array (
	    'msg_type' => $msgtype,
	    'deploy_status' => SYS_DEBUG_MODE ? 1 : 2
	);
	// 向目标设备发送一条消息
	$rs = $sdk -> pushMsgToSingleDevice($channelId, $message, $opts);
	//print_r($rs);die;
}

/**
 * 广播，向当前应用下所有设备发送一条消息，以后扩展
 */
// function pushMsgToAll($msgContent,$keyType=0,$keyID=0)
// {

// }

function sys_pushBatchUniMsg()
{
    // 创建消息内容
    $msg = array(
        'description' => 'notice msg',
    );
    
    // 消息控制选项。
    $opts = array(
        'msg_type' => 1,
    );
    
    // 发送给以下五个设备，每个设备ID应与终端设备上产生的 channel_id 一一对应。
    $idArr = array(
        '3773865912079976196',
        '5074314023481561321',
    );
    $sdk = new PushSDK(BAIDU_PUSH_CONFIG::android_apiKey,BAIDU_PUSH_CONFIG::android_secretkey);
    // 发送
    $rs = $sdk -> pushBatchUniMsg($idArr,$msg,$opts);
    print_r($rs);die;
    if($rs !== false){
        print_r($rs);    // 将打印出 msg_id 及 send_time
    }
}
 ?>