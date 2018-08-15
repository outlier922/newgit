<?php
$menus = $component['menus'];
$having_menus = $component['having_menus'];
?>
<div class="pd-20">
    <form action="<?=$component['action'];?>" method="post" class="form form-horizontal" id="form-layer">
        <input type="hidden" name="role_id"  value="<?=$component['role_id'];?>" />
		<div class="row cl">
			<div class="col-2">角色权限：</div>
			<div class="formControls col-10">
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
		<div class="row cl">
			<div class="col-8 col-offset-4">
				<input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;保存&nbsp;&nbsp;">
				<input class="btn btn-primary radius" type="button" id="form-cancel" value="&nbsp;&nbsp;关闭&nbsp;&nbsp;">
			</div>
		</div>
	</form>
</div>