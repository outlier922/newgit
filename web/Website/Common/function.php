<?php 
/**
 * 手机网站前台函数
 */


//检测用户是否登录
function wap_is_login(){
    $user = sys_get_cid();
    return empty($user) ? 0 : $user;
}
//商品详情页当前分类
function wap_current_cat($type,$cid,$tid)
{
    if($cid==$tid)
        echo $type==1 ? 'class="u-menu-on"' : 'class="u-cat-on"';
}
//评价
function wap_reply_get($replytype)
{
    if($replytype==1)
        $str="好评";
    else if($replytype==2)
        $str="中评";
    else 
        $str="差评";
    echo $str;
}
//用户头像，若为空则用默认头像
function wap_get_avatar($avatar){
    return $avatar ? $avatar : SYS_ROOT."images/default_avatar.png";
}
//默认图片
function wap_get_img($img){
    return $img ? $img : SYS_ROOT."images/default_backimg.png";
}
//截取手机号为152***123形式
function wap_get_mobile($mobile){
    return substr_replace($mobile,'***',3,5);
}
//订单状态
function wap_bill_status($payflag)
{
    if($payflag==0)
        $str="未付款";
    else if($payflag==1)
        $str="待发货";
    else if($payflag==2)
        $str="待收货";
    else if($payflag==3)
        $str="已收货";
    else if($payflag==4)
        $str="交易成功";
    else
        $str="交易成功";
    return $str;
}
//空信息显示
function wap_empty($str)
{
    return $str ? $str : '暂无';
}
//退款售后状态
function wap_after_status($type)
{
    if($type==1)
        $str="进行中";
    else if($type==2)
        $str="成功";
    else
        $str="失败";
    return $str;
}
 ?>