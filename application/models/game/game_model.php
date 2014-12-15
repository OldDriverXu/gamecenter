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
        |       'game_time'    => $game_time,
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

        // $array = array(
        //     'username' => $username,
        //     'game_id' => $game_id,
        //     'high_score' => $high_score
        //     );
        public function update_highscore($username, $game_id, $score){
            $select_sql = "SELECT username, high_score FROM game_high_score WHERE username ='".$username."' AND game_id=".$game_id;
            $select_query = $this->db->query($select_sql);
            if ($select_query->num_rows()>0){
                $create_time = getCurrentTime();
                $result = $select_query->result_array();
                // update records
                if ($result[0]['high_score'] < $score){
                    $update_sql = "UPDATE game_high_score SET high_score =".$score.", create_time = ".$create_time." WHERE username ='".$username."' AND game_id=".$game_id;
                    $update_query = $this->db->query($update_sql);
                }
            }else{
                // insert recores
                $insert_sql = "INSERT INTO game_high_score (username, game_id, high_score, create_time) VALUES ('".$username."',".$game_id.",".$score.",".$create_time.")";
                $insert_query = $this->db->query($insert_sql);
            }
        }

        public function update_invitescore($username, $game_id, $score){
            $select_sql = "SELECT username, invite_score FROM game_invite_score WHERE username ='".$username."' AND game_id=".$game_id;
            $select_query = $this->db->query($select_sql);
            if ($select_query->num_rows()>0){
                $result = $select_query->result_array();
                // update records
                if ($result[0]['invite_score'] < $score){
                    $update_sql = "UPDATE game_invite_score SET invite_score =".$score.", create_time = ".$create_time." WHERE username ='".$username."' AND game_id=".$game_id;
                    $update_query = $this->db->query($update_sql);
                }
            }else{
                // insert recores
                $insert_sql = "INSERT INTO game_invite_score (username, game_id, invite_score) VALUES ('".$username."',".$game_id.",".$score.",".$create_time.")";
                $insert_query = $this->db->query($insert_sql);
            }
        }

        public function get_highscore($username, $game_id){
            $this->db->select('high_score');
            $this->db->from('game_high_score');
            $this->db->where('username', $username);
            $this->db->where('game_id', $game_id);
            $query = $this->db->get();
            $result = $query->result_array();

            if ($result){
                return (int)$result[0]['high_score'];
            }else{
                return 0;
            }
        }

        public function get_invitescore($username, $game_id){
            $this->db->select('invite_score');
            $this->db->from('game_invite_score');
            $this->db->where('username', $username);
            $this->db->where('game_id', $game_id);
            $query = $this->db->get();
            $result = $query->result_array();

            if ($result){
                return (int)$result[0]['invite_score'];
            }else{
                return 0;
            }
        }

        public function get_totalscore($username, $game_id){
            $highscore = $this->get_highscore($username, $game_id);
            $invitescore = $this->get_invitescore($username, $game_id);
            $totalscore = (int)$highscore + (int)$invitescore;
            return $totalscore;
        }

        public function get_invitecount($username, $game_id){
            $this->db->distinct();
            $this->db->select('username');
            $this->db->from('game_log');
            $this->db->where('from_username', $username);
            $this->db->where('game_id', $game_id);
            $query = $this->db->get();
            $result = $query->result_array();
            $count = count($result);
            if($count){
                return $count;
            }else{
                return 0;
            }

        }

        public function get_usecount($username, $game_id){
            $this->db->from('game_log');
            $this->db->where('username', $username);
            $this->db->where('game_id', $game_id);
            $this->db->where('score_type', 'total');
            $count = $this->db->count_all_results();
            return $count;
        }

        public function get_rank($game_id, $limit=0){
            if ($limit){
                $sql = "SELECT h.username, h.high_score, i.invite_score, (h.high_score+i.invite_score) AS score FROM game_high_score AS h join game_invite_score AS i ON h.username = i.username WHERE h.game_id = ".$game_id." and i.game_id = ".$game_id. " ORDER BY score DESC, h.create_time LIMIT ".$limit;
            }else{
                $sql = "SELECT h.username, h.high_score, i.invite_score, (h.high_score+i.invite_score) AS score FROM game_high_score AS h join game_invite_score AS i ON h.username = i.username WHERE h.game_id = ".$game_id." and i.game_id = ".$game_id. " ORDER BY score DESC, h.create_time";
            }
            $query = $this->db->query($sql);
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