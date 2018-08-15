<?php
/**
 * API左侧菜单配制说明
 *
 * title：菜单名称
 * child：子菜单
 * newflag：为1时表示，菜单加红显示
 * index：为0时表示，指向mvc/phpfiles目录下的自定义文件
 *
 */

function get_class_all_methods($class){
    $r = new reflectionclass($class);
    foreach($r->getmethods() as $key=>$methodobj){
        if ($methodobj->class=="BaseAction" || $methodobj->class==$class) {
            if ($methodobj->name == "menu_root") continue;
            if ($methodobj->name == "push_add") continue;
            if ($methodobj->isprivate()) {
                //$methods[$key]['type'] = 'private';
                continue;
            } elseif ($methodobj->isprotected()) {
                //$methods[$key]['type'] = 'protected';
                continue;
            } else
                $methods[$key]['type'] = 'public';
            $methods[$key]['name'] = $methodobj->name;
            $methods[$key]['class'] = $methodobj->class;
        }
    }
    return $methods;
}

$path = dirname(dirname(dirname(__FILE__)));
require_once $path."/Webservice/Action/BaseAction.class.php";
$api_menus=BaseAction::menu_root();
$vv=$_GET['v'];
$version=str_replace(".","",$vv);
$className='V'. $version . 'Action';
$path = dirname(dirname(dirname(__FILE__)));
require_once $path."/Webservice/Action/$className.class.php";

$methods = get_class_all_methods($className);
//var_dump(json_encode($methods));
//die();
foreach ($methods as $m)
{
    $method_name=$m['name'];
    $rMethod = new ReflectionMethod($className, $method_name);
    $docComment = $rMethod->getDocComment();
    $docCommentArr = explode("\n", $docComment);
    $parent="";
    $description="";
    foreach ($docCommentArr as $comment) {
        $comment = trim($comment);
        if (strpos($comment, '@') === false && strpos($comment, '/') === false) {
            $description = substr($comment, strpos($comment, '*') + 1);
        }

        $pos = stripos($comment, '@parent');
        if ($pos !== false)
            $parent = substr($comment, $pos + 8);
    }
    $child_array=NULL;
    $child_array['title']=$description;
    if ($method_name=="webview"||$method_name=="app_tui"||$method_name=="plugins_share"||$method_name=="pushtype")
        $child_array['index']=0;
    $parent_root=explode(',',$parent);
    if (count($parent_root) == 1)
    {
        if ($parent !== "" && !empty($api_menus[$parent])) {
            $api_menus[$parent]['child'][$method_name] = $child_array;
        }
    }
    else if (count($parent_root) == 2)
    {
        $parent_root_one=$parent_root[0];
        $parent_root_two=$parent_root[1];
        if ($parent_root_one!=="" && $parent_root_two!=="" && !empty($api_menus[$parent_root_one]['child'][$parent_root_two]))
        {
            $api_menus[$parent_root_one]['child'][$parent_root_two]['child'][$method_name] = $child_array;
        }
    }
    else
    {
        echo "暂时只支持三级菜单！";
    }
}



