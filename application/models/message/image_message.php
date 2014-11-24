<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	require_once APPPATH.'models/message/base_message.php';

	//图片消息
	class Image_message extends Base_message
	{
		private $pic_url;    // 图片链接 
		private $pic_media_id;   // 图片消息媒体id 

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
			$this->pic_url       = (string) trim($weixin_array['PicUrl']);
			$this->pic_media_id  = (string) trim($weixin_array['MediaId']);
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
					'pic_url'	   => $this->pic_url,
					'pic_media_id' => $this->pic_media_id
				);
			$this->set_pic($array_subfix);
			return true;
		}

		public function get_pic($message_id){
			$this->_get_pic_url($message_id);
			$this->_get_pic_media_id($message_id);
			$data = array(
					'pic_url' => $this->pic_url,
					'pic_media_id'  => $this->pic_media_id
				);	
			return $data;
		}

		public function set_pic($array){
			$message_id         = $array['message_id'];
			$this->pic_url      = $array['pic_url'];
			$this->pic_media_id = $array['pic_media_id'];

			$this->_set_pic_url($message_id, $this->pic_url);
			$this->_set_pic_media_id($message_id, $this->pic_media_id);
		}

		private function _get_pic_url($message_id){			
			$this->db->from('wx_messagemeta');
			$this->db->where('message_id', $message_id);
			$this->db->where('meta_key', 'pic_url');
			$this->db->select('meta_value');
			$query=$this->db->get();
			$result = $query->result_array();

			if($result){		
  				$this->pic_url = $result[0]['meta_value'];
  			}
			return $this->pic_url;
		}
		
		private function _set_pic_url($message_id, $url){
			$data = array(
					'message_id' => $message_id,
					'meta_key'   => 'pic_url',
					'meta_value' => $url
				);
			$this->db->insert('wx_messagemeta', $data);

			$this->pic_url = $url;
		}

		private function _get_pic_media_id($message_id){
			$this->db->from('wx_messagemeta');
			$this->db->where('message_id', $message_id);
			$this->db->where('meta_key', 'pic_media_id');
			$this->db->select('meta_value');
			$query=$this->db->get();
			$result = $query->result_array();

			if($result){
  				$this->pic_media_id = $result[0]['meta_value'];
  			}
			return $this->pic_media_id;
		}
		
		private function _set_pic_media_id($message_id, $id){
			$data = array(
					'message_id' => $message_id,
					'meta_key'   => 'pic_media_id',
					'meta_value' => $id
				);
			$this->db->insert('wx_messagemeta', $data);			

			$this->pic_media_id = $id;
		}
	}
?>