<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <!--<LINK rel="Bookmark" href="/favicon.ico" >
    <LINK rel="Shortcut Icon" href="/favicon.ico" />-->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo SYS_UI_PLUGINS;?>html5.js"></script>
    <script type="text/javascript" src="<?php echo SYS_UI_PLUGINS;?>respond.min.js"></script>
    <script type="text/javascript" src="<?php echo SYS_UI_PLUGINS;?>PIE_IE678.js"></script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="<?php echo SYS_UI_URL;?>css/H-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo SYS_UI_URL;?>css/H-ui.login.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo SYS_UI_PLUGINS;?>Hui-iconfont/1.0.7/iconfont.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo SYS_UI_PLUGINS;?>icheck/icheck.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo SYS_UI_PLUGINS;?>laypage/skin/laypage.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo SYS_UI_URL;?>skin/default/skin.css" id="skin" />
    <link rel="stylesheet" type="text/css" href="<?php echo SYS_UI_URL;?>css/style.css" />
    <!--[if IE 6]>
    <script type="text/javascript" src="<?php echo SYS_UI_PLUGINS?>DD_belatedPNG_0.0.8a-min.js" ></script>
    <script>DD_belatedPNG.fix('*');</script>
    <![endif]-->
    <title><?=SYS_ZH_NAME?>_平台管理系统</title>
</head>
<body>
    <div class="header"><?=SYS_ZH_NAME?>_平台管理系统</div>
    <div class="loginWraper">
    <div id="loginform" class="loginBox">
        <form class="form form-horizontal" action="<?=U('Login/index')?>" method="post" id="form-layer">
            <div class="row cl">
                <label class="form-label col-xs-3"><i class="Hui-iconfont">&#xe60d;</i></label>
                <div class="formControls col-xs-8">
                    <input id="" name="username" type="text" placeholder="账户" value="<?=SYS_DEBUG_MODE ? 'admin' : '';?>" class="input-text size-L" required>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-3"><i class="Hui-iconfont">&#xe60e;</i></label>
                <div class="formControls col-xs-8">
                    <input id="" name="password" type="password" placeholder="密码" class="input-text size-L" value="<?=SYS_DEBUG_MODE ? '123456' : '';?>" required>
                </div>
            </div>
            <div class="row cl">
                <div class="formControls col-8 col-offset-3">
                    <input  name="verify" class="input-text size-L" type="text" placeholder="验证码" style="width:150px;" required>
                    <img class="verifyimg reloadverify" src="<?=U('Login/verify')?>"> <a class="reloadverify" href="javascript:;">看不清，换一张</a> </div>
            </div>
            <div class="row cl">
                <div class="formControls col-xs-8 col-xs-offset-3">
                    <input name="" type="submit" class="btn btn-success radius size-L" value="&nbsp;登&nbsp;&nbsp;&nbsp;&nbsp;录&nbsp;">
                    <input name="" type="reset" class="btn btn-default radius size-L" value="&nbsp;取&nbsp;&nbsp;&nbsp;&nbsp;消&nbsp;">
                </div>
            </div>
        </form>
    </div>
    </div>
    <div class="footer">Copyright &copy; <?php echo SYS_COMPANY?> All Rights Reserved.</div>

</body>
</html>
<script src="<?=SYS_UI_PLUGINS?>jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo SYS_UI_PLUGINS?>layer/2.2/layer.js"></script>
<script src="<?php echo SYS_UI_PLUGINS?>jquery.validation/1.14.0/jquery.validate.min.js"></script>
<script src="<?php echo SYS_UI_PLUGINS?>jquery.validation/1.14.0/validate-methods.js"></script>
<script src="<?php echo SYS_UI_PLUGINS?>jquery.validation/1.14.0/messages_zh.min.js"></script>
<script src="<?php echo SYS_UI_PLUGINS?>jquery/plugins/jquery.form.min.js"></script>

<script>
    $(function () {
        //刷新验证码
        var verifyimg = $(".verifyimg").attr("src");
        $(".reloadverify").click(function(){
            if( verifyimg.indexOf('?')>0){
                $(".verifyimg").attr("src", verifyimg+'&random='+Math.random());
            }else{
                $(".verifyimg").attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());
            }
        });

        //提交表单
        var form_layer = $("#form-layer");
        $(form_layer).validate({
            onkeyup:false,
            focusCleanup:false,
            success:"valid",
            submitHandler:function(form){
                var wait_index = layer.load(0);
                $(form_layer).ajaxSubmit({
                    success: function(rec) {
                        layer.close(wait_index);//关闭等待框
                        rec = $.parseJSON(rec);
                        var infor = rec.infor;
                        if(rec.success){
                            window.location.href = rec.infor.url;
                        }
                        else{
                            layer.alert("登录失败，原因："+rec.msg,{icon:5});
                            //刷新验证码
                            $(".reloadverify").click();
                        }
                    }
                });
            },
            rules:{
                loginname:{
                    required:true
                }
                ,
                loginpwd:{
                    required:true
                }
            },
            messages:{
                loginname:{
                    required:'用户名是必填字段'
                },
                loginpwd:{
                    required:'密码是必填字段'
                }
            }
        });
    });
</script>
