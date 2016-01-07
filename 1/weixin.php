<?php
/**
  * wechat php test
  */
 require_once("src/JPush/JPush.php");
 
error_reporting(E_ALL^E_NOTICE);
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
if (isset($_GET['echostr'])) {
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
}

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }
     

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
				$type = $postObj->MsgType;
				$customevent = $postObj->Event;
				$latitude = $postObj->Location_X;//获取纬度
				$longitude = $postObj->Location_Y;//获取经度
				$label = $postObj->Label;//获取地理信息
				$voice = $postObj->Recognition;//为语音识别结果
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";   
				
				$newTpl = 	"<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[news]]></MsgType>
							<ArticleCount>1</ArticleCount>
							<Articles>
							<item>
							<Title><![CDATA[易见联]]></Title>
							<Description><![CDATA[访问易见联网站]]></Description>
							<PicUrl><![CDATA[http://yijianchelian.sinaapp.com/view.jpg]]></PicUrl>
							<Url><![CDATA[yijianchelian.sinaapp.com/about.html]]></Url>	
							</item>
							<FuncFlag>0</FuncFlag>
							</Articles>
							</xml>"; 				
	
				$linkTP=	"<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[event]]></MsgType>
							<Event><![CDATA[VIEW]]></Event>
							<EventKey><![CDATA[http://yijianchelian.sinaapp.com/about.html/]]></EventKey>
							</xml> ";
						
				
				$mmc=memcache_init();
				$ret =$mmc->connect();            //使用本应用Memcache
				
			
				// 连主库
				$link=mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);

				// 连从库
				// $link=mysql_connect(SAE_MYSQL_HOST_S.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);

				if(!$link)
				{
					die("Connect Server Failed: " . mysql_error());
					$contentStr = "连接错误";
					$msgType = "text";
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				}else{
					mysql_select_db(SAE_MYSQL_DB,$link);
					//your code goes here
					
					
					$mysql = new SaeMysql();

					//$sql = "SELECT * FROM `user` LIMIT 10";
					//$data = $mysql->getData( $sql );
					//$name = strip_tags( $_REQUEST['name'] );
					//$age = intval( $_REQUEST['age'] );
					//$sql = "INSERT  INTO `user` ( `name`, `age`, `regtime`) VALUES ('"  . $mysql->escape( $name ) . "' , '" . intval( $age ) . "' , NOW() ) ";
					$sql = "SELECT * FROM qq WHERE qq ='{$fromUsername}' ";
					$query=mysql_query( $sql );//执行sql语句
					$rs=mysql_fetch_array($query);
					$QQ=$rs['qq'];
					
					
					if($QQ==$fromUsername){		/*****************绑定设备功能*********************/
						//$contentStr="欢迎老朋友";
						//$msgType = "text";
						//$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
					switch($type)
					{
						case "voice":
							//$contentStr=$voice;
							$len=mb_strlen($voice,'utf-8');
							if($len>3){
								$navWord=mb_substr($voice, 0, 3,'utf-8');
								if($navWord=="导航到"){
									$len=mb_strlen($voice,'utf-8');
									$navWord=mb_substr($voice, 3,$len-3,'utf-8');
									$br = '<br/>';
									$app_key = 'ad2add0d7bafaab683ca3b16';
									$master_secret = '2009611fff8213ed1bd0c3a6';
									// 初始化
									$client = new JPush($app_key, $master_secret);
									//$label='999899';
									// 简单推送示例
									$result = $client->push()
										->setPlatform('all')
										->addAllAudience()
										->setNotificationAlert("$navWord")
										->send();

									echo 'Result=' . json_encode($result) . $br;
									$contentStr="位置已发送,如果长时间未收到,可能是网络原因,请重试!";
								}else{
									$contentStr="您的语音有误,请重新输入";
								}
							}else{
								$contentStr="您的语音有误,请重新输入";
							}
							
							$msgType = "text";
							$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr.$len);

						break;
						case "link":
							$contentStr="功能完善中";
							$msgType = "text";
							$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
						break;
						case "shortvideo":
							$contentStr="功能完善中";
							$msgType = "text";
							$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
						break;
						case "location":
							//百度地图 | 全景地图
						/*
							$geourl="http://api.map.baidu.com/telematics/v3/reverseGeocoding?location={$longitude},{$latitude}&coord_type=gcj02&ak=m9fCLsMnPDsla4lTuKGNsw6c";//反Geocoding接口
							$apistr=file_get_contents($geourl);//读取文件
							$apiobj=simplexml_load_string($apistr);//xml解析
							$addstr=$apiobj->results->result[0]->name;//逐级解析
													
							$newTpl = 	"<xml>
									<ToUserName><![CDATA[%s]]></ToUserName>
									<FromUserName><![CDATA[%s]]></FromUserName>
									<CreateTime>%s</CreateTime>
									<MsgType><![CDATA[news]]></MsgType>
									<ArticleCount>1</ArticleCount>
									<Articles>
									<item>
									<Title><![CDATA[我的位置]]></Title>
									<Description><![CDATA[全景(测试功能)]]></Description>
									<PicUrl><![CDATA[]]></PicUrl>
									<Url><![CDATA[%s]]></Url>
									</item>
									<FuncFlag>0</FuncFlag>
									</Articles>
									</xml>";  
							$url="http://api.map.baidu.com/pano/?x={$longitude}&y={$latitude}&lc=0&ak=m9fCLsMnPDsla4lTuKGNsw6c";//百度地图
							//$url="http://api.map.baidu.com/marker?location={$latitude},{$longitude}&title=我的位置&content={$addstr}&output=html";//全景
						*/
							
							//JPush 功能
						
								$br = '<br/>';
								$app_key = 'ad2add0d7bafaab683ca3b16';
								$master_secret = '2009611fff8213ed1bd0c3a6';
								
								// 初始化
								$client = new JPush($app_key, $master_secret);
								// 简单推送示例
								$result = $client->push()
									->setPlatform('all')
									->addAllAudience()
									->setNotificationAlert("$label")
									->send();

							echo 'Result=' . json_encode($result) . $br;
							
							$contentStr="位置已发送,如果长时间未收到,可能是网络原因,请重试!";
						//	$contentStr=$longitude."8888".$label;
							$msgType = "text";
							$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
							
							
						break;
						case "image":
							$contentStr = "你的图片很漂亮！";
							$msgType = "text";
							$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
						break;
						case "event":
							$contentStr = "event0\n";
							if($customevent=="subscribe"){
								$contentStr = "感谢你的关注".$postObj->EventKey;
								$msgType = "text";
								$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
							}else if($customevent=="CLICK"){
								//$contentStr = "event";
								switch($postObj->EventKey)
								{
									case "about":
										$resultStr = sprintf($newTpl, $fromUsername, $toUsername, $time );	
										break;
									case "mycarplace":
										$contentStr = "请发送你的位置";
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
										break;
									case "navigation":

										$contentStr = '请发送要导航的位置信息。或者发送"导航到**",例如:导航到深圳大学';
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
										break;
										
										break;
										
									case "person":
										$contentStr = '请发送被接人的位置信息。或者发送"导航到**",例如:导航到深圳大学';
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
										break;
										
										break;
									break;
									case "devicemanagement":

										$contentStr="功能完善中";
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
			
										break;
									case "menu_register":
										$contentStr="请输入你的设备号";
										memcache_set($mmc,$fromUsername."key","qq",$flag=0,$expire=60);//设置缓存值
										/*
										设置'var_key'对应值，使用即时压缩
										失效时间为50秒
										*/
									//$mmc->set($fromUsername."key","qq",MEMCACHE_COMPRESSED,60);
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);	
										break;
									case "":
										break;
									default:
										$contentStr="功能完善中";
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
										break;
								}
							}else if($customevent=="SCAN"){//扫描关注事件
								$sql = "SELECT * FROM qq WHERE user='{$postObj->EventKey}' ";//判断有无此设备
								$query=mysql_query( $sql );//执行sql语句
								if(!$query){
									die("insert into Sheet1: " . mysql_error());
								}
								$rs=mysql_fetch_array($query);
								$USER=$rs['user'];//设备号
							//	$dev=$rs['qq'];
							//	if(!$dev){
								if($USER==($postObj->EventKey)){	//有此设备
									$sql = "update qq set qq='{$fromUsername}' where user={$postObj->EventKey}";//绑定设备
									$query=mysql_query( $sql );//执行sql语句
									$contentStr = "设备绑定成功".$postObj->EventKey;
									if(!$query){
										die("update qq: " . mysql_error()); 
										$contentStr = "设备绑定失败,请重新绑定";
									}
									$msgType = "text";
									$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
									
								}else{
									$contentStr="绑定失败,请重新绑定";
									//$contentStr=$err;
									$msgType = "text";
									$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
									
								}
							}
							
						break;
						case "text":
							//$navWord = substr($keyword,0,3);
							$navWord=mb_substr($keyword, 0, 3,'utf-8');
							if($navWord=="导航到"){
								$len=mb_strlen($keyword,'utf-8');
								$navWord=mb_substr($keyword, 3,$len-3,'utf-8');
								$br = '<br/>';
								$app_key = 'ad2add0d7bafaab683ca3b16';
								$master_secret = '2009611fff8213ed1bd0c3a6';
								
								// 初始化
								$client = new JPush($app_key, $master_secret);
								//$label='999899';
								// 简单推送示例
								$result = $client->push()
									->setPlatform('all')
									->addAllAudience()
									->setNotificationAlert("$navWord")
									->send();

								echo 'Result=' . json_encode($result) . $br;
								$contentStr="位置已发送,如果长时间未收到,可能是网络原因,请重试!";
								$msgType = "text";
								$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
							}
							else if($keyword=="1"){
								$contentStr="功能完善中";
								$msgType = "text";
								$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
							}
							else if($keyword=="2"){
								$contentStr="功能完善中";
								$msgType = "text";
								$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
							}
							else if($keyword=="天气"){
								$ch = curl_init();
								$url = 'http://apis.baidu.com/apistore/weatherservice/cityname?cityname=深圳';//天气默认深圳
								$header = array(
									'apikey: 0952a45711c6afb6de570c5378f3ecfb',//百度apikey
								);
								// 添加apikey到header
								curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
								// 执行HTTP请求
								curl_setopt($ch , CURLOPT_URL , $url);
								$res = curl_exec($ch);

								//var_dump(json_decode($res));
								$transon=json_decode($res);
								$weatherObj=$transon->retData->weather;//天气
								$dateObj=$transon->retData->date;//天气
								$tempObj=$transon->retData->temp;//最低气温
								$tmp_l=$transon->retData->l_tmp;//最低气温
								$tmp_h=$transon->retData->h_tmp;//最高气温
								$windObj=$transon->retData->WS;//风力
								$sunsetObj=$transon->retData->sunset;//天气
								$contentStr="深圳  {$weatherObj}  {$dateObj} \n气温    {$tmp_l}~{$tmp_h}° \n{$windObj} \n日落时间  {$sunsetObj}\n ";
								
								$msgType = "text";
								$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
							}
							else if($keyword=="4"){
								$contentStr="功能完善中";
								$msgType = "text";
								$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
							}
							else if($keyword=="5"){

							}
							else if($keyword=="6"){
								$weatherurl="http://api.map.baidu.com/telematics/v3/weather?location={$keyword}&output=xml&ak=m9fCLsMnPDsla4lTuKGNsw6c";
								$weatherstr=file_get_contents($weatherurl);//读取文件
								$weatherapi=simplexml_load_string($weatherstr);//xml解析
								$placeobj=$weatherapi->currentCity;//逐级解析 城市
								$weather_data_obj=$weatherapi->weather_data;//逐级解析 实时天气
								$weatherobj=$weatherapi->weather;//逐级解析 天气
								$windobj=$weatherapi->wind;//逐级解析 天气
								$contentStr="{$placeobj} {$weather_data_obj} {$weatherobj} {$windobj}";
								$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);	
							}
							else if($keyword=="你好"){				
								header("Content-Type:text/html;charset=utf-8");
						
								// 连主库
								$link=mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);

								// 连从库
								// $link=mysql_connect(SAE_MYSQL_HOST_S.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);

								if(!$link)
								{
									die("Connect Server Failed: " . mysql_error());
									$contentStr = "连接错误";
									$msgType = "text";
									$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
								}else{
									mysql_select_db(SAE_MYSQL_DB,$link);
									//your code goes here
									
									
									$mysql = new SaeMysql();

									$sql = "SELECT * FROM Sheet1 WHERE BM ='{$fromUsername}' LIMIT 0 , 30  ";
									$query=mysql_query( $sql );//执行sql语句
									$rs=mysql_fetch_array($query);
									$BM=$rs['BM'];
									if($BM==$fromUsername){
										$contentStr="欢迎老朋友";
									}else{
										$contentStr="欢迎新朋友";
										$sql = "insert into Sheet1(BM) values('{$fromUsername}')";
										$query=mysql_query( $sql );//执行sql语句
										//die("insert into Sheet1: " . mysql_error());
									}
									$msgType = "text";
									$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
									$mysql->closeDb();	
								}
							}
							else{
									
								$struser= memcache_get($mmc,$fromUsername."key");//获取缓存值
								if($struser=="qq"){
								$sql = "SELECT * FROM qq WHERE user='{$keyword}' ";//判断有无此设备
								$query=mysql_query( $sql );//执行sql语句
								if(!$query){
									die("insert into Sheet1: " . mysql_error());
								}
								$rs=mysql_fetch_array($query);
								$USER=$rs['user'];
								
								$dev=$rs['qq'];
								//	if(!$dev){
										if($USER==$keyword){	//有此设备
											 $sql = "update qq set qq='{$fromUsername}' where user={$USER}";//绑定设备
											$query=mysql_query( $sql );//执行sql语句
											$contentStr = "设备绑定成功";
											if(!$query){
												die("update qq: " . mysql_error()); 
												$contentStr = "设备绑定失败,请重新注册";
											}
											$msgType = "text";
											$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
											memcache_delete($mmc,$fromUsername."key", 0);//删除缓存
										}else{
											$contentStr="绑定失败,请重新注册";
											$msgType = "text";
											$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
											memcache_delete($mmc,$fromUsername."key", 0);//删除缓存
										}
								//	}else{
									/*	$contentStr="此设备已绑定,请绑定其他设备";
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
										memcache_delete($mmc,$fromUsername."key", 0);//删除缓存
									*/
								//	}
								}else{				
								
									////打开为baidu fanyi
									$tranurl="http://openapi.baidu.com/public/2.0/bmt/translate?client_id=m9fCLsMnPDsla4lTuKGNsw6c&q={$keyword}&from=auto&to=auto";
									$transtr=file_get_contents($tranurl);//读入文件
									$transon=json_decode($transtr);//json解析
									$contentStr=$transon->trans_result[0]->dst;//读取翻译内容
									$msgType = "text";
									$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
									
								}
							}
								
						break;
						default:
							if(!empty( $keyword ))
							{
								//$msgType = "text";
							
								

								//$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
								//echo $resultStr;
							}else{
								echo "Input something...";
							};
					}//switch_end
									
				}else{	/*******************未注册设备******************/
					
					//$sql = "insert into Sheet1(BM) values('{$fromUsername}')";
					//$query=mysql_query( $sql );//执行sql语句
					//die("insert into Sheet1: " . mysql_error());
					if(!$ret){
						$contentStr = "memcache 错误 ";
						$msgType = "text";
						$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
					}else{
						
				
						switch($type){
							case "event":
								$contentStr = "event0\n";
								if($customevent=="subscribe"){
									$contentStr = "感谢你的关注\n请绑定设备".$postObj->EventKey;
									if($postObj->EventKey){//绑定设备
										
										$rest = substr($postObj->EventKey, 8, strlen($postObj->EventKey)-8);
										
										$sql = "SELECT * FROM qq WHERE user='{$rest}' ";//判断有无此设备
										$query=mysql_query( $sql );//执行sql语句
										if(!$query){
											die("insert into Sheet1: " . mysql_error());
										}
										$rs=mysql_fetch_array($query);
										$USER=$rs['user'];
										
									//	$dev=$rs['qq'];
										//if(!$dev){
											if($USER==$rest){	//有此设备
												 $sql = "update qq set qq='{$fromUsername}' where user={$USER}";//绑定设备
												$query=mysql_query( $sql );//执行sql语句
												$contentStr = "感谢你的关注\n设备绑定成功";
												if(!$query){
													die("update qq: " . mysql_error()); 
													$contentStr = "感谢你的关注\n设备绑定失败,请重新注册";
												}
												$msgType = "text";
												$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
												
											}else{
												$contentStr="感谢你的关注\n绑定失败,请重新注册";
												$msgType = "text";
												$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
												
											}
									/*	}else{
											$contentStr="感谢你的关注\n此设备已绑定,请绑定其他设备";
											$msgType = "text";
											$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
											
										}*/
										
										
									}
									$msgType = "text";
									$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
								}else if($customevent=="CLICK"){
									//$contentStr = "event";
									switch($postObj->EventKey)
									{
										case "menu_register":
											$contentStr="请输入你的设备号";
											memcache_set($mmc,$fromUsername."key","qq",$flag=0,$expire=60);//设置缓存值
											/*
											设置'var_key'对应值，使用即时压缩
											失效时间为50秒
											*/
										//$mmc->set($fromUsername."key","qq",MEMCACHE_COMPRESSED,60);
											$msgType = "text";
											$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);	
											break;
										default:
											$contentStr="欢迎新朋友\n请绑定设备";
										
											$msgType = "text";
											$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);	
											break;
									}
								}else if($customevent=="SCAN"){

										//$postObj->EventKey = substr($postObj->EventKey, 8, strlen($postObj->EventKey)-8);
										$sql = "SELECT * FROM qq WHERE user='{$postObj->EventKey}' ";//判断有无此设备
										$query=mysql_query( $sql );//执行sql语句
										if(!$query){
											die("insert into Sheet1: " . mysql_error());
										}
										$rs=mysql_fetch_array($query);
										$USER=$rs['user'];//设备号
									//	$dev=$rs['qq'];
									//	if(!$dev){
											if($USER==($postObj->EventKey)){	//有此设备
												$sql = "update qq set qq='{$fromUsername}' where user={$postObj->EventKey}";//绑定设备
												$query=mysql_query( $sql );//执行sql语句
												$contentStr = "设备绑定成功".$postObj->EventKey;
												if(!$query){
													die("update qq: " . mysql_error()); 
													$contentStr = "设备绑定失败,请重新绑定";
												}
												$msgType = "text";
												$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
												
											}else{
												$contentStr="绑定失败,请重新绑定";
												//$contentStr=$err;
												$msgType = "text";
												$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
												
											}
									/*	}else{
											$contentStr="此设备已绑定,请绑定其他设备";
											
											$msgType = "text";
											$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
											
										}*/

									}
	
								
							break;
							default :
							
								$struser= memcache_get($mmc,$fromUsername."key");//获取缓存值
								if($struser=="qq"){
									$sql = "SELECT * FROM qq WHERE user='{$keyword}' ";//判断有无此设备
									$query=mysql_query( $sql );//执行sql语句
									if(!$query){
										die("insert into Sheet1: " . mysql_error());
									}
									$rs=mysql_fetch_array($query);
									$USER=$rs['user'];
									
									$dev=$rs['qq'];
									//if(!$dev){
										if($USER==$keyword){	//有此设备
											 $sql = "update qq set qq='{$fromUsername}' where user={$USER}";//绑定设备
											$query=mysql_query( $sql );//执行sql语句
											$contentStr = "设备绑定成功";
											if(!$query){
												die("update qq: " . mysql_error()); 
												$contentStr = "设备绑定失败,请重新注册";
											}
											$msgType = "text";
											$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
											memcache_delete($mmc,$fromUsername."key", 0);//删除缓存
										}else{
											$contentStr="绑定失败,请重新注册";
											$msgType = "text";
											$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
											memcache_delete($mmc,$fromUsername."key", 0);//删除缓存
										}
								/*	}else{
										$contentStr="此设备已绑定,请绑定其他设备";
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
										memcache_delete($mmc,$fromUsername."key", 0);//删除缓存
									}
								*/	
									
								}else{
										$contentStr="test欢迎新朋友\n请绑定设备";
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);	
								}
							break;
						}
					}
				//	$msgType = "text";
					//$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				}
				$mysql->closeDb();	
			}
			
			echo $resultStr;		

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );//吧数组元素合并成字符串
		$tmpStr = sha1( $tmpStr );//sha1 加密
		
		if( $tmpStr == $signature ){//判断加密后是否与signature相等
			return true;
		}else{
			return false;
		}
	}
}

////////////
class ApipostAction{
 
    private $_appkeys = 'ad2add0d7bafaab683ca3b16';
    private $_masterSecret = '2009611fff8213ed1bd0c3a6 ';
 
    function request_post($url="",$param="",$header="") {
        if (empty($url) || empty($param)) {
        return false;
        }
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        // 增加 HTTP Header（头）里的字段 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        // 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch);//运行curl
     
        curl_close($ch);
        return $data;
    }
 
    function send($title,$message) 
    {
        $url = 'https://api.jpush.cn/v3/push';
        $base64=base64_encode("$this->_appkeys:$this->_masterSecret");
        $header=array("Authorization:Basic $base64","Content-Type:application/json");
        // print_r($header);
        $param='{"platform":"all","audience":"all","notification" : {"alert" : "Hi,JPush!"},"message":{"msg_content":"'.$message.'","title":"'.$title.'"}}';
        $res = $this->request_post($url,$param,$header);
        $res_arr = json_decode($res, true);
         print_r($res_arr);
    }
}
////////


?>