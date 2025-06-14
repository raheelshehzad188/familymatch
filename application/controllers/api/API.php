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
        $co['core_values'] = $core_values;
        $co['blood_groups'] = $blood_groups;
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
        dd($data);
    if (!$family_id) {
                $result = $this->Profile_model->updateFamilyProfile($this->profile->id,$data);
                dd($result);

        echo json_encode(["status" => "error", "message" => "Invalid token"]);
        return;
    }
}

}
