<?php
header("content-type:text/html;charset=utf-8");
include("conn.php");
use sinacloud\sae\Storage as Storage;
$s = new Storage();

if(!empty($_POST['uName'])){
	$uName=$_POST['uName'];			//设备号
	echo $uName;
	$sql0="select * from uploadpicture where devID='{$uName}' ";
	$query0=mysql_query($sql0);
	$rs0=mysql_fetch_array($query0);
	$ret_time=$rs0['time'];
	$ret_devID=$rs0['devID'];
	$ret_name=$rs0['picName'];
	if($ret_devID===$uName){
		$uImg =$_FILES['uImg'];
		//print_r($uImg);

		//判断上传类型
		$ext=explode(".",$uImg["name"]);
		$extName=end($ext);
		if($extName!="jpg"){
			echo "文件类型错误<a href='uploadtostor.php'>返回</a>";
			exit;
		}

		//检测文件大小
		 if($uImg["size"]>2000000){
			echo "文件超过2M<a href='uploadtostor.php'>返回</a>";
			exit();
		} 

		$dir="uploadpicture/";

		
		$date=date("Y-m-d H:i:s");
		$datetime=explode(" ",$date);
		$firstname=$datetime['0']."T".$datetime['1'];
		echo "datetime".$datetime;
		$imgaeName=$firstname.rand(100,999);
		$fileName=$imgaeName.".".$extName;
		echo "upload pic name: ".$fileName;
		$uploadUrl=$dir.$fileName;

		// 把$_FILES全局变量中的缓存文件上传到test这个Bucket，设置此Object名为sae/1.txt
		$s->putObjectFile($_FILES['uImg']['tmp_name'], "yijianlian001", $uploadUrl);
		
		$picName=$ret_name.";".$imgaeName;
		echo " picName:".$picName;
		$sql = "update uploadpicture set time='{$imgaeName}', picName='{$picName}' where devID='{$uName}'";//绑定设备
		$query=mysql_query( $sql );//执行sql语句
		if(!$query){
			die("update uploadpicture: " . mysql_error()); 
		}else{

			echo " <p>上传成功</p>";
			echo "<a href='pic.php'>查看远程图片</a>";
		}
	}else{
		echo "设备号有误,请重新输入<a href='uploadtostor.php'>重输</a>";
	}
	
}else{
	echo "设备号不能为空!     <a href='uploadtostor.php'>返回</a>";
}

?>