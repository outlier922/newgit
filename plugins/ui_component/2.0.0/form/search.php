<?php
include __DIR__.'/base.php';
if($children){
?>
<div class="mt-20 mb-20 <?=$class?>" style="<?=$style?>">
    <form action="<?=$action?>" method="post">
        <input type="hidden" name="search-flag" value="1">
        <?php foreach ($children as $child){?>
            <span><?php _parse($child);?></span>
        <?php }?>
        <span>
            <button type="submit" class="btn btn-success radius">
                <i class="Hui-iconfont Hui-iconfont-search2"></i>
                搜索
            </button>
        </span>
    </form>
</div>
<?php }?>