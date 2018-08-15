<?php include __DIR__.'/base.php';?>
<div class="pd-20 <?=$class?>" style="<?=$style?>">
    <form action="<?=$action?>" method="post" class="form form-horizontal" id="form-layer"
          data-rules="<?=$rules?>" data-messages="<?=$messages?>"
    >
<?php foreach ($children as $child) {?>
		<div class="row cl" style="<?=$display?>">
		    <label class="form-label col-xs-4 col-sm-2">
		        <?=$red_star?>
		        <?=$label;?>：
		    </label>
		    <div class="formControls col-xs-8 col-sm-9">
		        <input type="<?=$type?>" class="input-text" datatype='*' id="<?=$name?>"
		               name="<?=$name?>" placeholder="<?=$placeholder?>" value="<?=$value?>" <?=$readonly?>
		        />
		    </div>
		</div>
<?php }?>
		<div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;保存&nbsp;&nbsp;">
                <input class="btn btn-primary radius" type="button" id="form-cancel" value="&nbsp;&nbsp;取消&nbsp;&nbsp;">
            </div>
        </div>
	</form>
</div>