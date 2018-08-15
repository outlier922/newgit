<?php
    $data = $component['data'];
    $data_a = explode(',',$data);
?>
<nav class="breadcrumb">
    <i class="Hui-iconfont Hui-iconfont-home"></i>
    <?php foreach ($data_a as $key=>$item){?>
        <?=$item?>
        <?php if($key == count($data_a) -1) break; ?>
        <span class=" dptk-suffix dptk-suffix-right-arrow">&gt;</span>
    <?php }?>
    <a class="btn-refresh btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.reload()" title="刷新" >
        <i class="Hui-iconfont Hui-iconfont-huanyipi"></i>
    </a>
</nav>
