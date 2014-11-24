<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	require_once APPPATH.'models/message/base_message.php';

	//链接消息
	class Link_message extends Base_message
	{	
		private $link_title;        // 消息标题
		private $link_description;  // 消息描述
		private $link_url; 		    // 消息链接

		public function __construct(){		
			parent::__construct();
		}

		public function init($weixin_array){
			// 获取共有参数
			$this->from_username = (string) trim($weixin_array['FromUserName']);
			$this->to_username   = (string) trim($weixin_array['ToUserName']);
			$this->create_time   = (int)    trim($weixin_array['CreateTime']);
			$this->msg_type      = (string) trim($weixin_array['MsgType']);
			$this->msg_id        = (int)    trim($weixin_array['MsgId']);

			// 私有参数
			$this->link_title        = (string) trim($weixin_array['Title']);
			$this->link_description  = (string) trim($weixin_array['Description']);
			$this->link_url          = (string) trim($weixin_array['Url']);
			if(!($this->from_username && $this->to_username && $this->msg_type)){
				return false;
			}
			// 消息入库
			$array = array(
					'message_username'       => $this->from_username,
					'message_date'		     => date('Y-m-d H:i:s', $this->create_time),
					'message_date_timestamp' => $this->create_time,					
					'message_type'           => $this->msg_type,
					'message_status'         => ''
				);
			$insert_id=$this->set_message($array);
			$array_subfix = array(
					'message_id'   => $insert_id,
					'link_title'	   => $this->link_title,
					'link_description' => $this->link_description,
					'link_url'         => $this->link_url
				);
			$this->set_link($array_subfix);
			return true;
		}

		public function get_link($message_id){
			$this->_get_link_title($message_id);
			$this->_get_link_description($message_id);
			$this->_get_link_url($message_id);
			$data = array(
					'link_title'        => $this->link_title,
					'link_description'  => $this->link_description,
					'link_url'          => $this->link_url					
				);
			return $data;
		}

		public function set_link($array){
			$message_id              = $array['message_id'];
			$this->link_title        = $array['link_title'];
			$this->link_description  = $array['link_description'];
			$this->link_url          = $array['link_url'];

			$this->_set_link_title($message_id, $this->link_title);
			$this->_set_link_description($message_id, $this->link_description);
			$this->_set_link_url($message_id, $this->link_url);
		}

		private function _get_link_title($message_id){
			$this->db->from('wx_messagemeta');
			$this->db->where('message_id', $message_id);
			$this->db->where('meta_key', 'link_title');
			$this->db->select('meta_value');
			$query=$this->db->get();
			$result = $query->result_array();

			if($result){			
  				$this->link_title = $result[0]['meta_value'];
  			}
			return $this->link_title;
		}
		
		private function _set_link_title($message_id, $text){
			$data = array(
					'message_id' => $message_id,
					'meta_key'   => 'link_title',
					'meta_value' => $text
				);
			$this->db->insert('wx_messagemeta', $data);

			$this->link_title = $text;
		}

		private function _get_link_description($message_id){
			$this->db->from('wx_messagemeta');
			$this->db->where('message_id', $message_id);
			$this->db->where('meta_key', 'link_description');
			$this->db->select('meta_value');
			$query=$this->db->get();
			$result = $query->result_array();	

			if($result){		
  				$this->link_description = $result[0]['meta_value'];
  			}
			return $this->link_description;
		}
		
		private function _set_link_description($message_id, $text){
			$data = array(
					'message_id' => $message_id,
					'meta_key'   => 'link_description',
					'meta_value' => $text
				);
			$this->db->insert('wx_messagemeta', $data);

			$this->link_description = $text;
		}

		private function _get_link_url($message_id){
			$this->db->from('wx_messagemeta');
			$this->db->where('message_id', $message_id);
			$this->db->where('meta_key', 'link_url');
			$this->db->select('meta_value');
			$query=$this->db->get();
			$result = $query->result_array();

			if($result){			
  				$this->link_url = $result[0]['meta_value'];
  			}
			return $this->link_url;
		}
		
		private function _set_link_url($message_id,$url){
			$data = array(
					'message_id' => $message_id,
					'meta_key'   => 'link_url',
					'meta_value' => $url
				);
			$this->db->insert('wx_messagemeta', $data);

			$this->link_url = $url;
		}
	}
?>