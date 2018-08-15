<?php
$data = $component['data'];
$name = $component['name'];
$values = $component['value'];
?>
<div class="row cl">
<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span><?php echo $component['label']; ?></label>
<div class="formControls col-xs-8 col-sm-9">
<?php foreach ($data as $key=>$value){?>
    <div class="radio-box" style="padding-left: 0px;">
        <input type="radio" id="collect_radio<?=$key?>" name="<?=$name?>" value="<?=$key?>" <?=$values == $key ? 'checked' : '';?> >
        <label for="collect_radio<?=$key?>"><?=$value?></label><br/>
	    <?php if($value == '固定积分'){?>
	    <div class="row cl">
		    <label class="form-label col-xs-7 col-sm-7">
		        <span class='c-red'>*</span>
		        固定积分值：
		    </label>
		    <div class="formControls col-xs-5 col-sm-5">
		        <input type="text" class="input-text" datatype='*' id="gu_score" name="gu_score" placeholder="输入固定积分值" />
		    </div>
		</div>
	    <div class="row cl">
		    <label class="form-label col-xs-7 col-sm-7">
		        <span class='c-red'>*</span>
		        固定积分中奖概率设置：
		    </label>
		    <div class="formControls col-xs-5 col-sm-5">
		        <input type="text" class="input-text" datatype='*' id="gu_chance" name="gu_chance" placeholder="输入固定积分值" />
		    </div>
		</div>
	    <?php }else if($value == '区间积分'){?>
	    <div class="row cl">
		    <label class="form-label col-xs-7 col-sm-7">
		        <span class='c-red'>*</span>
		        最小积分值：
		    </label>
		    <div class="formControls col-xs-5 col-sm-5">
		        <input type="text" class="input-text" datatype='*' id="qu_minscore" name="qu_minscore" placeholder="输入固定积分值" />
		    </div>
		</div>
	    <div class="row cl">
		    <label class="form-label col-xs-7 col-sm-7">
		        <span class='c-red'>*</span>
		        最大积分值：
		    </label>
		    <div class="formControls col-xs-5 col-sm-5">
		        <input type="text" class="input-text" datatype='*' id="qu_maxscore" name="qu_maxscore" placeholder="输入固定积分值" />
		    </div>
		</div>
	    <div class="row cl">
		    <label class="form-label col-xs-7 col-sm-7">
		        <span class='c-red'>*</span>
		        区间积分中奖概率设置：
		    </label>
		    <div class="formControls col-xs-5 col-sm-5">
		        <input type="text" class="input-text" datatype='*' id="qu_chance" name="qu_chance" placeholder="输入固定积分值" />
		    </div>
		</div>
	    <?php }?>
    </div>
<?php }?>
</div>
<div class="col-4"> </div>
</div>

