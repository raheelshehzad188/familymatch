<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'core/Admin_Controller.php');

class Referral extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/Referral_model');
        $this->dir = 'admin/'.$this->route;
    }
    public $dir = '';
    public $route = 'referral';
    public $sing = 'Referral';
    public $multi = 'Referrals';

    public function index() {
        $js = array();
            $js[] = 'https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap.min.js';
            
            $data['js'] = $js;
            $data['heading'] = $this->multi;
            $data['dtable']  = 'admin/'.$this->route.'/get_json';
        $this->admin($this->dir.'/list', $data);
    }
    public function get_json(){
        $users = $this->Referral_model->get_all_ethnicities();
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
            $this->Referral_model->insert_ethnicity($name);
            $this->session->set_flashdata('success', $this->sing.' added successfully.');
            redirect('admin/'.$this->route);
        }
        $data = array();
        $data['heading'] = $this->sing;
        $this->admin($this->dir.'/add',$data);
    }

    public function edit($id) {
        $data = array();
        $data['ethnicity'] = $this->Referral_model->get_ethnicity_by_id($id);
        if ($this->input->post()) {
            $name = $this->input->post('name');
            $this->Referral_model->update_ethnicity($id, $name);
            $this->session->set_flashdata('success', $this->sing.' updated successfully.');
            redirect('admin/'.$this->route);
        }
        $data['heading'] = $this->sing;
        $this->admin($this->dir.'/edit', $data);
    }

    public function delete($id) {
        $this->Referral_model->delete_ethnicity($id);
        $this->session->set_flashdata('success', 'Ethnicity deleted successfully.');
        redirect('admin/'.$this->route);
    }
}
