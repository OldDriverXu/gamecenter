<?php
/**
* 日志类
*/
class MiniLog {
	private static $_instance;  //单例
	private $_path;				//日志目录
	private $_pid;				//进程id
	private $_handleArr;		//保存不同日志级别文件fd
	
	function __construct($path) {
		$this->_path = $path;
		$this->_pid = getmypid();		
	}
	
	private function __clone() {
		
	}
	
	/**
	*  单例函数
	*/
	public static function instance($path = '/tmp/') {
		if(!(self::$_instance instanceof self)) {
			self::$_instance = new self($path);
		}
		
		return self::$_instance;
	}

	/**
	* 根据文件名获得文件fd
	* @param $fileName 文件名
	* @return 文件fd
	*/	
	private	function getHandle($fileName) {
		if($this->_handleArr[$fileName]) {
			return $this->_handleArr[$fileName];
		}
		date_default_timezone_set('PRC');
		$nowTime = time();
		$logSuffix = date('Ymd', $nowTime);
		$handle = fopen($this->_path . '/' . $fileName . $logSuffix . ".log", 'a');
		$this->_handleArr[$fileName] = $handle;
		return $handle;
	}

	/**
	* 向文件中写日志
	* @param $fileName 文件名
	* @param $message 消息
	*/	
	public function log($fileName, $message) {
		$handle = $this->getHandle($fileName);
		$nowTime = time();
		$logPreffix = date('Y-m-d H:i:s', $nowTime);
		fwrite($handle, "[$logPreffix][$this->_pid]$message\n");
		return true;
	}
	
	function __destruct(){
		foreach ($this->_handleArr as $key => $item) {
			if($item) {
				fclose($item);
			}
		}
	}
}

?>