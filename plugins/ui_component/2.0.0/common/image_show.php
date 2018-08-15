<?php
$data = $component['data'];
$totalcount = $component['totalcount'];
$goods_id = $component['goods_id'];
?>
<include file="common:header" />
<nav class="breadcrumb">     <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
	<div class="cl pd-5 bg-1 bk-gray mt-20">
	 <span class="l">
	 <a href="javascript:;" onclick="delModel('<?=U('Product/delImg')?>')" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>
	 <a class="btn btn-primary radius" onclick="layer_show('添加','<?=U('Product/addImg?id='.$goods_id)?>')" href="javascript:;"><i class="Hui-iconfont">&#xe600;</i> 添加</a>
	 
	 </span> 
	 <span class="r">共有数据：<strong><?=$totalcount?></strong> 条</span> 
	 </div>
	<div class="portfolio-content">
		<ul class="cl portfolio-area">
		<?php 
			if($data){
				foreach($data as $vo){
				$id = $vo['id'];
				$imgurl = $vo['imgurl'];
				$imgurlbig = $vo['imgurlbig'];
		?>
			<li class="item">
				<div class="portfoliobox">
				    <input type="checkbox" value="<?=$id?>" name="id[]">
					<div class="picbox"><a href="javascript:;"><img src="<?=$imgurl?>"></a></div>
				</div>
			</li>
		<?php 
				}
			}else{
		?>
		<td colspan="50" class="text-c"> 暂时还没有内容! </td>
		<?php }?>
		
		</ul>
	</div>
</div>
<include file="common:footer" />
<script type="text/javascript">
	//删除数据
function delModel(url,ids){
	if(ids==undefined){ //非单个删除
		var ids = getArray('id[]','checkbox');
		if(ids.length==0){
			layer.msg('您尚未选中任何记录！',{icon: 5,time:1000});
			return;
		}
	}
	layer.confirm('确认要删除吗？',function(index){
		window.location.href=url+'&ids='+ids;
	});
}
</script> 
</body>
</html>