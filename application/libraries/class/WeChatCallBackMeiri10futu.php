<?php
require_once dirname(__FILE__) . '/WeChatCallBack.php';
/**
 * 
 * meiri10futu implemention
 * @author pacozhong
 *
 */
class WeChatCallBackMeiri10futu extends WeChatCallBack{
	private $_content;
	private $_index;
	private $_oTable;
	
	public function init($postObj) {
		if(false == parent::init($postObj)) {
			interface_log ( ERROR, EC_OTHER, "init fail!" );
			return false;
		}
		try {
			$this->_oTable = new SingleTableOperation('picindex', 'MR');	
		} catch (Exception $e) {
			interface_log ( ERROR, EC_DB_OP_EXCEPTION, $e->getMessage () );
			return $this->makeFF_HINT ( FF_HINT_INNER_ERROR );
		}
		return true;
	}
	
	public function process() {
		if (false === $this->_registerUser ()) {
			return $this->makeHint ( MR_HINT_INNER_ERROR );
		}
		
		if ($this->_msgType != 'text') {
			return $this->makeHint ( MR_HINT_HELLO );
		}
		
		$this->_content = ( string ) trim ( $this->_postObject->Content );
		interface_log ( DEBUG, 0, 'content is ' . $this->_content );
		/*if (is_numeric ( $this->_content ) && ( int ) $this->_content > 0) {
			$index = ( int ) $this->_content;
			$this->_index = $index;
			$ret = $this->_getPicUrlByIndex ( $index );
			interface_log ( DEBUG, 0, 'get pic url by index result:' . var_export ( $ret, true ) );
			if ($ret ['code']) {
				return $this->makeHint ( $ret ['data'] );
			} else {
				$url = $ret ['data'];
			}
		} else */
		if (/*$this->_content == 'q' || $this->_content == 'Q' || */$this->_content == '?' || $this->_content == '？') {
			
			$ret = $this->_getNextUrl ();
			interface_log ( DEBUG, 0, 'get next url ret:' . var_export ( $ret, true ) );
			if ($ret ['code']) {
				return $this->makeHint ( $ret ['data'] );
			} else {
				$url = $ret ['data'];
			}
		} else {
			
			if ($this->_content == 'Hello2BizUser') {
				interface_log ( DEBUG, 0, 'hello to biz user' );
				return $this->makeHint ( MR_HINT_HELLO );
			}
			$ret = $this->_activation ();
			interface_log ( DEBUG, 0, 'activation ret:' . var_export ( $ret, true ) );
			if ($ret ['code']) {
				return $this->makeHint ( $ret ['data'] );
			}
			return $this->makeHint(MR_HINT_INPUT);
		}
		
		$resultStr = sprintf ( SUCC_TPL_MR, $this->_fromUserName, $this->_toUserName, $this->_time, $this->_index, $url, $url );
		return $resultStr;
	}
	
	private function _registerUser(){
		try {
			$ret = $this->_oTable->getObject(array('userName' => $this->_fromUserName));
			if(empty($ret)) {
				$this->_oTable->addObject(array('userName' => $this->_fromUserName));
			}
			return true;
		} catch (Exception $e) {
			interface_log ( ERROR, EC_DB_OP_EXCEPTION, $e->getMessage () );
			return false;
		}
	}
	
	private function _activation(){
		if($this->_content == $this->_fromUserName) {
			return array('data' => MR_HINT_ACTIVE_SELF, 'code' => 1);
		}
		try {
			$ret = $this->_oTable->getObject(array('userName' => $this->_content));
			if(empty($ret)) {
				return array('data' => '', 'code' => 0);
			}
			$toActive = $ret[0];
			if($toActive['limited'] == 0) {
				return array('data' => MR_HINT_ALREADY_ACTIVE, 'code' => 1);
			}
			$ret = $this->_oTable->getObject(array('userName' => $this->_fromUserName));
			if(empty($ret)) {
				//插入数据
				$this->_oTable->addObject(array('userName' => $this->_fromUserName));
			} else {
				$userInfo = $ret[0];
				if($userInfo['quota'] <= 0) {
					return array('code' => 1, 'data' => MR_HINT_NO_QUOTA);
				}
				//active
				$this->_oTable->updateObject(array('limited' => 0), array('userName' => $toActive['userName']));
				$this->_oTable->updateObject(array('quota' => $userInfo['quota'] - 1), array('userName' => $userInfo['userName']));
				return array('code' => 1, 'data' => MR_HINT_ACTIVE_SUCC);
			}
		} catch (Exception $e) {
			interface_log ( ERROR, EC_DB_OP_EXCEPTION, $e->getMessage () );
			return array('code' => 1, 'data' => MR_HINT_INNER_ERROR);
		}
	}
	
	private function _getPicUrlByIndex($index) {
		
		$fileName = dirname ( __FILE__ ) . "/../mr_image/" . $index . ".JPG";
		if (file_exists ( $fileName )) {
			$ret = $this->_addSeen();
			if($ret['code']) {
				return $ret;
			} 
			return array('data' => URL_HEADER . $index . ".JPG", 'code' => 0);
		} else {
			return array('data' => MR_HINT_NO_NEW_PIC, 'code' => 1);
		}
	
	}

	private function _addSeen() {
		date_default_timezone_set(PRC);
		$today = date('Ymd');
		
		try {
			$ret = $this->_oTable->getObject(array('userName' => $this->_fromUserName));
			if(empty($ret)) {
				$this->_oTable->addObject(array('userName' => $this->_fromUserName));
				$seen = 1;
				$limited = 1;
			} else {
				$lastSeen = $ret[0]['lastSeen'];
				if($lastSeen != $today) {
					$seen = 1;
				} else {
					$seen = $ret[0]['seenPic'] + 1;
				}
				$limited = $ret[0]['limited'];
			}
			if($seen > PIC_OF_DAY && $limited == 1) {
				return array('data' => MR_HINT_LIMITED . $this->_fromUserName, 'code' => 1);
			} else {
				$this->_oTable->updateObject(array('seenPic' => $seen, 'lastSeen' => $today), array('userName' => $this->_fromUserName));
				return array('data' => '', 'code' => 0);	
			}
		} catch (DB_Exception $e) {
			interface_log ( ERROR, EC_DB_OP_EXCEPTION, $e->getMessage () );
			return array('data' => MR_HINT_INNER_ERROR, 'code' => 1);
		}
	}
	
	private function _getNextUrl(){
		try {
			$ret = $this->_oTable->getObject(array('userName' => $this->_fromUserName));
			if(empty($ret)) {
				//插入
				$this->_oTable->addObject(array('userName' => $this->_fromUserName));
				$index = 1;
			} else {
				$index = $ret[0]['cur'];
			}
			$this->_index = $index;
			$ret = $this->_getPicUrlByIndex($index);
			if($ret['code'] == 0) {
				$this->_oTable->updateObject(array('cur' => $index + 1), array('userName' => $this->_fromUserName));
				return array('data' => $ret['data'], 'code' => 0 );
			} else {
				return $ret;
			}
			
		} catch (Exception $e) {
			interface_log ( ERROR, EC_DB_OP_EXCEPTION, $e->getMessage () );
			return array('data' => MR_HINT_INNER_ERROR, 'code' => 1);
		}
	}
}