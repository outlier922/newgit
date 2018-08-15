<?php
$imgurl = $component['imgurl'];
$imgurlbig = $component['imgurlbig'];
?>
<a href="javascript:;" class="dptk-image-big-show" data-imgurlbig="<?=$imgurlbig ? $imgurlbig: SYS_DEFAULT_IMAGE;?>" >
    <img src="<?=$imgurl ? $imgurl: SYS_DEFAULT_IMAGE;?>" class="avatar size-XL">
</a>
