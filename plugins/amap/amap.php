<?php require_once dirname(dirname(dirname(__FILE__)))."/include/system.core.php";?>
<?php
	$lng = $_GET['lng'];
	$lat = $_GET['lat'];
	if($lng == undefined || $lat == undefined){
		$lng = "119.52719";
		$lat = "35.41646";
	}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no, width=device-width">
    <title>高德地图插件</title>
    <link rel="stylesheet" href="http://cache.amap.com/lbs/static/main1119.css"/>
    <style>
        #map{
        }
        #search-city{
            position: fixed;
            right: 20px;
            top:20px;
            z-index:100;
        }
        #infor{
            position: fixed;
            right: 20px;
            /*bottom: 60px;*/
            top:45px;
            z-index:100;
            padding:3px;color:#F00;
            background-color:#FF6;
        }
    </style>
    <script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=6635e2b9f6ad3fb849fba7c6ce75c794&plugin=AMap.Geocoder"></script>
    <script>
        window.onload = function(){
            //初始化地图
            var lng = "<?=$lng?>";
            var lat = "<?=$lat?>";
            map = new AMap.Map("container", {
                level:10,
                resizeEnable: true,
                center:new AMap.LngLat(lng, lat),//济南市
                cursor:'move'
            });
            var _icon = new AMap.Icon({
                image : 'http://webapi.amap.com/theme/v1.3/markers/b/mark_bs.png',
                imageSize : new AMap.Size(19,39)//设置图标大小
            });
            new AMap.Marker({
	            map: map,
	            icon: _icon,
	            position: [lng,lat],
	            offset: new AMap.Pixel(-12, -36)
	        });

            //绑定单击地图事件
            AMap.event.addListener(map, 'click', function(e){
                AMap.HeMa.clickListener(e);
            });
            //绑定搜索城市事件
            AMap.event.addDomListener(document.getElementById('query'), 'click', function(e) {
                var city_name = document.getElementById('city_name').value;
                if (!city_name) {
                    city_name = '济南市';
                }
                AMap.HeMa.setCenterPosition(map,2,{city_name:city_name});
            });
            //绑定键盘按键
            document.onkeydown=function(event){
                var e = event || window.event || arguments.callee.caller.arguments[0];
                if(e && e.keyCode==13){ // enter 键
                    var city_name = document.getElementById('city_name').value;
                    if (!city_name) {
                        city_name = '济南市';
                    }
                    AMap.HeMa.setCenterPosition(map,2,{city_name:city_name});
                }
            };
        }
    </script>
</head>
<body>
<div id="map">
    <div id="container"></div>
    <div id="search-city" >
        <input id="city_name" class="inputtext" placeholder="请输入城市的名称" type="text"/>
        <input id="query" class="button" value="到指定的城市" type="button"/>
    </div>
    <div id="infor">
        <b>地点经度</b> <input type="text" id="lngX" name="lngX" readonly="true" style="width:60px;"/>
        <b>地点纬度</b> <input type="text" id="latY" name="latY" readonly="true" style="width:60px;"/>
        <b>具体位置</b> <input type="text" id="address" name="address"  style="width:220px"/>
        <b></b> <input type="hidden" id="province" name="province" />
        <b></b> <input type="hidden" id="city" name="city" />
        <b></b> <input type="hidden" id="district" name="district" />
        &nbsp;<input type="button" value="提交" onclick="whbSubmit()"/>
    </div>
    </td>
</div>
<script type="text/javascript" src="<?=SYS_UI_PLUGINS?>jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="<?=SYS_UI_PLUGINS?>layer/2.4/layer.js"></script>
<script type="text/javascript" src="<?=SYS_UI_URL?>js/H-ui.js"></script>
<script type="text/javascript" src="<?=SYS_UI_URL?>js/H-ui.admin.js"></script>
<script type="text/javascript">
    var map,mark;
    //定义河马插件
    (function(){
        AMap.HeMa = {};
        /**
         * 设置地图中心点
         * @param map 地图
         * @param type 1：根据lnglat对象；2：城市名
         * @param value json对象{lnglat或city_name}
         */
        AMap.HeMa.setCenterPosition = function(map,type,value){
            if(type == 1){//根据经纬度重新定位
                map.setCenter(value.lnglat);
            }
            else{//根据城市名称重新定位
                map.setCity(value.city_name);
            }
        }
        /**
         * 添加标注
         * @param map
         * @param lnglat
         * @param icon
         */
        AMap.HeMa.addMark = function(e){
            if(mark == undefined){
                var _icon = new AMap.Icon({
                    image : 'http://webapi.amap.com/theme/v1.3/markers/b/mark_bs.png',
                    imageSize : new AMap.Size(19,39)//设置图标大小
                });
                mark = new AMap.Marker({
                    id: "marker",
                    icon: _icon,
                    position: e.lnglat,
                    offset: new AMap.Pixel(-10, -36)
                });
                mark.setMap(e.target);
            }
        }
        /**
         * 删除标注
         * @param mark
         */
        AMap.HeMa.deleteMark = function(){
            if(!mark) return;
            mark.setMap(null);
            mark = null;
        }
        /**
         * 单击地图事件
         * @param e
         * @param map
         * @param mark
         */
        AMap.HeMa.clickListener = function(e){
	        AMap.HeMa.deleteMark();//删除标注
            AMap.HeMa.setDistrictInfor(e);//设置地区信息
            AMap.HeMa.setCenterPosition(e.target,1,{lnglat: e.lnglat});//设置中心点
            
            AMap.HeMa.addMark(e);//添加标注
        }
        /**
         * 设置页面上地区信息 使用逆地理
         * @param e
         * @param map
         */
        AMap.HeMa.setDistrictInfor = function(e){
            var lnglatXY = [e.lnglat.getLng(), e.lnglat.getLat()];
            var geocoder = new AMap.Geocoder({
                radius: 1000,
                extensions: "all"
            });
            geocoder.getAddress(lnglatXY, function(status, data) {
                if (status === 'complete' && data.info === 'OK') {
                    AMap.HeMa.setFormDistrict(data);
                    AMap.HeMa.setFormLngLat(e.lnglat);
                }
            });
        }
        /**
         * 设置表单中的地区信息
         * @param data
         */
        AMap.HeMa.setFormDistrict = function(data){
            var province,cityname,district;
            cityname=data.regeocode.addressComponent.city;
            if(cityname == ""){
                //为直辖市时取 市、区、街道
                province = data.regeocode.addressComponent.province;
                cityname=data.regeocode.addressComponent.district;
                district = data.regeocode.addressComponent.district;
            }
            else{
                province = data.regeocode.addressComponent.province;
                district = data.regeocode.addressComponent.district;
            }

            if(document.getElementById("province"))document.getElementById("province").value=province;
            if(document.getElementById("city"))document.getElementById("city").value=cityname;
            if(document.getElementById("district"))document.getElementById("district").value=district;
            if(document.getElementById("address"))document.getElementById("address").value=data.regeocode.formattedAddress;
        }
        /**
         * 设置表单中的经纬度信息
         * @param e
         */
        AMap.HeMa.setFormLngLat = function(lnglat){
            if(document.getElementById("lngX"))document.getElementById("lngX").value = lnglat.getLng();
            if(document.getElementById("latY"))document.getElementById("latY").value = lnglat.getLat();;
        }
    })();
    //定义提交表单函数
    function whbSubmit() {
        //whbmemo：给Ext父窗口赋值
        if(parent.window.document.getElementById('lng')) parent.window.document.getElementById('lng').value = document.getElementById("lngX").value;
        if(parent.window.document.getElementById('lat')) parent.window.document.getElementById('lat').value = document.getElementById("latY").value;
        if(parent.window.document.getElementById('address')) parent.window.document.getElementById('address').value = document.getElementById("address").value;
        if(parent.window.document.getElementById('province')) parent.window.document.getElementById('province').value = document.getElementById("province").value;
        if(parent.window.document.getElementById('city')) parent.window.document.getElementById('city').value = document.getElementById("city").value;
        if(parent.window.document.getElementById('district')) parent.window.document.getElementById('district').value = document.getElementById("district").value;

        if (typeof(layer_close) != "undefined") {
            layer_close("");//layer中的关闭弹出窗口函数
        }
        else{
            alert("你尚未定义关闭函数");
        }
    }
</script>
</body>
</html>