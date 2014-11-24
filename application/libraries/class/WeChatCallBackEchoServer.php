<?php
require_once dirname(__FILE__) . '/WeChatCallBack.php';
/**
 * echo server implemention
 * @author pacozhong
 *
 */

class WeChatCallBackEchoServer extends WeChatCallBack{
	
	public function process(){
		if ($this->_msgType != 'text') {
			return $this->makeHint ( "你发的不是文字" );
		}
		try {
			$db = DbFactory::getInstance('ES');
			$sql = "insert into userinput (userId, input) values(\"" . $this->_fromUserName . "\", \"" . $this->_postObject->Content . "\")";
			interface_log(DEBUG, 0, "sql:" . $sql);			
			$db->query($sql);
			$STO = new SingleTableOperation("userinput", "ES");
			$ret = $STO->getObject(array("userId" => $this->_fromUserName));
			$out = "";
			foreach ($ret as $item) {
				$out .= $item['input'] . ", ";
			}
		} catch (DB_Exception $e) {
			interface_log(ERROR, EC_DB_OP_EXCEPTION, "query db error" . $e->getMessage());
		}
		return $this->makeHint ($out);
	}	
}
