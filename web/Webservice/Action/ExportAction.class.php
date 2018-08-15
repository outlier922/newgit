<?php
/*
| --------------------------------------------------------
| 	文件功能：	公司XX项目定时推送任务类
|	程序作者：	王海滨（移动互联部）
|	时间版本：	2014-11-28
|	特别说明：	(1)本类必须继承公司公共框架PublicAction基类					
				 	(2)Linux系统定时器每2分钟执行一次本程序
| --------------------------------------------------------
*/
require_once dirname(dirname(dirname(dirname(__FILE__))))."/plugins/tcpdf/tcpdf.php";
class ExportAction {

    public function export_pdf()
    {
        $version=_GET('version');
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // 设置文档信息
        $pdf->SetCreator('hema');
        $pdf->SetAuthor('cloud');
        $pdf->SetTitle('Welcome to hemaapp.com!');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, PHP');

        // 设置页眉和页脚信息
        $pdf->SetHeaderData('logo.png', 30, 'hemaapp.com', '致力于定制化移动APP制作开发',
            array(0, 64, 255), array(0, 64, 128));
        $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

        // 设置页眉和页脚字体
        $pdf->setHeaderFont(Array('stsongstdlight', '', '10'));
        $pdf->setFooterFont(Array('helvetica', '', '8'));

        // 设置默认等宽字体
        $pdf->SetDefaultMonospacedFont('courier');

        // 设置间距
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);

        // 设置分页
        $pdf->SetAutoPageBreak(TRUE, 25);

        // set image scale factor
        $pdf->setImageScale(1.25);

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        //设置字体
        $pdf->SetFont('stsongstdlight', '', 14);

        $pdf->AddPage();

        $str1 = SYS_ZH_NAME.'API开发文档';

        $pdf->Write(0, $str1, '', 0, 'C', true, 0, false, false, 0);


        $path = dirname(dirname(dirname(__FILE__)));

        include_once $path.'/Webservice/Action/V'.$version.'Action.class.php';
        $className='V'.$version.'Action';
        $methods = sys_get_class_all_methods($className);

        $html="";
        foreach ($methods as $m)
        {
            $html="";
            $method_name=$m['name'];
            $rMethod = new ReflectionMethod($className, $method_name);
            $docComment = $rMethod->getDocComment();
            $docCommentArr = explode("\n", $docComment);
            $title="";
            $req_params_array=NULL;
            $req_desc="";
            $ret=0;
            $ret_level=1;
            $ret_infor_array=NULL;
            $special_array="";
            $special_array_i=0;
            foreach ($docCommentArr as $comment) {
                $comment = trim($comment);
                if (strpos($comment, '@') === false && strpos($comment, '/') === false) {
                    $title = substr($comment, strpos($comment, '*') + 1);
                }

                $pos = stripos($comment, '@req_params');
                if ($pos !== false) {
                    $req_params = substr($comment, $pos + 12);
                    $tmp_req_params_array=explode(" ",$req_params);
                    $req_params_name=$tmp_req_params_array[0];
                    $req_params_title=$tmp_req_params_array[1]!==NULL ? $tmp_req_params_array[1] : "";
                    $req_params_memo=$tmp_req_params_array[2]!==NULL ? $tmp_req_params_array[2] : "";
                    $req_params_default=$tmp_req_params_array[3]!==NULL ? $tmp_req_params_array[3] : "";
                    $req_params_newflag=$tmp_req_params_array[4]!==NULL ? $tmp_req_params_array[4] : 0;

                    $temp_array=NULL;
                    $temp_array['title']=$req_params_title;
                    $temp_array['memo']=$req_params_memo;
                    $temp_array['default']=$req_params_default;
                    $temp_array['newflag']=$req_params_newflag;

                    $req_params_array[$req_params_name]=$temp_array;
                }

                $pos = stripos($comment, '@req_desc');
                if ($pos !== false) {
                    $req_desc = substr($comment, $pos + 10);
                }

                $pos = stripos($comment, '@ret');
                if ($pos !== false) {
                    $ret = substr($comment, $pos + 5);
                }

                $pos = stripos($comment, '@ret_level');
                if ($pos !== false) {
                    $ret = substr($comment, $pos + 5);
                }

                $pos = stripos($comment, '@ret_infor');
                if ($pos !== false) {
                    $ret_infor=substr($comment, $pos + 11);
                    $tmp_ret_infor_array=explode(" ",$ret_infor);
                    $ret_infor_name=$tmp_ret_infor_array[0]!==NULL ? $tmp_ret_infor_array[0] : "";
                    $ret_infor_title=$tmp_ret_infor_array[1]!==NULL ? $tmp_ret_infor_array[1] : "";
                    $ret_infor_memo=$tmp_ret_infor_array[2]!==NULL ? $tmp_ret_infor_array[2] : "";
                    $ret_infor_newflag=$tmp_ret_infor_array[3]!==NULL ? $tmp_ret_infor_array[3] : 0;

                    $temp_array=NULL;
                    $temp_array['title']=$ret_infor_title;
                    $temp_array['memo']=$ret_infor_memo;
                    $temp_array['newflag']=$ret_infor_newflag;

                    $ret_infor_array[$ret_infor_name]=$temp_array;
                }

                $pos = stripos($comment, '@special');
                if ($pos !== false) {
                    $special = substr($comment, $pos + 9);
                    $tmp_special_array=explode(" ", $special);
                    $special_title=$tmp_special_array[0]!==NULL ? $tmp_special_array[0] : "";
                    $special_memo=$tmp_special_array[1]!==NULL ? $tmp_special_array[1] : "";
                    $temp_array=NULL;
                    $temp_array['title']=$special_title;
                    $temp_array['memo']=$special_memo;
                    $special_array[$special_array_i]=$temp_array;
                    $special_array_i++;
                }
            }
            $api_phpfiles[$method_name]['title']=$title;
            $api_phpfiles[$method_name]['req_params']=$req_params_array;
            $api_phpfiles[$method_name]['req_desc']=$req_desc;
            $api_phpfiles[$method_name]['ret']=$ret;
            $api_phpfiles[$method_name]['ret_level']=$ret_level;
            $api_phpfiles[$method_name]['ret_infor']=$ret_infor_array;
            $api_phpfiles[$method_name]['special']=$special_array;


            $apidoc=$api_phpfiles[$method_name];

            $pdf->Bookmark($apidoc['title'], 0, 0, '', 'B', array(0,64,128));
            $html.='<div style="position: absolute;width:95%;text-align:left;margin-top:2%;margin-left:2%;line-height:2;">';
            $html.='<p><img src=\"".SYS_EXTJS_URL."\" width="16" height="16" /><span style="font-size:22px;font-weight:bold;color:#c0272b">功能描述：'.$apidoc['title'].'接口</span></p>';
            $html.='<p style="font-size:18px;font-weight:bold;">（一）服务接口请求地址：</p>';
            $html.='<table width="90%" border="1" style="border-color:#000;width:100%;text-align:left;">';
            $html.='<tr style="font-weight:bold">';
            $html.='<td width="15%">字段名称</td>';
            $html.='<td width="85%">字段信息</td>';
            $html.='</tr>';
            $html.='<tr>';
            $html.='<td>请求的地址</td>';
            $html.='<td>';
            if($method_name=="init")
                $html.='[sys_root]Webservice/Index/init';
            else if ($method_name=="webview")
                $html.='[sys_web_service]".$method_name."/parm/【下面所列的参数值】';
            else if ($method_name=="alipaysign_get")
                $html.='[sys_plugins]OnlinePay/Alipay/alipaysign_get.php';
            else if ($method_name=="unionpay_get")
                $html.='[sys_plugins]OnlinePay/Unionpay/unionpay_get.php';
            else if ($method_name=="weixinpay_get")
                $html.='[sys_plugins]OnlinePay/Unionpay/weixinpay_get.php';
            else
                $html.='[sys_web_service]'.$method_name;
            $html.='</td></tr></table>';
            $html.='<p style="font-size:18px;font-weight:bold;text-align:left;">（二）POST参数列表：必填字段</p>';
            $html.='<table width="90%" border="1" style="border-color:#000;width:100%;text-align:left;">';
            $html.='<tr style="font-weight:bold">';
            $html.='<td width="23%">参数名称</td>';
            $html.='<td width="25%">参数说明</td>';
            $html.='<td width="52%">备注</td>';
            $html.='</tr>';
            if(isset($apidoc) && !empty($apidoc)) {
                if(isset($apidoc['req_params']) && count($apidoc['req_params'])>0 ) {
                    foreach($apidoc['req_params'] as $req_key =>$req_param){
                        if(count($req_param) == 1){
                            $html.='<td colspan="3"><p style="font-size:13px;color:#060;font-weight:bold;">'.$req_param['title'].'</p></td>';
                            continue;
                        }
                        $html.='<tr>';
                        $html.='<td>'.$req_key.'</td>';
                        $html.='<td>'.$req_param['title'].'</td>';
                        $html.='<td>'.str_replace('$', ' <br /> ', $req_param['memo']).'</td>';
                        $html.='</tr>';
                    }
                }
            }
            $html.='</table>';

            if(isset($apidoc['req_desc']) && !empty($apidoc['req_desc'])){
                $html.='<p style="font-size:13px;color:#060;font-weight:bold;">';
                $html.=$apidoc['req_desc'];
                $html.='</p>';
            }
            $html.='<p style="font-size:18px;font-weight:bold;text-align:left;">（三）服务接口响应请求：</p>';
            $html.='<table width="90%" border="1" style="border-color:#000;width:100%;text-align:left;">';
            $html.='<tr style="font-weight:bold">';
            $html.='<td width="51%">响应结果</td>';
            $html.='<td width="31%">备注</td>';
            $html.='</tr>';
            $html.='<tr><td><p>';
            $html.='{"success":true,"msg":"操作成功"';
            $html.= empty($apidoc['ret']) ? ' ': ',"infor":json信息串';
            $html.='}</p></td>';
            $html.='<td><p>';
            $html.=empty($apidoc['ret']) ? ' ': '详见（四）特别备注';
            $html.='</p></td></tr>';

            if ($apidoc["ret"]==1) {
                $html.='<tr style="font-size:13px;color:#060;font-weight:bold;">';
                $html.='<td colspan="2">infor形为：{[{item1}，{item2}]}</td></tr>';
            }
            else if($apidoc["ret"]==2) {
                $html.='<tr style="font-size:13px;color:#060;font-weight:bold;">';
                $html.='<td colspan="2">特别提示：任何一个取名"xxxx_list"形式的接口（无需分页的除外）,infor形为：{"totalCount":0,"listItems":[{item1}，{item2}]}<br>其中：totalCount 表示所有符合查询条件的总记录的个数（totalCount=0 表示暂无数据），listItems是分页时，每页的记录详情条目。</td></tr>';
            }
            $html.='<tr style="font-size:13px;color:#060;font-weight:bold;">';
            $html.='<td colspan="2">如果是{"success":false}，而失败情况又分不同情形时，服务器会提供一个error_code字段标识来加以区分 。</td></tr>';
            $html.='</table>';

            if (isset($apidoc["ret_infor"])){
                $html.='<p><span style="font-size:18px;font-weight:bold;text-align:left;">（四）特别备注</span>（infor字段说明，仅列出部分关键字段）</p>';
                $html.='<table width="90%" border="1" style="border-color:#000;width:100%;text-align:left;">';
                $html.='<tr style="font-weight:bold">';
                $html.="<td width='23%'>参数名称</td>";
                $html.='<td width="25%">参数说明</td>';
                $html.='<td width="52%">备注</td>';
                $html.='</tr>';

                foreach ($apidoc["ret_infor"] as $ret_key => $ret_param) {
                    if (count($ret_param) == 1) {
                        $html.='<td colspan="3"><p style="font-size:13px;color:#060;font-weight:bold;">';
                        $html.=$ret_param[0].'</p></td>';
                    }
                    $html.='<tr>';
                    $html.='<td>'.$ret_key.'</td>';
                    $html.='<td>'.$ret_param['title'].'</td>';
                    $html.='<td>'.str_replace('$', ' <br /> ', $ret_param['memo']).'</td>';
                    $html.='</tr>';
                }
                $html.='</table>';
            }

            if (isset($apidoc["ret_level"]) && $apidoc["ret_level"]==2) {
                foreach ($apidoc["ret_infor"] as $ret_key => $ret_param) {
                    if (isset($ret_param['ret_infor']) && count($ret_param['ret_infor']) > 0) {
                        $html .= '<p><span style="font-size:14px;font-weight:bold;"><?php echo $ret_key;?>字段说明</p>';
                        $html .= '<table width="90%" border="1" style="border-color:#000;width:100%;text-align:left;">';
                        $html .= '<tr style="font-weight:bold">';
                        $html .= '<td width="23%">参数名称</td>';
                        $html .= '<td width="25%">参数说明</td>';
                        $html .= '<td width="52%">备注</td>';
                        $html .= '</tr>';
                        foreach ($ret_param['ret_infor'] as $ret_l2_key => $ret_l2_param) {
                            if (count($ret_param) == 1) {
                                $html .= '<td colspan="3"><p style="font-size:13px;color:#060;font-weight:bold;">';
                                $html .= $ret_l2_param['title'] . '</p></td>';
                                continue;
                            }
                            $html .= '<tr>';
                            $html .= '<td>' . $ret_l2_key . '</td>';
                            $html .= '<td>' . $ret_l2_param['title'] . '</td>';
                            $html .= '<td>' . str_replace('$', ' <br /> ', $ret_l2_key['memo']) . '</td>';
                            $html .= '</tr>';
                        }
                        $html .= '</table>';
                    }
                }
            }

            if (count($apidoc["special"])>0) {
                $html.='<p><span style="font-size:18px;font-weight:bold;text-align:left;">（五）其他相关说明</span></p>';
                foreach($apidoc["special"] as $special) {
                    $html.='<p><b>'.$special["title"].'</b></p>';
                    if(is_array($special["memo"]) && count($special["memo"]) > 0) {
                        foreach($special["memo"] as $desc){
                            $html.='<p style="font-size: 13px; color:blue"><?php echo $desc;?></p>';
                        }
                    }
                    else{
                        $html.='<p style="font-size: 13px; color:blue">'.$special["memo"].'</p>';
                    }
                }
            }

            $html.='</div>';
            //var_dump($method_name);
            $pdf->writeHTML($html, true, false, true, false, '');
        }


        //$api_phpfiles_str=json_encode($api_phpfiles);
        //$pdf->Write(0, $api_phpfiles_str, '', 0, 'L', true, 0, false, false, 0);




// output the HTML content
        //$pdf->writeHTML($html, true, false, true, false, '');


        //输出PDF
        $pdf->Output('t.pdf', 'I');



        sys_out_success();
    }
}