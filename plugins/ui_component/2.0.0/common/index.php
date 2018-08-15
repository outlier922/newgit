<?php
$user = $component['user'];
$menu_list = $component['menu_list'];
?>
<header class="navbar-wrapper">
    <div class="navbar navbar-fixed-top">
        <div class="container-fluid cl">
            <a class="logo navbar-logo f-l mr-10 hidden-xs" href=""><?=SYS_ZH_NAME;?>_平台管理系统</a>
            <nav id="Hui-userbar" class="nav navbar-nav navbar-userbar hidden-xs">
                <ul class="cl">
<!--                    <li>-->
<!--                        <a href="--><?//=U('Webshop/Index/index')?><!--">前往分店后台<span style="color:red;">&gt;&gt;</span></a>-->
<!--                    </li>-->
                    <li class="ml-10">您好,</li>
                    <li class="dropDown dropDown_hover"> <a href="#" class="dropDown_A"><?=$user['username']?> <i class="Hui-iconfont">&#xe6d5;</i></a>
                        <ul class="dropDown-menu menu radius box-shadow">
	                        <?php
	                        	if($user['type'] == 'shop'){	                        	
	                        	}else{
	                        ?>
                            <li><a href="javascript:;" onclick="iframe_show('个人信息','<?=U('Manage/admin_get?id='.UID)?>',600,430)">个人信息</a></li>
                            <?php		                        	
	                        	}
	                        ?>
                            <li><a href="javascript:;" onclick="iframe_show('修改密码','<?=U('Index/password_save')?>',500,320)">修改密码</a></li>
                            <li><a href="<?=U('Login/logout')?>">退出</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>
<aside class="Hui-aside" style="/*background-color: #333;*/">
    <input runat="server" id="divScrollValue" type="hidden" value="" />
    <div class="menu_dropdown bk_2">
        <?php foreach($menu_list as $menu_i){?>
            <dl id="menu-client">
                <dt><i class="Hui-iconfont"><?=$menu_i['icon']?></i><?=$menu_i['name']?><i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
                <dd>
                    <ul>
                        <?php foreach($menu_i['children'] as $menu_i_i){?>
                            <li>
                                <a _href="<?=U($menu_i_i['c'].'/'.$menu_i_i['a']);?>" href="javascript:;" class="f-12" data-title="<?=$menu_i_i['name'];?>"><?=$menu_i_i['name'];?></a>
                            </li>
                        <?php }?>
                    </ul>
                </dd>
            </dl>
        <?php }?>
    </div>
</aside>

<div class="dislpayArrow"><a class="pngfix" href="javascript:void(0);" onClick="displaynavbar(this)"></a></div>

<section class="Hui-article-box">
    <div id="Hui-tabNav" class="Hui-tabNav">
        <div class="Hui-tabNav-wp">
            <ul id="min_title_list" class="acrossTab cl">
                <li class="active"><span title="我的桌面" data-href="<?=U('Index/welcome')?>">我的桌面</span><em></em></li>
            </ul>
        </div>
        <div class="Hui-tabNav-more btn-group"><a id="js-tabNav-prev" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d4;</i></a><a id="js-tabNav-next" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d7;</i></a></div>
    </div>
    <div id="iframe_box" class="Hui-article">
        <div class="show_iframe">
            <div style="display:none" class="loading"></div>
            <iframe scrolling="yes" frameborder="0" src="<?=U('Index/welcome')?>"></iframe>
        </div>
    </div>
</section>