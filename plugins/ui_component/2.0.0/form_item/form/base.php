<?php
$label = $component['label'];
$value = $component['value'];
$name = $component['name'];
$type = $component['type'] ? $component['type'] : 'text';
$placeholder = $component['placeholder'];
$readonly = $component['readonly'];//0,1,2可写，新增只读，修改只读
$required = $component['required'];
$class = $component['cls'];
$style = $component['style'];
$operate = $component['_operate'];

//纠正变量
$placeholder = $placeholder ? $placeholder : '请输入'.$label;
$operate = $operate ? $operate : 3;
if($type == 'password'){
	$GLOBALS['cur_operate'] = 1;
}else{
	$GLOBALS['cur_operate'] = $GLOBALS['cur_operate'] ? $GLOBALS['cur_operate'] : 2;
}
//红点
$red_star = ($required !== 0) ? "<span class='c-red'>*</span>" : '';
//只读
$readonly = $readonly ? $readonly : 0;
$readonly = (($GLOBALS['cur_operate'] & $readonly) == $GLOBALS['cur_operate']) ? 'readonly="readonly"' : '';
//控制显示与不显示
$display = (($GLOBALS['cur_operate'] & $operate) == $GLOBALS['cur_operate']) ? '' : 'display:none';
$display = $readonly ? '' : $display;



