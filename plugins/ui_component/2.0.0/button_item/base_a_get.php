<?php
$text = $component['text'];
$value = $component['value'];
$value = !sys_check_empty($value) ? $value : $text;
$title = $component['title'];
$full = $component['full'];
$target = $component['target'];
$url = $component['url'];
$url_param = $component['url_param'];
$url = U($url.'?'.kv_implode('&',$url_param));
$class = $component['cls'];
$style = $component['style'];

//纠正
$target = $target ? $target : 'inner_frame';
$full = $full ? $full : 0;


