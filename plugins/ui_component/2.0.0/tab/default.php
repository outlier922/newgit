<?php $children = $component['_children'];?>
<div id="tab-system" class="HuiTab">
    <div class="tabBar cl">
        <?php foreach ($children as $child){?>
            <span><?=$child['label']?></span>
        <?php }?>
    </div>

    <?php foreach ($children as $child){?>
        <div class="tabCon">
            <?php _parse($child);?>
        </div>
    <?php }?>
</div>