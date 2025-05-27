<?php
require APPPATH . 'core/API_Controller.php';

class Signup extends API_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/User_model');
        $this->authenticate();
    }

    public function register_post() {

        // Input validation
        $email = $this->post('email');
        $password = password_hash($this->post('password'), PASSWORD_DEFAULT);  // Password hashing
        $full_name = $this->post('full_name');
        $dob = $this->post('dob');
        $gender = $this->post('gender');
        $family_preference = $this->post('family_preference');
        $bio = $this->post('bio');
        $city = $this->post('city');
        $country = $this->post('country');

        if ( !$email || !$password) {
            $this->response(['status' => false, 'message' => 'All fields are required.'], REST_Controller::HTTP_BAD_REQUEST);
            return '';
        }

        // Check if email already exists
        $this->db->where('email', $email);
        $existing = $this->db->get('users')->row();
        if ($existing) {
            $this->response(['status' => false, 'message' => 'Email already exists.'], REST_Controller::HTTP_CONFLICT);
            return '';
        }

        // Insert new user
        $user_id = $this->User_model->insert_user($full_name, $email, $password);
        if(!$user_id)
        {
                       $this->response(['status' => false, 'message' => 'Invalid request!'], REST_Controller::HTTP_CONFLICT);
            return; 
        }
        // Insert data into the profiles table
        $pid = $this->User_model->insert_profile($user_id, $full_name, $dob, $gender, $bio);
        $token = $this->generate_token($user_id);
        $this->response([
            'status' => true,
            'message' => 'User registered successfully.',
            'token' => $token
        ], REST_Controller::HTTP_CREATED);
    }
}
