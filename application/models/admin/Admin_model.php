<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

    private $table = 'admins';

    public function get_all() {
    $this->db->select('admins.*, roles.title as role_title');
    $this->db->from($this->table);
    $this->db->join('roles', 'roles.id = admins.role_id', 'left');
    return $this->db->get()->result();
}


    public function insert($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return $this->db->insert($this->table, $data);
    }

    public function get_by_id($id) {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }
}
