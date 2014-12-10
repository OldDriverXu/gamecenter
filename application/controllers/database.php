<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class Database extends CI_Controller
    {
        public function __construct(){
            parent::__construct();
            $this->load->dbforge();
        }

        public function index(){
            //wx_message
            $fields1 = array(
                'id'=> array(
                    'type' => 'INT',
                    'constraint' => '8',
                    'auto_increment' => TRUE),
                'message_username' => array(
                    'type' =>'VARCHAR',
                    'constraint' => '32'),
                'message_date' => array(
                    'type' => 'DATETIME'),
                'message_date_timestamp' => array(
                    'type' => 'INT',
                    'constraint'=> '8'),
                'message_content' => array(
                    'type' => 'TEXT'),
                'message_title' => array(
                    'type' => 'TEXT'),
                'message_excerpt' => array(
                    'type' => 'TEXT'),
                'message_type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '8',
                    'default' => 'text'),
                'message_status' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '16',
                    'default' => '0')
                );
            $this->dbforge->add_field($fields1);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('wx_message', TRUE);

            //wx_messagemeta
            $fields2 = array(
                'meta_id' => array(
                    'type' => 'INT',
                    'constraint' => '8',
                    'auto_increment' => TRUE),
                'message_id' => array(
                    'type' => 'INT',
                    'constraint' => '8'),
                'meta_key' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '32'),
                'meta_value' => array(
                    'type' => 'TEXT')
                );
            $this->dbforge->add_field($fields2);
            $this->dbforge->add_key('meta_id', TRUE);
            $this->dbforge->create_table('wx_messagemeta', TRUE);

            //wx_article
            $fields3 = array(
                'article_id'=> array(
                    'type' => 'INT',
                    'constraint' => '8',
                    'auto_increment' => TRUE),
                'news_type'=>array(
                    'type'=> 'VARCHAR',
                    'constraint'=> '16'),
                'news_id' => array(
                    'type' =>'INT',
                    'constraint' => '8'),
                'article_order' => array(
                    'type' => 'TINYINT',
                    'constraint'=> '3'),
                'article_title' => array(
                    'type' => 'TEXT'),
                'article_description' => array(
                    'type' => 'TEXT'),
                'article_pic_url' => array(
                    'type' => 'TEXT'),
                'article_url' => array(
                    'type' => 'LONGTEXT'),
                'url_tracking' => array(
                    'type' => 'TINYINT',
                    'constraint' => '3',
                    'default' => 1)
                );
            $this->dbforge->add_field($fields3);
            $this->dbforge->add_key('article_id', TRUE);
            $this->dbforge->create_table('wx_article', TRUE);

            //wx_followers
            $fields4 = array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => '8',
                    'auto_increment' => TRUE),
                'follower_username' => array(
                    'type'=> 'VARCHAR',
                    'constraint' => '32'),
                'follower_subscribe_date' => array(
                    'type' => 'DATETIME'),
                'follower_subscribe_timestamp' => array(
                    'type' => 'INT',
                    'constraint'=> '8'),
                'follower_nickname' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '50'),
                'follower_tel' => array(
                    'type' => 'BIGINT',
                    'constraint' => '20'),
                'follower_headimgurl' => array(
                    'type' => 'LONGTEXT'),
                'follower_sex' => array(
                    'type'=> 'TINYINT',
                    'constraint' => '3'),
                'follower_province' => array(
                    'type' => 'text'),
                'follower_city' => array(
                    'type' => 'text'),
                'follower_country' => array(
                    'type' => 'text'),
                'follower_group' => array(
                    'type' => 'text'),
                'follower_status' => array(
                    'type' => 'text')
                );
            $this->dbforge->add_field($fields4);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('wx_followers', TRUE);

            //wx_openid
            $fields41 = array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => '8',
                    'auto_increment' => TRUE),
                'openid' => array(
                    'type'=> 'VARCHAR',
                    'constraint' => '32'),
                'unionid' => array(
                    'type'=> 'VARCHAR',
                    'constraint' => '32')
                );
            $this->dbforge->add_field($fields41);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('wx_unionid', TRUE);

            //wx_options
            $fields5 = array(
                'option_id' => array(
                    'type' => 'INT',
                    'constraint' => '8',
                    'auto_increment' => TRUE),
                'option_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '64'),
                'option_value' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '64')
                );
            $this->dbforge->add_field($fields5);
            $this->dbforge->add_key('option_id', TRUE);
            $this->dbforge->create_table('wx_options', TRUE);

            //wx_autoreply
            $fields6 = array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => '8',
                    'auto_increment' => TRUE),
                'autoreply_keyword' => array(
                    'type' => 'TEXT'),
                'autoreply_content' => array(
                    'type' => 'LONGTEXT'),
                'autoreply_title' => array(
                    'type' => 'TEXT'),
                'autoreply_excerpt' => array(
                    'type' => 'TEXT'),
                'autoreply_type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '8'),
                'autoreply_status' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '16')
                );
            $this->dbforge->add_field($fields6);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('wx_autoreply', TRUE);
            $fields5_params = array(
                array(
                    'autoreply_keyword' => '感恩节',
                    'autoreply_content' => '你好！感恩节就要到了',
                    'autoreply_type'  => 'text'
                    )
                );
            $this->db->insert_batch('wx_autoreply',$fields5_params);

            //game_log
            $fields7 = array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => '8',
                    'auto_increment' => TRUE),
                'username' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '32'),
                'game_id' => array(
                    'type' => 'INT',
                    'constraint' => '8'),
                'score_type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '16'),
                'score_value' => array(
                    'type' => 'INT',
                    'constraint' => '8'),
                'login_date' => array(
                    'type' => 'DATETIME'),
                'login_ip' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '16'),
                'login_ua' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '120'),
                'from_channel' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '32'),
                'from_username' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '32')
                );
            $this->dbforge->add_field($fields7);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('game_log', TRUE);

            //game
            $fields8 = array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => '8',
                    'auto_increment' => TRUE),
                'title' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '32'),
                'description' => array(
                    'type' => 'TEXT'),
                'url' => array(
                    'type' => 'LONGTEXT'),
                'author' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '20'),
                'pic' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100'),
                'pubtime' => array(
                    'type' => 'DATETIME'),
                'orderlist' => array(
                    'type' => 'INT',
                    'constraint' => '3'),
                'hot' => array(
                    'type' => 'INT',
                    'constraint' => '8')
                );
            $this->dbforge->add_field($fields8);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('game', TRUE);
        }
    }
?>