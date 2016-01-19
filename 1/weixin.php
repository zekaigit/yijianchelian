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
	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
	if (!empty($postStr)){
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
					
		$link=mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
		if(!$link){
			die("Connect Server Failed: " . mysql_error());
			$contentStr = "连接错误";
			$msgType = "text";
			$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
		}else{
			mysql_select_db(SAE_MYSQL_DB,$link);
			//your code goes here
			$mysql = new SaeMysql();
			$sql = "SELECT * FROM devConnect WHERE devID ='{$fromUsername}' ";
			$query=mysql_query( $sql );//执行sql语句
			$rs=mysql_fetch_array($query);
			$QQ=$rs['devID'];
			if($QQ==$fromUsername){				
				$wechatObj->xingchejiluyi();
			}else{
				$wechatObj->responseMsg();
			}
			$mysql->closeDb();	
		}
	}else {
		echo "";
		exit;
    }
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
     
	public function xingchejiluyi()
	{
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
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
						
			$link=mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
			if(!$link){
				die("Connect Server Failed: " . mysql_error());
				$contentStr = "连接错误";
				$msgType = "text";
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
			}else{
				
				mysql_select_db(SAE_MYSQL_DB,$link);
				//your code goes here
				$mysql = new SaeMysql();
				/*
				<xml>   
				<ToUserName><![CDATA[gh_204936aea56d]]></ToUserName>  
				<FromUserName><![CDATA[1111222233334444555]]></FromUserName> 
				<CreateTime>1452482277</CreateTime>
				<MsgType><![CDATA[action]]></MsgType>							
				<Content><![CDATA[金源商务大厦]]></Content> 
				<MsgId>1234567890abcdef</MsgId>
				</xml>
				*/
				
				$devAddr=$postObj->Content;
				$devID=$postObj->DevId;
				$sql = "SELECT * FROM deviceInfo WHERE devID ='{$fromUsername}' ";
				$query=mysql_query($sql );//执行sql语句
				if(!$query){
					die("SELECT * FROM deviceInfo: " . mysql_error());
					$contentStr = "SELECT * FROM deviceInfo 失败";
				}else{
					$rs=mysql_fetch_array($query);
					$ret=$rs['devID'];//设备号
					if($ret==$fromUsername){
						$rs=mysql_fetch_array($query);
						$sql = "update deviceInfo set devAddree='{$devAddr}' where devID ='{$fromUsername}'";//绑定设备
						//$sql = "update deviceInfo set devAddree='西乡站d55出站口' where devID ='1111222233334444555'";//绑定设备
						$query=mysql_query( $sql );//执行sql语句
						if(!$query){
							die("update deviceInfo: " . mysql_error()); 
							$contentStr = "更新地址失败.";
						}else{
							$contentStr = "更新地址成功.";
						}
					}else{
						$contentStr = "没有那个设备号";
						//$contentStr = $fromUsername;
					}
				}
				$msgType = "text";
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
			}
			$mysql->closeDb();
			echo $resultStr;	
		}else {
        	echo "";
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
							<Title><![CDATA[%s]]></Title>
							<Description><![CDATA[%s]]></Description>
							<PicUrl><![CDATA[%s]]></PicUrl>
							<Url><![CDATA[%s]]></Url>	
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
					$sql = "SELECT * FROM devConnect WHERE qq ='{$fromUsername}' ";
					$query=mysql_query( $sql );//执行sql语句
					$num_row=mysql_num_rows($query);
					//$rs=mysql_fetch_array($query);
					$devID=array();
					//print_r($rs);
					$i=0;
					while($rs=mysql_fetch_array($query)) 
					{	
						//echo $num_row--;
						//$devID[i]=$row->devID."<br/>";
						echo "i=".$i;
						echo $result_devID[$i]=$rs['devID'];
						echo $result_qq[$i]=$rs['qq'];
						$i++;
					}
					//$QQ=$rs['qq'];
					$QQ=$result_qq[0];
					echo $i;
				
					if(!strcmp($QQ,$fromUsername)){		/*****************绑定设备功能*********************/
					switch($type)
					{
						case "voice":
							//$contentStr=$voice;
							$contentStr=$this->dealVoice($voice);
						$msgType = "text";
						$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
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
							//JPush 功能
						//JPUSH应用
							$br = '<br/>';
							$app_key = 'ad2add0d7bafaab683ca3b16';
							$master_secret = '2009611fff8213ed1bd0c3a6';
							
							// 初始化
							$client = new JPush($app_key, $master_secret);
							// 简单推送示例
							$result = $client->push()
								->setPlatform('all')
								->addAllAudience()
								->setNotificationAlert("导航到"."$label")
								->send();

							echo 'test Result=' . json_encode($result) . $br;
							
							$app_key = '5f1e36080805488ab8f22631';
							$master_secret = '2bd1c368081d8ad860afb867';
							
							// 初始化
							$client = new JPush($app_key, $master_secret);
							// 简单推送示例
							$result = $client->push()
								->setPlatform('all')
								->addAllAudience()
								->setNotificationAlert("导航到"."$label")
								->send();

							echo 'jpush Result=' . json_encode($result) . $br;
					
							$contentStr="位置已发送,如果长时间未收到,可能是网络原因,请重试!";
							$msgType = "text";
							$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
						break;
						case "image":
							$contentStr = "你的图片很漂亮！";
							$msgType = "text";
							$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
						break;
						case "event":
							if($customevent=="subscribe"){
								$contentStr = "感谢你的关注".$postObj->EventKey;
								$msgType = "text";
								$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
							}else if($customevent=="CLICK"){
								//$contentStr = "event";
								switch($postObj->EventKey)
								{
									case "about":
										$title="易见联";
										$description="访问易见联网站";
										$picUrl="http://yijianchelian.sinaapp.com/source/view.jpg";
										$url="yijianchelian.sinaapp.com/html/about.html";
										$resultStr = sprintf($newTpl, $fromUsername, $toUsername, $time,$title,$description,$picUrl,$url);	
										break;
									case "mycarplace":
										$contentStr="设备  "."      位置";
										for($num=0;$num<$i;$num++){//i为当前账号绑定的设备数
											$sql = "SELECT * FROM deviceInfo WHERE devID ='{$result_devID[$num]}' ";
											$query=mysql_query( $sql );//执行sql语句
											$rs_info=mysql_fetch_array($query);
											$ret_devAddr=$rs_info['devAddree'];
											if(!$ret_devAddr){
												$ret_devAddr="无地址信息";
											}
											$contentStr = $contentStr."\n".$result_devID[$num].":    ".$ret_devAddr;
										}
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
										break;
									case "navigation":
										$contentStr = '请发送要导航的位置信息。或者发送"导航到**",例如:导航到深圳大学';
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
										break;
									case "person":
										$contentStr = '请发送被接人的位置信息。或者发送"导航到**",例如:导航到深圳大学';
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
										break;
									case "devicemanagement":
										$title="设备管理";
										$description="进入管理界面";
										$picUrl="http://yijianchelian.sinaapp.com/source/devicemanagement.jpg";
										$url="http://yijianchelian.sinaapp.com/html/devicemanagement.php?qq={$fromUsername}";
										$resultStr = sprintf($newTpl, $fromUsername, $toUsername, $time,$title,$description,$picUrl,$url);	
										break;
									case "menu_register":
										$sql = "insert into devRegister(time,qq) values(now(),'{$fromUsername}')";//加入注册表
										$query=mysql_query( $sql );//执行sql语句
										if(!$query){
											die("insert into Sheet1: " . mysql_error());
										}
										$contentStr="请输入你的设备号";
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);	
										break;
									case "mycarview"://视频回放
										$title="远程视频";
										$description="请点开击查看视频";
										$picUrl="http://yijianchelian.sinaapp.com/source/view.jpg";
										$url="yijianchelian.sinaapp.com/html/mycarview.html";
										$resultStr = sprintf($newTpl, $fromUsername, $toUsername, $time,$title,$description,$picUrl,$url);	
										break;
									case "mycarblack"://轨迹回放
										$title="轨迹回放";
										$description="请点开击回放轨迹";
										$picUrl="http://yijianchelian.sinaapp.com/source/jinyuanshawudasha.jpg";
										$url="yijianchelian.sinaapp.com/html/ditu.html";
										$resultStr = sprintf($newTpl, $fromUsername, $toUsername, $time,$title,$description,$picUrl,$url);	
										break;
										
									default:
										$contentStr="功能完善中";
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
										break;
								}

							}else if($customevent=="SCAN"){//扫描关注事件
								$sql = "SELECT * FROM devConnect WHERE user='{$postObj->EventKey}' ";//判断有无此设备
								$query=mysql_query( $sql );//执行sql语句
								if(!$query){
									die("insert into Sheet1: " . mysql_error());
								}
								$rs=mysql_fetch_array($query);
								$USER=$rs['user'];//设备号
							//	$dev=$rs['qq'];
							//	if(!$dev){
								if($USER==($postObj->EventKey)){	//有此设备
									$sql = "update devConnect set qq='{$fromUsername}' where user={$postObj->EventKey}";//绑定设备
									$query=mysql_query( $sql );//执行sql语句
									$contentStr = "设备绑定成功".$postObj->EventKey;
									if(!$query){
										die("update devConnect: " . mysql_error()); 
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
						case "action":
							/*****
							<xml>   
							<ToUserName><![CDATA[gh_204936aea56d]]></ToUserName>  
							<FromUserName><![CDATA[oF-YDuPUhZPhm3NfRwF2Gj1Coyd8]]></FromUserName> 
							<CreateTime>1452482277</CreateTime>
							<MsgType><![CDATA[action]]></MsgType>							
							<DevId><![CDATA[11112222333334444555]]></DevId>  
							<Content><![CDATA[金源商务大厦]]></Content> 
							<MsgId>1234567890abcdef</MsgId>
							</xml>
							****/
							$devAddr=$postObj->Content;
							$devID=$postObj->DevId;
							$sql = "SELECT * FROM deviceInfo WHERE devID ='{$devID}' ";
							$query=mysql_query($sql );//执行sql语句
							if(!$query){
								die("SELECT * FROM deviceInfo: " . mysql_error());
								$contentStr = "SELECT * FROM deviceInfo 失败";
							}else{
								$rs=mysql_fetch_array($query);
								$ret=$rs['devID'];//设备号
								if($ret!=0){
									$rs=mysql_fetch_array($query);
									$sql = "update deviceInfo set devAddree='{$devAddr}' where devID ='{$devID}'";//绑定设备
									//$sql = "update deviceInfo set devAddree='西乡站d55出站口' where devID ='1111222233334444555'";//绑定设备
									$query=mysql_query( $sql );//执行sql语句
									if(!$query){
										die("update deviceInfo: " . mysql_error()); 
										$contentStr = "更新地址失败.";
									}else{
										$contentStr = "更新地址成功.";
									}
								}else{
									$contentStr = "没有那个设备号";
								}
							}
							$msgType = "text";
							$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
						break;
						case "text":
							//$navWord = substr($keyword,0,3);
							$navWord=mb_substr($keyword, 0, 3,'utf-8');
							$sql0 = "SELECT * FROM devRegister WHERE qq='{$fromUsername}' ";//判断是否要绑定
							$query0=mysql_query( $sql0 );//执行sql语句
							if(!$query0){
								die("SELECT * FROM devRegister: " . mysql_error());
							}
							$rs0=mysql_fetch_array($query0);
							$USER=$rs0['qq'];
							if($USER==$fromUsername){//绑定操作
								$sql = "SELECT * FROM devConnect WHERE devID='{$keyword}' ";//判断有无此设备
								$query=mysql_query( $sql );//执行sql语句
								if(!$query){
									die("SELECT * FROM devConnect: " . mysql_error());
								}
								$rs=mysql_fetch_array($query);
								$USER=$rs['devID'];
								if($USER==$keyword){	//有此设备
									 $sql = "update devConnect set qq='{$fromUsername}' where devID={$USER}";//绑定设备
									$query=mysql_query( $sql );//执行sql语句
									if(!$query){
										die("update devConnect: " . mysql_error()); 
										$contentStr = "设备绑定失败,请重新绑定";
									}else{
										$contentStr = "设备绑定成功";
									}
									$msgType = "text";
									$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
								}else{
									$contentStr="设备绑定失败,请重新绑定";
									$msgType = "text";
									$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
								}
								$sql0 = "delete FROM devRegister WHERE qq='{$fromUsername}' ";//判断是否要绑定
								$query0=mysql_query( $sql0 );//执行sql语句
								if(!$query0){
									die("delete * FROM devRegister: " . mysql_error());
								}
							}else if($navWord=="导航到"){
								$contentStr=$this->dealVoice($keyword);
								$msgType = "text";
								$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
							}
							else if($keyword=="天气"){
								$contentStr=$this->dealWeather();
								$msgType = "text";
								$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
							}else{
								$contentStr=$this->baiduFanyi($keyword);
								$msgType = "text";
								$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
							}			
						break;
						default:
							if(!empty( $keyword ))
							{

							}else{
								echo "Input something...";
							};
					}//switch_end
									
				}else{	/*******************未注册设备******************/
					
					//$sql = "insert into Sheet1(BM) values('{$fromUsername}')";
					//$query=mysql_query( $sql );//执行sql语句
					//die("insert into Sheet1: " . mysql_error());

					switch($type){
						case "event":
							$contentStr = "event0\n";
							if($customevent=="subscribe"){
								$contentStr = "感谢你的关注\n请绑定设备".$postObj->EventKey;
								if($postObj->EventKey){//绑定设备
									$rest = substr($postObj->EventKey, 8, strlen($postObj->EventKey)-8);
									$sql = "SELECT * FROM devConnect WHERE user='{$rest}' ";//判断有无此设备
									$query=mysql_query( $sql );//执行sql语句
									if(!$query){
										die("insert into Sheet1: " . mysql_error());
									}
									$rs=mysql_fetch_array($query);
									$USER=$rs['user'];
									if($USER==$rest){	//有此设备
										 $sql = "update devConnect set qq='{$fromUsername}' where user={$USER}";//绑定设备
										$query=mysql_query( $sql );//执行sql语句
										$contentStr = "感谢你的关注\n设备绑定成功";
										if(!$query){
											die("update devConnect: " . mysql_error()); 
											$contentStr = "感谢你的关注\n设备绑定失败,请重新注册";
										}
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
										
									}else{
										$contentStr="感谢你的关注\n绑定失败,请重新注册";
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
										
									}
								}
								$msgType = "text";
								$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
							}else if($customevent=="CLICK"){
								//$contentStr = "event";
								switch($postObj->EventKey)
								{
									case "menu_register":
										$sql = "insert into devRegister(time,qq) values(now(),'{$fromUsername}')";//判断有无此设备
										$query=mysql_query( $sql );//执行sql语句
										if(!$query){
											die("insert into Sheet1: " . mysql_error());
										}
										$contentStr="请输入你的设备号";
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);	
										break;
									default:
										$contentStr="******"."欢迎新朋友\n请绑定设备";
									
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);	
										break;
								}
							}else if($customevent=="SCAN"){
									//$postObj->EventKey = substr($postObj->EventKey, 8, strlen($postObj->EventKey)-8);
									$sql = "SELECT * FROM devConnect WHERE user='{$postObj->EventKey}' ";//判断有无此设备
									$query=mysql_query( $sql );//执行sql语句
									if(!$query){
										die("insert into Sheet1: " . mysql_error());
									}
									$rs=mysql_fetch_array($query);
									$USER=$rs['user'];//设备号
									if($USER==($postObj->EventKey)){	//有此设备
										$sql = "update devConnect set qq='{$fromUsername}' where user={$postObj->EventKey}";//绑定设备
										$query=mysql_query( $sql );//执行sql语句
										$contentStr = "设备绑定成功".$postObj->EventKey;
										if(!$query){
											die("update devConnect: " . mysql_error()); 
											$contentStr = "设备绑定失败,请重新绑定";
										}
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
										
									}else{
										$contentStr="绑定失败,请重新绑定";
										$msgType = "text";
										$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
									}
								}
						break;
						default :
							$sql0 = "SELECT * FROM devRegister WHERE qq='{$fromUsername}' ";//判断是否要绑定
							$query0=mysql_query( $sql0 );//执行sql语句
							if(!$query0){
								die("SELECT * FROM devRegister: " . mysql_error());
							}
							$rs0=mysql_fetch_array($query0);
							$USER=$rs0['qq'];
							if($USER==$fromUsername){//绑定操作
								$sql = "SELECT * FROM devConnect WHERE devID='{$keyword}' ";//判断有无此设备
								$query=mysql_query( $sql );//执行sql语句
								if(!$query){
									die("SELECT * FROM devConnect: " . mysql_error());
								}
								$rs=mysql_fetch_array($query);
								$USER=$rs['devID'];
								if($USER==$keyword){	//有此设备
									 $sql = "update devConnect set qq='{$fromUsername}' where devID={$USER}";//绑定设备
									$query=mysql_query( $sql );//执行sql语句
									if(!$query){
										die("update devConnect: " . mysql_error()); 
										$contentStr = "设备绑定失败,请重新绑定";
									}else{
										$contentStr = "设备绑定成功";
									}
									$msgType = "text";
									$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
								}else{
									$contentStr="设备绑定失败,请重新绑定";
									$msgType = "text";
									$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
								}
								$sql0 = "delete FROM devRegister WHERE qq='{$fromUsername}' ";//判断是否要绑定
								$query0=mysql_query( $sql0 );//执行sql语句
								if(!$query0){
									die("delete * FROM devRegister: " . mysql_error());
								}
							}else{
								$contentStr="******"."欢迎新朋友\n请绑定设备";
								$msgType = "text";
								$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
							}
						break;
					}

				}
				$mysql->closeDb();	
			}
			
			echo $resultStr;		

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function dealVoice($voice)
	{
		$len=mb_strlen($voice,'utf-8');
		if($len>3){
			$navWord=mb_substr($voice, 0, 3,'utf-8');
			if($navWord=="导航到"){
				$len=mb_strlen($voice,'utf-8');
				$navWord=mb_substr($voice, 3,$len-3,'utf-8');
				
				$geourl="http://api.map.baidu.com/geocoder/v2/?ak=m9fCLsMnPDsla4lTuKGNsw6c&callback=renderOption&output=xml&address=$navWord";
				$apistr=file_get_contents($geourl);//读取文件
				$apiobj=simplexml_load_string($apistr);//xml解析
				$lat=$apiobj->result->location->lat;
				$lng=$apiobj->result->location->lng;
				

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
					->setNotificationAlert("导航到"."$navWord"."(".$lng.",".$lat.")")
					->send();

				echo 'Result1=' . json_encode($result) . $br;
				
				//JPUSH应用
				$app_key = '5f1e36080805488ab8f22631';
				$master_secret = '2bd1c368081d8ad860afb867';
				// 初始化
				$client = new JPush($app_key, $master_secret);
				//$label='999899';
				// 简单推送示例
				$result = $client->push()
					->setPlatform('all')
					->addAllAudience()
					->setNotificationAlert("导航到"."$navWord"."(".$lng.",".$lat.")")
					->send();

				echo 'Result0=' . json_encode($result) . $br;
				
				$contentStr="位置已发送,如果长时间未收到,可能是网络原因,请重试!";
			}else{
				$contentStr="您的语音有误,请重新输入";
			}
		}else{
			$contentStr="您的语音有误,请重新输入";
		}
		return $contentStr;

	}
	
	private function dealWeather()
	{
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
		return $contentStr;
	}
		
	private function baiduFanyi($keyword)
	{
		$tranurl="http://openapi.baidu.com/public/2.0/bmt/translate?client_id=m9fCLsMnPDsla4lTuKGNsw6c&q={$keyword}&from=auto&to=auto";
		$transtr=file_get_contents($tranurl);//读入文件
		$transon=json_decode($transtr);//json解析
		$contentStr=$transon->trans_result[0]->dst;//读取翻译内容
		//$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
		return $contentStr;
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




?>