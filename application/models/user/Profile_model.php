<?php

class Profile_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Update family profile
    public function updateFamilyProfile($id,$data) {
        // Start a transaction
        $this->db->trans_begin();

        // Update family table
        $up = [];

        if(isset($data['cvalues']) && $data['cvalues'])
        {
            $cvalues = explode(',', $data['cvalues']); // IDs from request
$profile_id = $id;

// Step 1: Get all existing interest IDs for the profile
$existing = $this->db->select('val_id')
    ->where('profile_id', $profile_id)
    ->get('profile_cvalues')
    ->result_array();

$existing_ids = array_column($existing, 'val_id');

// Step 2: Calculate interests to insert and delete
$to_insert = array_diff($cvalues, $existing_ids);
$to_delete = array_diff($existing_ids, $cvalues);

// Step 3: Delete removed interests
if (!empty($to_delete)) {
    $this->db->where('profile_id', $profile_id);
    $this->db->where_in('val_id', $to_delete);
    $this->db->delete('profile_cvalues');
}

// Step 4: Insert new interests
$insert_data = [];
foreach ($to_insert as $interest_id) {
    $insert_data[] = [
        'profile_id' => $profile_id,
        'val_id' => $interest_id
    ];
}

if (!empty($insert_data)) {
    $this->db->insert_batch('profile_cvalues', $insert_data);
}

        }

        if(isset($data['ethnic']) && $data['ethnic'])
        {
            
            $ethnics = explode(',', $data['ethnic']); // IDs from request
$profile_id = $id;

// Step 1: Get existing ethnic IDs for the profile
$existing = $this->db->select('ethnic_id')
    ->where('profile_id', $profile_id)
    ->get('profile_ethnic')
    ->result_array();

$existing_ids = array_column($existing, 'ethnic_id');

// Step 2: Identify new to insert and old to delete
$to_insert = array_diff($ethnics, $existing_ids);
$to_delete = array_diff($existing_ids, $ethnics);

// Step 3: Delete ethnicities that are no longer present
if (!empty($to_delete)) {
    $this->db->where('profile_id', $profile_id);
    $this->db->where_in('ethnic_id', $to_delete);
    $this->db->delete('profile_ethnic');
}

// Step 4: Insert new ethnicities
$insert_data = [];
foreach ($to_insert as $ethnic_id) {
    $insert_data[] = [
        'profile_id' => $profile_id,
        'ethnic_id' => $ethnic_id
    ];
}

if (!empty($insert_data)) {
    $this->db->insert_batch('profile_ethnic', $insert_data);
}


        }
        //profile_cvalues
        if(isset($data['height']))
        {
            $up['height'] = $data['height'];
        }
        if(isset($data['city_id']))
        {
            $up['city_id'] = $data['city_id'];
        }
        if(isset($data['state_id']))
        {
            $up['state_id'] = $data['state_id'];
        }
        if(isset($data['country_id']))
        {
            $up['country_id'] = $data['country_id'];
        }
        if(isset($data['gender']))
        {
            $up['gender'] = $data['gender'];
        }
        if(isset($data['religion_id']))
        {
            $up['religion_id'] = $data['religion_id'];
        }
        if(isset($data['reffer_id']))
        {
            $up['reffer_id'] = $data['reffer_id'];
        }
        if(isset($data['full_name']))
        {
            $up['full_name'] = $data['full_name'];
        }
        if(isset($data['bio']))
        {
            $up['bio'] = $data['bio'];
        }
        if(isset($data['dob']))
        {
            $up['dob'] = date("Y-m-d", strtotime($data['dob']));
        }
        if(isset($data['profile_pic']))
        {
            $up['profile_pic'] = $data['profile_pic'];
        }
        if(isset($data['country']))
        {
            $up['country'] = $data['country'];
        }

        if(isset($data['body_type']))
        {
            $up['body_type'] = $data['body_type'];
        }
        $this->db->where('id', $id);
        $this->db->update('profiles', $up);

        // Update children's details
        // foreach ($data['children'] as $child) {
        //     $this->db->where('family_id', $data['family_id']);
        //     $this->db->where('id', $child['id']);
        //     $this->db->update('children', [
        //         'name' => $child['name'],
        //         'age' => $child['age']
        //     ]);
        // }

        // Update interests (optional)
        if (!empty($data['interests'])) {
            $this->db->where('profile_id', $profile_id);
            $this->db->delete('profile_intersts');  // Remove existing interests
            $i = explode(',',$data['interests']);

            foreach ($i as $interest_id) {
                $this->db->insert('profile_intersts', [
                    'profile_id' => $profile_id,
                    'interest_id' => $interest_id
                ]);
            }
        }

        // Commit or rollback the transaction
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_roll_back();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
}
