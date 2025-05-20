<?php

require APPPATH . 'core/API_Controller.php';
class API extends API_Controller {

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
