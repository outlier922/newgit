<?php
$class = $component['cls'];
$style = $component['style'];
$children = $component['_children'];
?>
<div class="<?=$class;?>" style="<?=$style?>">
    <?php foreach ($children as $child){?>
        <?php _parse($child); ?>
    <?php }?>
</div>
