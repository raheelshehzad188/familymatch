<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Keys_model extends CI_Model {

    private $table = 'keys';

    public function get_all() {
        return $this->db->order_by('id', 'DESC')->get($this->table)->result();
    }

    public function insert($host, $key) {
        $data = array(
            'host' => $host,
            'key'  => $key
        );
        return $this->db->insert($this->table, $data);
    }

    public function host_exists($host) {
        return $this->db->where('host', $host)->count_all_results($this->table) > 0;
    }
}
