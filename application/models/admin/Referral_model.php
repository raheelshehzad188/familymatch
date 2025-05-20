<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Referral_model extends CI_Model {

    public function insert_ethnicity($name) {
        $this->db->insert('referrals', ['name' => $name]);
        return $this->db->insert_id();
    }

    public function get_all_ethnicities() {
        return $this->db->order_by('id', 'DESC')->get('referrals')->result_array();
    }

    public function get_ethnicity_by_id($id) {
        return $this->db->get_where('referrals', ['id' => $id])->row_array();
    }

    public function update_ethnicity($id, $name) {
        return $this->db->where('id', $id)->update('referrals', ['name' => $name]);
    }

    public function delete_ethnicity($id) {
        return $this->db->delete('referrals', ['id' => $id]);
    }
}
