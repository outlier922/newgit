<?php
$data = $component['data'];
foreach ($data as $data_item){
?>
    <div class="mr-10 mt-20 f-l" style="background-color: #6ce26c;width:490px;">
        <table class="table table-border table-bordered">
            <tr>
                <td colspan="10" class="text-c c-red f-16"><?=$data_item['title']?>课程表</td>
            </tr>
            <tr>
                <th></th>
                <th class="text-c">一</th>
                <th class="text-c">二</th>
                <th class="text-c">三</th>
                <th class="text-c">四</th>
                <th class="text-c">五</th>
                <th class="text-c">六</th>
                <th class="text-c">日</th>
            </tr>
            <?php for($i=1;$i<=8;$i++){?>
                <tr style="height: 40px;">
                    <td class="text-c"><?=$i?></td>
                    <?php for($j=1;$j<=7;$j++){?>
                        <td class="text-c w50">
                            <a href="javascript:;" _href="<?=U('Syllabus/syllabus_cell_list?class_id='.$data_item['id'].'&row='.$i.'&column='.$j)?>"
                               class="dptk-js-url-get" data-title="设置课程表" data-target="frame"
                            >去设置</a>
                        </td>
                    <?php }?>
                </tr>
            <?php }?>
        </table>
    </div>


<?php }?>
<div class="cl"></div>
