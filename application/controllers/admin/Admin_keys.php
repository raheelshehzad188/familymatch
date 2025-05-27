<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'core/Admin_Controller.php');
class Admin_keys extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/Keys_model');
    }
    public function delete($id) {
    $this->load->model('Keys_model');
    $this->db->where('id', $id)->delete('keys');
    redirect('admin/admin_keys');
}


    public function index() {
        $data['keys'] = $this->Keys_model->get_all();
        $data['msg'] = '';

        if ($this->input->post()) {
            $host = $this->input->post('host');
            $key = $this->input->post('key');

            // check for duplicate host
            if ($this->Keys_model->host_exists($host)) {
                $data['msg'] = '<div class="alert alert-warning">Host already exists!</div>';
            } else {
                $inserted = $this->Keys_model->insert($host, $key);
                if ($inserted) {
                    $data['msg'] = '<div class="alert alert-success">API Key added!</div>';
                } else {
                    $data['msg'] = '<div class="alert alert-danger">Insert failed.</div>';
                }
                $data['keys'] = $this->Keys_model->get_all(); // refresh
            }
        }

        $this->admin('admin/keys_view', $data);
    }
}
