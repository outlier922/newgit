<?php
$title = $component['title'];
$head = $component['head'];
$data = $component['data'];
?>
<div class="ml-20 mr-20">
    <h5 class="c-red "><?php echo $component['title']; ?></h5>
    <table class="table table-border table-bordered table-bg">
        <tr>
            <?php foreach ($head as $head_item){?>
            <th class="<?=$head_item['cls']?> text-c"><?=$head_item['title']?></th>
            <?php }?>
        </tr>
        <?php foreach ($data as $data_item){?>
        <tr>
            <?php foreach ($head as $head_item){?>
                <td><?=$data_item[$head_item['name']]?></td>
            <?php }?>
        </tr>
        <?php }?>
    </table>
    
</div>
