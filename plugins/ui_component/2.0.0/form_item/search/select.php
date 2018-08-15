<?php
    include __DIR__.'/base.php';
    $data = $component['data'];
?>

<span class="select-box inline ml-5">
    <select class="select <?=$class?>" style="<?=$style?>" name="<?=$name?>">
        <?php foreach ($data as $temp_option_key=>$temp_option_value){?>
            <option value="<?=$temp_option_key;?>" <?=(I($name) === $temp_option_key."") ? "selected=\"selected\" " : " ";?> >
                <?=$temp_option_value;?>
            </option>
        <?php }?>
    </select>
</span>
