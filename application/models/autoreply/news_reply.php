<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	require_once APPPATH.'models/autoreply/base_reply.php';

	//图文消息
	class News_reply extends Base_reply
	{	
		private $article_count;            // 图文消息个数，限制为10条以内
		private $article_title;  		   // 图文消息的标题
		private $article_description;      // 图文消息的描述
		private $article_pic_url;          // 图片链接，支持JPG、PNG格式，较好的效果为大图640*320，小图80*80，限制图片链接的域名需要与开发者填写的基本资料中的Url一致  
		private $article_url;              // 点击图文消息跳转链接
		private $news_array;			   // 图文消息数组

		public function __construct(){		
			parent::__construct();	    		          	        
		}

		/*  $array = array(
		 		'autoreply_to_username'    =>,
		 		'autoreply_from_username'  =>,
		 		'autoreply_type'=>,
		 		'autoreply_content'=> array(
		 			'0' => array(
							'article_order' => '1',
				 			'article_title' => "xxxx",
				 			'article_description' => "xxxx",
				 			'article_url' => 'xxxxx',
				 			'article_pic_url' => 'xxxxx'
 				  	),
				 	'1' =>array(
							'article_order' => '2',
				            'article_title' => "xxxx",
				 			'article_description' => "xxxx",
				 			'article_url' => 'xxxxx',
				 			'article_pic_url' => 'xxxxx'
				 	)
		 		)
		 	);
		*/
		public function init($array){
			$data = array(
				'autoreply_type'=> 'news',
				'autoreply_keyword' => $array['autoreply_keyword']
			);
			$ID = $array['id'];
			$news_type = $array['news_type'];
			$this -> update_reply($data, $ID);
			$articles = $array['articles'];
			$data_count = count($articles);

			for($i=0;$i<$data_count;$i++){
				$articles[$i]['news_id'] = $ID;
			}

			//清空图文
			$delete_array = array(
					'news_id' => $ID,
					'news_type' => $news_type
				);

			$this->db->delete('wx_article', $delete_array);
			for($i=0;$i<$data_count;$i++){
				$this->set_article($articles[$i]);
			}
		}
		public function create_xml($array){
			$article_template= '<item>
									<Title><![CDATA[%s]]></Title>
						            <Description><![CDATA[%s]]></Description>
						            <PicUrl><![CDATA[%s]]></PicUrl>
						            <Url><![CDATA[%s]]></Url>
								</item>';
			$this->news_array = $array['autoreply_content'];			
			$this->article_count = count($this->news_array);
			$articles = '';
			foreach ($this->news_array as $article) {
				$articles .= sprintf($article_template, $article['title'], $article['description'], $article['picurl'], $article['url']);
			}
			$time = time();

			return <<<EOT
				<xml>
					<ToUserName><![CDATA[{$array['autoreply_to_username']}]]></ToUserName>
					<FromUserName><![CDATA[{$array['autoreply_from_username']}]]></FromUserName>
					<CreateTime>{$time}</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<ArticleCount>{$this->article_count}</ArticleCount>
					<Articles>
						$articles
					</Articles>
				</xml>
EOT;
		}

		public function get_news($news_id, $news_type, $to_username){
			$this->db->from('wx_article');
			$this->db->where('news_id', $news_id);
			$this->db->where('news_type', $news_type);
			$this->db->order_by('article_order', 'asc');
			$query = $this->db->get();
			$result = $query->result_array();

			if($result){
				for ($i=0; $i<count($result);$i++) {
					$data[$i]['title']       = $result[$i]['article_title'];
					$data[$i]['description'] = $result[$i]['article_description'];
					if ($result[$i]['url_tracking']) {
						$data[$i]['url'] = $result[$i]['article_url']. '?uid='. $to_username;
					}else{
						$data[$i]['url'] = $result[$i]['article_url'];
					}
					$data[$i]['picurl']      = $result[$i]['article_pic_url'];
					$data[$i]['order']      = $result[$i]['article_order'];
				}
				$this->news_array = $data;
			}else{
				$this->news_array = NULL;
			}
			return $this->news_array;
		}

		public function get_article($article_id){
			$this->db->from('wx_article');
			$this->db->where('article_id', $article_id);
			$query = $this->db->get();
			$result = $query->result;

			if($result){
				return $result;
			}else{
				return NULL;
			}
		}

		/* $array = array(
		|		'news_id'                => $news_id,
		|		'article_order'          => $article_order,
		|		'article_title'          => $article_title,
		|		'article_description'    => $article_description,
		|		'article_pic_url'        => $article_pic_url,
		|		'article_url'            => $article_url,
		|		'url_tracking'           => $url_tracking
		|	);
		*/
		public function set_article($array){
			foreach ($array as $key => $value) {
				if ($value){
					$data[$key] = trim($value);
				}				
			}		
			$this->db->insert('wx_article', $data);
		}

		/* $array = array(
		|		'article_id'             => $article_id,
		|		'news_id'                => $news_id,
		|		'article_order'          => $article_order,
		|		'article_title'          => $article_title,
		|		'article_description'    => $article_description,
		|		'article_pic_url'        => $article_pic_url,
		|		'article_url'            => $article_url,
		|		'url_tracking'           => $url_tracking
		| 	);
		*/
		public function update_article($array){
			foreach ($array as $key => $value) {
				if ($value){
					$data[$key] = trim($value);
					//$this->db->set($key, $value);
				}
			}
			$this->db->where('article_id', $data['article_id']);
			$this->db->update('wx_article', $data);
		}

		public function delete_article($article_id){
			$this->db->where('article_id', $article_id);
			$this->db->delete('wx_article');
		}
		public function delete_article_by_news_id($news_id){
			$this->db->where('news_id',$news_id);
			$this->db->where('news_type','auto_reply');
			$this->db->delete('wx_article');
		}

		public function get_article_count(){
			return $this->article_count;
		}
		
		public function set_article_count($count){
			$this->article_count = $count;
		}
	}
?>