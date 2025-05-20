<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Interest_model extends CI_Model {

    public function insert_interest($title, $image = null) {
        $this->db->insert('interests', [
            'title' => $title,
            'image' => $image
        ]);
        return $this->db->insert_id();
    }

    public function get_all_interests() {
        return $this->db->order_by('id', 'DESC')->get('interests')->result_array();
    }

    public function get_interest_by_id($id) {
        return $this->db->get_where('interests', ['id' => $id])->row_array();
    }

    public function update_interest($id, $title, $image = null) {
        $data = ['title' => $title];
        if ($image) {
            $data['image'] = $image;
        }
        return $this->db->where('id', $id)->update('interests', $data);
    }

    public function delete_interest($id) {
        return $this->db->delete('interests', ['id' => $id]);
    }
}
