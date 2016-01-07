<?php

$appid = "wxcb21a9587975fca4";
$appsecret = "2e71bad8d65e6538b87e7241345af4c5 ";
$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";

$output = https_request($url);
$jsoninfo = json_decode($output, true);

$access_token = $jsoninfo["TEBlfHvNq8CAer0VFZPejpqmIKUcNoULZrr4FB6q2xyEnrvpXAbgGCme4hNkwNXngjiteCgMFHf35ZVBfhhp3eiNmEqMfF5GoSFlHkVU53sWRLfACACXR"];


$jsonmenu = '{
      "button":[
      {
            "name":"My Car",
           "sub_button":[
            {
               "type":"click",
               "name":"位置查询",
               "key":"mycarplace"
            },
            {
               "type":"click",
               "name":"轨迹回放",
               "key":"mycarblack"
            },
            {
               "type":"click",
               "name":"远程视频",
               "key":"mycarview"
            },
            {
               "type":"view",
               "name":"远程图片",
               "url":"http://v.qq.com/"
            }]

       },
       {
           "name":"导航服务",
           "sub_button":[
            {
               "type":"click",
               "name":"接人",
               "key":"person"
            },
            {
               "type":"click",
               "name":"导航",
               "key":"navigation"
            }]
       

       },
	   {
           "name":"更多",
           "sub_button":[
            {
               "type":"click",
               "name":"绑定设备",
               "key":"menu_register"
            },
           {
               "type":"click",
               "name":"设备管理",
               "key":"devicemanagement"
            },
            {

			   "type":"view",
               "name":"违章查询",
               "url":"http://m.weizhang8.cn"
            },
			{
               "type":"click",
               "name":"关于",
               "key":"about"
            }]
       }
       ]
 }';


$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
$result = https_request($url, $jsonmenu);
var_dump($result);

function https_request($url,$data = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

?>