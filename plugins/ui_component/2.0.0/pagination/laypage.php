<?php
    $totalcount = $component['total_count'];
    $page_count = $component['page_count'];
    $alltotalfee = $component['alltotalfee'];
    $allarrival_fee = $component['allarrival_fee'];
    $allwealth_fee = $component['allwealth_fee'];
    $allservice_fee = $component['allservice_fee'];
    $allredbag = $component['allredbag'];
    $totalfee = $component['totalfee'];
    $arrival_fee = $component['arrival_fee'];
    $cash_fee = $component['cash_fee'];
    $total_redbagfee = $component['total_redbagfee'];
    $total_redbagscore = $component['total_redbagscore'];
    $total_shopscore = $component['total_shopscore'];
    $total_cashscore = $component['total_cashscore'];

    $pages = ceil($totalcount / $page_count);
    $cur_page = $_REQUEST['page'] ? $_REQUEST['page'] : 1;

    unset($_GET['page']);
    unset($_POST['page']);
    $http_param = array_merge($_POST,$_GET);
    $location = U(MODULE_NAME.'/'.ACTION_NAME.'?'.kv_implode('&',$http_param));
?>
<div class="f-r mt-10 mr-20">
	<?php
		if($alltotalfee || $allservice_fee || $allarrival_fee || $allwealth_fee || $allredbag || $total_redbagfee || $total_redbagscore || $total_shopscore || $total_cashscore){
	?>
	<div class="f-l lh-30">
		<span class="mr-10">平台总交易额：<?=$alltotalfee?></span>
		<span class="mr-10">平台入账总额：<?=$allservice_fee?></span>
		<span class="mr-10">商家入账总额：<?=$allarrival_fee?></span>
		<span class="mr-10">用户总财气值：<?=$allwealth_fee?></span>
		<span class="mr-10">商家总红包池：<?=$allredbag?></span>
		<span class="mr-10">红包总现金：<?=$total_redbagfee?></span>
		<span class="mr-10">红包总积分：<?=$total_redbagscore?></span>
		<span class="mr-10">商城兑换总积分：<?=$total_shopscore?></span>
		<span class="mr-10">兑换总现金：<?=$total_cashscore?></span>
	</div>
	<?php
		}
	?>
	<?php
		if($arrival_fee){
	?>
	<div class="f-l lh-30">
		<span class="mr-10">入账总额：<?=$arrival_fee?></span>
	</div>
	<?php
		}
	?>
    <div class="f-l lh-30"><span class="mr-10">共有记录<?=$totalcount?>条</span><span></span></div>
    <div class="f-l" id="page"></div>
    <div class="f-l lh-30"><?=$cur_page . '/' . $pages;?></div>
</div>
<div class="clearfix"></div>
<script>
    $(function () {
        laypage({
            cont: 'page',
            pages: <?=$pages?>,
            curr: <?=$cur_page?>,
            skip: true,
            groups: 10,
            jump: function(obj, first){ //触发分页后的回调
                if(!first){
                    location.href = "<?=$location?>"+'&page='+obj.curr;
                }
            }
        })
    });
</script>