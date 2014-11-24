<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	require_once APPPATH.'models/autoreply/base_reply.php';

	//音乐消息
	class Music_reply extends Base_reply
	{	
		
		private $music_title;  		  // 音乐标题
		private $music_description;     // 音乐描述
		private $music_url;       // 音乐链接
		private $hqmusic_url;     //高质量音乐链接，WIFI环境优先使用该链接播放音乐
		

		public function __construct(){		
			parent::__construct();
		}

		/*$array = array(
				'autoreply_to_username'    =>,
		 		'autoreply_from_username'  =>,
		 		'autoreply_type'=>,
		 		'autoreply_content'=> array(
		 				'music_title' =>,
		 				'music_description' =>,
		 				'music_url'=>,
		 				'hqmusic_url'=>
		 			)
			);
		*/
		public function init($array){
			$data = array(
					'autoreply_type'=> 'music',
					'autoreply_keyword' => $array['autoreply_keyword']			
			);
			$ID = $array['id'];
			//更新wx_autoreply
			$this->update_reply($data,$ID);
			$music = $array['music'];
			$update_array = array(
				'autoreply_id'      => trim($ID),
				'music_title'       => trim($music['music_title']),
				'music_description' => trim($music['music_description']),
				'music_url'         => trim($music['music_url']),
				'hqmusic_url'       => trim($music['hqmusic_url'])
			);
			//更新wx_autoreplymeta
			$this->update_music($update_array);
		}
		public function create_xml($array){
			$xml_template='<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[%s]]></MsgType>
								<Music>
									<Title><![CDATA[%s]]></Title>
									<Description><![CDATA[%s]]></Description>
									<MusicUrl><![CDATA[%s]]></MusicUrl>
									<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
								</Music>
							</xml>';
			return sprintf($xml_template, $array['autoreply_to_username'], $array['autoreply_from_username'], time(), $array['autoreply_type'], $array['autoreply_content']['music_title'], $array['autoreply_content']['music_description'], $array['autoreply_content']['music_url'], $array['autoreply_content']['hqmusic_url']);
		}

		public function get_music($autoreply_id){
			$this->_get_music_title($autoreply_id);
			$this->_get_music_description($autoreply_id);
			$this->_get_music_url($autoreply_id);
			$this->_get_hqmusic_url($autoreply_id);
			$data = array(
					'music_title'        => $this->music_title,
					'music_description'  => $this->music_description,
					'music_url'          => $this->music_url,
					'hqmusic_url'        => $this->hqmusic_url
				);
			return $data;
		}

		public function set_music($array){
			$autoreply_id             = $array['autoreply_id'];
			$this->music_title        = $array['music_title'];
			$this->music_description  = $array['music_description'];
			$this->music_url          = $array['music_url'];
			$this->hqmusic_url        = $array['hqmusic_url'];

			$this->_set_music_title($autoreply_id, $this->music_title);
			$this->_set_music_description($autoreply_id, $this->music_description);
			$this->_set_music_url($autoreply_id, $this->music_url);
			$this->_set_hqmusic_url($autoreply_id, $this->hqmusic_url);
		}

		public function update_music($array){

			$autoreply_id             = $array['autoreply_id'];
			$this->music_title        = $array['music_title'];
			$this->music_description  = $array['music_description'];
			$this->music_url          = $array['music_url'];
			$this->hqmusic_url        = $array['hqmusic_url'];

			$this->_update_music_title($autoreply_id, $this->music_title);
			$this->_update_music_description($autoreply_id, $this->music_description);
			$this->_update_music_url($autoreply_id, $this->music_url);
			$this->_update_hqmusic_url($autoreply_id, $this->hqmusic_url);
		}

		private function _get_music_title($autoreply_id){
			$this->db->from('wx_autoreplymeta');
			$this->db->where('autoreply_id', $autoreply_id);
			$this->db->where('meta_key', 'music_title');
			$this->db->select('meta_value');
			$query=$this->db->get();
			$result = $query->result_array();

			if($result){			
  				$this->music_title = $result[0]['meta_value'];
  			}
			return $this->music_title;
		}
		
		private function _set_music_title($autoreply_id, $text){
			$data = array(
					'autoreply_id' => $autoreply_id,
					'meta_key'   => 'music_title',
					'meta_value' => $text
				);
			$this->db->insert('wx_autoreplymeta', $data);

			$this->music_title = $text;
		}

		private function _update_music_title($autoreply_id, $music_title){
			$data = array(
					'autoreply_id' => $autoreply_id,
					'meta_key'   => 'music_title'					
				);
			$this->db->where($data);
			$this->db->update('wx_autoreplymeta', array('meta_value'=>$music_title)); 
		}

		private function _get_music_description($autoreply_id){
			$this->db->from('wx_autoreplymeta');
			$this->db->where('autoreply_id', $autoreply_id);
			$this->db->where('meta_key', 'music_description');
			$this->db->select('meta_value');
			$query=$this->db->get();
			$result = $query->result_array();	

			if($result){		
  				$this->music_description = $result[0]['meta_value'];
  			}
			return $this->music_description;
		}
		
		private function _set_music_description($autoreply_id, $text){
			$data = array(
					'autoreply_id' => $autoreply_id,
					'meta_key'   => 'music_description',
					'meta_value' => $text
				);
			$this->db->insert('wx_autoreplymeta', $data);

			$this->music_description = $text;
		}

		private function _update_music_description($autoreply_id, $music_description){
			$data = array(
					'autoreply_id' => $autoreply_id,
					'meta_key'     => 'music_description'
				);
			$this->db->where($data);
			$this->db->update('wx_autoreplymeta', array('meta_value'=>$music_description));
		}

		private function _get_music_url($autoreply_id){
			$this->db->from('wx_autoreplymeta');
			$this->db->where('autoreply_id', $autoreply_id);
			$this->db->where('meta_key', 'music_url');
			$this->db->select('meta_value');
			$query=$this->db->get();
			$result = $query->result_array();	

			if($result){		
  				$this->music_url = $result[0]['meta_value'];
  			}
			return $this->music_url;
		}
		
		private function _set_music_url($autoreply_id, $url){
			$data = array(
					'autoreply_id' => $autoreply_id,
					'meta_key'   => 'music_url',
					'meta_value' => $url
				);
			$this->db->insert('wx_autoreplymeta', $data);

			$this->music_url = $url;
		}

		private function _update_music_url($autoreply_id, $url){
			$data = array(
					'autoreply_id' => $autoreply_id,
					'meta_key'     => 'music_url'
				);
			$this->db->where($data);
			$this->db->update('wx_autoreplymeta', array('meta_value'=>$url));
		}

		private function _get_hqmusic_url($autoreply_id){
			$this->db->from('wx_autoreplymeta');
			$this->db->where('autoreply_id', $autoreply_id);
			$this->db->where('meta_key', 'hqmusic_url');
			$this->db->select('meta_value');
			$query=$this->db->get();
			$result = $query->result_array();

			if($result){			
  				$this->hqmusic_url = $result[0]['meta_value'];
  			}
			return $this->hqmusic_url;
		}
		
		private function _set_hqmusic_url($autoreply_id, $url){
			$data = array(
					'autoreply_id' => $autoreply_id,
					'meta_key'   => 'hqmusic_url',
					'meta_value' => $url
				);
			$this->db->insert('wx_autoreplymeta', $data);
			
			$this->hqmusic_url = $url;
		}

		private function _update_hqmusic_url($autoreply_id, $url){
			$data = array(
					'autoreply_id' => $autoreply_id,
					'meta_key'     => 'hqmusic_url'
				);
			$this->db->where($data);
			$this->db->update('wx_autoreplymeta', array('meta_value'=>$url));
		}
		public function is_have_data($autoreply_id){
			$this->db->where('autoreply_id',$autoreply_id);
			$result = $this->db->get('wx_autoreplymeta');
			$result = $result->result_array();
			return count($result);
		}
	}
?>