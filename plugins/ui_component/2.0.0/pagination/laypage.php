<?php
    $totalcount = $component['total_count'];
    $page_count = $component['page_count'];
    $alltotalfee = $component['alltotalfee'];
    $allarrival_fee = $component['allarrival_fee'];
    $allwealth_fee = $component['allwealth_fee'];
    $allservice_fee = $component['allservice_fee'];
    $allredbag = $component['allredbag'];

    $pages = ceil($totalcount / $page_count);
    $cur_page = $_REQUEST['page'] ? $_REQUEST['page'] : 1;

    unset($_GET['page']);
    unset($_POST['page']);
    $http_param = array_merge($_POST,$_GET);
    $location = U(MODULE_NAME.'/'.ACTION_NAME.'?'.kv_implode('&',$http_param));
?>
<div class="f-r mt-10 mr-20">
	<?php
		if($alltotalfee && $allservice_fee && $allarrival_fee && $allwealth_fee && $allredbag){
	?>
	<div class="f-l lh-30">
		<span class="mr-10">平台总交易额度：<?=$alltotalfee?></span>
		<span class="mr-10">平台入账总额度：<?=$allservice_fee?></span>
		<span class="mr-10">商家可提现总额：<?=$allarrival_fee?></span>
		<span class="mr-10">用户总财气值：<?=$allwealth_fee?></span>
		<span class="mr-10">商家总红包池：<?=$allredbag?></span>
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