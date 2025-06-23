<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends CI_Model {

    private $table = 'roles';

    public function get_all() {
        return $this->db->get($this->table)->result();
    }
}
