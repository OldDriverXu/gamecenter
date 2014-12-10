<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Game_model extends CI_Model
    {
        public function __construct(){
            parent::__construct();
            $this->load->database();
        }

        /* $array = array(
        |       'username'     => $username,
        |       'game_id'      => $game_id,
        |       'score_type'   => $score_type,
        |       'score_value'  => $score_value,
        |       'login_date'   => $login_date,
        |       'login_ip'     => $login_ip,
        |       'login_ua'     => $login_ua,
        |       'from_channel' => $from_channel,
        |       'from_username'=> $from_username
        |   );
        */
        public function set_gamelog($array){
            foreach ($array as $key => $value) {
                if ($value){
                    $data[$key] = $value;
                }
            }
            $this->db->insert('game_log', $data);
        }

        public function get_highscore($username, $game_id, $score_type){
            $this->db->select('score_value');
            $this->db->from('game_log');
            $this->db->where('username', $username);
            $this->db->where('game_id', $game_id);
            $this->db->where('score_type', $score_type);
            $this->db->order_by('score_value', 'desc');
            $this->db->limit(1);
            $query = $this->db->get();
            $result = $query->result_array();

            if ($result){
                return (int)$result[0]['score_value'];
            }else{
                return null;
            }
        }

        public function get_totalscore($username, $game_id, $score_type){
            $this->db->select('score_value');
            $this->db->from('game_log');
            $this->db->where('username', $username);
            $this->db->where('game_id', $game_id);
            $this->db->where('score_type', $score_type);
            $query = $this->db->get();
            $result = $query->result_array();

            $totalscore = 0;

            if ($result){
                for ($i=0; $i<count($result); $i++) {
                    $totalscore += (int)$result[$i]['score_value'];
                }
                return $totalscore;
            }else{
                return null;
            }
        }

        public function get_activecount($username, $game_id){
            $this->db->distinct();
            $this->db->select('username');
            $this->db->from('game_log');
            $this->db->where('from_username', $username);
            $this->db->where('game_id', $game_id);
            $this->db->where('score_type', 'total');
            $query = $this->db->get();
            $result = $query->result_array();
            $count = count($result);
            return $count;
        }

        public function get_usecount($username, $game_id){
            $this->db->from('game_log');
            $this->db->where('username', $username);
            $this->db->where('game_id', $game_id);
            $this->db->where('score_type', 'total');
            $count = $this->db->count_all_results();
            return $count;
        }

        public function get_rank($game_id, $limit){
            $this->db->from('game_rank');
            $this->db->where('game_id', $game_id);
            $this->db->order_by('score', 'desc');
            $this->db->limit($limit);
            $query = $this->db->get();
            $result = $query->result_array();
            return $result;
        }

        public function get_awards($game_id){
            $this->db->select_max('score_value', 'award');
            $this->db->from('game_log');
            $this->db->where('game_id', $game_id);
            $this->db->where('score_type', 'good');
            $this->db->group_by('username');
            $query = $this->db->get();
            $result = $query->result_array();
            $tmp = array();
            for ($i=0; $i< count($result); $i++){
                array_push($tmp, (string)$result[$i]['award']);
            }
            $ac=array_count_values($tmp);
            return $ac;
        }

        public function get_award_status($username, $game_id){
            $this->db->from('game_log');
            $this->db->where('username', $username);
            $this->db->where('game_id', $game_id);
            $this->db->where('score_type', 'use');
            $query = $this->db->get();
            $result = $query->result_array();
            if ($result){
                return true;
            }else{
                return false;
            }
        }

        public function get_allusers(){
            $this->db->distinct();
            $this->db->select('username');
            $this->db->from('game_log');
            $query = $this->db->get();
            $result = $query->result_array();
            return $result;
        }

        public function get_validlastlog($username, $count){
            $this->db->from('game_log');
            $this->db->where('username', $username);
            $this->db->where('score_type', 'total'); 
            $this->db->limit($count);
            $query = $this->db->get();
            $result = $query->result_array();
            if ($result){
                return $result[count($result)-1];
            }else{
                return null;
            }
        }

        public function delete_log($username, $login_date, $except){
            $this->db->where('username', $username);
            $this->db->where('login_date >', $login_date);
            $this->db->where('score_type <>', $except);
            $this->db->delete('game_log');
            return true;
        }
    }