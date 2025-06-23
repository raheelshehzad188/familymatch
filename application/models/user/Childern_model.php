<?php

class Childern_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Update family profile
    public function insert($data) {
        return $this->db->insert('user_to_childern',$data);
    }

    // Update family profile
    public function delete($id) {
        return $this->db->where('id',$id)->delete('user_to_childern');
    }



}
