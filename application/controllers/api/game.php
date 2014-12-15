<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    require_once APPPATH.'libraries/common/GlobalFunctions.php';
    require APPPATH.'/libraries/REST_Controller.php';

    class Game extends REST_Controller
    {
        public function __construct(){
            parent::__construct();
            $this->load->library('user_agent');
            $this->load->model('follower/follower_model');
            $this->load->model('game/game_model');
        }

        public function index_get(){
            $this->response(array('status'=> 'success', 'content' => 'hello world'));
        }

        public function login_post(){
            // 服务号openid
            $unionid = $this->post('unionid');
            // 订阅号openid
            $username = $this->post('uid');
            $nickname = $this->post('nickname');
            $headimgurl = $this->post('headimgurl');

            if (!$username){
                $data = "no openid";
                $this->response(array('status'=> 'failed', 'content' => $data));
                return;
            }

            $follower = $this->follower_model->get_follower($username);
            if ($follower){
                $array = array(
                    'follower_unionid' => $unionid,
                    'follower_username' => $username,
                    'follower_nickname' => $nickname,
                    'follower_headimgurl' => $headimgurl);
                $this->follower_model->update_follower($array);
                $data = "更新成功";
                $this->response(array('status'=> 'success', 'content' => $data));
                return;
            }else{
                $subscribe_date = getCurrentTime();
                $subscribe_timestamp = getTime();
                $array = array(
                    'follower_unionid' => $unionid,
                    'follower_username' => $username,
                    'follower_subscribe_date' => $subscribe_date,
                    'follower_subscribe_timestamp' => $subscribe_timestamp,
                    'follower_nickname' => $nickname,
                    'follower_headimgurl' => $headimgurl);
                $this->follower_model->set_follower($array);
                $data = "激活成功";
                $this->response(array('status'=> 'success', 'content' => $data));
                return;
            }
        }

        public function activate_post(){
            $follower_username = $this->post('uid');
            $follower_nickname = $this->post('name');
            $follower_tel = $this->post('tel');

            if (!$follower_nickname){
                $data = "请输入姓名";
                $this->response(array('status'=> 'failed', 'content' => $data));
            }

            if (!$follower_tel){
                $data = "请输入手机号码";
                $this->response(array('status'=> 'failed', 'content' => $data));
            }

            if (!(preg_match("/^1[1-9]{1}[0-9]{9}$/", $follower_tel))){
                $data = "请输入正确的手机号码";
                $this->response(array('status'=> 'failed', 'content' => $data));
            }

            $follower = $this->follower_model->get_follower($follower_username);
            if ($follower){
                $array = array(
                    'follower_username' => $follower_username,
                    'follower_nickname' => $follower_nickname,
                    'follower_tel' => $follower_tel);
                $this->follower_model->update_follower($array);
                $data = "激活成功";
                $this->response(array('status'=> 'success', 'content' => $data));
            }else{
                $data = "请关注公共账号后重新激活";
                $this->response(array('status'=> 'failed', 'content' => $data));
            }
        }

        public function log_post(){
            $username = $this->post('uid');
            $game_id = '2';
            $score_type = 'log';
            $score_value = (int)$this->post('score');
            $from_username = $this->post('fid');
            $game_time = $this->post('gametime');

            $login_date = getCurrentTime();
            $login_ip = getIp();
            $login_ua = $this->agent->agent_string();

            // 验证是否是粉丝
            // $follower = $this->follower_model->get_follower($username);
            // if ($follower){

            if ($username){
                //游戏日志表
                $array = array(
                    'username' => $username,
                    'game_id' => $game_id,
                    'score_type' => $score_type,
                    'score_value' => $score_value,
                    'game_time' => $game_time,
                    'login_date' => $login_date,
                    'login_ip' => $login_ip,
                    'login_ua' => $login_ua,
                    'from_username' => $from_username);

                $this->game_model->set_gamelog($array);

                //邀请表
                // 自己邀请的人数
                if($username){
                    $invitecount = $this->game_model->get_invitecount($username, $game_id);
                    $this->game_model->update_invitescore($username, $game_id, $invitecount);
                }
                // Invitor的邀请人数
                if($from_username){
                    $invitecount = $this->game_model->get_invitecount($from_username, $game_id);
                    $this->game_model->update_invitescore($from_username, $game_id, $invitecount);
                }

                //最高分表
                $this->game_model->update_highscore($username, $game_id, $score_value);

                $data = "提交成功";
                $this->response(array('status'=> 'success', 'content' => $data));
            }
        }

        public function score_get(){
            if (!$this->get('uid')){
                $this->response(NULL, 400);
                return;
            }else{
                $username = $this->get('uid');
            }

            if (!$this->get('game_id')){
                $game_id = '2';
            }else{
                $game_id = $this->get('game_id');
            }

            $follower = $this->follower_model->get_follower($username);
            $nickname = $follower['follower_nickname'];
            $tel = $follower['follower_tel'];
            $ranking = 0;

            $rank = $this->game_model->get_rank($game_id);
            for ($i=0; $i<count($rank); $i++){
                if ($rank[$i]['username'] == $username){
                    $ranking = $i+1;
                    break;
                }
            }

            $highscore = $this->game_model->get_highscore($username, $game_id);
            $invitescore = $this->game_model->get_invitescore($username, $game_id);
            $totalscore = $this->game_model->get_totalscore($username, $game_id);
            $this->response(array('nickname'=>$nickname, 'tel'=>$tel, 'ranking'=>$ranking, 'highscore'=> $highscore, 'invitescore' => $invitescore, 'totalscore' => $totalscore), 200);
        }

        public function rank_get(){
            $game_id = 2;
            $limit = 100;
            $rank = $this->game_model->get_rank($game_id, $limit);

            for ($i=0; $i<count($rank); $i++){
                $result[$i]['rank'] = $i+1;
                $result[$i]['uid'] = $rank[$i]['username'];
                $follower = $this->follower_model->get_follower($rank[$i]['username']);
                if ($follower['follower_nickname']){
                    $result[$i]['name'] = $follower['follower_nickname'];
                }else{
                    $result[$i]['name'] = '匿名';
                }

                if ($follower['follower_tel']){
                    $result[$i]['tel'] = substr($follower['follower_tel'],0,3)."****".substr($follower['follower_tel'],7,4);
                }else{
                    $result[$i]['tel'] = '***********';
                }
                $result[$i]['high_score'] = $rank[$i]['high_score'];
                $result[$i]['invite_score'] = $rank[$i]['invite_score'];
                $result[$i]['score'] = $rank[$i]['score'];
            }

            $this->response($result, 200);
        }

        public function awards_get(){
            $game_id = 1;
            $awards = $this->game_model->get_awards($game_id);
            $this->response($awards, 200);
        }

        public function clear_get(){
            $users = $this->game_model->get_allusers();
            foreach ($users as $key=>$value) {
                $user = $value['username'];
                $count = $this->game_model->get_activecount($user, '1');
                $count = $count + 5;
                $last_log = $this->game_model->get_validlastlog($user, $count);
                if ($last_log){
                    $this->game_model->delete_log($user, $last_log['login_date'], 'use');
                }
            }
            $this->response(array('status' => 'success', 'cotent'=>'clear log success'), 200);
        }
    }
?>