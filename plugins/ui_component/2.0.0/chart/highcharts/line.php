<?php
$data = $component['data'];
$class = $component['cls'];
$name = $component['name'];
$title = $component['title'];
$categories = $component['categories'];;
$series = $component['series'];;
?>
<div id="<?=$name?>" class="<?=$class?>"></div>

<script type="text/javascript" src="<?=SYS_UI_PLUGINS?>Highcharts/4.2.5/highcharts.js"></script>
<script type="text/javascript" src="<?=SYS_UI_PLUGINS?>Highcharts/4.2.5/modules/exporting.js"></script>
<script>
    $(function () {
        $('#<?=$name?>').highcharts({
            chart: {
                type: 'line'
            },
            title: {
                text: '<?=$title?>'
            },
            xAxis: {
                categories: <?=$categories?>
            },
            yAxis: {
                title: ''
            },
            tooltip: {
                enabled: true,
                formatter: function() {
                    return this.x +': '+ this.y +'';
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                }
            },
            legend:{
                enabled: false
            },
            credits: {
                enabled:false
            },
            series: [{
                data: <?=$series?>
            }]
        });
    });
</script>