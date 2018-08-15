<?php
    $data = $component['data'];
    $date_str = $component['date'];
    function sign_show(&$sign_data,$date,$cur_timestamp){
        if($cur_timestamp < strtotime($date)) return '';
        $str = "";
        if($sign_data['in']){
            $str .= "<div class='sign sign-in' data-sign='已到场：".$sign_data['in']['regdate'].";<p>操作老师：".$sign_data['in']['realname']."'>已到场</div>";
        }
        else if($sign_data['out']){
            $str .= "<div class='sign sign-out bk-gold'  data-sign='缺席：".$sign_data['out']['regdate'].";<p>操作老师：".$sign_data['out']['realname'].";<p>缺席原因：".$sign_data['out']['content']."'>缺席</div>";
        }
        else{
            $str .= "<div class='sign sign-out bk-red'  data-sign='未操作'>未操作</div>";
        }
        return $str;
    }
?>
<?php foreach ($data as $data_item){?>
    <?php
        $child_id = $data_item['id'];
        $realname = $data_item['realname'];
        $sign_data = $data_item['sign'];
    ?>
    <div class="mr-20 pd-20 f-l " style="width:480px;">
        <table class="table table-border table-bordered">
            <tr>
                <th class="text-c f-16" colspan="7" style="background-color: #6ce26c">
                    <?=$realname?>签到情况
                </th>
            </tr>
            <tr>
                <th class="text-c" colspan="2">
                    <a href="javascript:;" data-url="<?=U(MODULE_NAME.'/'.ACTION_NAME.'?child_id='.$child_id.'&regdate='.date('Y-m',strtotime('-1 months',strtotime($date_str))))?>" class="dptk-js-sign-pre">&lt;&lt;&nbsp;上一月</a>
                </th>
                <th class="text-c" colspan="3">
                    <?=$date_str?>
                </th>
                <th class="text-c" colspan="2" border="0">
                    <a href="javascript:;" data-url="<?=U(MODULE_NAME.'/'.ACTION_NAME.'?child_id='.$child_id.'&regdate='.date('Y-m',strtotime('+1 months',strtotime($date_str))))?>" class="dptk-js-sign-next">下一月&nbsp;&gt;&gt;</a>
                </th>
            </tr>
            <tr>
                <th class="text-c">日</th>
                <th class="text-c">一</th>
                <th class="text-c">二</th>
                <th class="text-c">三</th>
                <th class="text-c">四</th>
                <th class="text-c">五</th>
                <th class="text-c">六</th>
            </tr>
            <tbody class="dptk-js-sign-wrap">
            <?php
            //计算一共行数
            $timestamp = strtotime($date_str);
            $cur_timestamp = strtotime(date('Y-m-d'));//控制不显示超过今天的
            $first_w = date('w',$timestamp);//周几0-6
            $days = date('t',$timestamp);//指定月份的天数
            $last_w = ($first_w + $days ) % 7;//最后一天是周几
            $row =  ceil(($first_w + $days ) / 7);//一共显示多少行
            $now = 1;
            $str = "";
            for($i=1; $i<=$row; $i++){
                $str .= "<tr>";
                for($j=1; $j<=7; $j++){
                    $str .= "<td class='text-c'>";
                    if($i == 1){
                        if($j>=($first_w+1)){
                            $sign_str = sign_show($sign_data[$now],$date_str.'-'.$now,$cur_timestamp);
                            if($sign_str){
                                $str .= "<div class='wrap cursor-pointer mr-5 h40'>";
                                $str .= "<div class='f-20 c-red'>$now</div>";
                                $str .= $sign_str;
                                $str .= "</div>";
                            }
                            else{
                                $str .= "<div class='f-20 c-red h40'>$now</div>";
                            }
                            $now++;
                        }
                    }
                    else if($i == $row){
                        if($j<($last_w+1)){
                            $sign_str = sign_show($sign_data[$now],$date_str.'-'.$now,$cur_timestamp);
                            if($sign_str){
                                $str .= "<div class='wrap cursor-pointer mr-5 h40'>";
                                $str .= "<div class='f-20 c-red'>$now</div>";
                                $str .= $sign_str;
                                $str .= "</div>";
                            }
                            else{
                                $str .= "<div class='f-20 c-red h40'>$now</div>";
                            }
                            $now++;
                        }
                    }
                    else{
                        $sign_str = sign_show($sign_data[$now],$date_str.'-'.$now,$cur_timestamp);
                        if($sign_str){
                            $str .= "<div class='wrap cursor-pointer mr-5 h40'>";
                            $str .= "<div class='f-20 c-red'>$now</div>";
                            $str .= $sign_str;
                            $str .= "</div>";
                        }
                        else{
                            $str .= "<div class='f-20 c-red h40'>$now</div>";
                        }
                        $now++;
                    }

                    $str .= "</td>";
                }
                $str .= "</tr>";
            }
            echo $str;
            ?>
            </tbody>
        </table>
    </div>
<?php }?>
<div class="cl"></div>

