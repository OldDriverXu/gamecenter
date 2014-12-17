<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    class Oauth_model extends CI_Model
    {
        function __construct()
        {
            parent::__construct();
            $this->load->helper('url');
            $this->load->database();
            $this->config->load('weixin_config',TRUE);
            require_once APPPATH.'libraries/common/GlobalFunctions.php';
        }

        public function get_oauth_access_token($code){
            $grant_type = $this->config->item('oauth_grant_type', 'weixin_config');
            $appid = $this->config->item('appid', 'weixin_config');
            $secret = $this->config->item('secret', 'weixin_config');

            $query_url = $this->config->item('oauth_url', 'weixin_config');
            $query_string = '?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type='.$grant_type;
            $get_url = $query_url.$query_string;
            $curl_result = doCurlGetRequest($get_url);

            $output_array = json_decode($curl_result,true);
            if(!$output_array || $output_array['errcode']){
                interface_log(ERROR, EC_OTHER, 'request wx to get token error:'.var_export($output_array, true));
                return false;
            }
            return $output_array;
        }

        public function get_oauth_userinfo($access_token, $openid){
            $query_url = 'https://api.weixin.qq.com/sns/userinfo';
            $query_string = '?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
            $get_url = $query_url.$query_string;

            $curl_result = doCurlGetRequest($get_url);
            $data =  objectToArray(json_decode($curl_result));
            $result = array(
                    'openid' => $data['openid'],
                    'nickname' => $data['nickname'],
                    'sex' => $data['sex'],
                    'province' => $data['province'],
                    'city' => $data['city'],
                    'country'      => $data['country'],
                    'headimgurl'     => $data['headimgurl']
                );
            return $result;
        }
    }
?>