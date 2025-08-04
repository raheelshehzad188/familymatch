<?php

require APPPATH . 'core/API_Controller.php';

class Signup extends API_Controller
{
    /** @var User_model */
    public $User_model;

    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('user/User_model');
        // $this->authenticate();
    }
    public function index_get()
    {
        // die('index_get');
    }
    public function create_user_slug($name, $id)
    {
        // Convert name to lowercase
        $slug = strtolower($name);

        // Remove special characters and replace spaces with hyphens
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);

        // Append ID
        return $slug . '-' . $id;
    }


    public function register_post()
    {
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

        if (!$email || !$password) {
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
        $verification_code = bin2hex(random_bytes(16));
        $user_id = $this->User_model->insert_user($full_name, $email, $password, $verification_code);
        if (!$user_id) {
            $this->response(['status' => false, 'message' => 'Invalid request!'], REST_Controller::HTTP_CONFLICT);
            return;
        }
        // Insert data into the profiles table
        $pid = $this->User_model->insert_profile($user_id, $full_name, $dob, $gender, $bio);
        $slug = $this->create_user_slug($full_name, $pid);
        $this->db->where('id', $pid)->update('profiles', ['slug' => $slug]);
        $token = $this->generate_token($user_id);


        $template = file_get_contents(APPPATH . 'views/email_verify.php');
        $verification_link = base_url("verify-email?code=" . urlencode($verification_code) . "&email=" . urlencode($email));
        $email_body = str_replace('{{VERIFICATION_LINK}}', $verification_link, $template);

        $this->load->library('email');

        $this->email->from('familymatch@aakilarose.com', 'FamilyMatch');
        $this->email->to($email);
        $this->email->subject('Email Verification');
        $this->email->message($email_body);

        if ($this->email->send()) {
            $this->response([
                'status' => true,
                'message' => 'User registered successfully. Please check your email to verify your account.',
                'token' => $token
            ], REST_Controller::HTTP_CREATED);
        } else {
            $error = $this->email->print_debugger(['headers']);
            $this->response([
                'status' => false,
                'message' => 'Failed to send verification email.',
                'debug' => $error
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Email verification endpoint
    public function verify_email_get()
    {
        $code = $this->input->get('code');
        $code = preg_replace('/\s+/', '', $code);
        $email = $this->input->get('email');
        $data = [];

        if (!$code || !$email) {
            $data['status'] = false;
            $data['message'] = "Invalid verification link.";
            $view = 'email_verification_result';
        } else {
            $user = $this->db->where(['email' => $email, 'verification_code' => $code])->get('users')->row();
            if ($user) {
                $this->db->where('id', $user->id)->update('users', ['is_verified' => 1, 'verification_code' => null]);
                $data['status'] = true;
                $data['message'] = "Email verified successfully! You can now log in.";
            } else {
                $data['status'] = false;
                $data['message'] = "Invalid or expired verification link.";
            }
            $view = 'email_verification_result';
        }

        // Load a custom view file for email verification result
        $this->load->view('custom_email_verification_result', $data);
    }
}
