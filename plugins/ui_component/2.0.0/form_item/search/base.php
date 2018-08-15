<?php
$label = $component['label'];
$name = $component['name'];
$width = $component['width'];
$placeholder = $component['placeholder'];
$class = $component['cls'];
$style = $component['style'];

$input_type = $component['type'];

$placeholder = $placeholder ? $placeholder : '请输入'.$label;
$input_type = $input_type ? $input_type : 'text';