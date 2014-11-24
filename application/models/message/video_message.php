<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	require_once APPPATH.'models/message/base_message.php';

	//视频消息
	class Video_message extends Base_message
	{
		private $video_media_id;    // 视频消息媒体id  
		private $video_thumb_id;    // 视频消息缩略图的媒体id

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
			$this->video_media_id = (string) trim($weixin_array['MediaId']);
			$this->video_thumb_id = (string) trim($weixin_array['ThumbMediaId']);
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
					'video_media_id'  => $this->video_media_id,
					'video_thumb_id'  => $this->video_thumb_id
				);
			$this->set_video($array_subfix);
			return true;
		}

		public function get_video($message_id){
			$this->_get_video_media_id($message_id);
			$this->_get_video_thumb_id($message_id);
			$data = array(
					'video_media_id' => $this->video_media_id,
					'video_thumb_id'  => $this->video_thumb_id
				);	
			return $data;
		}

		public function set_video($array){
			$message_id         = $array['message_id'];
			$this->video_media_id = $array['video_media_id'];
			$this->video_thumb_id = $array['video_thumb_id'];

			$this->_set_video_media_id($message_id, $this->video_media_id);
			$this->_set_video_thumb_id($message_id, $this->video_thumb_id);
		}

		private function _get_video_media_id($message_id){			
			$this->db->from('wx_messagemeta');
			$this->db->where('message_id', $message_id);
			$this->db->where('meta_key', 'video_media_id');
			$this->db->select('meta_value');
			$query=$this->db->get();
			$result = $query->result_array();

			if($result){		
  				$this->video_media_id = $result[0]['meta_value'];
  			}
			return $this->video_media_id;
		}
		
		private function _set_video_media_id($message_id, $id){
			$data = array(
					'message_id' => $message_id,
					'meta_key'   => 'video_media_id',
					'meta_value' => $id
				);
			$this->db->insert('wx_messagemeta', $data);

			$this->video_media_id = $id;
		}

		private function _get_video_thumb_id($message_id){
			$this->db->from('wx_messagemeta');
			$this->db->where('message_id', $message_id);
			$this->db->where('meta_key', 'video_thumb_id');
			$this->db->select('meta_value');
			$query=$this->db->get();
			$result = $query->result_array();

			if($result){
  				$this->video_thumb_id = $result[0]['meta_value'];
  			}
			return $this->video_thumb_id;
		}
		
		private function _set_video_thumb_id($message_id, $id){
			$data = array(
					'message_id' => $message_id,
					'meta_key'   => 'video_thumb_id',
					'meta_value' => $id
				);
			$this->db->insert('wx_messagemeta', $data);			

			$this->video_thumb_id = $id;
		}
	}
?>