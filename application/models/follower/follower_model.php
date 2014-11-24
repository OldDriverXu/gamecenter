<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	class Follower_model extends CI_Model
	{
		public function __construct(){
			parent::__construct();
			$this->load->database();
			require_once APPPATH.'libraries/common/GlobalFunctions.php';
		}

		public function get_follower($username){
			$this->db->from('wx_followers');
			$this->db->where('follower_username', $username);
			$query = $this->db->get();
			$result = $query->result_array();

			if($result){
				return $result[0];
			}else{
				return NULL;
			}
		}

		/* $array = array(
		|		'follower_username'            => $follower_username,
		|		'follower_subscribe_date'      => date('Y-m-d H:i:s', $follower_subscribe_timestamp),
		|		'follower_subscribe_timestamp' => $follower_subscribe_timestamp,
		|		'follower_nickname'            => $follower_nickname,
		|		'follower_tel'                 => $follower_tel,
		|		'follower_headimgurl'          => $follower_headimgurl,
		|		'follower_sex'                 => $follower_sex,
		|		'follower_city'                => $follower_city,
		|		'follower_province'            => $follower_province,
		|		'follower_country'             => $follower_country,
		|		'follower_group'               => $follower_group,
		|		'follower_status'              => $follower_status
		|	);
		*/
		public function set_follower($array){
			foreach ($array as $key => $value) {
				if ($value){
					$data[$key] = $value;
				}
			}
			$this->db->insert('wx_followers', $data);
		}

		/* $array = array(
		|		'follower_username'            => $follower_username,
		|		'follower_subscribe_date'      => date('Y-m-d H:i:s', $follower_subscribe_timestamp),
		|		'follower_subscribe_timestamp' => $follower_subscribe_timestamp,
		|		'follower_nickname'            => $follower_nickname,
		|		'follower_tel'                 => $follower_tel,
		|		'follower_headimgurl'          => $follower_headimgurl,
		|		'follower_sex'                 => $follower_sex,
		|		'follower_city'                => $follower_city,
		|		'follower_province'            => $follower_province,
		|		'follower_country'             => $follower_country,
		|		'follower_group'               => $follower_group,
		|		'follower_status'              => $follower_status
		|	);
		*/
		public function update_follower($array){
			foreach ($array as $key => $value) {
				if ($value){
					$data[$key] = $value;
					//$this->db->set($key, $value);
				}
			}
			$this->db->where('follower_username', $data['follower_username']);
			$this->db->update('wx_followers', $data);
		}

		public function delete_follower($username){
			$this->db->where('follower_username', $username);
			$this->db->delete('wx_followers');
		}

		public function get_follower_list($access_token,$next_openid=''){
			$get_url     = 'https://api.weixin.qq.com/cgi-bin/user/get';
			$curl_string = "?access_token=".$access_token."&next_openid=".$next_openid;
			$curl_url    = $get_url.$curl_string;

			$curl_result = doCurlGetRequest($curl_url);
			return objectToArray(json_decode($curl_result));
		}

		public function get_followers_list($access_token){
			$list = $this->get_follower_list($access_token);
			$data = $list['data']['openid'];
			$next_openid = $list['next_openid'];

			while ($next_openid){
				$temp = $this->get_follower_list($access_token, $next_openid);
				$merge = $temp['data']['openid'];
				if($merge){
					$data = array_merge($data, $merge);
				}
				$next_openid = $temp['next_openid']; 
			}
			return $data;
		}

		public function get_follower_info($access_token, $username){
			$get_url     = 'https://api.weixin.qq.com/cgi-bin/user/info';
			$curl_string = "?access_token=".$access_token."&openid=".$username."&lang=zh_CN";
			$curl_url    = $get_url.$curl_string;

			$curl_result = doCurlGetRequest($curl_url);	
			$data =  objectToArray(json_decode($curl_result));
			$result = array(
					'follower_username' => $data['openid'],
					'follower_subscribe' => $data['subscribe'],
					'follower_subscribe_timestamp' => $data['subscribe_time'],
					'follower_nickname' => $data['nickname'],
					'follower_headimgurl' => $data['headimgurl'], 
					'follower_sex'      => $data['sex'],
					'follower_city'     => $data['city'],
					'follower_province' => $data['province'],
					'follower_country'  => $data['country']
				);
			return $result;
		}

		/**
		* @param $count 查询的记录数(limit)
		* @param $shift 查询的偏移(offset)
		*/ 
		public function get_followers($count, $shift=0){
			$query = $this->db->get('wx_followers', $count, $shift);
			$result = $query->result_array();
			return $result;
		}
		public function get_all_followers(){
			$query = $this->db->get('wx_followers');
			$result = $query->result_array();
			return $result;
		}
		//获取分页数据
		public function get_pagination($num='',$offset=''){
			$this->db->limit($num, $offset);
			$this->db->order_by('ID');
			$query = $this->db->get('wx_followers');
			$result = $query->result_array();
			return $result;
		}
	}
?>