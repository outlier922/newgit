<?php
$children = $component['_children'];
$GLOBALS['position_button'] = 2;
if($children){
?>
<td class="text-l" style="">
    <span class="dropDown ml-20">
        <a class="dropDown_A c-blue dropDown_A_2 radius pt-5 pb-5 pl-10 pr-10" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            操作<i class="Hui-iconfont Hui-iconfont-arrow2-bottom"></i>
        </a>
        <ul class="dropDown-menu menu radius box-shadow">
            <?php
                foreach ($children as $child) {
                    //位置
                    if ($child['position']) {
                        if (($child['position'] & $GLOBALS['position_button']) != $GLOBALS['position_button']) continue;
                    }

                    //访问
                    $global_action_access = pow(2,$GLOBALS['_action_access']);
                    if(isset($GLOBALS['_action_access']) && $GLOBALS['_action_access'] !== 0){
                        if(!$child['_action_access'] || ($child['_action_access'] & $global_action_access) !== $global_action_access) continue;
                    }
                    else{
                        if($child['_action_access'] && ($child['_action_access'] & $global_action_access) !== $global_action_access) continue;
                    }

                    if($child['_after_parser']){
                        $temp_component = $child['_after_parser'];
                        $temp_component['position'] = 'row';
                        echo '<li>';_parse($temp_component);echo '</li>';
                    }
                    else{
                        $child['_parser'] = 'button_item/position/button';
                        $child['position'] = 'row';
                        //dump($child);
                        echo '<li>';_parse($child);echo '</li>';
                    }
                }
            ?>
        </ul>
    </span>
</td>
<?php }?>