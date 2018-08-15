<?php include __DIR__.'/base.php';?>
<?=$label?>：
<input class="input-text w120 <?=$class?>" style="<?=$style?>" placeholder="开始时间" onfocus="WdatePicker()" name="<?=$name?>_start" value="<?=I($name."_start");?>">
&nbsp;-&nbsp;
<input class="input-text w120 <?=$class?>" style="<?=$style?>" placeholder="结束时间" onfocus="WdatePicker()" name="<?=$name;?>_end"  value="<?=I($name."_end");?>">