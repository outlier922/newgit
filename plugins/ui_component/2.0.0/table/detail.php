<?php $fields = $component['fields']; $cls = $component['cls'];?>
<div class="ml-20 mr-20 <?=$cls?>">
    <h5 class="c-red"><?php echo $component['title']; ?></h5>
    <table class="table table-border table-bordered table-bg">
        <?php foreach ($fields as $field){?>
            <tr>
                <td style="width: 150px;"><?=$field['title']?></td>
                <td>
                    <?php
                        if($field['_after_parser']){
                            $temp_component = $field['_after_parser'];

                            $temp_data = array();
                            component_reset_value($temp_component,$temp_data);
                            _parse($temp_component);
                        }
                    else{
                        echo $field['value'];
                    }
                    ?>
                </td>
            </tr>
        <?php }?>
    </table>
    
</div>
