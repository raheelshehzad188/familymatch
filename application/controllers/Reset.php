<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reset extends CI_Controller
{
    public function index()
    {
        $msg = null;
        $msg_type = null;
        $token = $this->input->get('token');
        if ($this->input->method() === 'post') {
            $new_password = $this->input->post('new_password');
            $confirm_password = $this->input->post('confirm_password');
            $token = $this->input->post('token');

            // Call the API endpoint internally
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, base_url('api/profile/reset_password'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'token' => $token,
                'new_password' => $new_password,
                'confirm_password' => $confirm_password
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = json_decode($response, true);
            if ($http_code === 200 && isset($result['status']) && $result['status']) {
                $msg = 'Password reset successfully. You can now log in with your new password.';
                $msg_type = 'success';
            } else {
                $msg = isset($result['message']) ? $result['message'] : 'Failed to reset password.';
                $msg_type = 'error';
            }
        }
        $this->load->view('reset_password', [
            'msg' => $msg,
            'msg_type' => $msg_type
        ]);
    }
} 