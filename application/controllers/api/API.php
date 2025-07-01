<?php

require APPPATH . 'core/API_Controller.php';
class Api extends API_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/Gernal_model');
        $this->load->helper('url');
        $this->authenticate();
    }
    public function countries_get() {
        $co = $this->Gernal_model->get_countries();
    $this->response([
            'status' => true,
            'data' => $co
        ], REST_Controller::HTTP_OK);
}
    public function genders_get() {
        $co = $this->Gernal_model->get_all_genders();
    $this->response([
            'status' => true,
            'data' => $co
        ], REST_Controller::HTTP_OK);
}

    public function profile_options_get() {
        $all = $this->Gernal_model->get_all_data('profile_fields');
            $A = ARRAY();
        foreach($all as $k=> $v)
        {
            $all[$k]['options'] = $this->Gernal_model->get_all_data($v['tbl']); 
            if(!isset($a[$v['akey']]))
            {
                $a[$v['akey']] = array();
            }
            $a[$v['akey']][] = $all[$k];
        }
    $this->response([
            'status' => true,
            'data' => $a
        ], REST_Controller::HTTP_OK);
}
        public function results_get() {
            $filters = ($_GET)?$_GET:array();
            $limit = (isset($_GET['per_page']))?$_GET['per_page']:5;
            $page = $this->input->get('page') ?? 1;
            $page = max(1, (int)$page); // Avoid page 0 or negative

            $offset = ($page - 1) * $limit;

                    $matches = $this->Gernal_model->get_guest_profiles($limit,$offset,$filters);
                $this->response([
                        'status' => true,
                        'data' => $matches
                    ], REST_Controller::HTTP_OK);
        }
    public function search_get() {
        $qualifications = $this->Gernal_model->get_all_data('qualifications');
        $genders = $this->Gernal_model->get_all_data('genders');
        $religions = $this->Gernal_model->get_all_data('religions');
        $marital_status = $this->Gernal_model->get_all_data('marital_status');
        $blood_groups = $this->Gernal_model->get_all_data('blood_groups');
        $interests = $this->Gernal_model->get_all_data('interests');
        $ethnicities = $this->Gernal_model->get_all_data('ethnicities');
        foreach ($interests as $key => $value) {
            $interests[$key]['image'] = base_url($interests[$key]['image']);
        }
        $core_values = $this->Gernal_model->get_all_data('core_values');
        foreach ($core_values as $key => $value) {
            $core_values[$key]['image'] = base_url($core_values[$key]['image']);
        }
        $countries = $this->Gernal_model->get_countries();
        foreach ($core_values as $key => $value) {

             $core_values[$key]['image'] = base_url($core_values[$key]['img']);
             unset($core_values[$key]['img']);
        }
        $eth = $this->Gernal_model->get_all_ethnicities();
        $body = $this->Gernal_model->get_all_body_types();
        $quest = $this->Gernal_model->get_all_questions_with_options();
        $ref = $data;
        $co = array();
        $co['qualifications'] = $qualifications;
        $co['genders'] = $genders;
        $co['religions'] = $religions;
        $co['marital_status'] = $marital_status;
        $co['countries'] = $countries;
        $co['body'] = $body;
        $co['interests'] = $interests;
        $co['ethnicities'] = $ethnicities;
        $co['core_values'] = $core_values;
        $co['blood_groups'] = $blood_groups;
        $smoking_options = $this->Gernal_model->get_all_data('smoking_options');
        $travel_frequency_options = $this->Gernal_model->get_all_data('travel_frequency');
        $co['music'] = $this->Gernal_model->get_all_data('music_types');
        $co['allergy'] = $this->Gernal_model->get_all_data('allergies');
        $co['medical_conditions'] = $this->Gernal_model->get_all_data('medical_conditions');

    $this->response([
            'status' => true,
            'data' => $co
        ], REST_Controller::HTTP_OK);
}
    public function options_get() {
        $interests = $this->Gernal_model->get_all_interests();
        $i = array();
        foreach ($interests as $key => $value) {
            $n = $value['title'];
            unset($interests[$key]['title']);
            $interests[$key]['name'] = $n;
             $interests[$key]['image'] = base_url('uploads/interests/'.$interests[$key]['image']);
        }
        $data = $this->Gernal_model->get_all_refferals();//
        $core_values = $this->Gernal_model->get_all_data('core_values');
        $religions = $this->Gernal_model->get_all_data('religions');
        
        foreach ($core_values as $key => $value) {

             $core_values[$key]['image'] = base_url($core_values[$key]['img']);
             unset($core_values[$key]['img']);
        }
        $eth = $this->Gernal_model->get_all_ethnicities();
        $body = $this->Gernal_model->get_all_body_types();
        $quest = $this->Gernal_model->get_all_questions_with_options();
        $ref = $data;
        $co = array($ref,$religions,$body);
        foreach ($quest['options'] as $key => $value) {
            $op = array();
            foreach($value as $kk=> $vv)
            {
                $op[] = array('id'=>$vv['id'],'name'=>$vv['option_text']);
            }
            $co[] = $op;
        }
        $co[] = $eth;
        $co[] = $interests;
        $co[] = $core_values;
    $this->response([
            'status' => true,
            'data' => $co
        ], REST_Controller::HTTP_OK);
}
    public function states_get() {
        $d = $_GET;
        $co = $this->Gernal_model->get_states($d);
    $this->response([
            'status' => true,
            'data' => $co
        ], REST_Controller::HTTP_OK);
}
    public function email_post() {
        $email = $this->post('email');

        if (!$email) {
            $this->response(['status' => false, 'message' => 'Email  required.'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        if(!$this->is_real_email($email))
        {
            $this->response(['status' => false, 'message' => 'Invalid email'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $this->db->where('email', $email);
        $existing = $this->db->get('users')->row();
        if ($existing) {
            $this->response(['status' => false, 'message' => 'Email already exists.','already'=>1], REST_Controller::HTTP_CONFLICT);
            return;
        }
        $this->response([
            'status' => true,
            'data' => $email
        ], REST_Controller::HTTP_OK);

}
    function is_real_email($email) {
    // Step 1: Format check
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    // Step 2: Extract domain and check MX records
    $domain = substr(strrchr($email, "@"), 1);
    if (!checkdnsrr($domain, "MX")) {
        return false;
    }

    return true;
}

    public function cities_get() {
        $d = $_GET;
        $co = $this->Gernal_model->get_cities($d);
    $this->response([
            'status' => true,
            'data' => $co
        ], REST_Controller::HTTP_OK);
}

public function questions_get() {
    $data = $this->Gernal_model->get_all_questions_with_options();
    $this->response([
            'status' => true,
            'data' => $data
        ], REST_Controller::HTTP_OK);
}
public function refferals_get() {
    $data = $this->Gernal_model->get_all_refferals();
    $this->response([
            'status' => true,
            'data' => $data
        ], REST_Controller::HTTP_OK);
}
public function body_types_get() {
    $data = $this->Gernal_model->get_all_body_types();
    $this->response([
            'status' => true,
            'data' => $data
        ], REST_Controller::HTTP_OK);
}


    // Update family profile
    public function update_profile_post() {
        $data = $_POST;
    if (!$family_id) {
                $result = $this->Profile_model->updateFamilyProfile($this->profile->id,$data);

        echo json_encode(["status" => "error", "message" => "Invalid token"]);
        return;
    }
}

public function admins_get() {
    $this->load->model('admin/Admin_model');
    $admins = $this->Admin_model->get_all();
    $result = array();
    foreach ($admins as $admin) {
        $result[] = array(
            'name' => $admin->name,
            'email' => $admin->email
        );
    }
    $this->response([
        'status' => true,
        'data' => $result
    ], REST_Controller::HTTP_OK);
}



}
