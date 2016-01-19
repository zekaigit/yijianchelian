<?php 
include("conn.php");
if(!empty($_POST['sub'])){
	$title=$_POST['title'];
	$content=$_POST['con'];
	echo $content;
	echo $title;
	/*
	$sql=" INSERT INTO `app_yijianchelian`.`deviceInfo` (`devID` ,`devAddree` ,`devStatus` ,`devDate`)
			VALUES ('$title', '$content', '在线', '2016-01-13')";
	*/
	$sql="select * from deviceInfo where devStatus='在线'";
	mysql_query($sql);
	while($rs=mysql_fetch_array($query)){
	}
}		
?>
<form action ="devicemanagement.php" method="post">
标题<input type="text" name = "title"><br>
内容<textarea rows="5" cols="50" name="con"></textarea><br>
<input type="submit" name = "sub" value="发表">
</form>