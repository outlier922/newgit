<?php include __DIR__.'/base.php';?>
<?=$label?>:
<input class="input-text w60 <?=$class?>" style="<?=$style?>" placeholder="最小值" name="<?=$name?>_min" value="<?=I($name."_min");?>">
&le;
<input class="input-text w60 <?=$class?>" style="<?=$style?>" placeholder="最大值" name="<?=$name;?>_max"  value="<?=I($name."_max");?>">