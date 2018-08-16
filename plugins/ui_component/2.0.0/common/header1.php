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
    <link rel="stylesheet" type="text/css" href="<?php echo SYS_UI_URL;?>css/H-ui.admin.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo SYS_UI_PLUGINS;?>Hui-iconfont/1.0.7/iconfont.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo SYS_UI_PLUGINS;?>icheck/icheck.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo SYS_UI_PLUGINS;?>laypage/skin/laypage.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo SYS_UI_URL;?>skin/default/skin.css" id="skin" />
    <link rel="stylesheet" type="text/css" href="<?php echo SYS_UI_URL;?>css/style.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo SYS_UI_PLUGINS;?>lightbox2/2.8.1/css/lightbox.css" >
    <link rel="stylesheet" type="text/css" href="<?php echo SYS_UI_PLUGINS;?>jquery.dptk.combobind/combobind.css" >
    <!--[if IE 6]>
    <script type="text/javascript" src="<?php echo SYS_UI_PLUGINS?>DD_belatedPNG_0.0.8a-min.js" ></script>
    <script>DD_belatedPNG.fix('*');</script>
    <![endif]-->
    <title><?php echo SYS_ZH_NAME?>_商家管理系统</title>
</head>
<body>
<script type="text/javascript" src="<?php echo SYS_UI_PLUGINS?>jquery/1.9.1/jquery.min.js"></script>
<script>
    var upload_file_url = "<?=U('Index/upload_file_2')?>";
</script>
<script>
    //空字符返回默认值
    function com_value_get(value,def) {
        def = def ? def : '';
        return value ? value : def;
    }
</script>
