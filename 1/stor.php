<?php
use sinacloud\sae\Storage as Storage;

**类初始化**

// 方法一：在SAE运行环境中时可以不传认证信息，默认会从应用的环境变量中取
$s = new Storage();

// 方法二：如果不在SAE运行环境或者要连非本应用的storage，需要传入所连应用的"应用名:应用AccessKey"和"应用SecretKey"
$s = new Storage("$AppName:$AccessKey", $SecretKey);

**Bucket操作**

// 创建一个Bucket test
$s->putBucket("test");

// 获取Bucket列表
$s->listBuckets();

// 获取Bucket列表及Bucket中Object数量和Bucket的大小
$s->listBuckets(true);

// 获取test这个Bucket中的Object对象列表，默认返回前1000个，如果需要返回大于1000个Object的列表，可以通过limit参数来指定。
$s->getBucket("test");

// 获取test这个Bucket中所有以 *a/* 为前缀的Objects列表
$s->getBucket("test", 'a/');

// 获取test这个Bucket中所有以 *a/* 为前缀的Objects列表，只显示 *a/N* 这个Object之后的列表（不包含 *a/N* 这个Object）。
$s->getBucket("test", 'a/', 'a/N');

// Storage也可以当成一个伪文件系统来使用，比如获取 *a/* 目录下的Object（不显示其下的子目录的具体Object名称，只显示目录名）
$s->getBucket("test", 'a/', null, 10000, '/');

// 删除一个空的Bucket test
$s->deleteBucket("test");

**Object上传操作**

// 把$_FILES全局变量中的缓存文件上传到test这个Bucket，设置此Object名为1.txt
$s->putObjectFile($_FILES['uploaded']['tmp_name'], "test", "1.txt");

// 把$_FILES全局变量中的缓存文件上传到test这个Bucket，设置此Object名为sae/1.txt
$s->putObjectFile($_FILES['uploaded']['tmp_name'], "test", "sae/1.txt");

// 上传一个字符串到test这个Bucket中，设置此Object名为string.txt，并且设置其Content-type
$s->putObject("This is string.", "test", "string.txt", Storage::ACL_PUBLIC_READ, array(), array('Content-Type' => 'text/plain'));

// 上传一个文件句柄（必须是buffer或者一个文件，文件会被自动fclose掉）到test这个Bucket中，设置此Object名为file.txt
$s->putObject(Storage::inputResource(fopen($_FILES['uploaded']['tmp_name'], 'rb'), filesize($_FILES['uploaded']['tmp_name']), "test", "file.txt", Storage::ACL_PUBLIC_READ);

**Object下载操作**

// 从test这个Bucket读取Object 1.txt，输出为此次请求的详细信息，包括状态码和1.txt的内容等
var_dump($s->getObject("test", "1.txt"));

// 从test这个Bucket读取Object 1.txt，把1.txt的内容保存在SAE_TMP_PATH变量指定的TmpFS中，savefile.txt为保存的文件名;SAE_TMP_PATH路径具有写权限，用户可以往这个目录下写文件，但文件的生存周期等同于PHP请求，也就是当该PHP请求完成执行时，所有写入SAE_TMP_PATH的文件都会被销毁
$s->getObject("test", "1.txt", SAE_TMP_PATH."savefile.txt");

// 从test这个Bucket读取Object 1.txt，把1.txt的内容保存在打开的文件句柄中
$s->getObject("test", "1.txt", fopen(SAE_TMP_PATH."savefile.txt", 'wb'));

**Object删除操作**

// 从test这个Bucket删除Object 1.txt
$s->deleteObject("test", "1.txt");

**Object复制操作**

// 把test这个Bucket的Object 1.txt内容复制到newtest这个Bucket的Object 1.txt
$s->copyObject("test", "1.txt", "newtest", "1.txt");

// 把test这个Bucket的Object 1.txt内容复制到newtest这个Bucket的Object 1.txt，并设置Object的浏览器缓存过期时间为10s和Content-Type为text/plain
$s->copyObject("test", "1.txt", "newtest", "1.txt", array('expires' => '10s'), array('Content-Type' => 'text/plain'));

**生成一个外网能够访问的url**

// 为私有Bucket test中的Object 1.txt生成一个能够在外网用GET方法临时访问的URL，次URL过期时间为600s
$s->getTempUrl("test", "1.txt", "GET", 600);

// 为test这个Bucket中的Object 1.txt生成一个能用CDN访问的URL
$s->getCdnUrl("test", "1.txt");

**调试模式**

// 开启调试模式，出问题的时候方便定位问题，设置为true后遇到错误的时候会抛出异常而不是写一条warning信息到日志。
$s->setExceptions(true);
?>