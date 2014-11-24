<?php
require_once dirname(__FILE__) . '/WeChatCallBack.php';

class WeChatCallBackMYZL extends WeChatCallBack{
  
  
  private $interfaceName;
  private $value;
  private $fightInfo;
  private $lastOpInfo;
  private $userInfo;
  private $retStr;
  
  private function getOperandAndOperator(){
    //查询用户是否在准备列表中
    $STO = new SingleTableOperation("cWaitingUser", "MYZL");
    $waitingRet = $STO->getObject(array("userId" => $this->_fromUserName));
    interface_log(DEBUG, 0, var_export($waitingRet , true));
    
    //查询用户信息
    $STO->setTableName("cUser");
    $userInfo = $STO->getObject(array("userId" => $this->_fromUserName));
    $this->userInfo = $userInfo;
    interface_log(DEBUG, 0, var_export($userInfo, true));
    
    //处理用户关注公众帐号的动作
    if($this->_msgType == 'event' && trim((string)$this->_postObject->Event) == "subscribe") {
      if(empty($userInfo)) {
        //首次关注，添加用户信息
        $this->interfaceName = "AddUser";
        return true;
      } else {
        //取消关注后再次关注，给用户欢迎信息
        $this->interfaceName = "WelcomeBack";
        return true;
      }
      
    }
    //获取用户操作自定义菜单的EvenetKey
    $eventKey = trim((string)$this->_postObject->EventKey);
    //获取EventKey中的操作码和操作数
    $tmp = explode("_", $eventKey);
    $operator = trim($tmp[0]);
    $operand = trim($tmp[1]);
    interface_log(DEBUG, 0, 'operator:' . $operator . '  operand:' . $operand);
    //处理“下一步”的情况
    if($operator == "NEXT"){
      //如果用户在操作准备列表中，则说明匹配尚未成功，设置interfaceName为WaitOp，稍后给到提示
      if(!empty($waitingRet)) {
        $this->interfaceName = "WaitOp";
        return true;
      }
      //执行GetOp类，获取当前用户需要做的的操作
      require_once dirname(__FILE__) . '/../interface/GetOp.php';
      $instance = new GetOp();
      
      //执行GetOp类的基本函数
      if (! $instance->verifyCommonInput ( $this->_postObject )) {
        $ret =  $instance->renderOutput ();
        if($ret['retVal'] == EC_INVALID_INPUT) {
          $this->interfaceName = "InputErrorHint";
          return true;
        }
      }
      
      if (! $instance->initialize ()) {
        $ret =  $instance->renderOutput ();
        if($ret['retVal'] == EC_DB_OP_EXCEPTION) {
          $this->interfaceName = "DbErrorHint";
          return true;
        }
      }
      
      $instance->prepareData ();
      
      if (! $instance->process ()) {
        //如果GetOp类的process函数返回false，获取结果
        $ret =  $instance->renderOutput ();
        if($ret['retVal'] == EC_DB_OP_EXCEPTION) {
          $this->interfaceName = "DbErrorHint";
          return true;
        }
        if($ret['retVal'] == EC_USER_NOT_EXIST) {
          //在用户不存在的情况下，设定interfaceName为AddUser，表示需要添加用户
          $this->interfaceName = "AddUser";
          return true;
        }
        if($ret['retVal'] == EC_FIGHT_NOT_EXIST) {
          //如果游戏记录不存在，表示用户需要加入到准备队列，设置interfaceName为Ready
          $this->interfaceName = "Ready";
          return true;
        }
        if($ret['retVal'] == EC_MULTIPLE_FIGHT) {
          $this->interfaceName = "MultiFightHint";
          return true;
        }
        
      }
      
      $ret = $instance->renderOutput ();
      //在没有返回错误码的情况下，获取GetOp返回的游戏记录数据
      $this->fightInfo = $ret['retData'];
          
      $this->retStr = $instance->getResponseText();
      interface_log(DEUBUG, 0, "this->retStr:" . $this->retStr);
      if($this->fightInfo['operation'] == START || $this->fightInfo['operation'] == FIRST_END) {
        //当操作码为START或者FIRST_END，说明是游戏开始阶段
        if($this->fightInfo['first'] == $this->_fromUserName) {
          //当前用户为开始游戏的一方，设定interfaceName为Start
          $this->interfaceName = "Start";
        }else {
          //当前用户不是开始游戏的一方，设定interfaceName为WaitStart，稍后代码逻辑中给到提示
          $this->interfaceName = "WaitStart";
        }
        return true;
      }
      
      if($this->fightInfo['operation'] == SECOND_END) {
        //获取最后操作结果的情况
        $this->interfaceName = "SecondEndHint";
        return true;
      }
      
      if($this->fightInfo['operator'] != $this->_fromUserName) {
        //等待对方操作的情况
        $this->interfaceName = "WaitOperation";
        $this->value = $this->fightInfo['operation'];
        return true;
      }else {
        if($this->fightInfo['operation'] == PUT_MAGIC) {
          if($userInfo[0]['xsft'] <= 0 && 
            $userInfo[0]['hdcx'] <= 0 && 
            $userInfo[0]['chxs'] <= 0 && 
            $userInfo[0]['sszm'] <= 0){
              //在用户没有道具的情况下，NEXT的操作就是使用道具，但是值为空
            $this->interfaceName = "PutMagic";
            $this->value = "";
          }else {
            //提示用户按使用道具的自定义菜单使用道具
            $this->interfaceName = "PutMagicHint";
          }
        }
        if($this->fightInfo['operation'] == CHIP_IN) {
          //提示用户下注
          $this->interfaceName = "ChipInHint";
        }
        
        if($this->fightInfo['operation'] == SHOOT) {
          //如果操作码为SHOOT，NEXT操作就是Shoot
          $this->interfaceName = "Shoot";
        }
      }
      
    }else if($operator == "CHIPIN") {
      $this->interfaceName = "ChipIn";
      $this->value = (int)$operand;
      interface_log(DEBUG, 0, "this->value" . $this->value);
    }else if($operator == "PUTMAGIC") {
      $this->interfaceName = "PutMagic";
      $this->value = $operand;
    } else {
      $this->interfaceName = "InputErrorHint";
      return true;
    }
  }
  
  
  public function process(){
    
    if($this->_msgType != 'event') {
      //在消息类型不为event的时候，直接给到用户提示信息
      return $this->makeHint(MYZL_HINT);
    }
    $retStr = "";
    $this->getOperandAndOperator();
    interface_log(DEBUG, 0, "interfaceName:" . $this->interfaceName . "  value:" . $this->value);
    //根据getOperanAndOperator中设置的interfaceName进行对应操作，并设置相应的返回信息
    if($this->interfaceName == "WelcomeBack") {
      $retStr = "WelcomeBack";
    }
    if($this->interfaceName == "WaitOp") {
      $retStr = "等待系统匹配玩家";
    }
    
    if($this->interfaceName == "PutMagicHint") {
      $retStr = "请使用道具";
    } 
    if($this->interfaceName == "ChipInHint") {
      $retStr = "请下注";
    }
    
    if($this->interfaceName == "MultiFightHint") {
      $retStr = "错误的游戏状态";
    }
    
    if($this->interfaceName == "InputErrorHint" ) {
      $retStr = "输入错误";
    } 
    if($this->interfaceName == "DbErrorHint") {
      $retStr = "数据库连接错误";
    }
    
    if($this->interfaceName == "SecondEndHint") {
      if($this->_fromUserName == $this->fightInfo['current']) {
        $retStr = "等待对方结束游戏";  
      } else {
        $retStr = " ";//不能为空字符换
      }
    } 
    if($this->interfaceName == "WaitOperation") {
      $retStr = "等待对方【" . $GLOBALS['constants']['stepName'][$this->value] . "】，请稍后重试";
    }
    if($this->interfaceName == "WaitStart") {
      $retStr = "等待对方开始游戏！";
    }
    if($retStr) {
      //上述情况可以直接根据GetOp返回的responseText给到用户提示
      return $this->makeHint($this->retStr ?  ($this->retStr . ($retStr == " " ? "": "， ") . $retStr) :  $retStr);
    }
    
    //对需要进行逻辑处理的情况包含相应的文件，并new相应的对象
    if($this->interfaceName == "Ready") {
      require_once dirname(__FILE__) . '/../interface/Ready.php';
      $obj = new Ready();
    } 
    if($this->interfaceName == "Start") {
      require_once dirname(__FILE__) . '/../interface/Start.php';
      $obj = new Start();
    }
    if($this->interfaceName == "AddUser") {
      require_once dirname(__FILE__) . '/../interface/AddUser.php';
      $obj = new AddUser();
    }
    if($this->interfaceName == "PutMagic") {
      require_once dirname(__FILE__) . '/../interface/PutMagic.php';
      $obj = new PutMagic();
      $obj->setOperand($this->value);
    } 
    
    if($this->interfaceName == "Shoot") {
      require_once dirname(__FILE__) . '/../interface/Shoot.php';
      $obj = new Shoot();
    } 
    if($this->interfaceName == "ChipIn") {
      require_once dirname(__FILE__) . '/../interface/ChipIn.php';
      $obj = new ChipIn();
      $obj->setOperand($this->value);
      interface_log(DEBUG, 0, 'this->value:' . $this->value . ' _operand:' . $obj->getOperand());
    } 
    //执行new出来的对象的相应函数
    $ret = $obj->verifyCommonInput($this->_postObject);
    if(false == $ret) {
      $rt = $obj->renderOutput();
      return $this->makeHint($rt['retStr']);
    }
    $ret = $obj->initialize();
    if(false == $ret) {
      $rt = $obj->renderOutput();
      return $this->makeHint($rt['retStr']);
    }
    
    $ret = $obj->prepareData();
    if(false == $ret) {
      $rt = $obj->renderOutput();
      return $this->makeHint($rt['retStr']);
    }
    
    $ret = $obj->process();
    if(false == $ret) {
      $rt = $obj->renderOutput();
      interface_log(DEBUG, 0, var_export($rt, true));
      return $this->makeHint($rt['retStr']);
    }
    //获取对象中设置好的返回信息，并封装文本返回给用户
    return $this->makeHint($obj->getResponseText());
    
  }
}