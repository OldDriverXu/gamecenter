<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	require_once APPPATH.'models/message/base_message.php';

	//文本消息
	class Text_message extends Base_message
	{	
		private $content;    // 消息内容  

		public function __construct(){
			parent::__construct();
			$this->load->model('system_model');
			$this->load->model('autoreply/base_reply');
			$this->load->model('autoreply/text_reply');
			$this->load->model('autoreply/music_reply');
			$this->load->model('autoreply/news_reply');
			$this->load->model('follower/follower_model');
			$this->load->library('parser');
			$this->load->helper(array('form', 'url'));
		}

		public function init($weixin_array){
			// 获取共有参数
			$this->from_username = (string) trim($weixin_array['FromUserName']);
			$this->to_username   = (string) trim($weixin_array['ToUserName']);
			$this->create_time   = (int)    trim($weixin_array['CreateTime']);
			$this->msg_type      = (string) trim($weixin_array['MsgType']);
			$this->msg_id        = (int)    trim($weixin_array['MsgId']);

			// 私有参数
			$this->content       = (string) trim($weixin_array['Content']);

			if(!($this->from_username && $this->to_username && $this->msg_type)){
				return false;
			}
			// 消息入库wx_message
			$array = array(
				'message_username'       => $this->from_username,
				'message_date'           => date('Y-m-d H:i:s', $this->create_time),
				'message_date_timestamp' => $this->create_time,
				'message_content'        => $this->content,
				'message_type'           => $this->msg_type,
				'message_status'         => ''
			);
			$this->set_message($array);

			// 粉丝表更新wx_follower
			$follower = $this->follower_model->get_follower($this->from_username);
			if ($follower){

			}else{
				$array_follower = array(
					'follower_username'            => $this->from_username,
					'follower_subscribe_date'      => date('Y-m-d H:i:s', $this->create_time),
					'follower_subscribe_timestamp' => $this->create_time
				);
				$this->follower_model->set_follower($array_follower);
			}

			// 自动回复
			$this->key_word_reply($this->content);
		}

		//关键词自动回复
		public function key_word_reply($key_word){
			$array_autoreply = array(
				'autoreply_to_username' => $this->from_username,
				'autoreply_from_username' => $this->to_username
			);
			$results = $this->base_reply->get_all_reply();
			for($i=0,$len=count($results);$i<$len;$i++){
				$result = $results[$i];
				$key = $result['autoreply_keyword'];
				$matches = array();
				if(preg_match('/'.$key.'/', $key_word, $matches)){
					$array_autoreply['id'] = $result['id'];
					$array_autoreply['autoreply_type'] = $result['autoreply_type'];
					$this->exec_autoreply($array_autoreply);
					break;
				}
			}
			return true;
		}


		public function get_text_content(){
			return $this->content;
		}
		
		public function set_text_content($text){
			$this->content = $text;
		}
		public function exec_autoreply($array){
			$result = '';
			$id = $array['id'];
			$type = $array['autoreply_type'];
			$arry['autoreply_type'] = $type;
			if($type=='text'){
				$results = $this->base_reply->get_reply(array('id'=>$id));
				$array['autoreply_content'] = $results[0]['autoreply_content'];
				$xml = $this->text_reply->create_xml($array);
			}else if($type=='news'){
				$news = $this->news_reply->get_news($id, 'auto_reply', $this->from_username);
				$array['autoreply_content'] = $news;
				$xml = $this->news_reply->create_xml($array);
			}else if($type=='music'){
				$music = $this->music_reply->get_music($id);
				$array['autoreply_content'] = $music;
				$xml = $this->music_reply->create_xml($array);
			}
			var_dump($xml);

			interface_log(INFO, EC_OK, "***********************************");
			interface_log(INFO, EC_OK, "***** autoreply output start *****");
			interface_log(INFO, EC_OK, 'output:' . var_export($xml, true));
			die;
		}
	}
?>