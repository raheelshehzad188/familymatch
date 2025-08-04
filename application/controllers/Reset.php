<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reset extends CI_Controller
{
    public function index()
    {
        $msg = null;
        $msg_type = null;
        $token = trim($this->input->get('token'));
$token = preg_replace('/\s+/', '', $token);
        if ($this->input->method() === 'post') {
            $new_password = $this->input->post('new_password');
            $confirm_password = $this->input->post('confirm_password');
            $token = $this->input->post('token');
            $token = preg_replace('/\s+/', '', $token);

            if (!$token || !$new_password || !$confirm_password) {
                $msg = 'All fields are required.';
                $msg_type = 'error';
            } elseif ($new_password !== $confirm_password) {
                $msg = 'Passwords do not match.';
                $msg_type = 'error';
            } else {
                $this->load->model('user/User_model');
                $user = $this->User_model->get_user_by_token($token);
                if (!$user) {
                    $msg = 'Invalid or expired token.';
                    $msg_type = 'error';
                } else {
                    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                    $this->db->where('id', $user->id)->update('users', ['password' => $hashed]);
                    $this->User_model->clear_reset_token($user->id);
                    $msg = 'Password reset successfully. You can now log in with your new password.';
                    $msg_type = 'success';
                }
            }
        }
        $this->load->view('reset_password', [
            'msg' => $msg,
            'msg_type' => $msg_type,
            'token' => $token // <-- yeh zaroor pass karen
        ]);
    }
} 