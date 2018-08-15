<?php
include __DIR__.'/base.php';
$init_url = $component['init_url'];
?>

<span class="select-box inline ml-5">
    <select class="select dptk-js-select-bind <?=$class?>" style="<?=$style?>" name="<?=$name?>"
            value="<?=I($name)?>" data-init_url="<?=$init_url?>"
    >
        <option value="0"><?=$label?></option>
    </select>
</span>
