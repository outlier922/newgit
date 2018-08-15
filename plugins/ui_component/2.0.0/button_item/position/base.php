<?php
$text = $component['text'];
$title = $component['title'];
$position = $component['position'];
$icon = $component['icon'];
$full = $component['full'];
$class = $component['cls'];
$style = $component['style'];
$target = $component['target'];
$checked = $component['checked'];
$url = $component['url'];
$url_param = $component['url_param'];
$testing = $component['testing'];
$close_button = $component['close_button'];
$close_button = $close_button === 0 ? 0 : 1;
//纠正
$target = $target ? $target : 'inner_frame';
$checked = $checked ? $checked : 0;
$full = $full ? $full : 0;

$testing = $testing ? $testing : array();
$testing = base64_encode(json_encode($testing));
$url_param = $url_param ? $url_param : array();
$url_param = base64_encode(json_encode($url_param));

//显示的内容
$show = "";
if($position == 'top'){
    $show = '<i class="Hui-iconfont Hui-iconfont-'.$icon.'" ></i>'.$text;
}
else if($position == 'row'){
    $show = $title;
}