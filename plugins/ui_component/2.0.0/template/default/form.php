<?php
form_data_correct(2,$form_items);
$validator = form_validation_correct($form_items);

//模板部分-------------------------------开始
$component_data = array(
    '_parser'=>'container/default',
    'cls' => 'pd-20',
    '_children'=>array(
        array(
            '_parser'=>'form/default',
            '_children'=>$form_items,
            'action' => __SELF__,
            'rules' => base64_encode(json_encode($validator['rules'])),
            'messages' => base64_encode(json_encode($validator['messages'])),
        )
    )
);
component_display($component_data);