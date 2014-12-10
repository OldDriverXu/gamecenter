<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    class System_model extends CI_Model
    {
        function __construct()
        {
            parent::__construct();
            $this->load->helper('url');
            $this->load->database();
            $this->config->load('weixin_config',TRUE);
            require_once APPPATH.'libraries/common/GlobalFunctions.php';
        }

        public function check_signature($signature, $timestamp, $nonce){
            $token  = $this->config->item('token', 'weixin_config');
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr);
            $tmpStr = implode($tmpArr);
            $tmpStr = sha1($tmpStr);
            if( $tmpStr == $signature ){
                return true;
            }else{
                return false;
            }
        }
    }
?>