<?php
/**
 * 基础容器组件
 * 定义cls键值对，标志容器的class
 */
$cls = $component['cls'];
$children = $component['_children'];
?>
<div class="ml-20 <?=$cls;?>">
    <?php foreach ($children as $child){?>
        <?php _parse($child); ?>
    <?php }?>
</div>
