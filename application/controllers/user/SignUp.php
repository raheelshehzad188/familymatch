<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SignUp extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Load the model for users and profiles
        $this->load->model('user/User_model');
    }

    public function index() {
        $this->load->view('user/signup_form');  // View for the sign-up form
    }

    public function register() {
        // Get form data
        $username = $this->input->post('username');
        $email = $this->input->post('email');
        $password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);  // Password hashing
        $full_name = $this->input->post('full_name');
        $age = $this->input->post('age');
        $gender = $this->input->post('gender');
        $family_preference = $this->input->post('family_preference');
        $bio = $this->input->post('bio');
        $city = $this->input->post('city');
        $country = $this->input->post('country');

        // Insert data into the users table
    $user_id = $this->User_model->insert_user($username, $email, $password);
    // Insert data into the profiles table
    $pid = $this->User_model->insert_profile($user_id, $full_name, $age, $gender, $family_preference, $bio, $city, $country);

        // Redirect to a success page or show a success message
        echo "Registration successful!";
    }
}
