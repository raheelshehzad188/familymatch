<?php
require APPPATH . 'core/API_Controller.php';
class Login extends API_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('user/User_model');
    }

    public function login_post() {
    	$this->authenticate();

        // Input validation
        $email = $this->post('email');
        $password = $this->post('password');

        if (!$email || !$password) {
            $this->response(['status' => false, 'message' => 'Email and Password are required.'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Check if email exists
        $this->db->where('email', $email);
        $user = $this->db->get('users')->row();

        if (!$user) {
            $this->response(['status' => false, 'message' => 'Email not found.'], REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        // Verify password
        if (!password_verify($password, $user->password)) {
            $this->response(['status' => false, 'message' => 'Invalid credentials.'], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        // Generate a token (JWT or custom token)
        $token = $this->generate_token($user->id);

        // Return the success response with token and user data
        $this->response([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'token' => $token
            ]
        ], REST_Controller::HTTP_OK);
    }
}
