<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	require_once APPPATH.'models/autoreply/base_reply.php';

	//文本回复
	class Text_reply extends Base_reply
	{
		private $content;    // 消息内容  

		public function __construct(){
			parent::__construct();		
			require_once APPPATH.'libraries/common/GlobalFunctions.php';
		}

		public function init($array){
			$ID = $array['id'];
			$data = array(
				'autoreply_type'    => trim($array['autoreply_type']),
				'autoreply_keyword'    => trim($array['autoreply_keyword']),
				'autoreply_content' => trim($array['autoreply_content'])

			);
			$this->update_reply($data, $ID);
		}
		// $array = array(
		// 		'autoreply_to_username' =>,
		// 		'autoreply_from_username' =>,
		// 		'autoreply_type'=>,
		// 		'autoreply_content' =>
		// 	)
		public function create_xml($array){
			$xml_template = '<xml>
		                        <ToUserName><![CDATA[%s]]></ToUserName>
		                        <FromUserName><![CDATA[%s]]></FromUserName>
		                        <CreateTime>%s</CreateTime>
		                        <MsgType><![CDATA[%s]]></MsgType>
		                        <Content><![CDATA[%s]]></Content>
		                    </xml>';
		    return sprintf($xml_template, $array['autoreply_to_username'], $array['autoreply_from_username'], getTime(), $array['autoreply_type'],  $array['autoreply_content']);
		}

		public function get_text_content(){
			return $this->content;
		}
		
		public function set_text_content($text){
			$this->content = $text;
		}

		public function update_text_content($ID, $content){
			$data = array(
				'id' => $ID,
				'autoreply_type' => 'text'
				);
			$this->db->where($data);
			$this->db->update('wx_autoreply', array('autoreply_content'=>trim($content)));
		}
	}
?>