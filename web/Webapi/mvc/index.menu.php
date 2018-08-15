<?php
function dg($apis,$method_l,$prefile)
{
    static $i;
    static $j;//控制展开
    $i++ ;
    $str = '';

    $str .= '{' ;
    $str .= 'id:"menu_'.$i.'",';
    
    if(isset($apis['newflag']) && !empty($apis['newflag'])){
        $str .= 'text:"<span style=\'color:red;\'>'.$apis['title'].'</span>",';
    }
    else {
        $str .= 'text:"'.$apis['title'].'",';
    }
    
    
    $str .= 'leaf:'.(isset($apis['child']) && count($apis['child'])>0 ? 'false' : 'true,');

    if(!isset($apis['child']) || count($apis['child'])==0)
    {
        $str .= 'hrefTarget:SYS_API_ROOT+"phpfiles/'.(($apis['index']===0 || $apis['index'] === '0')?  $method_l :'index' ).'.php?m='.$method_l.'&v='.$prefile.'"';
        return $str.'},' ;
    }

    $j++ ;
    $str .= ',expanded: '.($j%2 == 0 ? 'false' : 'false').',';//默认都关闭
    $str .= 'children:[';

    foreach($apis['child'] as $method_l =>$api_l )
    {
        $str .= dg($api_l,$method_l,$prefile);
    }

    $str .= "]},";
    return $str;
}

error_reporting(E_ALL & ~E_NOTICE);
$version = $_GET['v'];
$prefile = 'V'.str_replace('.', '', $version);

$path = dirname(dirname(dirname(__FILE__)));

include_once $path.'/Webapi/version/'.$prefile.'.api.menu.php';


$str = "[";
foreach ($api_menus as $method_l=>$api_l)
{
    $str .= dg($api_l,$method_l,$prefile);
}

$str .= "]";

die($str);
