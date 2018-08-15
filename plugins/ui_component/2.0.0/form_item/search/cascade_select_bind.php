<?php
include __DIR__.'/base.php';
$init_url = $component['init_url'];
$related = $component['related'];
?>

<span class="dptk-cascade-wrapper">
    <?php for ($i=0; $i<count($related); $i++){?>
    <span class="select-box inline dptk-cascade-select-wrapper">
        <select class="select  dptk-js-cascade-select-bind" name="<?=$related[$i]['name'];?>" id="<?=$related[$i]['name'];?>" style="<?=$related[$i]['style'];?>"
                value="<?=I($related[$i]['name']);?>"
                data-init_url="<?=$init_url?>"
        >
            <option value="0"><?=$related[$i]['label']?></option>
        </select>
    </span>
    <?php }?>
</span>
