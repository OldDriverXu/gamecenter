<?php
include_once dirname(__FILE__).'/GlobalDefine.php';
include_once dirname(__FILE__).'/ErrorCode.php';
//include_once dirname(__FILE__).'/ParamChecker.php';
include_once dirname(__FILE__).'/MiniLog.php';

// function checkParam($rules = array(), &$args) {
// 	return ParamChecker::getInstance()->checkParam($rules, $args);
// }

//定义日志级别
define("DEBUG", "DEBUG");
define("INFO", "INFO");
define("ERROR", "ERROR");
//define("STAT", "STAT");

/*
 * 默认打开所有的日志文件文件
 * ERROR,INFO,DEBUG日志级别分别对应的关闭标记文件为：NO_ERROR, NO_INFO, NO_DEBUG
 */
function isLogLevelOff($logLevel)
{
	$swithFile = ROOT_PATH . '/log/' . 'NO_' . $logLevel;
	if (file_exists($swithFile)){
		return true;
	}else {
		return false;
	}
}

/**
 * @author pacozhong
 * 日志函数的入口
 * @param string $confName 日志配置名
 * @param string $logLevel 级别
 * @param int $errorCode 错误码
 * @param string $logMessage 日志内容
 */
function ccdb_log($confName ,$logLevel, $errorCode, $logMessage = "no error msg")
{
	if (isLogLevelOff($logLevel)){
		return;
	}
	
	$st = debug_backtrace(); //debug_backtrace 产生一条回溯跟踪(backtrace)

	$function = ''; //调用interface_log的函数名
	$file = '';     //调用interface_log的文件名
	$line = '';     //调用interface_log的行号
	foreach($st as $item) {
		if($file) {
			$function = $item['function'];
			break;
		}
		if($item['function'] == 'interface_log') {
			$file = $item['file'];
			$line = $item['line'];
		}
	}
	
	$function = $function ? $function : 'main';
	
	//为了缩短日志的输出，file只取最后一截文件名
	$file = explode("/", rtrim($file, '/'));
	$file = $file[count($file)-1];
	//组装日志的头部
	$prefix = "[$file][$function][$line][$logLevel][$errorCode] ";
	if($logLevel == INFO || $logLevel == STAT) {
		$prefix = "[$logLevel]" ;
	}
	$logFileName = $confName . "_" . strtolower($logLevel);
	$logMessage = genErrMsg($errorCode , $logMessage);	
	MiniLog::instance(ROOT_PATH . "/log/")->log($logFileName, $prefix . $logMessage);
	if (isLogLevelOff("DEBUG") || $logLevel == "DEBUG"){
		return ;
	}else {
		MiniLog::instance(ROOT_PATH . "/log/")->log($confName . "_" . "debug", $prefix . $logMessage);
	}
}

/**
 * @author pacozhong
 * 接口层日志函数
 */
function interface_log($logLevel, $errorCode, $logMessage = "no error msg")
{
	ccdb_log('interface', $logLevel, $errorCode, $logMessage);
}

/**
 * @author pacozhong
 * matcher log
 */
function matcher_log($logLevel, $errorCode, $logMessage = "no error msg")
{
	ccdb_log('matcher', $logLevel, $errorCode, $logMessage);
}

function getIp()
{
	if (isset($_SERVER)){
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
			$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
			$realip = $_SERVER["HTTP_CLIENT_IP"];
		} else {
			$realip = $_SERVER["REMOTE_ADDR"];
		}
	} else {
		if (getenv("HTTP_X_FORWARDED_FOR")){
			$realip = getenv("HTTP_X_FORWARDED_FOR");
		} else if (getenv("HTTP_CLIENT_IP")) {
			$realip = getenv("HTTP_CLIENT_IP");
		} else {
			$realip = getenv("REMOTE_ADDR");
		}
	}

	return $realip;
}



/**
 * @desc 封装curl的调用接口，post的请求方式
 */
function doCurlPostRequest($url, $requestString, $timeout = 5) {   
	if($url == "" || $requestString == "" || $timeout <= 0){
		return false;
	}

    $con = curl_init((string)$url);
    curl_setopt($con, CURLOPT_HEADER, false);
    curl_setopt($con, CURLOPT_POSTFIELDS, $requestString);
    curl_setopt($con, CURLOPT_POST, true);
    curl_setopt($con, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($con, CURLOPT_SSL_VERIFYPEER, false);  //https 不需要验证
    curl_setopt($con, CURLOPT_TIMEOUT, (int)$timeout);

    return curl_exec($con);    
}  

/**
 * @desc 封装curl的调用接口，get的请求方式
 */
function doCurlGetRequest($url, $data = array(), $timeout = 10) {
	if($url == "" || $timeout <= 0){
		return false;
	}
	if($data != array()) {
		$url = $url . '?' . http_build_query($data);	
	}
	
	$con = curl_init((string)$url);
	curl_setopt($con, CURLOPT_HEADER, false);
	curl_setopt($con, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($con, CURLOPT_SSL_VERIFYPEER, false);  //https 不需要验证
	curl_setopt($con, CURLOPT_TIMEOUT, (int)$timeout);

	return curl_exec($con);
}

function getTime(){
	date_default_timezone_set('PRC');
	return time();
}

function getCurrentTime() 
{   
	date_default_timezone_set('PRC');
	$secondTime = time();
	return date('Y-m-d H:i:s', $secondTime);	
}

function getCurrentDate(){
	date_default_timezone_set('PRC');
	$secondTime = time();
	return date('Y-m-d', $secondTime);
}



//获取当前时间，毫秒级别,如果startTime传入，则计算当前时间与startTime的时间差
function getMillisecond($startTime = false) {
	$endTime = microtime(true) * 1000;
		
	if($startTime !== false) {
		$consumed = $endTime - $startTime;
		return round($consumed);
	}
		
	return $endTime;
}


function rSortByTimeStamp($a, $b){
	$ret = strnatcmp($a['addTimeStamp'], $b['addTimeStamp']);
	if ($ret > 0){
		return -1;
	}
	if ($ret < 0){
		return 1;
	}
	return 0;
}

function rSortByName($domainListA, $domainListB){
	$ret = strcmp($domainListA['domainName'], $domainListB['domainName']);
	if ($ret > 0){
		return 1;
	}
	if ($ret < 0){
		return -1;
	}
	return 0;
}

function arrayToObject($e){
    if( gettype($e)!='array' ) return;
    foreach($e as $k=>$v){
        if( gettype($v)=='array' || getType($v)=='object' )
            $e[$k]=(object)arrayToObject($v);
    }
    return (object)$e;
}
 
function objectToArray($e){
    $e=(array)$e;
    foreach($e as $k=>$v){
        if( gettype($v)=='resource' ) return;
        if( gettype($v)=='object' || gettype($v)=='array' )
            $e[$k]=(array)objectToArray($v);
    }
    return $e;
}

?>