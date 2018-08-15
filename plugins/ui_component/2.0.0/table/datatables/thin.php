<?php include __DIR__.'/base.php';?>
<div class="pt-5 pb-5 pr-5">
    <table class="table table-border table-bordered table-hover table-data">
        <thead>
        <tr class="text-c">
            <th class="text-c w20"><input type="checkbox" name="" value=""></th>
            <?php foreach ($head as $head_item){?>
                <th class="<?=$head_item['cls']?>"><?=$head_item['title']?></th>
            <?php }?>
            <?php if($row_button['_children']) {?>
                <th class="text-c w150">操作</th>
            <?php }?>
        </tr>
        </thead>
        <tbody>
        <?php if(!$data){?>
            <tr><td colspan="50" class="text-c">暂时没有数据</td></tr>
        <?php } else {?>
            <?php foreach ($data as $data_item) { ?>
                <tr class="data_tr" data-data="<?=base64_encode(json_encode($data_item))?>">
                    <td class="text-c"><input type="checkbox" value="<?=$data_item['id']?>" name="id[]"></td>
                    <?php foreach ($head as $head_item) { ?>
                        <td>
                            <?php
                                if($head_item['_after_parser']){
                                    $temp_component = $head_item['_after_parser'];
                                    component_reset_value($temp_component,$data_item);
                                    _parse($temp_component);
                                }
                                else{
	                                if(!$data_item[$head_item['name']] && $head_item['name'] == 'lastlogintime'){
		                                echo '从未登录';
	                                }
                                    echo $data_item[$head_item['name']];
                                }
                            ?>
                        </td>
                    <?php } ?>
                    <?php if($row_button){_parse($row_button);}?>
                </tr>
            <?php }?>
        <?php }?>
        </tbody>
    </table>
</div>

