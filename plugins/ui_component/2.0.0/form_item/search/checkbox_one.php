<?php
include __DIR__.'/base.php';
$data = $component['data'];
$default = $component['default'];
$value = $component['value'];
?>

<?php foreach($data as $data_value=>$data_label){ ?>
    <input type="checkbox" name="<?=$name?>" value="<?=$data_value?>" <?= in_array($data_value, $value) ? 'checked': '' ?> /> <?=$data_label?>
<?php }?>

