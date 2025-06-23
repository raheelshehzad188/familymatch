<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'core/Admin_Controller.php');
class Admins extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/Admin_model');
        $this->load->model('admin/Role_model');
    }

    public function index($edit_id = null) {
        $data['admins'] = $this->Admin_model->get_all();
        $data['admin_user'] = null;
        $data['roles'] = $this->Role_model->get_all();

        if ($edit_id) {
            $data['admin_user'] = $this->Admin_model->get_by_id($edit_id);
        }

        $this->admin('admin/admin_crud', $data);
    }

    public function save() {
        $id = $this->input->post('id');

        $data = [
            'name' => $this->input->post('name'),
            'username' => $this->input->post('username'),
            'email' => $this->input->post('email'),
            'role_id' => $this->input->post('role_id')
        ];

        if ($id) {
            if (!empty($this->input->post('password'))) {
                $data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
            }
            $this->Admin_model->update($id, $data);
        } else {
            $data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
            $this->Admin_model->insert($data);
        }

        redirect('admin/admins');
    }

    public function delete($id) {
        $this->Admin_model->delete($id);
        redirect('admin/admins');
    }
}
