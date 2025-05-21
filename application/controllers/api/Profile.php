<?php

require APPPATH . 'core/API_Controller.php';
class Profile extends API_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/Profile_model');
        $this->load->helper('url');
        $this->validate_token();
    }
    public function index_get() {
    $this->response([
            'status' => true,
            'data' => $this->profile
        ], REST_Controller::HTTP_OK);
}

    // Update family profile
    public function update_profile_post() {
    	$data = $_POST;
    if ($this->profile->id) {
    	        $result = $this->Profile_model->updateFamilyProfile($this->profile->id,$data);
                if($result)
                {
                    $sur = array();
                    foreach($data as $k=> $v)
                    {

                        if (strpos($k, 'suervey') !== false) {
                            $exp = explode('-',$k);
                            $sur[$exp[1]] = $v;
                        }

                    }
                $user_id = $this->user_id;

    foreach ($sur as $q=>$answer) {
        $question_id = $q;
        $option_id = $answer;

        // Insert each answer
        $this->db->insert('responses', [
            'user_id' => $user_id,
            'question_id' => $question_id,
            'option_id' => $option_id
        ]);
    }
                }
                $this->validate_token();
                $this->response([
            'status' => true,
            'message' => 'Update successfully.',
            'profile' => $this->profile
        ], REST_Controller::HTTP_OK);

            }
    }
    public function submit_survey_post()
{
    $this->validate_token();
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $answers = $data['answers'];
    $user_id = $this->user_id;

    foreach ($answers as $answer) {
        $question_id = $answer['question_id'];
        $option_id = $answer['option_id'];

        // Insert each answer
        $this->db->insert('responses', [
            'user_id' => $user_id,
            'question_id' => $question_id,
            'option_id' => $option_id
        ]);
    }
    $this->response([
            'status' => true,
            'message' => 'Survey responses saved.',
        ], REST_Controller::HTTP_OK);
}

}
