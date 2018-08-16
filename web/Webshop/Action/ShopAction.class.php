<?php

class ShopAction extends BaseAction
{
	public function shop_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        $shop_id = $_SESSION['shop']['auth']['uid'];
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        //基本查询
        $field_list = "mt.*,c.name one_classify_name,cl.name as two_classify_name ";
        $sql_suffix = "from sys_shop mt ";
        $sql_suffix .= "left join sys_classify c on mt.one_classify_id = c.id ";
        $sql_suffix .= "left join sys_classify cl on mt.two_classify_id = cl.id ";
        $sql_suffix .= "where mt.id=$shop_id ";
        $orderby_str = "mt.id desc";
        //查询数据
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        $list_items[0]['cardfee'] = $this->get_one_bysql("select sum(total_price) from sys_cardorder where shop_id=$shop_id and payflag=1");
        
        //UI部分
        $breadcrumb_data = '首页,商家管理,商家管理';
        $table_headers = array(           
            array('name'=>'name','cls'=>'w100','title'=>'商家名称'),
            array('name'=>'img','title'=>'商家封面','cls'=>'w50','_after_parser'=>array(
                '_parser'=>'image/thumb','imgurl'=>'3_img','imgurlbig'=>'3_bigimg'
            )),
            array('name'=>'qrcode','title'=>'收款二维码','cls'=>'w50','_after_parser'=>array(
                '_parser'=>'image/thumb','imgurl'=>'3_qrcode','imgurlbig'=>'3_qrcode_big'
            )),
            ['name'=>'avatar','title'=>'商家图片','cls'=>'w70','_after_parser'=>[
                '_parser'=>'button_item/td_a_get','text'=>'1_图片管理','title'=>'1_图片管理','full'=>0,
                'target'=>'inner_frame',
                'url'=>MODULE_NAME.'/image_list','url_param'=>['keytype'=>'1_2','keyid'=>'3_id']
            ]],
            array('name'=>'telphone','cls'=>'w70','title'=>'商家联系电话'),
            array('name'=>'linker','cls'=>'w70','title'=>'商家联系人'),
            array('name'=>'one_classify_name','cls'=>'w80','title'=>'经营范围'),
            array('name'=>'two_classify_name','cls'=>'w80','title'=>'细分领域'),
            array('name'=>'cardfee','cls'=>'w80','title'=>'优惠券总收入'),
            array('name'=>'','title'=>'优惠券总收益','cls'=>'w60','_after_parser'=>array(
                '_parser'=>'button_item/td_a_get','text'=>'3_cardfee','title'=>'1_收益记录','full'=>0,
                'target'=>'inner_frame',
                'url'=>'Shop/cardfee_get','url_param'=>array('id'=>'3_id')
            )),
            array('name'=>'','title'=>'商家详情','cls'=>'w100','_after_parser'=>array(
                '_parser'=>'button_item/td_a_get','text'=>'查看商家详情','title'=>'1_查看商家详情','full'=>0,
                'target'=>'inner_frame',
                'url'=>'Shop/indexshop_get','url_param'=>array('id'=>'3_id')
            )),
            array('name'=>'','title'=>'充值红包池金额','cls'=>'w60','_after_parser'=>array(
                '_parser'=>'button_item/td_a_get','text'=>'3_recharge_redbag','title'=>'1_充值记录','full'=>0,
                'target'=>'inner_frame',
                'url'=>'Shop/recharge_get','url_param'=>array('id'=>'3_id')
            )),
            array('name'=>'address','cls'=>'w200','title'=>'商家地址'),
        );
        $component_data = array(
            '_parser' => 'container/default',
            '_children' => array(
                array('_parser' => 'breadcrumb/default', 'data' => $breadcrumb_data),
                array('_parser' => 'container/content',
                    '_children' => array(
                        array('_parser' => 'table/datatables/thin',
                            'head' => &$table_headers,
                            'data' => &$list_items,
                            'row_button' => array('_parser' => 'button/row_dropdown')
                        ),
                        array('_parser' => 'pagination/laypage',
                            'total_count' => $GLOBALS['totalcount'],
                            'page_count' => $GLOBALS['page_count'],
                        ),
                    )
                )
            )
        );
        //解析组件
        _display($component_data);

    }
    

    public function recharge_get($_action_access=0){
	    //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
	    //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        $id = _REQUEST('id');
        if(!$id) layer_out_fail("参数传递不正确");

        //基本查询
        $field_list = "mt.*";
        $sql_suffix = "from sys_recharge_redbag mt ";
        $sql_suffix .= "where mt.shop_id=$id and mt.type=1 ";
        $orderby_str = "mt.id desc";
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        int_to_string($list_items,array(
        ));
        
		$breadcrumb_data = '首页,商家管理,充值记录';
        $table_headers = array(
            array('name'=>'fee','cls'=>'w60','title'=>'充值金额'),
            array('name'=>'regdate','cls'=>'w60','title'=>'充值时间'),
            array('name'=>'add_before','cls'=>'w60','title'=>'充值前'),
            array('name'=>'add_after','cls'=>'w60','title'=>'充值后'),
        );
        $component_data = array(
            '_parser' => 'container/default',
            '_children' => array(
                array('_parser' => 'breadcrumb/default', 'data' => $breadcrumb_data),
                array('_parser' => 'container/content',
                    '_children' => array(
                        array('_parser' => 'table/datatables/thin',
                            'head' => &$table_headers,
                            'data' => &$list_items
                        ),
                        array('_parser' => 'pagination/laypage',
                            'total_count' => $GLOBALS['totalcount'],
                            'page_count' => $GLOBALS['page_count'],
                        ),
                    )
                )
            )
        );
        _display($component_data);
    }

    
    public function cardfee_get($_action_access=0){
	    //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
	    //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        $id = _REQUEST('id');
        if(!$id) layer_out_fail("参数传递不正确");

        //基本查询
        $field_list = "mt.*,c.nickname ";
        $sql_suffix = "from sys_cardorder mt ";
        $sql_suffix .= "left join sys_client c on mt.client_id=c.id ";
        $sql_suffix .= "where mt.shop_id=$id and mt.payflag=1 ";
        $orderby_str = "mt.id desc";
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        int_to_string($list_items,array(
        ));
        
		$breadcrumb_data = '首页,商家管理,优惠券记录';
        $table_headers = array(
            array('name'=>'nickname','cls'=>'w60','title'=>'用户昵称'),
            array('name'=>'id','cls'=>'w100','title'=>'优惠券ID'),
            array('name'=>'name','cls'=>'w100','title'=>'优惠券名称'),
            array('name'=>'imgurl','title'=>'优惠券图片','cls'=>'w100','_after_parser'=>array(
                '_parser'=>'image/thumb','imgurl'=>'3_imgurl','imgurlbig'=>'3_imgurlbig'
            )),    
            array('name'=>'total_price','cls'=>'w100','title'=>'金额'),
        );
        $component_data = array(
            '_parser' => 'container/default',
            '_children' => array(
                array('_parser' => 'breadcrumb/default', 'data' => $breadcrumb_data),
                array('_parser' => 'container/content',
                    '_children' => array(
                        array('_parser' => 'table/datatables/thin',
                            'head' => &$table_headers,
                            'data' => &$list_items
                        ),
                        array('_parser' => 'pagination/laypage',
                            'total_count' => $GLOBALS['totalcount'],
                            'page_count' => $GLOBALS['page_count'],
                        ),
                    )
                )
            )
        );
        _display($component_data);
    }


    public function indexshop_get(){
        $id = _REQUEST('id');
        if(!$id) layer_out_fail("参数传递不正确");

        $sqlstr = "select * from sys_shop where id =$id";
        $result_r = $this -> get_list_bysql($sqlstr);
        int_to_string($result_r,array(

        ));
        $temp_array = $result_r[0];

        $fields = array(
            array('title'=>'商家详情','value'=>$temp_array['content']),
        );
        $component_data = array('_parser'=>'table/detail','title'=>'',
            'fields'=>$fields
        );
        _display($component_data);
    }


}