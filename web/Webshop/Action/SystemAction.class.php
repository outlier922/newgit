<?php
class SystemAction extends BaseAction{
    //全景概览
    public function all_list(){
        $form_items = array(
            array('name'=>'shopshowww','_parser'=>'form_item/form/hidden','value'=>1),
            array('_parser'=>'tab/default','_children'=>array(
                array('label'=>'当日数据','_parser'=>'container/default','_children'=>array(
                    array('name'=>'to_sale_num','label'=>'销售量',
                        '_parser'=>'form_item/form/input','type'=>'text','readonly'=>2
                    ),
                    array('name'=>'to_buy','label'=>'当日订单',
                        '_parser'=>'form_item/form/input','type'=>'text','readonly'=>2
                    ),
                    array('name'=>'to_verification_num','label'=>'已核销',
                        '_parser'=>'form_item/form/input','type'=>'text','readonly'=>2
                    ),
                    array('name'=>'to_noverification_num','label'=>'待核销',
                        '_parser'=>'form_item/form/input','type'=>'text','readonly'=>2
                    ),
                    array('name'=>'to_return_num','label'=>'已退款',
                        '_parser'=>'form_item/form/input','type'=>'text','readonly'=>2
                    ),
                )),
                array('label'=>'当月数据','_parser'=>'container/default','_children'=>array(
                    array('name'=>'mo_sale_num','label'=>'销售量',
                        '_parser'=>'form_item/form/input','type'=>'text','readonly'=>2
                    ),
                    array('name'=>'mo_buy','label'=>'当月订单',
                        '_parser'=>'form_item/form/input','type'=>'text','readonly'=>2
                    ),
                    array('name'=>'mo_verification_num','label'=>'已核销',
                        '_parser'=>'form_item/form/input','type'=>'text','readonly'=>2
                    ),
                    array('name'=>'mo_return_num','label'=>'已退款',
                        '_parser'=>'form_item/form/input','type'=>'text','readonly'=>2
                    ),
                )),
                array('label'=>'本年数据','_parser'=>'container/default','_children'=>array(
                    array('name'=>'y_sale_num','label'=>'销售量',
                        '_parser'=>'form_item/form/input','type'=>'text','readonly'=>2
                    ),
                    array('name'=>'y_buy','label'=>'本年订单',
                        '_parser'=>'form_item/form/input','type'=>'text','readonly'=>2
                    ),
                    array('name'=>'y_verification_num','label'=>'已核销',
                        '_parser'=>'form_item/form/input','type'=>'text','readonly'=>2
                    ),
                    array('name'=>'y_return_num','label'=>'已退款',
                        '_parser'=>'form_item/form/input','type'=>'text','readonly'=>2
                    ),
                )),
            )),
        );
        form_validation_create($form_items,$rules,$messages);//获取验证规则
        $shop_id = $_SESSION['shop']['auth']['uid'];
        $day = date("Y-m-d",time());
        $to_verification_num = $this->get_one_bysql("select count(*) from sys_o2order_record re left join sys_o2order o on re.order_id=o.id where re.shop_id=$shop_id and o.assess_time like '$day%'");
        $to_sale_num = 0;
        $to_buy = $this->get_one_bysql("select count(*) from sys_o2order_record re left join sys_o2order o on re.order_id=o.id where re.shop_id=$shop_id and o.paytime like '$day%'");
        $to_noverification_num = $this->get_one_bysql("select count(*) from sys_o2order_record re left join sys_o2order o on re.order_id=o.id where re.shop_id=$shop_id and o.assess_time is NULL and re.regdate like '$day%' and re.is_return=2");
        $to_return_num = $this->get_one_bysql("select count(*) from sys_o2order_record where shop_id=$shop_id and regdate like '$day%' and is_return=1");
        $to_order_list = $this->get_list_bysql("select re.* from sys_o2order_record re left join sys_o2order o on re.order_id=o.id where re.shop_id=$shop_id and o.paytime like '$day%'");
        foreach( $to_order_list as $k => $v )
        {
        	$num = $v['num'];
        	$to_sale_num = $to_sale_num + $num;
        }
        $month = date("Y-m",time());
        $mo_order_list = $this->get_list_bysql("select re.* from sys_o2order_record re left join sys_o2order o on re.order_id=o.id where re.shop_id=$shop_id and o.paytime like '$month%'");
        $mo_sale_num = 0;
        $mo_buy = $this->get_one_bysql("select count(*) from sys_o2order_record re left join sys_o2order o on re.order_id=o.id where re.shop_id=$shop_id and o.paytime like '$month%'");
        $mo_verification_num = $this->get_one_bysql("select count(*) from sys_o2order_record re left join sys_o2order o on re.order_id=o.id where re.shop_id=$shop_id and o.assess_time like '$month%'");
        $mo_return_num = $this->get_one_bysql("select count(*) from sys_o2order_record where shop_id=$shop_id and regdate like '$month%' and is_return=1");
        foreach( $mo_order_list as $k => $v )
        {
        	$num = $v['num'];
        	$mo_sale_num = $mo_sale_num + $num;
        }
        $year = date("Y",time());
        $y_order_list = $this->get_list_bysql("select re.* from sys_o2order_record re left join sys_o2order o on re.order_id=o.id where re.shop_id=$shop_id and o.paytime like '$year%'");
        $y_sale_num = 0;
        $y_buy = $this->get_one_bysql("select count(*) from sys_o2order_record re left join sys_o2order o on re.order_id=o.id where re.shop_id=$shop_id and o.paytime like '$year%'");
        $y_verification_num = $this->get_one_bysql("select count(*) from sys_o2order_record re left join sys_o2order o on re.order_id=o.id where re.shop_id=$shop_id and o.assess_time like '$year%'");
        $y_return_num = $this->get_one_bysql("select count(*) from sys_o2order_record where shop_id=$shop_id and regdate like '$year%' and is_return=1");
        foreach( $y_order_list as $k => $v )
        {
        	$num = $v['num'];
        	$y_sale_num = $y_sale_num + $num;
        }
        $temp_array[0]['to_sale_num'] = $to_sale_num;
        $temp_array[0]['to_buy'] = $to_buy;
        $temp_array[0]['to_verification_num'] = $to_verification_num;
        $temp_array[0]['to_noverification_num'] = $to_noverification_num;
        $temp_array[0]['to_return_num'] = $to_return_num;
        $temp_array[0]['mo_sale_num'] = $mo_sale_num;
        $temp_array[0]['mo_buy'] = $mo_buy;
        $temp_array[0]['mo_verification_num'] = $mo_verification_num;
        $temp_array[0]['mo_return_num'] = $mo_return_num;
        $temp_array[0]['y_sale_num'] = $y_sale_num;
        $temp_array[0]['y_buy'] = $y_buy;
        $temp_array[0]['y_verification_num'] = $y_verification_num;
        $temp_array[0]['y_return_num'] = $y_return_num;
        form_item_add_value($form_items,$temp_array[0]);//赋值
        $component_data = array('_parser'=>'form/default',
            'action' => U(MODULE_NAME.'/'.ACTION_NAME),'_children'=>$form_items,
            'rules' => $rules,'messages' => $messages,
        );
        _display($component_data);
        
    }
}