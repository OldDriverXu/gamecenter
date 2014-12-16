<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    require_once APPPATH.'libraries/common/GlobalFunctions.php';
    require APPPATH.'/libraries/REST_Controller.php';

    class Follower extends REST_Controller
    {
        public function __construct(){
            parent::__construct();
            $this->load->model('follower/follower_model');
        }

        public function index_get(){
            $this->response(NULL, 400);
        }

        public function isfollower_get(){
            $unionid = $this->get('unionid');
            if (!$unionid){
                $this->response(array('isfollower'=> false, 'uid'=> ''));
            }
            $isfollower = $this->follower_model->get_follower_uid_by_unionid($unionid);
            if($isfollower){
                $this->response(array('isfollower'=> true, 'uid'=> $isfollower));
            }else{
                $this->response(array('isfollower'=> false, 'uid'=> ''));
            }
        }
    }
?>