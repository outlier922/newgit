<?php
$menus = $component['menus'];
$having_menus = $component['having_menus'];
$label = $component['label'];
?>
<div class="row cl">
	<label class="form-label col-xs-4 col-sm-2">
        <?=$label;?>：
    </label>
	<div class="formControls col-xs-8 col-sm-9">
        <?php foreach ($menus as $menu){?>
            <?php $item_checked = is_checked($menu['id'],$having_menus);?>
            <dl class="permission-list">
                <dt>
                    <label>
                        <input type="checkbox" <?=$item_checked;?> value="<?=$menu['id'];?>" name="menuId[]">
                        <?=$menu['name'];?>
                    </label>
                </dt>
                <dd>
                    <?php foreach ($menu['children'] as $menu_child){?>
                        <?php
                            $item_checked = is_checked($menu_child['id'],$having_menus);
                        ?>
                        <dl class="cl permission-list2">
                            <dt>
                                <label>
                                    <input type="checkbox" <?=$item_checked;?> value="<?=$menu_child['id'];?>" name="menuId[]">
                                    <?=$menu_child['name'];?>
                                </label>
                            </dt>
                            <dd>
                                <?php foreach ($menu_child['children'] as $hm_tpl_menu_child_2){?>
                                    <?php
                                    $item_checked = is_checked($hm_tpl_menu_child_2['id'],$having_menus);
                                    ?>

                                    <label>
                                        <input type="checkbox" <?=$item_checked;?> value="<?=$hm_tpl_menu_child_2['id'];?>" name="menuId[]">
                                        <?=$hm_tpl_menu_child_2['name'];?>
                                    </label>

                                <?php }?>
                            </dd>
                        </dl>
                    <?php }?>
                </dd>

            </dl>
        <?php }?>
	</div>
</div>