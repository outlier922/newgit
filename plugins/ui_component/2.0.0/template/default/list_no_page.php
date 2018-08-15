<?php
//面包屑
if($breadcrumb_items){
    $template[] = array(
        '_parser'=>'breadcrumb/default',
        'items' => $breadcrumb_items
    );
}
//页面区
$page_container = array(
    '_parser'=>'container/default',
    'cls' => 'page-container',
    '_children' => array()
);
//搜索
if($search_items){
    $page_container['_children'][] = array(
        '_parser'=>'container/default',
        'cls' => 'text-c',
        '_children'=>array(
            array(
                '_parser' => 'form/search',
                'action' => __SELF__,
                'button_block' => false,
                '_children' => $search_items //这里是表单区
            )
        )
    );
}
//按钮
if($button_items){
    $top_button_children = component_button_children(1,$button_items);
    $page_container['_children'][] = array(
        '_parser' => 'button/top',
        '_children' => $top_button_children
    );
}
//表格
if($field_items){
    $table_head_children = generate_table_head_children($field_items);
    $table_data_children = generate_table_data_children($list_items,$field_items,$button_items);
    $page_container['_children'][] = array(
        '_parser'=>'container/default',
        'cls' => 'mt-10 dataTables_wrapper',
        '_children'=>array(
            array(
                '_parser'=>'table/default',
                '_children'=>array(
                    array(
                        '_parser'=>'table_head/default',
                        '_children'=>array(
                            array(
                                '_parser'=>'table_tr/head',
                                '_children'=> $table_head_children
                            ),
                        )
                    ),
                    array(
                        '_parser'=>'table_body/default',
                        '_children'=>$table_data_children
                    ),
                ),
            ),
        )
    );
}
$template[] = $page_container;