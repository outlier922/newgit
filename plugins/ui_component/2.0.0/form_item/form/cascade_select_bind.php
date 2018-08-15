<?php
include __DIR__.'/base.php';
$init_url = $component['init_url'];
$related = $component['related'];
?>

<div class="dptk-cascade-wrapper">
    <?php for ($i=0; $i<count($related); $i++){?>
        <div class="row cl dptk-cascade-select-wrapper">
            <label class="form-label col-xs-4 col-sm-2">
                <?php if($i == 0){?>
                    <?=$red_star?>
                    <?=$label;?>：
                <?php }?>
            </label>
            <div class="formControls col-xs-8 col-sm-9">
                <select class="select input-text dptk-js-cascade-select-bind" name="<?=$related[$i]['name'];?>" id="<?=$related[$i]['name'];?>"
                        value="<?=$related[$i]['value'];?>"
                        data-init_url="<?=$init_url?>"
                >
                    <option value="0"><?=$related[$i]['label']?></option>
                </select>
            </div>
        </div>
    <?php }?>
</div>
