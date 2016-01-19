<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width"/>
<meta name="viewport" content="inital-scale=1.0,user-scalable=no"/>
<meta name="apple-mobile-web-app-capavle" content="yes"/>
<meta name="apple-mobile-web-app-status-bar-style" content="black"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>易见联</title>
</head>


<?php 
include("conn.php");

if(!empty($_GET['close'])){
	$get_devID=$_GET['close'];
	echo "正在为你关机.....";
}	
	
?>