<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'core/Admin_Controller.php');

class Login extends Admin_Controller {

    public function __construct() {

        parent::__construct();
        $m = $this->router->fetch_method();
        if(isset($_SESSION['admin']) && $m != 'logout')
            {
        redirect('admin/dashboard');
            }

        // Load anything you need across all admin controllers
        $this->load->model('admin/Login_model');


    }
    public function logout() {

    // Unset admin session data
    unset($_SESSION['admin']);
    unset($_SESSION['error']);
    unset($_SESSION['success']);

    // Optional: flash message
    $_SESSION['success'] = 'You logout successfully!';

    // Redirect to login page
    redirect('admin/login');
}


    public function index() {
        $this->full('admin/login');
    }
    public function post() {
    $ip = $this->input->ip_address();
    
    if ($this->Login_model->is_blocked($ip)) {
        echo "Too many failed attempts. Your IP is blocked.";
        return;
    }

    $email = $this->input->post('email');
    $password = $this->input->post('password');

    // Login check logic here
    $admin = $this->Login_model->verify_admin($email, $password);

    if ($admin) {
        $this->Login_model->reset_attempts($ip); // Reset on success
        $_SESSION['admin'] = $admin;
        redirect('admin/dashboard');
    } else {
        $this->Login_model->add_attempt($ip);
        $_SESSION['error'] = 'Invalid email or password.';
        redirect('admin/login');

    }
}

}
