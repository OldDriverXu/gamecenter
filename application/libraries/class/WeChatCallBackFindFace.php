<?php
require_once dirname(__FILE__) . '/WeChatCallBack.php';
require_once dirname(__FILE__) . '/faceStub.php';
/**
 * find face implemention
 * @author pacozhong
 *
 */

class WeChatCallBackFindFace extends WeChatCallBack{
	private $_url;
	
	private function downloadPic(){

			interface_log ( DEBUG, 0, microtime(true) );
		$picData = doCurlGetRequest($this->_url);
		//向文件中写数据
		$fileName = $this->_fromUserName . "_" . time() . ".jpg";
		
		file_put_contents(ROOT_PATH . "/ff_image/" . $fileName, $picData);
		
		interface_log ( DEBUG, 0,"end download " . microtime(true) );
		return $fileName;
		
	}
	
	public function process() {
		//return $this->makeHint("系统正在升级！请稍候使用");
		try {
			if ($this->_msgType != 'image') {
				$contentStr = FF_HINT_TYPE_ERROR;
				if ($this->_msgType == 'text') {
					$contents = ( string ) trim ( $this->_postObject->Content );
					if ($contents == 'Hello2BizUser') {
						$contentStr = FF_HINT_HELLO;
					}
				}
				if($this->_msgType == 'event') {
					$event = (string) trim($this->_postObject->Event);
					if($event == 'subscribe') {
						$contentStr = FF_HINT_HELLO;
					}
				}
				return $this->makeHint ( $contentStr );
			}
			
			$this->_url = trim ( $this->_postObject->PicUrl );
			
			//下载图片到本地
			$fileName = $this->downloadPic();
			$this->_url = FF_URL_HEADER . $fileName;
			// 1.检验是否有脸
			$ret = faceStub::detect ( $this->_url );
			if ($ret === false) {
				return $this->makeHint ( FF_HINT_FACE_ERROR );
			}
			if (count ( $ret ['face'] ) == 0) {
				return $this->makeHint ( FF_HINT_NO_FACE );
			}
			if (count ( $ret ['face'] ) > 1) {
				return $this->makeHint ( FF_HINT_MULTIPLE_FACE );
			}
			
			$faceId = $ret ['face'] [0] ['face_id'];
			$oTable = new SingleTableOperation ();
			// 插入face到cFace
			$oTable->setTableName ( 'cface' );
			$oTable->addObject ( array ('faceId' => $faceId, 'personName' => $this->_fromUserName, 'url' => $ret ['url'] ) );
			
			// 2.找到最像的脸
			$ret = faceStub::search ( $faceId, GROUP_NAME, 2 );
			if ($ret === false) {
				return $this->makeHint ( FF_HINT_FACE_ERROR );
			}
			$candidates = $ret ['candidate'];
			if (count ( $candidates ) == 0) {
				return $this->makeHint ( FF_HINT_FACE_NO_CANDIDATE );
			}
			// 查找查询用户，看是否已上传过face
			

			$oTable->setTableName ( 'cperson' );
			$ret = $oTable->getObject ( array ('personName' => $this->_fromUserName ) );
			
			if (count ( $ret )) {
				$userFaceIds = array ();
				foreach ( $ret as $item ) {
					$userFaceIds [] = $item ['faceId'];
				}
				$newCandidate = array ();
				// 从结果集中删除掉用户自己
				foreach ( $candidates as $item ) {
					if (! in_array ( $item ['face_id'], $userFaceIds )) {
						$newCandidate [] = $item;
					}
				}
				$candidates = $newCandidate;
				// 更新用户的faceId
				$oTable->updateObject ( array ('faceId' => $faceId ), array ('personName' => $this->_fromUserName ) );
				// 更新faceplusplus的person:先删除person的face，再加入该faceId
				$result = faceStub::removeFaceFromPerson ( $this->_fromUserName, 'all' );
				if ($result === false) {
					return $this->makeHint ( FF_HINT_FACE_ERROR );
				}
				$result = faceStub::addFaceToPerson ( $this->_fromUserName, $faceId );
				if ($result === false) {
					return $this->makeHint ( FF_HINT_FACE_ERROR );
				}
			} else {
				// 插入记录到cPerson
				$oTable->addObject ( array ('personName' => $this->_fromUserName, 'faceId' => $faceId ) );
				// 请求faceplusplus创建person和，并加到group中
				$result = faceStub::createPerson ( $this->_fromUserName, $faceId, GROUP_NAME );
				if ($result === false) {
					return $this->makeHint ( FF_HINT_FACE_ERROR );
				}
			}
			if (count ( $candidates ) == 0) {
				return $this->makeHint ( FF_HINT_FACE_NO_CANDIDATE );
			}
			// 从数据库中查询face的url
			$oTable->setTableName ( 'cface' );
			$ret = $oTable->getObject ( array ('faceId' => $candidates [0] ['face_id'] ) );
			if (count ( $ret ) == 0) {
				return $this->makeHint ( FF_HINT_FACE_NO_CANDIDATE );
			}
			$url = $ret [0] ['url'];
			$resultStr = sprintf ( SUCC_TPL_FINDFACE, $this->_fromUserName, $this->_toUserName, $this->_time, $url, $url );
			$oTable->setTableName ( 'cstatus' );
			$ret = $oTable->getObject(array('key' => 'lastTrain'));
			$lastTrain = $ret[0]['value'];
			date_default_timezone_set(PRC);
			$now = date('YmdG');
			if ($now != $lastTrain) {
				faceStub::train ( GROUP_NAME, 'search' );
				$oTable->updateObject(array('value' => $now), array('key' => 'lastTrain'));
			}
			return $resultStr;
		
		} catch ( Exception $e ) {
			interface_log ( ERROR, EC_DB_OP_EXCEPTION, $e->getMessage () );
			return $this->makeHint ( FF_HINT_INNER_ERROR );
		}
	
	}
}
