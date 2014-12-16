<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Gameaward_model extends CI_Model
    {
        public function __construct(){
            parent::__construct();
            $this->load->database();
        }

        /* $array = array(
        |       'game_id'      => $game_id,
        |       'award'        => $award,
        |       'price'        => $price,
        |       'amount'       => $amount,
        |       'remaining'    => $remaining
        |   );
        */
        public function set_awards($array){
            foreach ($array as $key => $value) {
                if ($value){
                    $data[$key] = $value;
                }
            }
            $this->db->insert('game_award_pool', $data);
        }

        public function get_awards($game_id){
            $this->db->select('award');
            $this->db->select('price');
            $this->db->select('remaining');
            $this->db->from('game_award_pool');
            $this->db->where('game_id', $game_id);
            $this->db->where('remaining >', 0);
            $this->db->order_by('price', 'desc');
            $query = $this->db->get();
            $result = $query->result_array();
        }

        public function get_award_status($username, $game_id){
            $this->db->select('status');
            $this->db->from('game_award_delivered');
            $this->db->where('username', $username);
            $this->db->where('game_id', $game_id);
            $query = $this->db->get();
            $result = $query->result_array();
            if ($result){
                return $result[0];
            }else{
                return null;
            }
        }

        public function set_award_status($username, $game_id, $award, $status){
            $array = array(
                'username' => $username,
                'game_id' => $game_id,
                'award' => $award,
                'status' => $status);
            foreach ($array as $key => $value) {
                if ($value){
                    $data[$key] = $value;
                }
            }
            $this->db->insert('game_award_delivered', $data);
        }

        public function update_award_status($username, $game_id, $award, $status){
            $this->db->set('award', $award);
            $this->db->set('status', $status);
            $this->db->where('username', $username);
            $this->db->where('game_id', $game_id);
            $this->db->update('game_award_delivered');
        }
    }
?>
