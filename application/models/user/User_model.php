<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Insert data into the users table
    public function insert_user($username, $email, $password) {
        $data = array(
            'name' => $username,
            'email' => $email,
            'password' => $password
        );
        
        // Insert into users table
        $this->db->insert('users', $data);
        return $this->db->insert_id();  // Return the user_id
    }

    // Insert data into the profiles table
    public function insert_profile($user_id, $full_name, $dob, $gender, $family_preference, $bio, $city, $country) {
        $data = array(
            'user_id' => $user_id,
            'full_name' => $full_name,
            'dob' => date("Y-m-d", strtotime($dob)),
            'gender' => $gender,
            'family_preference' => $family_preference,
            'bio' => $bio,
            'city' => $city,
            'country' => $country
        );
        
        // Insert into profiles table
        $this->db->insert('profiles', $data);
        return $this->db->insert_id();  // Return the user_id

    }
}
