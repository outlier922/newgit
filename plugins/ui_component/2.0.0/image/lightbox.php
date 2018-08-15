<?php
$imageItems = $component['imageItems'];
?>
<div class="mt-10 ml-20">
    <h5 class="c-red"><?=$component['title']; ?></h5>
    <div>
        <?php foreach ($imageItems as $image){?>
            <a href="<?=$image['imgurlbig']?>" data-lightbox="gallery" data-title="">
                <img  class="avatar size-XXXL" src="<?=$image['imgurl']?>">
            </a>
        <?php }?>
    </div>
</div>
