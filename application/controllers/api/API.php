<?php

require APPPATH . 'core/API_Controller.php';
class Api extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user/Gernal_model');
        $this->load->helper('url');
        $this->authenticate();
    }
    public function countries_get()
    {
        $co = $this->Gernal_model->get_countries();
        $this->response([
                'status' => true,
                'data' => $con
            ], REST_Controller::HTTP_OK);
    }
    public function genders_get()
    {
        $co = $this->Gernal_model->get_all_genders();
        $this->response([
                'status' => true,
                'data' => $co
            ], REST_Controller::HTTP_OK);
    }

    public function profile_options_get()
    {
        $all = $this->Gernal_model->get_all_data('profile_fields');
        $a = [];
        foreach ($all as $k => $v) {
            $all[$k]['options'] = $this->Gernal_model->get_all_data($v['tbl']);
            if (!isset($a[$v['akey']])) {
                $a[$v['akey']] = [];
            }
            $a[$v['akey']][] = $all[$k];
        }
        $this->response([
                'status' => true,
                'data' => $a
            ], REST_Controller::HTTP_OK);
    }
    public function results_get()
    {
        $filters = ($_GET) ? $_GET : [];
        $limit = (isset($_GET['per_page'])) ? $_GET['per_page'] : 5;
        $page = $this->input->get('page') ?? 1;
        $page = max(1, (int)$page); // Avoid page 0 or negative

        $offset = ($page - 1) * $limit;

        $matches = $this->Gernal_model->get_guest_profiles($limit, $offset, $filters);
        $this->response([
                'status' => true,
                'data' => $matches
            ], REST_Controller::HTTP_OK);
    }
    public function search_get()
    {
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
        $co = [];
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
    public function options_get()
    {
        $interests = $this->Gernal_model->get_all_interests();
        $i = [];
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
        $co = [$ref,$religions,$body];
        foreach ($quest['options'] as $key => $value) {
            $op = [];
            foreach ($value as $kk => $vv) {
                $op[] = ['id' => $vv['id'],'name' => $vv['option_text']];
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
    public function states_get($cid = 0)
    {
        $d = $cid;
        $co = $this->Gernal_model->get_states($d);
        $this->response([
                'status' => true,
                'data' => $co
            ], REST_Controller::HTTP_OK);
    }
    public function email_post()
    {
        $email = $this->post('email');

        if (!$email) {
            $this->response(['status' => false, 'message' => 'Email  required.'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        if (!$this->is_real_email($email)) {
            $this->response(['status' => false, 'message' => 'Invalid email'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $this->db->where('email', $email);
        $existing = $this->db->get('users')->row();
        if ($existing) {
            $this->response(['status' => false, 'message' => 'Email already exists.','already' => 1], REST_Controller::HTTP_CONFLICT);
            return;
        }
        $this->response([
            'status' => true,
            'data' => $email
        ], REST_Controller::HTTP_OK);

    }
    public function is_real_email($email)
    {
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

    public function cities_get($sid = 0)
    {
        $d =$sid;
        $co = $this->Gernal_model->get_cities($d);
        $this->response([
                'status' => true,
                'data' => $co
            ], REST_Controller::HTTP_OK);
    }

    public function questions_get()
    {
        $data = $this->Gernal_model->get_all_questions_with_options();
        $this->response([
                'status' => true,
                'data' => $data
            ], REST_Controller::HTTP_OK);
    }

    public function new_question_get()
    {
        $signup_questions = $this->Gernal_model->get_all_data('signup_questions');
        $quest = $this->Gernal_model->get_all_questions_with_options();
        
        $questions = [
        ["id" => 5, "apiname" => "family_nickname", "question" => "What's a special name or nickname for your family?", "input" => "text", "description" => "Something that represents your family vibe."],
        // ["id" => 6, "apiname" => "home_location", "question" => "Where does your family call home?", "input" => "dropdown-text", "description" => "Type or select your location"],
    ];
    foreach($questions as $k=> $v)
    {
        $signup_questions[] = $v;
    }
    $questions = $signup_questions;
    
    $interests = $this->Gernal_model->get_all_data('interests');
    foreach ($interests as $key => $value) {
            $interests[$key]['image'] = base_url('uploads/interests/'.$interests[$key]['image']);
        }
        $interest_reason = $interests;
    $in = array();
    foreach($interest_reason as $k=> $v)
    {
        $v['name'] = $v['title'];
        unset($v['title']);
        $in[] = $v;
    }
    $interest_reason = $in;
    $family_size = $this->Gernal_model->get_all_data('family_size');
    $life_stages = $this->Gernal_model->get_all_data('life_stages');
    $family_status = $this->Gernal_model->get_all_data('family_status');
    
    $questions[] = ["id" => 7, "apiname" => "interests", "question" => "Why are you interested in Family Match?", "input" => "interests", "options" => $interest_reason];
    $questions[] = ["id" => 8, "apiname" => "family_size", "question" => "How many family members make up your family?", "input" => "radio", "options" => $family_size, "description" => "Include yourself too"];
    foreach($quest['questions'] as $k=> $v)
    {
        $o = isset($quest['options'][$k])?$quest['options'][$k]:array();
        $op = array();
        foreach($o as $ok=> $ov)
        {
            $op[] = array('id'=>$ov['id'],'name'=>$ov['option_text']);
        }
        $sing = ["id" => $v['id'].'_survey', "apiname" => "survey_".$v['id'], "question" => $v['question'], "input" => "radio", "options" => $op];
        $questions[] = $sing;
    }
    $marital_status = $this->Gernal_model->get_all_data('marital_status');
    $questions[] = ["id" => 9, "apiname" => "marital_status", "question" => "Which of the following best describes your family's current life stage?", "input" => "radio", "options" => $life_stages];
    $questions[] = ["id" => 10, "apiname" => "family_status", "question" => "What's your family status?", "input" => "radio", "options" => $family_status];
    $core_values = $this->Gernal_model->get_all_data('core_values');
        foreach ($core_values as $key => $value) {
            $core_values[$key]['image'] = base_url($core_values[$key]['img']);
            unset($core_values[$key]['img']);
        }
    $questions[] = ["id" => 11, "apiname" => "cvalues", "question" => "What values are at the heart of your family?", "input" => "core_value", "options" => $core_values];
    $ethnicities = $this->Gernal_model->get_all_data('ethnicities');
    $questions[] = ["id" => 12, "apiname" => "ethnic", "question" => "What is your family's ethnicity?", "input" => "interests", "options" => $ethnicities];
    $questions[] = ["id" => 13, "apiname" => "languages_spoken", "question" => "What languages do you speak at home or with friends?", "input" => "checkbox", "options" => ["English","Urdu","Arabic","Punjabi","Spanish","French","Others"]];
    $questions[] = ["id" => 14, "apiname" => "family_activities", "question" => "What activities does your family love doing together?", "input" => "checkbox", "options" => ["Cooking","Sports","Board games","Traveling","Watching movies","Outdoor adventures","Other"]];
    $questions[] = ["id" => 15, "apiname" => "activities_with_others", "question" => "What activities would your family enjoy doing with other families?", "input" => "checkbox", "options" => ["Picnics","Cultural events","Playdates","Volunteering","Game nights","Group travel","Other"]];
    $questions[] = ["id" => 17, "apiname" => "family_story", "question" => "What's a little something about your family's story or traditions?", "input" => "textarea"];
    $questions[] = ["id" => 18, "apiname" => "family_photo", "question" => "Would you like to share a photo or fun avatar?", "input" => "image"];
    $i = 0;
    foreach($questions as $k=> $v)
    {
        $i++;
        $v['id'] = $i;
        $questions[$k] = $v;
        
    }
        
        $this->response([
            'status' => true,
            'data' => $questions
        ], REST_Controller::HTTP_OK);
    }
    public function refferals_get()
    {
        $data = $this->Gernal_model->get_all_refferals();
        $this->response([
                'status' => true,
                'data' => $data
            ], REST_Controller::HTTP_OK);
    }
    public function body_types_get()
    {
        $data = $this->Gernal_model->get_all_body_types();
        $this->response([
                'status' => true,
                'data' => $data
            ], REST_Controller::HTTP_OK);
    }


    // Update family profile
    public function update_profile_post()
    {
        $data = $_POST;
        if (!$family_id) {
            $result = $this->Profile_model->updateFamilyProfile($this->profile->id, $data);

            echo json_encode(["status" => "error", "message" => "Invalid token"]);
            return;
        }
    }

    public function admins_get()
    {
        $this->load->model('admin/Admin_model');
        $admins = $this->Admin_model->get_all();
        $result = [];
        foreach ($admins as $admin) {
            $result[] = [
                'name' => $admin->name,
                'email' => $admin->email
            ];
        }
        $this->response([
            'status' => true,
            'data' => $result
        ], REST_Controller::HTTP_OK);
    }

    public function verify_email_get() {
        $token = $this->input->get('token');
        if (!$token) {
            // Show error
        }
        $user = $this->db->where('email_verification_token', $token)->get('users')->row();
        if ($user) {
            $this->db->where('id', $user->id)->update('users', [
                'email_verified' => 1,
                'email_verification_token' => null
            ]);
            // Show success message
        } else {
            // Show invalid token message
        }
    }


}
