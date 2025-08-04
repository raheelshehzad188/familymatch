<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH.'core/Admin_Controller.php');

class User extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/User_model');
        
        // For AJAX requests to get_users, don't redirect to login
        $method = $this->router->fetch_method();
        if ($method === 'get_users' && !isset($_SESSION['admin'])) {
            header('Content-Type: application/json');
            echo json_encode([
                "error" => "Authentication required",
                "redirect" => base_url('admin/login')
            ]);
            exit;
        }
    }

    public function edit($id)
    {
        $data['user'] = $this->User_model->get_user_by_id($id);
        if (!$data['user']) {
            show_404(); // if not found
        }
        $this->admin('admin/edit_user', $data);
    }

    public function update_user()
    {
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

    public function index()
    {
        $data = [];
        $data['title'] = 'User';
        $js = [];
        $js[] = 'https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap.min.js';
        $js[] = $this->assets_url.'js/user.js';
        $data['js'] = $js;
        $this->admin('admin/user_list', $data);
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

    public function core_values($user_id)
    {
        $profile_id = $this->User_model->get_profile_id_by_user($user_id);

        if (!$profile_id) {
            echo "Profile not found.";
            return;
        }
        $data['interests'] = $this->User_model->get_profile_core_values($profile_id);
        $this->load->view('admin/tabs/ethnicities', $data);
    }
    
    public function view($user_id)
    {
        $data = [];
        $user = $this->User_model->get_user_profile($user_id);
        if (!$user) {
            die('Forbidden request');
        }
        $data['user_id'] = $user_id;
        $data['user'] = $user;

        $this->admin('admin/user_profile_ajax_view', $data);
    }
    
    public function test_datatable()
    {
        $data = [];
        $data['title'] = 'Test DataTable';
        $js = [];
        $js[] = 'https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap.min.js';
        $js[] = $this->assets_url.'js/user.js';
        $data['js'] = $js;
        $this->admin('admin/test_datatable', $data);
    }
    
    public function test_db()
    {
        header('Content-Type: application/json');
        
        try {
            // Test database connection
            $this->db->simple_query('SELECT 1');
            
            // Check if users table exists
            $tables = $this->db->list_tables();
            $users_table_exists = in_array('users', $tables);
            
            // Get user count
            $user_count = $this->db->count_all('users');
            
            echo json_encode([
                "status" => "success",
                "database_connected" => true,
                "users_table_exists" => $users_table_exists,
                "user_count" => $user_count,
                "tables" => $tables
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
        exit;
    }
    
    public function get_users()
    {
        // Set proper content type for JSON response
        header('Content-Type: application/json');
        
        try {
            // Get users from model
            $users = $this->User_model->get_all_users();
            $data = [];

            foreach ($users as $user) {
                $action = '<a href="'.$this->admin_url.'user/view/'.$user->id.'" class="btn btn-sm btn-primary">Profile</a> | <a href="'.$this->admin_url.'user/edit/'.$user->id.'" class="btn btn-sm btn-warning">Edit</a> | <button class="btn btn-sm btn-danger btn-delete-user" data-id="'.$user->id.'">Delete</button>';
                $verified_badge = $user->is_verified ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-danger">Not Verified</span>';
                $data[] = [
                    $user->id,
                    $user->name,
                    $user->email,
                    $verified_badge,
                    $user->phone,
                    date('Y-m-d', strtotime($user->created_at)),
                    $action
                ];
            }

            echo json_encode([
                "data" => $data
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "error" => "Database error: " . $e->getMessage()
            ]);
        }
        exit; // Ensure no additional output
    }

    // Delete a user and all related data
    public function delete_user($user_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            show_error('Invalid request method', 405);
            return;
        }
        $this->load->model('admin/User_model');
        $this->load->database();
        $success = true;
        $msg = '';
        try {
            // Get profile id
            $profile_id = $this->User_model->get_profile_id_by_user($user_id);
            // Delete profile interests
            if ($profile_id) {
                $this->db->where('profile_id', $profile_id)->delete('profile_intersts');
                $this->db->where('profile_id', $profile_id)->delete('profile_ethnic');
                $this->db->where('profile_id', $profile_id)->delete('profile_cvalues');
            }
            // Delete survey responses
            $this->db->where('user_id', $user_id)->delete('responses');
            // Delete user images from media table and filesystem
            $images = $this->User_model->get_user_images($user_id);
            foreach ($images as $img) {
                if (!empty($img->thumb_path) && file_exists(FCPATH . $img->thumb_path)) {
                    @unlink(FCPATH . $img->thumb_path);
                }
                if (!empty($img->file_path) && file_exists(FCPATH . $img->file_path)) {
                    @unlink(FCPATH . $img->file_path);
                }
            }
            $this->db->where('user_id', $user_id)->delete('media');
            // Delete profile
            $this->db->where('user_id', $user_id)->delete('profiles');
            // Delete user
            $this->db->where('id', $user_id)->delete('users');
            $msg = 'User and all related data deleted successfully.';
        } catch (Exception $e) {
            $success = false;
            $msg = 'Error: ' . $e->getMessage();
        }
        $this->output->set_content_type('application/json')->set_output(json_encode([
            'success' => $success,
            'message' => $msg
        ]));
    }
}
