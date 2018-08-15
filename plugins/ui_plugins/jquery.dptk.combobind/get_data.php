<?php
$keyword = $_POST['keyword'];

$data = [
    ['id'=>'1','name'=>'abc1'],
    ['id'=>'2','name'=>'abc2'],
    ['id'=>'3','name'=>'abc3'],
    ['id'=>'4','name'=>'def1'],
    ['id'=>'5','name'=>'def2'],
    ['id'=>'6','name'=>'def3'],
    ['id'=>'7','name'=>'ghi1'],
    ['id'=>'8','name'=>'ghi2'],
    ['id'=>'9','name'=>'ghi3'],
];

//过滤关键字
if($keyword){
    $new_data = [];
    foreach ($data as $data_i){
        if(strpos($data_i['name'], $keyword) !== false){
            array_push($new_data, $data_i);
        }
    }
    $data = $new_data;
}

sys_out_success(0, $data);



//向客户端输出成功信息
function sys_out_success($parmMsg=NULL,$infor_array=[]){
    unset($result_array);
    $result_array['success'] = true;//注意：为了和extjs兼容，此处必须不带引号

    if(empty($parmMsg)) $parmMsg="操作成功！";

    $result_array['msg'] = $parmMsg;
    $result_array['infor'] = $infor_array;//固定输出infor字段，以适配各种复杂情况
    sys_out_json($result_array);
}
//向客户端发送JSON串
function sys_out_json($parm_array)
{
    //防止PHP自带json_encode函数把中文转成unicode(必须是PHP5.4以上版本)
    //die(json_encode($parm_array,JSON_UNESCAPED_UNICODE));
    if (version_compare(PHP_VERSION,'5.4.0','<'))
        echo json_encode($parm_array);
    else
        echo json_encode($parm_array, JSON_UNESCAPED_UNICODE);
    die();//非常重要，请勿删除
}


