<?php
$access_token = "_LDBAv1BbZPkWZdq2Uhku5rIJslcWXBLuFBsor9EjZhzNDBP_GlYgPYIsv5NQtkOMNc5b27qlX-FPXiHUQ1n0gcgBMP2Ud4p_CjNqOOL4JcXBWiAGAOSN";
//永久
$qrcode = '{
	"action_name": "QR_LIMIT_SCENE",
	"action_info":{
		"scene":{
			"scene_id":1000
		}
	}
}';
$url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$access_token";
$result = https_post($url,$qrcode);
$jsoninfo = json_decode($result,true);
$ticket = $jsoninfo["ticket"];

function https_post($url,$data = null){
	$curl = curl_init();// 创建一个新cURL资源
	// 设置URL和相应的选项
	$curl_setopt($curl,CURLOPT_URL,$url);
	$curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
	$curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,FALSE);
	if(!empty($data)){
		curl_setopt($curl,CURLOPT_POST,1);
		curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
	}
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
	$output = curl_exec($curl);// 抓取URL并把它传递给浏览器
	curl_close($curl);// 关闭cURL资源，并且释放系统资源
	return $output;
}

?>
	
