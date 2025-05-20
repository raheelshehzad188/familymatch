<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'core/Admin_Controller.php');

class User extends Admin_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('admin/User_model');
    }

    public function edit($id) {
    $data['user'] = $this->User_model->get_user_by_id($id);
    if (!$data['user']) {
        show_404(); // if not found
    }
    $this->admin('admin/edit_user', $data);
}

public function update_user() {
    $id = $this->input->post('id');

    $data = [
        'name'  => $this->input->post('name'),
        'email' => $this->input->post('email'),
        'phone' => $this->input->post('phone')
    ];

    $this->User_model->update_user($id, $data);
    $this->session->set_flashdata('success', 'User updated successfully!');
    redirect('admin/users');
}

    public function index() {
        $data = array();
        $data['title'] = 'User';
        $js = array();
        $js[] = 'https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap.min.js';
        $js[] = $this->assets_url.'js/user.js';
        $data['js'] = $js;
        $this->admin('admin/user_list',$data);
    }
    public function basic_info($user_id)
    {
        $user = $this->User_model->get_user_profile($user_id);
        $this->load->view('admin/tabs/basic_info', ['user' => $user]);
    }

    public function images($user_id)
    {
        $images = $this->User_model->get_user_images($user_id);
        $this->load->view('admin/tabs/images', ['images' => $images]);
    }

    public function survey($user_id)
    {
        $survey = $this->User_model->get_user_survey($user_id);

        $this->load->view('admin/tabs/survey', ['survey' => $survey]);
    }

    public function interst($user_id)
    {

        $profile_id = $this->User_model->get_profile_id_by_user($user_id);

        if (!$profile_id) {
            echo "Profile not found.";
            return;
        }

            $data['interests'] = $this->User_model->get_profile_interests($profile_id);
        $this->load->view('admin/tabs/interst', $data);
    }

    public function ethnicities($user_id)
    {

        $profile_id = $this->User_model->get_profile_id_by_user($user_id);

        if (!$profile_id) {
            echo "Profile not found.";
            return;
        }
            $data['interests'] = $this->User_model->get_profile_ethnicities($profile_id);
        $this->load->view('admin/tabs/ethnicities', $data);
    }
    public function view($user_id)
    {
        $data = array();
        $user = $this->User_model->get_user_profile($user_id);
        if(!$user)
        {
            die('Forbidden request');
        }
        $data['user_id'] = $user_id;
        $data['user'] = $user;

        $this->admin('admin/user_profile_ajax_view', $data);
    }
      public function get_users() {

        //User_model
        $users = $this->User_model->get_all_users();
        $data = array();

        foreach ($users as $user) {
            $action = '<a href="'.$this->admin_url.'user/view/'.$user->id.'" class="btn">Profile</a>|<a href="'.$this->admin_url.'user/edit/'.$user->id.'" class="btn">Edit</a>|<a href="#" class="btn">Childerns</a>|<a href="#" class="btn">View</a>|<a href="#" class="btn">Block</a>';
            $data[] = array(
                $user->id,
                $user->name,
                $user->email,
                $user->phone,
                date('Y-m-d', strtotime($user->created_at)),
                $action
            );
        }

        echo json_encode([
            "data" => $data
        ]);
    }
}
