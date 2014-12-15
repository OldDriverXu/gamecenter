<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class Database extends CI_Controller
    {
        public function __construct(){
            parent::__construct();
            $this->load->dbforge();
        }

        public function index(){
            echo "DB Initialization .....\n";
            echo "<br/>";
            //wx_message
            $fields_wx_message = array(
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
            $this->dbforge->add_field($fields_wx_message);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('wx_message', TRUE);
            echo "Create Table wx_message \n";
            echo "<br/>";

            //wx_messagemeta
            $fields_wx_messagemeta = array(
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
            $this->dbforge->add_field($fields_wx_messagemeta);
            $this->dbforge->add_key('meta_id', TRUE);
            $this->dbforge->create_table('wx_messagemeta', TRUE);
            echo "Create Table wx_messagemeta \n";
            echo "<br/>";

            //wx_article
            $fields_wx_article = array(
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
            $this->dbforge->add_field($fields_wx_article);
            $this->dbforge->add_key('article_id', TRUE);
            $this->dbforge->create_table('wx_article', TRUE);
            echo "Create Table wx_article \n";
            echo "<br/>";

            //wx_followers
            $fields_wx_followers = array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => '8',
                    'auto_increment' => TRUE),
                'follower_username' => array(
                    'type'=> 'VARCHAR',
                    'constraint' => '32'),
                'follower_unionid' => array(
                    'type' => 'VARCHAR',
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
            $this->dbforge->add_field($fields_wx_followers);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('wx_followers', TRUE);
            echo "Create Table wx_followers \n";
            echo "<br/>";

            //wx_followers_relationship
            $fields_wx_followers_relationship = array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => '8',
                    'auto_increment' => TRUE),
                'source_username' => array(
                    'type'=> 'VARCHAR',
                    'constraint' => '32'),
                'source_unionid' => array(
                    'type'=> 'VARCHAR',
                    'constraint' => '32'),
                'end_username' => array(
                    'type'=> 'VARCHAR',
                    'constraint' => '32'),
                'end_unionid' => array(
                    'type'=> 'VARCHAR',
                    'constraint' => '32')
                );
            $this->dbforge->add_field($fields_wx_followers_relationship);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('wx_followers_relationship', TRUE);
            echo "Create Table wx_followers_relationship \n";
            echo "<br/>";

            //wx_options
            $fields_wx_options = array(
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
            $this->dbforge->add_field($fields_wx_options);
            $this->dbforge->add_key('option_id', TRUE);
            $this->dbforge->create_table('wx_options', TRUE);
            echo "Create Table wx_options \n";
            echo "<br/>";

            //wx_autoreply
            $fields_wx_autoreply = array(
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
            $this->dbforge->add_field($fields_wx_autoreply);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('wx_autoreply', TRUE);
            echo "Create Table wx_autoreply \n";
            echo "<br/>";
            // $fields5_params = array(
            //     array(
            //         'autoreply_keyword' => '感恩节',
            //         'autoreply_content' => '你好！感恩节就要到了',
            //         'autoreply_type'  => 'text'
            //         )
            //     );
            // $this->db->insert_batch('wx_autoreply',$fields5_params);

            //game_log
            $fields_game_log = array(
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
                'game_time' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '50'),
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
            $this->dbforge->add_field($fields_game_log);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('game_log', TRUE);
            echo "Create Table game_log \n";
            echo "<br/>";

            //game_high_score
            $fields_high_score = array(
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
                'high_score' => array(
                    'type' => 'INT',
                    'constraint' => '8'),
                'create_time' => array(
                    'type' => 'DATETIME')
                );
            $this->dbforge->add_field($fields_high_score);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('game_high_score', TRUE);
            echo "Create Table game_high_score \n";
            echo "<br/>";

            //game_invite_score
            $fields_invate_score = array(
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
                'invite_score' => array(
                    'type' => 'INT',
                    'constraint' => '8'),
                'create_time' => array(
                    'type' => 'DATETIME')
                );
            $this->dbforge->add_field($fields_invate_score);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('game_invite_score', TRUE);
            echo "Create Table game_invite_score \n";
            echo "<br/>";

            //game_award_pool
            $fields_award_pool = array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => '8',
                    'auto_increment' => TRUE),
                'game_id' => array(
                    'type' => 'INT',
                    'constraint' => '8'),
                'award' => array(
                    'type' => 'TEXT'),
                'price' => array(
                    'type' => 'INT',
                    'constraint' => '8'),
                'amount' => array(
                    'type' => 'INT',
                    'constraint' => '8'),
                'remaining' => array(
                    'type' => 'INT',
                    'constraint' => '8')
                );
            $this->dbforge->add_field($fields_award_pool);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('game_award_pool', TRUE);
            echo "Create Table game_award_pool \n";
            echo "<br/>";

            // game_award_delivered
            $fields_award_delivered = array(
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
                'award' => array(
                    'type' => 'TEXT'),
                'status' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '10')
                );
            $this->dbforge->add_field($fields_award_delivered);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('game_award_delivered', TRUE);
            echo "Create Table game_award_delivered \n";
            echo "<br/>";

            //game_info
            $fields_game_info = array(
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
                'published_date' => array(
                    'type' => 'DATETIME'),
                'expired_date' => array(
                    'type' => 'DATETIME'),
                'orderlist' => array(
                    'type' => 'INT',
                    'constraint' => '3'),
                'hot' => array(
                    'type' => 'INT',
                    'constraint' => '8')
                );
            $this->dbforge->add_field($fields_game_info);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('game_info', TRUE);
            echo "Create Table game_info \n";
            echo "<br/>";

            echo "Success";
        }
    }
?>