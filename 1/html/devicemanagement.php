<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width"/>
<meta name="viewport" content="inital-scale=1.0,user-scalable=no"/>
<meta name="apple-mobile-web-app-capavle" content="yes"/>
<meta name="apple-mobile-web-app-status-bar-style" content="black"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>设备管理</title>
</head>


<?php 
include("conn.php");

if(!empty($_GET['qq'])){
	$get_devID=$_GET['qq'];
	$sql0="select * from devConnect where qq='{$get_devID}' ";
	
	$query0=mysql_query($sql0);
	while($rs0=mysql_fetch_array($query0)){
		
		$ret_devID=$rs0['devID'];
		$sql="select * from deviceInfo where devID='{$ret_devID}' ";
		$query=mysql_query($sql);
		while($rs=mysql_fetch_array($query)){
			

?>
<p>设备号:<?php echo $rs['devID']?>|<?php echo $rs['devStatus']?>|<?php echo $rs['devDate']?>
	<a href="deviceclose.php?close=<?php echo $rs['devID']?>">关机|</a>
	<a href="devicemanagement.php?qq=<?php echo $get_devID?>">更新</a>
</p>
<p>地址:<?php echo $rs['devAddree']?></p><hr>
<?php
		}
	}
}else{
	echo "没有这台设备";
}
?>

</html>