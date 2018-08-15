<?php
$action = $component['action'];
$method = $component['method'];
$children = $component['_children'];
$rules = $component['rules'];
$messages = $component['messages'];

$class = $component['cls'];//wrap的class
$style = $component['style'];//wrap的style

//校验
$rules = $rules ? $rules : array();
$messages = $messages ? $messages : array();
$rules = base64_encode(json_encode($rules));
$messages = base64_encode(json_encode($messages));


