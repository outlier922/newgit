<?php include __DIR__.'/base.php';?>
<div class="row cl" style="<?=$display?>">
    <label class="form-label col-xs-4 col-sm-2">
        <?=$red_star?>
        <?=$label;?>：
    </label>
    <div class="formControls col-xs-8 col-sm-9">
        <input class="input-text w120 <?=$class?>" id="<?=$name?>_start" style="<?=$style?>" placeholder="开始时间" onfocus="WdatePicker({dateFmt:'HH:mm'})" name="<?=$name?>_start" value="<?=I($name."_start");?>">
&nbsp;-&nbsp;
		<input class="input-text w120 <?=$class?>" id="<?=$name?>_end" style="<?=$style?>" placeholder="结束时间" onfocus="WdatePicker({dateFmt:'HH:mm'})" name="<?=$name;?>_end"  value="<?=I($name."_end");?>">
        <?php include __DIR__.'/base.php';?>
    </div>
</div>
