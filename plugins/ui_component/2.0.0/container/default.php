<?php
/**
 * 基础容器组件
 * 定义cls键值对，标志容器的class
 */
$class = $component['cls'];
$style = $component['style'];
$children = $component['_children'];
?>
<div class="<?=$class;?>" style="<?=$style?>">
    <?php foreach ($children as $child){?>
        <?php _parse($child); ?>
    <?php }?>
</div>
