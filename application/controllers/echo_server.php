<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Echo_server extends CI_Controller
	{

		public function __construct(){
			parent::__construct();
			$this->load->model('system_model');
			$this->load->model('message/text_message'); 
			$this->load->model('message/image_message'); 
			$this->load->model('message/link_message'); 
			$this->load->model('message/location_message'); 
			$this->load->model('message/voice_message'); 
			$this->load->model('message/video_message');
		}

		public function index(){

			$startTime = microtime(true);
			
			// //签名验证							
			// if( $this->system_model->check_signature($_GET['signature'], $_GET ['timestamp'], $_GET ['nonce']) ){
			// 	if($_GET["echostr"]) {
			// 		echo $_GET["echostr"];
			// 		exit(0);
			// 	}
			// }else{
			// 	//恶意请求：获取来来源ip，并写日志
			// 	exit(0);
			// }

			function exitErrorInput(){
				echo 'error input!';
				interface_log(INFO, EC_OK, "***** interface request end *****");
				interface_log(INFO, EC_OK, "*********************************");
				interface_log(INFO, EC_OK, "");
				exit ( 0 );
			}

			//获得微信服务器post的数据
			$postStr = file_get_contents ( "php://input" );
			interface_log(INFO, EC_OK, "");
			interface_log(INFO, EC_OK, "***********************************");
			interface_log(INFO, EC_OK, "***** interface request start *****");
			interface_log(INFO, EC_OK, 'request:' . $postStr);
			interface_log(INFO, EC_OK, 'get:' . var_export($_GET, true));
			if (empty ( $postStr )) {
				interface_log ( ERROR, EC_OK, "error input!" );
				exitErrorInput();
			}

			// 获取参数
			$postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
			if(NULL == $postObj) {
				interface_log(ERROR, 0, "can not decode xml");	
				exit(0);
			}
			$postArray = objectToArray($postObj);
			
			function getWeixinObj($postArray){
				switch ($postArray['MsgType']) {
					case 'text':						
						return new Text_message();	
					case 'image':
						return new Image_message();
					case 'link':
						return new Link_message();
					case 'location':
						return new Location_message();
					case 'voice':
						return new Voice_message();
					case 'video':
						return new Video_message();
					default:
						# code...
						break;
				}
			}
			$weixinObj = getWeixinObj($postArray);
			//消息初始化: 消息入库
			$ret = $weixinObj->init($postArray);
		}
	}

?>
