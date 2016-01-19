<?php 
class MiniLog{
	private static $_instance;//����
	private $_path ;//��־Ŀ¼
	private $_pid;//����id
	private $_handleArr;//���治ͬ��־�����ļ�fd
	/**
	*$path ��־�����Ӧ����־Ŀ¼
	*/
	function __construct($path){
		$this->_path=$path;
		$this->_pid=getmypid();
	}
	private function __clone(){
		
	}
	/*
	��������
	*/
	public static function instance($path='/tmp/'){
		if(!(self::$_instance instanceof self)){
			self::$_instance = new self($path);
		}
		return self::$_instance;
	}
	/*
	�����ļ�����ȡ�ļ�fd
	*/
	private function getHandle($fileName){
		if($this->_handleArr[$fileName]){
			return $this->_handleArr[$fileName];
		}
		date_default_timezone_set('PRC');
		$nowTime=time();
		$logSuffix=date('Ymd',$nowTime);
		$handle=fopen($this->_path.'/'.$fileName.$logSuffix.".log",'a');
		$this->_handleArr[$fileName]=$handle;
		return $handle;
		
	}
	/*
	���ļ���д�ļ�
	$fileName �ļ���
	$message ��Ϣ
	*/
	public function log($fileName,$message){
		$handle=$this->getHandle(fileName);
		$nowTime=time();
		$logPreffix=date('Y-m-d H:i:s',$nowTime);
		//д�ļ�
		fwrite($handle,"[$logPreffix][$this->_pid]$message\n");
		return true;
	}
	function __destruct(){
		foreach($this->_handleArr as $key=>$item){
			if($item){
				fclose($item);
			}
		}
	}
	
	
}
?>