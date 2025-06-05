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
        if(isset($data['blood_group']))
        {
            $up['blood_group'] = $data['blood_group'];
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
        if(isset($data['marital_status']))
        {
            $up['marital_status'] = $data['marital_status'];
        }
        if(isset($data['qualification_id']))
        {
            $up['qualification_id'] = $data['qualification_id'];
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
    public function get_profile_id($user_id)
    {
        $tbl = 'profiles';
        $d = array('user_id'=>$user_id);
        $already = $this->db->where($d)->get($tbl)->row();
        if($already)
        {
            return $already->id;
        }
        else
        {
            return 0;
        }
    }
    public function get_user_survey($user_id)
    {
        $this->db->where('u.id', $user_id);
        $this->db->select('
            u.id as user_id,
            u.name as user_name,
            q.id as question_id,
            q.question,
            o.id as option_id,
            o.option_text,
            r.created_at
        ');
        $this->db->from('responses r');
        $this->db->join('users u', 'u.id = r.user_id');
        $this->db->join('survey_questions q', 'q.id = r.question_id');
        $this->db->join('survey_options o', 'o.id = r.option_id');
        $this->db->order_by('r.user_id, q.id');
        $query = $this->db->get();

        return $query->result ();
    }
    public function get_user_id($user_id)
    {
        $tbl = 'profiles';
        $d = array('id'=>$user_id);
        $already = $this->db->where($d)->get($tbl)->row();
        if($already)
        {
            return $already->user_id;
        }
        else
        {
            return 0;
        }
    }
    public function ignore_profile($user_id,$profile_id)
    {
        $tbl = 'profile_ignore';
        $d = array('user_id'=>$user_id,'profile_id'=>$profile_id);
        $already = $this->db->where($d)->get($tbl)->row();
        if($already)
        {
            return $this->db->where('id',$already->id)->delete($tbl);
        }
        else
        {
            return $this->db->insert($tbl,$d);
        }
    }
    public function like_profile($user_id,$profile_id)
    {
        $tbl = 'profile_like';
        $d = array('user_id'=>$user_id,'profile_id'=>$profile_id);
        $already = $this->db->where($d)->get($tbl)->row();
        if($already)
        {
            return $this->db->where('id',$already->id)->delete($tbl);
        }
        else
        {
            return $this->db->insert($tbl,$d);
        }
    }
    public function get_likes($user_id)
    {
        $tbl = 'profile_like';
        $d = array('user_id'=>$user_id);
        return $already = $this->db->where($d)->get($tbl)->result_array();
        
    }
    public function get_sql_matched_profiles($user_id, $limit = 10, $offset = 0, $filters = []) {
    // Start base SQL
    $sql = "
        SELECT 
            p.*,
        m.thumb_path AS profile_image,



            (
                (CASE WHEN p.country_id = u.country_id THEN 20 ELSE 0 END) +
                (CASE WHEN p.state_id = u.state_id THEN 15 ELSE 0 END) +
                (CASE WHEN p.city_id = u.city_id THEN 10 ELSE 0 END) +
                (CASE WHEN p.religion_id = u.religion_id THEN 20 ELSE 0 END) +
                (CASE WHEN p.body_type = u.body_type THEN 10 ELSE 0 END) +
                (CASE 
                    WHEN ABS(TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) - TIMESTAMPDIFF(YEAR, u.dob, CURDATE())) <= 3 
                    THEN 20 ELSE 0 
                END)
            ) AS match_score
        FROM profiles p
        LEFT JOIN media m ON m.id = p.profile_pic
        JOIN profiles u ON u.user_id = ?
        WHERE p.user_id != ?
    ";

    $params = [$user_id, $user_id];

    // Dynamic filters
    if (!empty($filters['gender'])) {
        $sql .= " AND p.gender = ? ";
        $params[] = $filters['gender'];
    }

    if (!empty($filters['country_id'])) {
        $sql .= " AND p.country_id = ? ";
        $params[] = $filters['country_id'];
    }

    if (!empty($filters['state_id'])) {
        $sql .= " AND p.state_id = ? ";
        $params[] = $filters['state_id'];
    }

    if (!empty($filters['city_id'])) {
        $sql .= " AND p.city_id = ? ";
        $params[] = $filters['city_id'];
    }

    // You can add: only show verified/active profiles
    // $sql .= " AND p.verified = 1 ";

    // Sort by best matches
    $sql .= " ORDER BY match_score DESC ";

    // Pagination
    $sql .= " LIMIT ? OFFSET ? ";
    $params[] = (int)$limit;
    $params[] = (int)$offset;

    $query = $this->db->query($sql, $params);
    $base_upload_url = base_url();

foreach ($query->result() as $row) {
    if($row->profile_image)
    $row->profile_image = $base_upload_url . $row->profile_image;
}
    return $query->result();
}

}
