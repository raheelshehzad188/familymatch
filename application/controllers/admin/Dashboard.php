<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'core/Admin_Controller.php');

class Dashboard extends Admin_Controller {

    public function index() {
        $this->admin('admin/dashboard');

    }
    public function settings() {
        $this->load->model('admin/Setting_model');

        if ($this->input->post()) {
            $meta_data = $this->input->post('meta');
            $this->Setting_model->update_settings_batch($meta_data);
            $this->session->set_flashdata('success', 'Settings updated!');
            redirect('admin/dashboard/settings');
        }


        $data['settings'] = $this->Setting_model->get_all_settings();
        $this->admin('admin/settings', $data);
    }
}
