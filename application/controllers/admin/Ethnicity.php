<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'core/Admin_Controller.php');

class Ethnicity extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/Ethnicity_model');
        $this->dir = 'admin/'.$this->route;
    }
    public $die = '';
    public $route = 'ethnicity';
    public $sing = 'Ethnicity';
    public $multi = 'Ethnicities';

    public function index() {
        $js = array();
            $js[] = 'https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap.min.js';
            
            $data['js'] = $js;
            $data['dtable']  = 'admin/'.$this->route.'/get_json';
        $this->admin($this->dir.'/list', $data);
    }
    public function get_json(){

        $users = $this->Ethnicity_model->get_all_ethnicities();
        $data = array();

        foreach ($users as $user) {
            $action = '<a href="'.$this->admin_url.$this->route.'/edit/'.$user['id'].'" class="btn">Edit</a>|<a href="'.$this->admin_url.$this->route.'/delete/'.$user['id'].'" class="btn">Delete</a>';
            $data[] = array(
                $user['id'],
                $user['name'],
                date('Y-m-d', strtotime($user['created_at'])),
                $action
            );
        }

        echo json_encode([
            "data" => $data
        ]);
    }

    public function add() {
        if ($this->input->post()) {
            $name = $this->input->post('name');
            $this->Ethnicity_model->insert_ethnicity($name);
            $this->session->set_flashdata('success', $this->sing.' added successfully.');
            redirect('admin/'.$this->route);
        }
        $this->admin($this->dir.'/add');
    }

    public function edit($id) {
        $data['ethnicity'] = $this->Ethnicity_model->get_ethnicity_by_id($id);
        if ($this->input->post()) {
            $name = $this->input->post('name');
            $this->Ethnicity_model->update_ethnicity($id, $name);
            $this->session->set_flashdata('success', $this->sing.' updated successfully.');
            redirect('admin/'.$this->route);
        }
        $this->admin($this->dir.'/edit', $data);
    }

    public function delete($id) {
        $this->Ethnicity_model->delete_ethnicity($id);
        $this->session->set_flashdata('success', 'Ethnicity deleted successfully.');
        redirect('admin/'.$this->route);
    }
}
