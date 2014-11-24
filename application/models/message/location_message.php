<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	require_once APPPATH.'models/message/base_message.php';

	// 地理位置消息
	class Location_message extends Base_message
	{
		private $location_x; // 地理位置纬度 
		private $location_y; // 地理位置经度 
		private $scale;      // 地图缩放大小 
		private $label;      // 地理位置信息

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
			$this->location_x    = (string) trim($weixin_array['Location_X']);
			$this->location_y    = (string) trim($weixin_array['Location_Y']);
			$this->scale         = (string) trim($weixin_array['Scale']);
			$this->label         = (string) trim($weixin_array['Label']);
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
					'location_x'   => $this->location_x,
					'location_y'   => $this->location_y,
					'scale'	       => $this->scale,
					'label'        => $this->label
				);
			$this->set_location($array_subfix);
			return true;
		}

		public function get_location($message_id){
			$this->_get_location_x($message_id);
			$this->_get_location_y($message_id);
			$this->_get_scale($message_id);
			$this->_get_label($message_id);
			$data = array(
					'location_x'  => $this->location_x,
					'location_y'  => $this->location_y,
					'scale'       => $this->scale,
					'label'       => $this->label
				);			
			return $data;
		}

		public function set_location($array){
			$message_id        = $array['message_id'];
			$this->location_x  = $array['location_x'];
			$this->location_y  = $array['location_y'];
			$this->scale       = $array['scale'];
			$this->label       = $array['label'];

			$this->_set_location_x($message_id, $this->location_x);
			$this->_set_location_y($message_id, $this->location_y);
			$this->_set_scale($message_id, $this->scale);
			$this->_set_label($message_id, $this->label);
		}

		private function _get_location_x($message_id){
			$this->db->from('wx_messagemeta');
			$this->db->where('message_id', $message_id);
			$this->db->where('meta_key', 'location_x');
			$this->db->select('meta_value');
			$query=$this->db->get();
			$result = $query->result_array();

			if($result){			
  				$this->location_x = $result[0]['meta_value'];
  			}
			return $this->location_x;
		}

		private function _set_location_x($message_id ,$location){
			$data = array(
					'message_id' => $message_id,
					'meta_key'   => 'location_x',
					'meta_value' => $location
				);
			$this->db->insert('wx_messagemeta', $data);

			$this->location_x = $location;
		}

		private function _get_location_y($message_id){
			$this->db->from('wx_messagemeta');
			$this->db->where('message_id', $message_id);
			$this->db->where('meta_key', 'location_y');
			$this->db->select('meta_value');
			$query=$this->db->get();
			$result = $query->result_array();	

			if($result){		
  				$this->location_y = $result[0]['meta_value'];
  			}
			return $this->location_y;
		}

		private function _set_location_y($message_id, $location){
			$data = array(
					'message_id' => $message_id,
					'meta_key'   => 'location_y',
					'meta_value' => $location
				);
			$this->db->insert('wx_messagemeta', $data);

			$this->location_y = $location;
		}

		private function _get_scale($message_id){
			$this->db->from('wx_messagemeta');
			$this->db->where('message_id', $message_id);
			$this->db->where('meta_key', 'scale');
			$this->db->select('meta_value');
			$query=$this->db->get();
			$result = $query->result_array();

			if($result){			
  				$this->scale = $result[0]['meta_value'];
  			}
			return $this->scale;
		}

		private function _set_scale($message_id, $grade){
			$data = array(
					'message_id' => $message_id,
					'meta_key'   => 'scale',
					'meta_value' => $grade
				);
			$this->db->insert('wx_messagemeta', $data);

			$this->scale = $grade;
		}

		private function _get_label($message_id){
			$this->db->from('wx_messagemeta');
			$this->db->where('message_id', $message_id);
			$this->db->where('meta_key', 'label');
			$this->db->select('meta_value');
			$query=$this->db->get();
			$result = $query->result_array();	

			if($result){		
  				$this->label = $result[0]['meta_value'];
  			}
			return $this->label;
		}

		private function _set_label($message_id, $text){
			$data = array(
					'message_id' => $message_id,
					'meta_key'   => 'label',
					'meta_value' => $text
				);
			$this->db->insert('wx_messagemeta', $data);

			$this->label = $text;
		}
	}
?>