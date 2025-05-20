<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Body_model extends CI_Model {

    public function insert_ethnicity($name) {
        $this->db->insert('body_types', ['name' => $name]);
        return $this->db->insert_id();
    }

    public function get_all_ethnicities() {
        return $this->db->order_by('id', 'DESC')->get('body_types')->result_array();
    }

    public function get_ethnicity_by_id($id) {
        return $this->db->get_where('body_types', ['id' => $id])->row_array();
    }

    public function update_ethnicity($id, $name) {
        return $this->db->where('id', $id)->update('body_types', ['name' => $name]);
    }

    public function delete_ethnicity($id) {
        return $this->db->delete('body_types', ['id' => $id]);
    }
}
