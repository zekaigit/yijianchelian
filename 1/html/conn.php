<?php 
	@mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS) or die ("数据库连接失败");
	@mysql_select_db(SAE_MYSQL_DB) or die("连接失败");
?>