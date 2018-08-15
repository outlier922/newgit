<?php include dirname(__FILE__).'/base.php';?>
<fieldset>
    <label></label>
    <?php foreach ($children as $child){component_parse($child);}?>
</fieldset>