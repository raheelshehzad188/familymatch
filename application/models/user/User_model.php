<?php

defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Insert data into the users table
    public function insert_user($username, $email, $password)
    {
        $data = [
            'name' => $username,
            'email' => $email,
            'password' => $password
        ];

        // Insert into users table
        $this->db->insert('users', $data);
        return $this->db->insert_id();  // Return the user_id
    }

    // Insert data into the profiles table
    public function insert_profile($user_id, $full_name, $dob, $gender, $bio)
    {
        $data = [
            'user_id' => $user_id,
            'full_name' => $full_name,
            'dob' => date("Y-m-d", strtotime($dob)),
            'gender' => $gender,
            'bio' => $bio,
        ];

        // Insert into profiles table
        $this->db->insert('profiles', $data);
        return $this->db->insert_id();  // Return the user_id

    }

    public function set_reset_token($email, $token)
    {
        $this->db->where('email', $email);
        return $this->db->update('users', [
            'reset_token' => $token,
            'reset_token_expiry' => date('Y-m-d H:i:s', strtotime('+1 day')) // 1 din expiry
        ]);
    }

    public function get_user_by_token($token)
    {
        $this->db->where('reset_token', $token);
        $this->db->where('reset_token_expiry >=', date('Y-m-d H:i:s'));
        return $this->db->get('users')->row();
    }

    public function clear_reset_token($user_id)
    {
        $this->db->where('id', $user_id);
        return $this->db->update('users', [
            'reset_token' => null,
            'reset_token_expiry' => null
        ]);
    }
}
