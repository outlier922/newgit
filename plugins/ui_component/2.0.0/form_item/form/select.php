<?php
include __DIR__.'/base.php';
$data = $component['data'];
$default = $component['default'];
?>
<div class="row cl">
    <label class="form-label col-xs-4 col-sm-2">
        <?=$red_star?>
        <?=$label;?>：
    </label>
    <div class="formControls col-xs-8 col-sm-9">
        <select class="select <?=$class?> " name="<?=$name?>" placeholder="<?=$placeholder?>" value="<?=$value?>" id="<?=$name?>"
            style="font-size:14px; height:31px;line-height:1.42857;padding:4px;<?=$style?>"
        >
            <?php foreach ($data as $select_key=>$select_value){?>
                <?php
                    if($value){//修改
                        $checked = $value == $select_key ? "selected='selected'" : '';
                    }else{//新增
                        $checked = $default == $select_key ? "selected='selected'" : '';
                    }
                ?>
                <option value="<?=$select_key?>" <?=$checked?> >
                    <?=$select_value?>
                </option>
            <?php }?>
        </select>
    </div>
</div>
