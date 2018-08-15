<?php
$value = $component -> get_value();
$ext_data = $component -> get_ext_data();
$name = $component -> get_name();
?>
<a href="javascript:;" onclick="hm_image_show(this,event)" data-data="<?=$ext_data?>" data-name="<?=$name?>">
    <img src="<?=$value;?>" class="avatar size-XL" accept="image/*">
</a>
