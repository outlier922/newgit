<?php include __DIR__.'/base.php';?>
<?php
	$lng = $_GET['lng'];
	$lat = $_GET['lat'];
?>

<div class="row cl" style="<?=$display?>">
    <label class="form-label col-xs-4 col-sm-2">
        <?=$red_star?>
        <?=$label;?>：
    </label>
    <div class="formControls col-xs-8 col-sm-9">
	    <?php
	    	if($lng && $lat){
	    ?>
	    <input type="text" id="<?=$name?>" class="input-text dptk-amap-item" data-lng="<?=$lng?>" data-lat="<?=$lat?>" autocomplete="off" datatype='*'
               placeholder="双击在地图上选择位置"  name="<?=$name?>"
               value="<?=$value?>" <?=$readonly?>>
        <?php
    		}else{
        ?>      
        <input type="text" id="<?=$name?>" class="input-text dptk-amap-item" autocomplete="off" datatype='*'
               placeholder="双击在地图上选择位置"  name="<?=$name?>"
               value="<?=$value?>" <?=$readonly?>>
        <?php
    		}
        ?>     
    </div>
</div>