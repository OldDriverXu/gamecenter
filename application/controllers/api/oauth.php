<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    require APPPATH.'/libraries/REST_Controller.php';
    class Oauth extends REST_Controller
    {
        public function __construct(){
            parent::__construct();
            $this->load->model('oauth/oauth_model');
        }

        public function index_get(){
            $code = $this->get('code');
            $token_array = $this->oauth_model->get_oauth_access_token($code);

            $access_token = $token_array['access_token'];
            $openid = $token_array['openid'];

            $userinfo = $this->oauth_model->get_oauth_userinfo($access_token, $openid);
            $this->response($userinfo, 200);
        }
    }
?>