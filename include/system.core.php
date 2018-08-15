<?php
/*
| --------------------------------------------------------
| 	文件功能：系统通用核心函数定义文件（所有函数均以sys_开头）（表示system系统）
|	程序作者：王海滨（技术部）
|	时间版本：2015.3.18
|	特别提示：本核心函数文件作为FrameWork的底层承载部分，由王海滨专职维护，请勿更改
| --------------------------------------------------------
*/
//嵌入系统核心文件
require_once "config.inc.php";
require_once "language.inc.php";
require_once "classes/mail.class.php";
require_once "classes/ftp.class.php";
require_once "classes/mysql.class.php";//此处加载只为方便/plugins第三方插件使用
require_once "extend.inc.php";	//加载业务扩展函数定义文件(加载顺序必须在mysql.class.php之后)
// require_once(dirname(dirname(__FILE__)))."/plugins/BaiduPush/push.inc.php";//引用百度云推送插件
require_once(dirname(dirname(__FILE__)))."/plugins/GetuiPush/push.inc.php";//引用个推推送插件
require_once(dirname(dirname(__FILE__)))."/plugins/PHPExcel/PHPExcel.php";//引用phpexcel插件
require_once(dirname(dirname(__FILE__)))."/plugins/phpqrcode.php";//引用二维码生成插件
//百度推送测试可以直接用下面这句测试(sys_push和 sys_broadcast($msgContent,$tagName)在push.inc.php中定义)
//sys_push(NULL,1,"749489688113742196",'baidupush',0,0,1);//deviceid=749489688113742196

//重写$_GET,$_POST,$_REQUEST,$SESSION "读取" 机制，防止sql注入攻击，屏蔽Notice型报警
function _GET($parm){ 
	return isset($_GET[$parm]) ? sys_check_string($_GET[$parm]) : ""; //默认返回空串 
} 
function _POST($parm){ 
	return isset($_POST[$parm]) ? sys_check_string($_POST[$parm]) : ""; //默认返回空串 
} 
function _REQUEST($parm){ 
	return isset($_REQUEST[$parm]) ? sys_check_string($_REQUEST[$parm]) : ""; //默认返回空串 
} 
function _SESSION($parm){ 
	return isset($_SESSION[$parm]) ? sys_check_string($_SESSION[$parm]) : ""; //默认返回空串 
}

function sys_get_class_all_methods($class){
	$r = new reflectionclass($class);
	foreach($r->getmethods() as $key=>$methodobj){
		if ($methodobj->class!="BaseAction" && $methodobj->class!=$class) continue;
		if ($methodobj->name == "menu_root") continue;
		if ($methodobj->name == "push_add") continue;
		if($methodobj->isprivate()) {
			//$methods[$key]['type'] = 'private';
			continue;
		}
		elseif($methodobj->isprotected()) {
			//$methods[$key]['type'] = 'protected';
			continue;
		}
		else
			$methods[$key]['type'] = 'public';
		$methods[$key]['name'] = $methodobj->name;
		$methods[$key]['class'] = $methodobj->class;
	}
	return $methods;
}

//字符串安全过滤，防止sql注入
function sys_check_string($string)
{
	$result=trim(preg_replace('/习近平|TMD|法轮功|操逼|操你妈|草你妈|阴茎|阴屄|屌|FUCK|AV女优|激情|中华民国|共产党|王八蛋|王八羔子|傻逼|二逼|二货/','*',$string));//过滤敏感词汇

	$result = sys_strip_sql($result);
	$result = str_replace("'",'',$result);//屏蔽单引号，否则会影响JSON解析
	$result = str_replace('"','',$result);//屏蔽双引号，否则会影响JSON解析
	
	return $result;
}
//字符串安全过滤
function sys_strip_sql($string) {
	$search = array("/union/i","/0x([a-z0-9]{2,})/i","/select([[:space:]\*\/\-])/i","/update([[:space:]\*\/])/i","/replace([[:space:]\*\/])/i","/delete([[:space:]\*\/])/i","/drop([[:space:]\*\/])/i","/outfile([[:space:]\*\/])/i","/dumpfile([[:space:]\*\/])/i","/load_file\(/i","/substring\(/i","/substr\(/i","/concat\(/i","/concat_ws\(/i","/ascii\(/i","/hex\(/i","/ord\(/i","/char\(/i");
	$replace = array('unio&#110;','0&#120;\\1','selec&#116;\\1','updat&#101;\\1','replac&#101;\\1','delet&#101;\\1','dro&#112;\\1','outfil&#101;\\1','dumpfil&#101;\\1','load_fil&#101;(','substrin&#103;(','subst&#114;(','conca&#116;(','concat_w&#115;(','asci&#105;(','he&#120;(','or&#100;(','cha&#114;(');
	return is_array($string) ? array_map('strip_sql', $string) : preg_replace($search, $replace, $string);
}

//校正客户端传来的带英文逗号的id数字组合串为标准格式
function sys_get_idlist($id_list)
{	
	$start=substr($id_list,0,1);
	$end=substr($id_list,-1);
	if($start==",") $id_list=substr($id_list,1);
	if($end==",") $id_list=substr($id_list,0,-1);
	return $id_list;
}
//将带英文逗号的字母组合串格式化输出（sql中in 对字符串必须加引号）
function sys_get_strlist($parm_list)
{
	$str_list=sys_get_idlist($parm_list);
	$temp_array = explode(',', $str_list);
	foreach ($temp_array as $k => $v) {
		$temp_array[$k] = "'".$v."'";
	}
	$str_list = implode(',', $temp_array);
	return $str_list;
}

//sys_get_qrcode("http://www.baidu.com");
//生成二维码图片
function sys_get_qrcode($parmurl)
{	
	$save_path="uploadfiles/".date("Y")."/".date("m")."/".sys_get_no().".png";
	//第二个参数表示是图片存储地址，false则表示直接输出到前台
	QRcode::png($parmurl, SYS_ROOT_PATH.$save_path,  "L", "4");
		
	return SYS_ROOT.$save_path;
}

//生成大二维码图片
function sys_get_big_qrcode($parmurl)
{	
	$save_path="uploadfiles/".date("Y")."/".date("m")."/".sys_get_no().".png";
	//第二个参数表示是图片存储地址，false则表示直接输出到前台
	QRcode::png($parmurl, SYS_ROOT_PATH.$save_path,  "L", "8");
		
	return SYS_ROOT.$save_path;
}

//图片上传核心函数区域（兼容分布式存储架构）_________________________________begin

//常量定义
define("SYS_LIMIT_EXT","jpg,jpeg,gif,png,wav,mp3,amr,mp4,3gp");	//支持上传文件类型
define("SYS_IMAGE_EXT","jpg,jpeg,gif,png");				//支持上传图片类型

//循环检测并创建多级目录
function sys_mkdir($dir) 
{ 
	if (is_dir($dir) || @mkdir($dir,0777,true))//ture表示级联创建目录
	{
		chmod($dir,0777); //一定要单独提权，否则会有umask问题 
		return true; 
	} 
	if (!sys_mkdir(dirname($dir))) return false; 
}
/*
| --------------------------------------------------------
| 	函数功能：检查文件是否上传成功
|	形式参数：temp_file数组
|	返回结果：无
| --------------------------------------------------------
*/
function sys_check_upload($temp_file)
{
	/*
	0——没有错误发生，文件上传成功。  

    1——上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。  
    2——上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。  
    3——文件只有部分被上传。  
    4——没有文件被上传。
	 */
	switch($temp_file['error'] )
	{
		case 0:
			break;
		case 1:
			sys_out_fail(sys_get_msg(301),301);
			break;
		case 4:
			sys_out_fail(sys_get_msg(304),304);
			break;
		default:
			sys_out_fail(sys_get_msg(300),300);
	}	
	//判断文件大小
	if($temp_file["size"]>SYS_UPLOAD_MAX*1024000)
		sys_out_fail(sys_get_msg(301),301);	
}

//上传控制器
//实现文件上传功能
/*
| --------------------------------------------------------
|	形式参数：
|	(1)$file_type：1：图片 2：音频  3：视频
|	返回结果：上传文件保存的路径
| --------------------------------------------------------
*/
function sys_upload_file($file_type,$thumbWidth=120,$thumbHeight=120)
{
	$img_array = NULL;//设置初始值		
	@$temp_file=$_FILES['temp_file'];//与前台表单type="file"中的name对应	
	sys_check_upload($temp_file);//首先检测是否上传成功
	
	//判断文件格式是否合法_____________________________________________________begin
	//以下两句是为了取得上传文件的扩展名
	$temp_name =explode(".",$temp_file['name']);
	$ext_name =strtolower(end($temp_name));
	//当上传文件类型不符合规范时，需要报302错误
	if($file_type!=3 && stripos(SYS_LIMIT_EXT,$ext_name)===false) sys_out_fail(sys_get_msg(302),302);
	//当仅允许上传图片时却上传了其他类型，也需要报302错误
	if($file_type!=3 && $file_type==1 && stripos(SYS_IMAGE_EXT,$ext_name)===false)  sys_out_fail(sys_get_msg(302),302);
	//当不允许上传图片时却上传了图片，也需要报302错误
	if($file_type>1 && $file_type!=3 && stripos(SYS_IMAGE_EXT,$ext_name)!==false)
	{
		sys_out_fail(sys_get_msg(302),302);
		////调试专用，打印出错误类型	
		//$out_array['status'] = 0;//成功：1 失败 0			
		//$out_array['msg'] = sys_get_msg("302");
		//$out_array['error_code'] = $ext_name;		
		//sys_out_json($out_array);//返回	
	}
	if ($file_type==3 && stripos("xls,xlsx",$ext_name)===false) sys_out_fail(sys_get_msg(302),302);
	//判断文件格式是否合法_____________________________________________________end
	
	if(SYS_DFS)
	{
		return sys_upload_file_dfs($temp_file,$file_type,$ext_name,$thumbWidth,$thumbHeight);
	}
	else
	{
		return sys_upload_file_local($temp_file,$file_type,$ext_name,$thumbWidth,$thumbHeight);
	}
}

//上传本机
function sys_upload_file_local($temp_file,$file_type,$ext_name,$thumbWidth,$thumbHeight)
{	
	//首先按年月创建对应目录
	sys_mkdir(SYS_ROOT_PATH."uploadfiles/".date("Y")."/".date("m"));	
	//批量传递时，秒级别有可能覆盖，故再加个4位随机数,uploadfiles必须手动在项目根目录创建，且开放IIS来宾账户写权限	
	$save_file=SYS_ROOT_PATH."uploadfiles/".date("Y")."/".date("m")."/".sys_get_no().".".$ext_name;//重新命名，防止不同用户上传同名文件
	//PHP实际上是把你上传的文件从临时目录移动到指定目录,这句最关键,copy比 move_uploaded_file 通用性更强			
	move_uploaded_file($temp_file['tmp_name'],$save_file);//
	
	//如果是图片类型，则生成缩略图
	if($file_type==1){
		$img_array = NULL;
		$img_array[0] = sys_make_thumb($save_file,640,640,0); //大图
		$img_array[1] = sys_make_thumb($save_file,$thumbWidth,$thumbHeight); //小图
	}	
	else	$img_array[0]=SYS_ROOT.substr($save_file,mb_strlen(SYS_ROOT_PATH));//去除头部的"../../"
	
	return $img_array;//返回文件保存的路径	
}

function sys_upload_file_local2($temp_file,$file_type,$ext_name,$thumbWidth,$thumbHeight)
{
	//首先按年月创建对应目录
	sys_mkdir(SYS_ROOT_PATH."uploadfiles/".date("Y")."/".date("m"));
	//批量传递时，秒级别有可能覆盖，故再加个4位随机数,uploadfiles必须手动在项目根目录创建，且开放IIS来宾账户写权限
	$save_file=SYS_ROOT_PATH."uploadfiles/".date("Y")."/".date("m")."/".sys_get_no().".".$ext_name;//重新命名，防止不同用户上传同名文件
	//PHP实际上是把你上传的文件从临时目录移动到指定目录,这句最关键,copy比 move_uploaded_file 通用性更强
	move_uploaded_file($temp_file['tmp_name'],$save_file);//

	//如果是图片类型，则生成缩略图
	if($file_type==1){
		$img_array = NULL;
		$img_array_tmp = NULL;
		$img_array_tmp = sys_make_thumb2($save_file,640,640,0);
		$img_array[0] = $img_array_tmp[1];
		$img_array[2] = $img_array_tmp[2];
		$img_array[3] = $img_array_tmp[3];
		$img_array_tmp = NULL;
		$img_array_tmp = sys_make_thumb2($save_file,$thumbWidth,$thumbHeight); //小图
		$img_array[1] = $img_array_tmp[1];
	}
	else	$img_array[0]=SYS_ROOT.substr($save_file,mb_strlen(SYS_ROOT_PATH));//去除头部的"../../"

	return $img_array;//返回文件保存的路径
}
//上传DFS
function sys_upload_file_dfs($temp_file,$file_type,$ext_name,$thumbWidth,$thumbHeight)
{
	//whb:采用DFS架构时，各项目的临时上传文件均统一保存到远端服务器根目录htdocs\uploadfiles文件夹下
	//(rsync同步软件已经排除此文件夹的同步，以防并发冲突)
	//首先创建对应目录
	sys_mkdir($_SERVER['DOCUMENT_ROOT']."/uploadfiles");
	//秒级别有可能覆盖，故通过sys_create_code再加个4位随机数	
	$save_file=$_SERVER['DOCUMENT_ROOT']."/uploadfiles/".sys_get_no().".".$ext_name;
	//PHP实际上是把你上传的文件从临时目录移动到指定目录,这句最关键,copy比 move_uploaded_file 通用性更强			
	move_uploaded_file($temp_file['tmp_name'],$save_file);				

	//如果是图片类型，则生成缩略图
	if($file_type==1)
	{
		$img_array[0]=sys_make_thumb($save_file,640,640,0); //大图
		$img_array[1]=sys_make_thumb($save_file,$thumbWidth,$thumbHeight);//小图
		//将图片从WEB上传到DFS服务器
		$img_array[0]=sys_upload_file_dfs_core($img_array[0]);//上传原图
		$img_array[1]=sys_upload_file_dfs_core($img_array[1]);//上传缩略图
	}	
	else $img_array[0]=sys_upload_file_dfs_core($save_file);
	
	return $img_array;//返回文件保存的路径	
}
//上传DFS核心函数（形参为本地文件名，返回DFS存储文件名）
function sys_upload_file_dfs_core($local_filename)
{	
	$ftp = new WhbFtp(DFS_LOCAL_IP,DFS_FTP_PORT,DFS_FTP_USER,DFS_FTP_PWD);	//打开FTP连接 
	$dfs_name=date("Y")."/".date("m")."/".basename($local_filename);//按年月区分文件夹
	$result=$ftp->upload_file($local_filename,$dfs_name,true); 		//上传文件(true表示没有目录时自动创建)
	if($result) 
	{
		//删除web上的临时文件
		@unlink($local_filename);
		$file_save_url="http://".DFS_NET_IP."/".$dfs_name;//(注意此处默认了分布式服务器的WEB端口是80)	
	}
	else
	{
		$file_save_url='';
	}
	$ftp->close();
	return $file_save_url;//返回文件保存的路径	
}

/*
* 函数功能：生成缩略图（等比缩放,以[source]_thumb.png形式保存在原图目录下）
* $source_img   源图片地址 
* $dest_width   缩放后图片最大宽度 
* $dest_height  缩放后图片最大高度
* 返回：img_array
* 其中：img_array[0]=原始图片名，img_array[1]=缩略图名，img_array[2]=缩略图宽度，img_array[3]=缩略图高度
*/ 
function sys_make_thumb($source_img,$dest_width,$dest_height,$smallflag=1){
	if(!file_exists($source_img))
	{
		return NULL;
	}	
	list($source_width,$source_height,$source_type)=getimagesize($source_img);//取得原图的尺寸,类型
	
	//修正目标图片的宽、高，防止变大模糊
	if($source_width<$dest_width) $dest_width=(int)$source_width;
	if($source_height<$dest_height) $dest_height=(int)$source_height;
	
	//等比缩放核心算法
	if ($dest_width && ($source_width < $source_height)) {
    	$dest_width = (int)(($dest_height / $source_height) * $source_width);
	}
	else
	{
		$dest_height = (int)(($dest_width / $source_width) * $source_height);
	}

    switch($source_type) {
        case 1:
            $source_temp=imagecreatefromgif($source_img);//创建原图资源
            $createfunc = 'imagegif';
            $fileext = 'gif';
            break;
        case 3:
            $source_temp=imagecreatefrompng($source_img);//创建原图资源
            $createfunc = 'imagepng';
            $fileext = 'png';
            break;
        case 2://jpeg
        default:
            $source_temp=imagecreatefromjpeg($source_img);//创建原图资源
            $createfunc = 'imagejpeg';
            $fileext = 'jpg';
            break;
    }
    $dest_temp=imagecreatetruecolor($dest_width,$dest_height);//创建新图片资源
    //保持GIF或PNG图片的透明背景色____________________________________begin
    if ( ($source_type == 1) || ($source_type == 3) ) {
        $trnprt_indx = imagecolortransparent($source_temp);
        // If we have a specific transparent color
        if ($trnprt_indx >= 0) {
            // Get the original image's transparent color's RGB values
            $trnprt_color    = imagecolorsforindex($source_temp, $trnprt_indx);
            // Allocate the same color in the new image resource
            $trnprt_indx    = imagecolorallocate($dest_temp, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
            // Completely fill the background of the new image with allocated color.
            imagefill($dest_type, 0, 0, $trnprt_indx);
            // Set the background color for new image to transparent
            imagecolortransparent($dest_temp, $trnprt_indx);
        }
        // Always make a transparent background color for PNGs that don't have one allocated already
        elseif ($source_type == 3) {
            // Turn off transparency blending (temporarily)
            imagealphablending($dest_temp, false);
            // Create a new transparent color for image
            $color = imagecolorallocatealpha($dest_temp, 0, 0, 0, 127);

			// Completely fill the background of the new image with allocated color.
			imagefill($dest_temp, 0, 0, $color);

			// Restore transparency blending
			imagesavealpha($dest_temp, true);
		}
	}	
	//保持GIF或PNG图片的透明背景色____________________________________end	
	imagecopyresampled($dest_temp,$source_temp,0,0,0,0,$dest_width,$dest_height,$source_width,$source_height);//等比缩放图片
	//缩放结果图片统一命名为[source]_thumb.png,并保存到source相同目录
	//$source_name=basename($source_img);//无路径的文件名
	//处理生成压缩后的大图和小图
	if($smallflag)
	{
        $dest_img=substr($source_img,0,strrpos($source_img,"."))."_thumb.".$fileext;//可以更改为形式参数，由外部传入，此处内部定义
	}else
	{
		$dest_img = $source_img;
	}
    $createfunc($dest_temp, $dest_img);//统一生成PNG格式
	//imagejpeg($dest_temp, $dest_img, 70);//统一生成JPG(不支持图片透明，故废弃)
	imagedestroy($dest_temp); //销毁资源
	imagedestroy($source_temp);  
	
	//组装结果数组
	//如果是DFS架构则直接赋值
	if(SYS_DFS)
	{
		$img_array[]=$source_img;	
		$img_array[]=$dest_img;
	}
	else//否则需要去除前置的"../../"
	{
		$img_array[]=SYS_ROOT.substr($source_img,mb_strlen(SYS_ROOT_PATH));	
		$img_array[]=SYS_ROOT.substr($dest_img,mb_strlen(SYS_ROOT_PATH));				
	}	
	////如果想组装缩略图的宽度和高度，则采用如下2句：
	////$img_array[]=$dest_width;
	////$img_array[]=$dest_height;
	return $img_array[1];
}

function sys_make_thumb2($source_img,$dest_width,$dest_height,$smallflag=1){
	if(!file_exists($source_img))
	{
		return NULL;
	}
	list($source_width,$source_height,$source_type)=getimagesize($source_img);//取得原图的尺寸,类型

	//修正目标图片的宽、高，防止变大模糊
	if($source_width<$dest_width) $dest_width=(int)$source_width;
	if($source_height<$dest_height) $dest_height=(int)$source_height;

	//等比缩放核心算法
	if ($dest_width && ($source_width < $source_height)) {
		$dest_width = (int)(($dest_height / $source_height) * $source_width);
	}
	else
	{
		$dest_height = (int)(($dest_width / $source_width) * $source_height);
	}

	switch($source_type) {
		case 1:
			$source_temp=imagecreatefromgif($source_img);//创建原图资源
			$createfunc = 'imagegif';
			$fileext = 'gif';
			break;
		case 3:
			$source_temp=imagecreatefrompng($source_img);//创建原图资源
			$createfunc = 'imagepng';
			$fileext = 'png';
			break;
		case 2://jpeg
		default:
			$source_temp=imagecreatefromjpeg($source_img);//创建原图资源
			$createfunc = 'imagejpeg';
			$fileext = 'jpg';
			break;
	}
	$dest_temp=imagecreatetruecolor($dest_width,$dest_height);//创建新图片资源
	//保持GIF或PNG图片的透明背景色____________________________________begin
	if ( ($source_type == 1) || ($source_type == 3) ) {
		$trnprt_indx = imagecolortransparent($source_temp);
		// If we have a specific transparent color
		if ($trnprt_indx >= 0) {
			// Get the original image's transparent color's RGB values
			$trnprt_color    = imagecolorsforindex($source_temp, $trnprt_indx);
			// Allocate the same color in the new image resource
			$trnprt_indx    = imagecolorallocate($dest_temp, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
			// Completely fill the background of the new image with allocated color.
			imagefill($dest_type, 0, 0, $trnprt_indx);
			// Set the background color for new image to transparent
			imagecolortransparent($dest_temp, $trnprt_indx);
		}
		// Always make a transparent background color for PNGs that don't have one allocated already
		elseif ($source_type == 3) {
			// Turn off transparency blending (temporarily)
			imagealphablending($dest_temp, false);
			// Create a new transparent color for image
			$color = imagecolorallocatealpha($dest_temp, 0, 0, 0, 127);

			// Completely fill the background of the new image with allocated color.
			imagefill($dest_temp, 0, 0, $color);

			// Restore transparency blending
			imagesavealpha($dest_temp, true);
		}
	}
	//保持GIF或PNG图片的透明背景色____________________________________end
	imagecopyresampled($dest_temp,$source_temp,0,0,0,0,$dest_width,$dest_height,$source_width,$source_height);//等比缩放图片
	//缩放结果图片统一命名为[source]_thumb.png,并保存到source相同目录
	//$source_name=basename($source_img);//无路径的文件名
	//处理生成压缩后的大图和小图
	if($smallflag)
	{
		$dest_img=substr($source_img,0,strrpos($source_img,"."))."_thumb.".$fileext;//可以更改为形式参数，由外部传入，此处内部定义
	}else
	{
		$dest_img = $source_img;
	}
	$createfunc($dest_temp, $dest_img);//统一生成PNG格式，暂时屏蔽
	//imagejpeg($dest_temp, $dest_img, 70);//统一生成JPG(不支持图片透明，故废弃)
	imagedestroy($dest_temp); //销毁资源
	imagedestroy($source_temp);

	//组装结果数组
	//如果是DFS架构则直接赋值
	if(SYS_DFS)
	{
		$img_array[]=$source_img;
		$img_array[]=$dest_img;
	}
	else//否则需要去除前置的"../../"
	{
		$img_array[]=SYS_ROOT.substr($source_img,mb_strlen(SYS_ROOT_PATH));
		$img_array[]=SYS_ROOT.substr($dest_img,mb_strlen(SYS_ROOT_PATH));
	}
	////如果想组装缩略图的宽度和高度，则采用如下2句：
	$img_array[]=$dest_width;
	$img_array[]=$dest_height;
	return $img_array;
}

//图片上传核心函数区域（兼容分布式存储架构）_________________________________begin


//判断操作系统类型
function sys_isLinuxOS()
{
	return (strrpos(strtolower(PHP_OS),"win") === false);
}

//获取系统提示语 language.inc.php
function sys_get_msg($parm){
	global $msg;	
	return $msg[$parm];
}
//得到当前登录用户主键id,此处直接返回$_SESSION['cid']，将来可方便直接扩展为更安全的加密，解密模式
function sys_get_cid()
{	
	return _SESSION('client_id');
}

//获取系统唯一串号
function sys_get_no()
{
	//时间戳再加4位随机数，共18位
	return date("YmdHis").sys_create_code();
}
//获取系统交易订单号（统一支付宝，银联交易单号）
function sys_get_payno()
{
	//格式形如："14位时间戳+"ID"+client_id(非固定长度)"（举例：GM20140917172349ID2）；
	return date("YmdHis")."ID".sys_get_cid();
}
//获取4位定长数字随机串
function sys_create_code()
{	
	return rand(1000,9999);
}
//向客户端发送JSON串
function sys_out_json($parm_array)
{	
	//防止PHP自带json_encode函数把中文转成unicode(必须是PHP5.4以上版本)
	//die(json_encode($parm_array,JSON_UNESCAPED_UNICODE));
	if (version_compare(PHP_VERSION,'5.4.0','<'))
        echo json_encode($parm_array);
    else
        echo json_encode($parm_array, JSON_UNESCAPED_UNICODE);
	die();//非常重要，请勿删除
}

//向客户端输出错误信息(500表示是服务器端异常错误，需要重试)
function sys_out_fail($parmMsg=NULL,$errorNumber=500)
{		
	unset($result_array);
	$result_array['success'] = false;//注意：为了和extjs兼容，此处必须不带引号
	
	if(empty($parmMsg)) $parmMsg="操作失败！";
	else $parmMsg=	$parmMsg;
	
	$result_array['msg'] = $parmMsg;	
	$result_array['error_code'] = $errorNumber;
	
	sys_out_json($result_array);
}
//向客户端输出成功信息
function sys_out_success($parmMsg=NULL,$infor_array=NULL)
{	
	unset($result_array);
	$result_array['success'] = true;//注意：为了和extjs兼容，此处必须不带引号	
	
	if(empty($parmMsg)) $parmMsg="操作成功！";	
	
	$result_array['msg'] = $parmMsg;	
	$result_array['infor'] = $infor_array;//固定输出infor字段，以适配各种复杂情况
	sys_out_json($result_array);
}
//封装一下简单（默认）输出(result一般是数据库操作或函数返回的布尔值)
function sys_out_result($result)
{		
	if($result !== false)  
		sys_out_success();
	else
		sys_out_fail();
}
//封装一下简单输出(404专用于处理客户端id传错时的情况)
function sys_out_404()
{		
	sys_out_fail(sys_get_msg(404),404);	
}

//字符串长度检查
function sys_check_length($str,$minLen,$maxLen)
{
	if(empty($str) || strlen($str)<$minLen || strlen($str)>$maxLen)
		sys_out_fail('POST字符串长度不合法',101);
}

//重写判空函数（把0排除）
function sys_check_empty($parm)
{
	if(!isset($parm) || strlen(trim($parm))==0)
		return true;
	return false;		
}
//检测多个post参数是否完整并且不为空值
function sys_check_post($post_array) {
	foreach ($post_array as $parm) {
		if (!isset($_POST[$parm]) || sys_check_empty($_POST[$parm])) {		
			sys_out_fail($parm." 参数不能为空",100);
		}
	}	
}
//检测单个post参数是否完整（前台不再生成数组以便节省开销）
function sys_check_post_single($parm)
{
	if (!isset($_POST[$parm]) || sys_check_empty($_POST[$parm])) {		
		sys_out_fail($parm." 参数不能为空",100);
	}	
}

//防止枚举类型非法提交
function sys_check_keytype($parmType,$maxValue)
{
	if(!is_numeric($parmType) || $parmType>$maxValue ||$parmType<0) 
		sys_out_fail('keytype取值范围不正确',101);
}

//后台管理员登录有效性检测，为提高调试效率暂时屏蔽
function sys_check_admin()
{
	////$_SESSION['admin_id']="";//可以利用此句模拟SESSION超时
	if(!_SESSION('admin_id')) {
		header("Location: ".SYS_ROOT."index.php?g=webadmin");
		sys_out_fail("登录超时，请重新登录！",999);	//系统规定999是session失效的错误编码
	}
}
//检查登录令牌是否有效(此处直接为检查$_SESSION['cid'],将来可方便直接扩展为检查$_POST['token']模式）
function sys_check_token() //形如：TK_150261_848
{	
    if(!_POST('token'))	sys_out_fail(sys_get_msg(100),100);
	if(_SESSION('token')!=_POST('token')) 
		sys_out_fail(sys_get_msg(200),200);	
}
function sys_check_temp_token()
{
	if(_SESSION('temp_token')!=_POST('temp_token'))  
		sys_out_fail(sys_get_msg(200),200);
	else unset($_SESSION['temp_token']);
}

//退出登录
function sys_login_out()
{
	session_unset(); //释放内存
	session_destroy(); //删除临时文件
}

//根据client_id来生成并返回token
function sys_get_token($client_id,$name='')
{	
	$myToken = "TK_".sys_create_code()."_".$client_id;//命名规则：TK_6位随机数_用户主键ID
	$_SESSION['client_id']=$client_id;
	$_SESSION['name']=$name;	
	$_SESSION['token']=$myToken;
	return $myToken;
}
function sys_get_temp_token($parmStr)
{	
	$myToken= "TK_".sys_create_code()."_".$parmStr;//命名规则：TK_6位随机数_用户登录名(此登录名重设密码时需要用到，不可或缺)
	$_SESSION['temp_token']=$myToken;
	return $myToken;
}

//=============================================
//=============================================
//=============================================

/**
 *根据中心点经纬度，来计算以radius千米为距离半径，所画圆圈的正切正方形的四个点坐标
 *
 *param lng float 经度
 *param lat float 纬度
 *param radius float 该点所在圆的半径，默认值为10 千米
 *return array 正方形的四个点的经纬度坐标
 */
function sys_get_squarePoint($lng, $lat,$radius = 10){
 
    $dlng =  2 * asin(sin($radius /12742) / cos(deg2rad($lat)));//12742为地球直径
    $dlng = rad2deg($dlng);
	     
    $dlat = $radius/6371; //6371为地球平均半径
    $dlat = rad2deg($dlat);
     
    return array(
                'left-top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),
                'right-top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),
                'left-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),
                'right-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)
                );
}

//判断手机号是济南本地还是外地
function sys_check_local($mobile)
{
	$checkresult=1;//默认本地
	if(empty($mobile)) return $checkresult;
		
	$url = 'http://webservice.webxml.com.cn/WebServices/MobileCodeWS.asmx/getMobileCodeInfo';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "mobileCode={$mobile}&userId=");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($ch);	
	curl_close($ch);
	$data = simplexml_load_string($data);
	//echo $data;//调试
	$tempArray=explode(' ',$data);
	
	if(count($tempArray)>2 && $tempArray[1]!="济南")
		$checkresult=0;
		
	return $checkresult;	
}

//////////////////============= 手机&邮件发送 =============////////////////////

//发送验证码
function sys_send_code($username)
{	
	if(SYS_DEBUG_MODE)
	{
		$_SESSION['verify_code']='1234';   //测试阶段，固定使用1234
	}
	else
	{
		if(sys_check_empty(_SESSION('verify_code')))
		{
			$_SESSION['verify_code']=sys_create_code();	 //确保同一用户重发同一验证码
		}		
		$content="【".SYS_ZH_NAME."】验证码："._SESSION('verify_code');//编辑发送内容
		
		if(sys_check_email($username))//如果是邮箱
			sys_send_mail($username,$content,$content);//如果是邮箱验证，则需要发送电子邮件
		else
			ext_send_sms($username,$content);//如果是手机验证，则需要发送手机短信				
	}	
}
//验证随机码是否正确
function sys_check_code($parmCode)
{
	if(SYS_DEBUG_MODE)
	{
		if($parmCode == "1234")
			return true;//测试阶段固定传1234
	}
	else
	{
		if(_SESSION('verify_code') && $parmCode ==_SESSION('verify_code')) 
			return true;//正式部署
	}	
	sys_out_fail(sys_get_msg(103),103);		
}

//发电子邮件
function sys_send_mail($to,$subject,$message)
{
	$whbmail=new WhbMail();
	$whbmail->send($to,SYS_ZH_NAME."客服中心",$subject,$message);
	unset($whbmail);
} 

//////////////////=================== 系统公共函数 =====================////////////////////

//获取并格式化当前时间
function sys_get_time()
{	
	return date("Y-m-d H:i:s",time());
}
//得到时间间隔(传入之前确保enddate>=startdate,否则为负数)
//$return_type：返回的类型,详见switch内部注释
function sys_get_timespan($startdate,$enddate,$return_type)
{
	//echo(strtotime(date('Y-m-d')) - strtotime($mydate)."<br>");
	$result = 0;
	switch ($return_type)
	{
		case "i"://如果是分
			$result =floor((strtotime($enddate) - strtotime($startdate))/60);
		 	break;
		case "h"://如果是时
			$result =floor((strtotime($enddate) - strtotime($startdate))/3600);
		 	break;
		case "d"://如果是天
			$result =floor((strtotime($enddate) - strtotime($startdate))/3600/24);
		 	break;
		case "w"://如果是周
			$result =floor((strtotime($enddate) - strtotime($startdate))/3600/24/7);
		 	break;
		case "m"://如果是月
			$result =floor((strtotime($enddate) - strtotime($startdate))/3600/24/30);
		 	break;		
		default:
			break;
	}		
	return $result;
}

//根据经纬度计算距离（单位：公里）
function sys_get_distance($lat1, $lng1, $lat2, $lng2)  
{  
   //$s = round(((2 * asin(sqrt(pow(sin((($lat1 * 3.1415926535898 / 180.0) - ($lat2 * 3.1415926535898 / 180.0))/2),2) + cos($lat1 * 3.1415926535898 / 180.0)*cos( $lat2 * 3.1415926535898 / 180.0)*pow(sin((($lng1 * 3.1415926535898 / 180.0) - ($lng2 * 3.1415926535898 / 180.0))/2),2)))) * 6378.137) * 10000) / 10000;
   $s=round(12756274*asin(Sqrt(power(sin((lat1-lat2)*0.008726646),2)+Cos(lat1*0.0174533)*Cos(lat2*0.0174533)*power(sin((lng1-lng2)*0.008726646),2)))/1000,2) ;  
   return $s;  
} 

//根据出生日期计算年龄
function sys_get_age($parmBirthday){
    $birth=$parmBirthday;
    @list($by,$bm,$bd)=explode('-',$birth);
    $cm=date('n');
    $cd=date('j');
    $age=date('Y')-$by-1;
    if ($cm>$bm || $cm==$bm && $cd>$bd) $age++;
    return $age;
}

//函数功能：将string中的html代码，全部替换成普通文本
function sys_parse_html_single($string)    
{
	$string = strip_tags($string);
	//$string = preg_replace ('/\n/is', '', $string);//保留换行符
	//$string = preg_replace ('/ |　/is', '', $string);//保留原始空格
	$string = preg_replace ('/&nbsp;/is', '', $string);
	$string = preg_replace ('/&amp;/is', '&', $string);
	$string = preg_replace ('/&lt;/is', '<', $string);
	$string = preg_replace ('/&gt;/is', '>', $string);	
	return $string;
}

//函数功能：将temp_array数组中的key1字段中的html代码，全部替换成普通文本
function sys_parse_html($temp_array,$key1)
{
	$result_array = NULL;
	if (is_array($temp_array))
	{		
		foreach($temp_array as $rows)
		{	
			$rows[$key1]=sys_parse_html_single($rows[$key1]);		
			$result_array[]=$rows;		
		}	
	}		
	//print_r($result_array);	
	return($result_array);
}

//无限级联树
//返回结果：一个字符串型
function sys_out_treelist($table_name,$id)
{	
	$sql_helper=new Mysql();	
	//此处采用自封装JSON格式
	$temp_json = tmp_out_treelist($table_name,$id,$sql_helper);		
	$reslut_json="{\"status\":1,\"msg\":\"".sys_get_msg('get_succ')."\",\"infor\":".$temp_json;
	echo $reslut_json;
	sys_close_db($sql_helper);//关闭连接，释放资源
	die();	
}

//中间函数:sys_get_treelist所用到的
function tmp_out_treelist($table_name,$id,$sql_helper)
{		
	$sqlstr=" select id,name,parentid,orderby,iconurl  from $table_name where parentid=$id order by orderby,id ";
	//$sqlstr=" select *  from $table_name where parentid=$id order by orderby,id ";
	$result = $sql_helper->get_list_bysql($sqlstr);

	$num_rows=count($result);
 	
	//$one_record ="\"children\":[";
	$one_record ="[";
	
	if($num_rows>0)
	{			
		for($i=0;$i<$num_rows;$i++) //行循环
		{
			$one_record=$one_record."{";							
			$rs=$result[$i];
			
			$temp_json=json_encode($rs);	
			$temp_json=ltrim($temp_json,'{');	
			$temp_json=rtrim($temp_json,'}');	
			
			$one_record=$one_record.$temp_json.",";			
			
			//判断是否为叶子节点
            $temp_id = $rs['id']; 			
			$sqlstr=" select count(id) from $table_name where parentid=$temp_id order by orderby  ";
			$temp_num_rows = $sql_helper->get_one_bysql($sqlstr);	
	
			//如果不是叶子,就先打印父类子项，再递归children
			if ($temp_num_rows>0)
			{				 
				//添加不是叶子节点标示,并通过expanded属性设置全部打开模式
				////如果想传递0打开全部___________________________________________begin
				//$one_record=$one_record."\"leaf\":\"0\",";                   
				////递归children					
				//$one_record=$one_record.tmp_out_treelist($table_name,$temp_id,$sql_helper).",";
				////如果想传递0打开全部___________________________________________end
				$one_record=$one_record."\"leaf\":\"0\"},";    
				
			}
			else //如果是,就直接打印		
			{
				//添加是叶子节点标示
				$one_record=$one_record."\"leaf\":\"1\"},";                    
			}		
											
		}		
		//去掉最后一个逗号
		$one_record=substr($one_record, 0, -1); 			
	}//if结束
	
	$one_record=$one_record."]}";	//封装尾部 		
    return $one_record;
}
//////////////////=================== 系统检查 =====================////////////////////

//检测手机号是否合法
function sys_check_mobile($mobile)
{
	$check_result = true;
	//检测机制
	if(strlen($mobile) != 11)
	{
		$check_result = false;
	}
	if(!$check_result) sys_out_fail('手机号码格式错误',101);
}
//判断邮箱是否合法（正则表达式）
function sys_check_email($email)
{
	$pregEmail = "/([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?/i";
    $check_result=preg_match($pregEmail,$email); 
	if($check_result) return true;
	return false;	
}

//二维关联数组转换成字符串
function sys_array_string($array)  
{  
    // 定义存储所有字符串的数组  
    static $r_arr = array();        
    if (is_array($array)) {  
        foreach ($array as $key => $value) {  
            if (is_array($value)) {  
                // 递归遍历  
                sys_array_string($value);  
            } else {  
                $r_arr[] = $value;  
            }  
        }  
    } else if (is_string($array)) {  
            $r_arr[] = $array;  
    }            
    //数组去重  
    $r_arr = array_unique($r_arr);  
    $string = implode(";", $r_arr);        
    return $string;  
}

//关闭数据库连接，释放内存资源
function sys_close_db($sql_helper)
{	
	//手动关闭连接和释放资源
	if($sql_helper)
	{
		$sql_helper->close();	
		unset($sql_helper);	
	}
}

//通过curl方式获取远程网址内容（file_get_contents(url)效率不如curl高）
function sys_get_curl($url)   
{   
    $ch = curl_init();   
    curl_setopt($ch, CURLOPT_URL, $url);            //设置访问的url地址   
    //curl_setopt($ch,CURLOPT_HEADER,1);            //是否显示头部信息   
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);           //设置超时      
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);      //跟踪301   
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果   
    $result = curl_exec($ch);   
    curl_close($ch);   
    return $result;   
}

////根据汉字获取拼音
function sys_get_pinyin($str, $charset="utf-8") {
		$restr = '';
		$str = trim($str);
		if ("utf-8" == $charset) {
			$str = iconv("utf-8", "gbk", $str);
		}		
		$slen =strlen($str);
		$pinyins = array();
		$fp = fopen(SYS_ROOT_PATH.'plugins/pinyin.dat', 'r');
		while (!feof($fp)) {
			$line = trim(fgets($fp));
			$pinyins[$line[0] . $line[1]] = substr($line, 3, strlen($line)-3);
		}
		fclose($fp);
		for ($i=0; $i<$slen; $i++) {
			if (ord($str[$i]) > 0x80) {
				$c = $str[$i] . $str[$i+1];
				$i++;
				if (isset($pinyins[$c])) {
					$restr .= $pinyins[$c];
				}else {
					$restr .= "z";
				}
			}elseif (preg_match("/[a-z0-9]/i", $str[$i])) {
				$restr .= $str[$i];
			}else {
				$restr .= "z";
			}
		}
		return $restr;
}
//输出信息到系统日志文件（极端复杂情况下非常实用，请勿删除）
//请注意服务器是否开通fopen配置
function  sys_log($content) { 
	if(SYS_DEBUG_MODE)
	{
		$fp = fopen(SYS_ROOT_PATH."sys_debug.log","a+");//
		flock($fp, LOCK_EX) ;
		fwrite($fp,"[".date("Y-m-d H:i:s",time())."]".$content."\n");
		flock($fp, LOCK_UN);
		fclose($fp);
	}	
}
?>