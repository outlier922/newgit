<?php
$sysInfo = $component['sys_info'];
$userInfo = $component['user_info'];
?>
<div class="pd-20" style="padding-top:20px;">
    <p class="f-20 text-success">欢迎使用</p>
    <p>登录次数：<?=$userInfo['login']?> </p>
    <p>上次登录IP：<?=$userInfo['lastloginip']?>   上次登录时间：<?=$userInfo['lastlogintime']?></p>
    
    <table class="table table-border table-bordered table-bg mt-20">
        <thead>
        <tr>
            <th colspan="2" scope="col">服务器信息</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($sysInfo as $key => $value){?>
            <tr>
                <td><?=$key?></td>
                <td><?=$value?></td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<footer class="footer">
    <p>Copyright &copy; <?=SYS_COMPANY?> All Rights Reserved.<br>
</footer>