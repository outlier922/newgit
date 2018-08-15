<?php
$children = $component['_children'];
$GLOBALS['position_button'] = 1;
if($children){
?>
<div class="cl  bg-1 bk-gray" style="padding-top:8px;padding-bottom: 8px;">
    <?php
        foreach ($children as $child) {
            //位置
            if ($child['position']) {
                if (($child['position'] & $GLOBALS['position_button']) != $GLOBALS['position_button'])
 continue;
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
                $temp_component['cls'] = ' btn btn-primary radius ml-5 ';
                $temp_component['position'] = 'top';
                echo '<span class="f-l mr-5">'; _parse($temp_component); echo '</span>';
            }
            else{
                $child['_parser'] = 'button_item/position/button';
                $child['cls'] .= ' btn btn-primary radius ml-5 ';
                $child['position'] = 'top';
                echo '<span class="f-l mr-5">'; _parse($child); echo '</span>';
            }

        }
    ?>
    <span class="f-r c-red pt-10 pr-10 f-16"><?=$component['notice']?></span>
</div>
<?php }?>
