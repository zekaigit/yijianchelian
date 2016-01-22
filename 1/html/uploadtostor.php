<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width"/>
<meta name="viewport" content="inital-scale=1.0,user-scalable=no"/>
<meta name="apple-mobile-web-app-capavle" content="yes"/>
<meta name="apple-mobile-web-app-status-bar-style" content="black"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>上传远程图片</title>
</head>


<body>

	<form action="up.php" method="post" enctype="multipart/form-data">
	设备号:<input type="text" name="uName"><br/>
	选择上传到服务器图片:<input type="file" name="uImg"><br/>
	<input type="submit" value="提交"><br/>
	</form>
	
</body>

</html>