<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width"/>
<meta name="viewport" content="inital-scale=1.0,user-scalable=no"/>
<meta name="apple-mobile-web-app-capavle" content="yes"/>
<meta name="apple-mobile-web-app-status-bar-style" content="black"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>远程图片</title>
</head>


<?php 
include("conn.php");
use sinacloud\sae\Storage as Storage;
$s = new Storage();
$text_list=$s->listBuckets();//获取bucket信息


$devID="oF-YDuCa_GxDWDvfPjZTF579mVrY";
$sql0="select * from uploadpicture where devID='{$devID}' ";
	$query0=mysql_query($sql0);
	$rs0=mysql_fetch_array($query0);
	$ret_time=$rs0['time'];
	$ret_devID=$rs0['devID'];
	$ret_name=$rs0['picName'];
	$ext=explode(";",$ret_name);
	$filename=end($ext);
	echo $filename;//找到最新图片name

	//获取一个Object的外网临时访问URL
$contentStr=$s->getTempUrl("yijianlian001", "uploadpicture/{$filename}.jpg", "GET", 600);
?>
<body>

<p><img src="<?php echo $contentStr?>" ></p>
</body>
</html>