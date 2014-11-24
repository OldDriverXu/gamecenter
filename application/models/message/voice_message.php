<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	require_once APPPATH.'models/message/base_message.php';

	//音频消息
	class Voice_message extends Base_message
	{	
		private $voice_media_id;        // 媒体ID 
		private $voice_format;  		// 语音格式，如amr，speex等

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
			$this->voice_media_id = (string) trim($weixin_array['MediaId']);
			$this->voice_format   = (string) trim($weixin_array['Format']);
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
					'message_id'      => $insert_id,
					'voice_media_id'  => $this->voice_media_id,
					'voice_format'    => $this->voice_format
				);
			$this->set_voice($array_subfix);
			return true;
		}

		public function get_voice($message_id){
			$this->_get_voice_media_id($message_id);
			$this->_get_voice_format($message_id);
			$data = array(
					'voice_media_id'  => $this->voice_media_id,
					'voice_format'    => $this->voice_format
				);
			return $data;
		}

		public function set_voice($array){
			$message_id            = $array['message_id'];
			$this->voice_media_id  = $array['voice_media_id'];
			$this->voice_format    = $array['voice_format'];

			$this->_set_voice_media_id($message_id, $this->voice_media_id);
			$this->_set_voice_format($message_id, $this->voice_format);
		}

		private function _get_voice_media_id($message_id){
			$this->db->from('wx_messagemeta');
			$this->db->where('message_id', $message_id);
			$this->db->where('meta_key', 'voice_media_id');
			$this->db->select('meta_value');
			$query=$this->db->get();
			$result = $query->result_array();

			if($result){			
  				$this->voice_media_id = $result[0]['meta_value'];
  			}
			return $this->voice_media_id;
		}
		
		private function _set_voice_media_id($message_id, $id){
			$data = array(
					'message_id' => $message_id,
					'meta_key'   => 'voice_media_id',
					'meta_value' => $id
				);
			$this->db->insert('wx_messagemeta', $data);

			$this->voice_media_id = $id;
		}

		private function _get_voice_format($message_id){
			$this->db->from('wx_messagemeta');
			$this->db->where('message_id', $message_id);
			$this->db->where('meta_key', 'voice_format');
			$this->db->select('meta_value');
			$query=$this->db->get();
			$result = $query->result_array();

			if($result){			
  				$this->voice_format = $result[0]['meta_value'];
  			}
			return $this->voice_format;
		}
		
		private function _set_voice_format($message_id, $text){
			$data = array(
					'message_id' => $message_id,
					'meta_key'   => 'voice_format',
					'meta_value' => $text
				);
			$this->db->insert('wx_messagemeta', $data);
			
			$this->voice_format = $text;
		}
	}
?>