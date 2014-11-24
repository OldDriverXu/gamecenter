<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	class Base_reply extends CI_Model
	{
		public $to_username;    // 接收方账号（一个OpenID）
		public $from_username;  // 发送方账号（一个OpenID）
		public $create_time;    // 消息创建时间 （整型）
		public $rly_type;       // 消息类型（text/image/location/link等）				

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
		public function get_rly_type(){
			return $this->rly_type;
		}
		public function set_rly_type($type){
			$this->rly_type = $type;
		}

		/* $array = array(
		| 		'autoreply_keyword'  => $autoreply_keyword,
		| 		'autoreply_content'  => $autoreply_content,
		| 		'autoreply_title'    => $autoreply_title,
		|		'autoreply_excertpt' => $autoreply_excertpt,
		|		'autoreply_type'     => $autoreply_type,
		| 		'autoreply_status'   => $autoreply_status 		
		| 	);
		*/
		public function get_reply($array){
			foreach ($array as $key => $value) {
				if ($value){
					$data[$key] = $value;
				}				
			}			 
			$this->db->from('wx_autoreply');
			$this->db->where($data);
			$query = $this->db->get();
			$query = $query->result_array();			
			return $query;
		}
		public function get_all_reply(){
			$query = $this->db->get('wx_autoreply');
			$query = $query->result_array();
			return $query;
		}

		/* $array = array(
		| 		'autoreply_keyword'  => $autoreply_keyword,
		| 		'autoreply_content'  => $autoreply_content,
		| 		'autoreply_title'    => $autoreply_title,
		|		'autoreply_excertpt' => $autoreply_excertpt,
		|		'autoreply_type'     => $autoreply_type,
		| 		'autoreply_status'   => $autoreply_status 		
		| 	);
		*/
		public function set_reply($array){
			foreach ($array as $key => $value) {
				if ($value){
					$data[$key] = trim($value);
				}				
			}			
			$this->db->insert('wx_autoreply', $data); 
			return $this->db->insert_id();
		}

		public function update_reply($array, $id){
			foreach ($array as $key => $value) {
				if ($value){
					$data[$key] = trim($value);
				}				
			}
			$this->db->where('id', $id);
			$this->db->update('wx_autoreply', $data);
		}
		public function update_reply_by_keyword($array, $keyword){
			foreach ($array as $key => $value) {
				if ($value){
					$data[$key] = trim($value);
				}				
			}
			$this->db->where('autoreply_keyword', $keyword);
			$this->db->update('wx_autoreply', $data);			
		}

		/* $array = array(
		|		'ID'				 => $ID, 	
		| 		'autoreply_content'  => $autoreply_content,
		| 		'autoreply_title'    => $autoreply_title,
		|		'autoreply_excertpt' => $autoreply_excertpt,
		|		'autoreply_type'     => $autoreply_type,
		| 		'autoreply_status'   => $autoreply_status 		
		| 	);
		*/
		public function update_reply_keyword($array,$keyword){
			foreach ($array as $key => $value) {
				if ($value){
					$data[$key] = $value;
				}				
			}			
			$this->db->where($data);
			$this->db->update('wx_autoreply', array('autoreply_keyword'=>$keyword)); 
		}
		//根据关键词获取对象
		public function get_autoreply_by_key($key){
			$this->db->where('autoreply_keyword', $key);
			$query = $this->db->get('wx_autoreply');
			$result = $query->result_array();
			return $result[0];
		}
		//删除自动回复及相关的信息
		public function dele_reply($id){

			$this->db->where_in('id', $id);
			$this->db->delete('wx_autoreply');

			$this->db->where_in('autoreply_id', $id);
			$this->db->delete('wx_autoreplymeta');

			$this->db->where('news_type', 'auto_reply');
			$this->db->where_in('news_id', $id);
			$this->db->delete('wx_article');
		}
		//获取自动回复，去掉欢迎语、下班回复、自定义菜单
		public function get_min_auto_reply(){
			//select * from wx_autoreply where autoreply_keyword not in('welcome_reply','off_duty_reply') and autoreply_keyword not like('selfmenu-%');
			$keyword = array('welcome_reply', 'off_duty_reply');
			$this->db->where_not_in('autoreply_keyword', $keyword);
			$this->db->not_like('autoreply_keyword', 'selfmenu');
			$query = $this->db->get('wx_autoreply');
			$result = $query->result_array();
			return $result;
		}
	}
?>