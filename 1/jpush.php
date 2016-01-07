<?php



$output = https_request($url);
$jsoninfo = json_decode($output, true);

$access_token = $jsoninfo["TAyfoPYxUq9J_8pgXsVV1xMz3pcaeSSwon8QHF9pWYkgSBmK1rAfqwsHEik_nhSvRV4Ak0uTWxVOH9pisIeHPBlDVCqKVIQBersD7F5f-m8DMVgACAYHQ"];
$jsonmenu = '';
curl --insecure -X POST -v https://api.jpush.cn/v3/push -H "Content-Type: application/json" -u "ad2add0d7bafaab683ca3b16:2009611fff8213ed1bd0c3a6 " -d '{"platform":"all","audience":"all","notification":{"alert":"Hi,JPush !","android":{"extras":{"android-key1":"android-value1"}},"ios":{"sound":"sound.caf","badge":"+1","extras":{"ios-key1":"ios-value1"}}}}'


$url = "https://api.jpush.cn/v3/push";
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