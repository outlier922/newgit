<?php
$data = $component['data'];
?>
<div class="portfolio-content">
    <ul class="cl portfolio-area">
        <?php if(!$data){?>
            暂无图片
        <?php } else {?>
            <?php foreach ($data as $image_r_i){?>
                <li class="item">
                    <div class="data_tr inline" data-data="<?=base64_encode(json_encode($image_r_i)) ?>" >
                        <div class="portfoliobox">
                            <input class="checkbox" name="id[]" type="checkbox" value="{$vo.id}">
                            <div class="picbox"><a href="<?=$image_r_i['imgurlbig']?>" data-lightbox="gallery" data-title=""><img src="<?=$image_r_i['imgurl']?>"></a></div>
                            <div class="textbox"> </div>
                        </div>
                    </div>
                </li>
            <?php }?>
        <?php }?>
    </ul>
</div>