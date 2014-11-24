<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	class Base_message extends CI_Model
	{
		public $to_username;    //接收方账号（一个OpenID）
		public $from_username;  //发送方账号（一个OpenID）
		public $create_time;     // 消息创建时间 （整型）
		public $msg_type;        // 消息类型（text/image/music/news等）
		public $msg_id;          // 消息id，64位整型  

		public function __construct(){
			parent::__construct();
			$this->load->database();
		}

		//基础方法
		public function get_to_username(){
			return $this->to_username;
		}
		public function set_to_username($username){
			$this->to_username = $username;
		}
		public function get_from_username(){
			return $this->from_username;
		}
		public function set_from_user_name($username){
			$this->from_username = $username;
		}
		public function get_create_time(){
			return $this->create_time;
		}
		public function set_create_time($time){
			$this->create_time = $time;
		}
		public function get_msg_type(){
			return $this->msg_type;
		}
		public function set_msg_type($type){
			$this->msg_type = $type;
		}
		public function get_msg_id(){
			return $this->msg_id;
		}
		public function set_msg_id($id){
			$this->msg_id = $id;
		}

		/* $array = array(
		|		'message_username'       => $message_username,
		|		'message_date'           => date('Y-m-d H:i:s', $message_timestamp),
		|		'message_date_timestamp' => $message_timestamp,
		|		'message_content'        => $message_content,
		|		'message_title'          => $message_title,
		|		'message_excerpt'        => $message_excerpt,
		|		'message_type'           => $message_type,
		|		'message_status'         => $message_status
		|	);
		*/
		public function get_message($array){
			foreach ($array as $key => $value) {
				if ($value){
					$data[$key] = $value;
				}
			}			 
			$this->db->from('wx_message');
			$this->db->where($data);
			$query = $this->db->get();
			$query = $query->result_array();			
			return $query;
		}

		/* $array = array(
		|		'message_username'       => $message_username,
		|		'message_date'           => date('Y-m-d H:i:s', $message_timestamp),
		|		'message_content'        => $message_content,
		|		'message_title'          => $message_title,
		|		'message_excerpt'        => $message_excerpt,
		|		'message_type'           => $message_type,
		|		'message_status'         => $message_status
		|	);
		*/
		public function set_message($array){
			foreach ($array as $key => $value) {
				if ($value){
					$data[$key] = $value;
				}
			}
			$this->db->insert('wx_message', $data);
			//这个id号是执行数据插入时的id。 
			return $this->db->insert_id();
		}

		public function update_message($array){
			foreach ($array as $key => $value) {
				if ($value){
					$data[$key] = $value;
				}				
			}
			$this->db->where('id', $data['id']);
			$this->db->update('wx_message', $data);
		}
	}
?>