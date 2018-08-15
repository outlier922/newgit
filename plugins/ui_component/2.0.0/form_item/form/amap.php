<?php include __DIR__.'/base.php';?>
<div class="row cl" style="<?=$display?>">
    <label class="form-label col-xs-4 col-sm-2">
        <?=$red_star?>
        <?=$label;?>：
    </label>
    <div class="formControls col-xs-8 col-sm-9">
        <input type="text" id="<?=$name?>" class="input-text dptk-amap-item" autocomplete="off" datatype='*'
               placeholder="双击在地图上选择位置"  name="<?=$name?>"
               value="<?=$value?>" <?=$readonly?>>
    </div>
</div>