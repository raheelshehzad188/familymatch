<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crud_model extends CI_Model {
    public $tbl = '';
    public $key = '';
    public $field = 'name';

    public function insert_data($data) {
        $this->db->insert($this->tbl, $data);
        return $this->db->insert_id();
    }

    public function get_all_data() {
        return $this->db->order_by($this->key, 'DESC')->get($this->tbl)->result_array();
    }

    public function get_data_by_id($id) {
        return $this->db->get_where($this->tbl, [$this->key => $id])->row_array();
    }

    public function update_data($id, $data) {
        return $this->db->where('id', $id)->update($this->tbl, $data);
    }

    public function delete_data($id) {
        return $this->db->delete($this->tbl, [$this->key => $id]);
    }
}
