<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	require_once APPPATH.'libraries/common/GlobalFunctions.php';
	class Test extends CI_Controller
	{
		function __construct(){
			parent::__construct();
			$this->load->model('system_model');
			$this->load->model('autoreply/base_reply');
			$this->load->model('autoreply/text_reply');
			$this->load->model('autoreply/news_reply');
			$this->load->model('message/text_message');
			$this->load->model('follower/follower_model');
			$this->load->model('game/game_model');
			$this->load->library('parser');
			$this->load->helper(array('form', 'url'));
		}

		function index(){

			$data = array(
				'message_id' => '10',
	            'pic_url' => 'fatsheep.cn',
	            'pic_media_id' => 'fdsafdsa'
				);
			//print_r($data);
			$curl_handle = curl_init();
	        curl_setopt($curl_handle, CURLOPT_URL, 'http://127.0.0.1/weikefu/index.php/api/message/pic');
	        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($curl_handle, CURLOPT_POST, 1);
	        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);

	        $buffer = curl_exec($curl_handle);
        	curl_close($curl_handle);


        	$result = json_decode($buffer);
        	print_r($result);
		}

		function text_welcome(){
			$data = array(
				'autoreply_to_username'	    => 'mm1',            //开发者微信号
				'autoreply_from_username'	    => 'mm2',            //发送方帐号（一个OpenID）
				'autoreply_time'	    => 7892,            //消息创建时间 （整型）
				'autoreply_type'	        => 'event',           //消息类型，event
				'autoreply_content'             => 'subscribe',

				);
			$result = $this->subscribe->reply_welcome($data);
			// $url = 'http://localhost/weikefu/index.php/echo_server/index'
			// $result = doCurlPostRequest($url, $data);
			var_dump($result);
			die;
		}
		function test_autoreply(){
			$data = array(
				'FromUserName' =>'oWDB4t6M2fPqfV7sMzOMcqG8iRYo',
				'ToUserName'=>'to',
				'CreateTime'=>'mmmm',
				'MsgType'=>'event',
				'MsgId'=>'mmmm',
	            'Content'=>'p',
				'Event'=>'CLICK',
				'EventKey'=>'CLICK'

			);
			$result = $this->text_message->init($data);
			// $url = 'http://localhost/weikefu/index.php/echo_server/index'
			// $result = doCurlPostRequest($url, $data);
			var_dump($result);
			die;
		}
		function test_offline(){
			$data = array(
				'FromUserName' =>'oWDB4t6M2fPqfV7sMzOMcqG8iRYo',
				'ToUserName'=>'to',
				'CreateTime'=>'mmmm',
				'MsgType'=>'text',
				'MsgId'=>'mmmm',
	            'Content'=>'p'
				// 'MsgType'=>'event',
				// 'Event'=>'subscribe'
			);
			///根据客服上班状态进行自动回复
			$result = $this->base_reply->get_online_status();
			if($result=='offline'){
				$data['Content'] = 'off_duty_reply';
				$this->text_message->init($data);
			}else{

			}
			die;
		}


		function text_follower(){
			$array_follower = array(
					'follower_username'            => 'oWDB4t6M2fPqfV7sMzOMcqG8iRYo',
					'follower_subscribe_date'      => '',
					'follower_subscribe_timestamp' => '',
					'follower_nickname'            => '',
					'follower_headimgurl'          => '',
					'follower_sex'                 => '',
					'follower_province'            => '',
					'follower_city'                => '',
					'follower_country'             => '',
					'follower_group'               => '',
					'follower_status'              => ''
				);
			$this->follower_model->set_follower($array_follower);
		}


		function score(){
			$game_id = '2';
			$rank = $this->game_model->get_rank($game_id);
			print_r($rank);
		}
























		function getnews(){
			$this->load->model('advanced/advanced_news_reply');
			$result=$this->advanced_news_reply->get_news(5);
			//var_dump($result);


			$data = array(
					'touser'  => '12313',
					'msgtype' => 'news',
					'news'    => array(
							'articles' => $result
						)
				);

			//var_dump($data);
			var_dump(json_encode($data));

			//rint_r($result);
		}

		function get(){

			$string=
						'{
			    "touser":"OPENID",
			    "msgtype":"news",
			    "news":{
			        "articles": [
			         {
			             "title":"Happy Day",
			             "description":"Is Really A Happy Day",
			             "url":"URL",
			             "picurl":"PIC_URL"
			         },
			         {
			             "title":"Happy Day",
			             "description":"Is Really A Happy Day",
			             "url":"URL",
			             "picurl":"PIC_URL"
			         }
			         ]
			    }
			}';
		$array = json_decode($string);
		print_r($array);
		}

		function follower(){
			$this->load->model('system_model');
			$this->load->model('personnel/follower_model');
			$token = $this->system_model->get_access_token();
			$list  = $this->follower_model->get_follower_list($token);

			//echo $list;
			print_r($list);


		}


		function upload(){
			$header["title"] = "TEST";
			$this->load->view("templates/header",$header);







			$data['access_token']="K2oMVLVYI-Aozy9HxCO00Ew016ETqVf8vSySwFb_AJE0iq2AcBxDEN56sjNScNED2WvDHe5_zlAhRbtXmFTbP_OtubcR_9Dz10QSdIhUIwF-qvURVh4y1gKnmz7E18CP0Bm8v570uw1DlZkFbGt0Og";
			$data['type'] ="image";
			//$file =array("fax_file"=>"@D:/1.jpg", "aa"=>"bb");

			$this->load->view("test",$data);


			$this->load->view("templates/footer");

		}


		function upload_media(){

			function uploadMedia(){
		        $file = realpath('D:/2.jpg'); //要上传的文件



		        $access_token="Ulcy7xXUpEz7p_I69SK3DsPhuUtF6wMqZMtYePK2U5r3dU03aWkNCHb5Bsq9oblBpjFNvHTdKH6MUaOCCkYHgyd0h-3Jzmt1YBeXkDVr4-rzDs5SrsKdkjrFhM_pGSpwB7kE_-PbJwYK1x18GZOlVg";
		        $type="image";
		        $fields['media'] = '@'.$file;

		        $post_url = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token='.$access_token.'&type='.$type;
		        $ch = curl_init($post_url) ;
		        //curl_setopt($ch, CURLOPT_POST, 1);
		        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		        curl_setopt($ch, CURLOPT_POST, 1);
		        curl_setopt($ch, CURLOPT_POSTFIELDS,$fields);
		        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		        $result = curl_exec($ch) ;
		        if (curl_errno($ch)) {
		         return curl_error($ch);
		        }
		        curl_close($ch);
		        return $result;
			}

			// $result1= uploadMedia();
			// var_dump($result1);




			$this->load->model('advanced/advanced_media');

			$file = 'D:/2.jpg'; //要上传的文件


			$access_token="Ulcy7xXUpEz7p_I69SK3DsPhuUtF6wMqZMtYePK2U5r3dU03aWkNCHb5Bsq9oblBpjFNvHTdKH6MUaOCCkYHgyd0h-3Jzmt1YBeXkDVr4-rzDs5SrsKdkjrFhM_pGSpwB7kE_-PbJwYK1x18GZOlVg";
		    $type="image";
		    $media_id="XB3ivvY1FgL1NVuJI9rI7VJOgQQP4sYAaYktN_nio1GyzQkYQlVX70-4N-QKxcI_";

			$result= $this->advanced_media->get($access_token, $media_id);

			var_dump($result);


		}


	}

?>