<?php

require APPPATH . 'core/API_Controller.php';
class Profile extends API_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user/Profile_model');
        $this->load->helper('url');
        $this->validate_token();
    }
    public function matches_get() {
        $filters = ($_GET)?$_GET:array();
        $limit = (isset($_GET['per_page']))?$_GET['per_page']:5;
$page = $this->input->get('page') ?? 1;
$page = max(1, (int)$page); // Avoid page 0 or negative

$offset = ($page - 1) * $limit;

        $matches = $this->Profile_model->get_sql_matched_profiles($this->user_id,$limit,$offset,$filters);
    $this->response([
            'status' => true,
            'data' => $matches
        ], REST_Controller::HTTP_OK);
}
    public function user_matches_get($profile_id) {
        $user_id = $this->Profile_model->get_user_id($profile_id);
        $filters = ($_GET)?$_GET:array();
        $limit = (isset($_GET['per_page']))?$_GET['per_page']:5;
$page = $this->input->get('page') ?? 1;
$page = max(1, (int)$page); // Avoid page 0 or negative

$offset = ($page - 1) * $limit;

        $matches = $this->Profile_model->get_sql_matched_profiles($user_id,$limit,$offset,$filters);
    $this->response([
            'status' => true,
            'data' => $matches
        ], REST_Controller::HTTP_OK);
}
    public function user_profile_get($id = 0) {
    $this->response([
            'status' => true,
            'data' => $this->getProfile($id),
        ], REST_Controller::HTTP_OK);
}
    public function user_about_get($id = 0) {
        $p =(array) $this->getProfile($id);
        $sur = $this->Profile_model->get_user_survey($id);
        $s = array();
        foreach($sur as $k=> $v)
        {
            $s[$v->question_id] = $v->option_text;
        }
        $sur= $s;
        $admin_aassets = $this->config->item('admin_assets');
        $about = array();

        if(isset($p['marital_status_name']) && $p['marital_status_name'])
        {
            $about[] = array('img'=>$admin_aassets.'/img/aboutdivorce.png','val'=>$p['marital_status_name']);
        }

        if(isset($p['qualification']) && $p['qualification'])
        {
            $about[] = array('img'=>$admin_aassets.'/img/qualification.jpg','val'=>$p['qualification']);
        }
        if(isset($p['religion']) && $p['religion'])
        {
            $about[] = array('img'=>$admin_aassets.'/img/religion.png','val'=>$p['religion']);
        }
        if(isset($sur[16]) && $sur[16])
        {
            $about[] = array('img'=>$admin_aassets.'/img/kids.png','val'=>$sur[16]);
        }
        if(isset($sur[17]) && $sur[17])
        {
            $about[] = array('img'=>$admin_aassets.'/img/baby.png','val'=>$sur[17]);
        }
        
        if(isset($p['height']) && $p['height'])
        {
            $about[] = array('img'=>$admin_aassets.'/img/height.png','val'=>$p['height']);
        }
    $this->response([
            'status' => true,
            'data' => $about,
        ], REST_Controller::HTTP_OK);
}
    public function index_get($id = 0) {
    $this->response([
            'status' => true,
            'data' => $this->profile
        ], REST_Controller::HTTP_OK);
}
    public function likes_get() {
        $likes = $this->Profile_model->get_likes($this->user_id);
        $n = array();
        foreach ($likes as $key => $value) {
            $n [] = $this->getProfile($this->Profile_model->get_user_id($value['profile_id']));
        }
    $this->response([
            'status' => true,
            'data' => $n
        ], REST_Controller::HTTP_OK);
}

    // Update family profile
    public function ignore_profile_post() {
        $user_id = $this->user_id;
        $profile_id = 0;

        if(isset($_POST))
        {
            $profile_id = $_POST['profile_id'];
        }
        if(!$profile_id)
        {
            $this->response(['status' => false, 'message' => 'Profile ID required.'], REST_Controller::HTTP_CONFLICT);
        }
        $r = $this->Profile_model->ignore_profile($user_id,$profile_id);
        if($r)
        {
            $this->response([
            'status' => true,
            'message' => 'Action perform successfully.',
        ], REST_Controller::HTTP_CREATED);

        }
        else
        {
            $this->response(['status' => false, 'message' => 'Server error try again'], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
    public function like_profile_post() {
        $user_id = $this->user_id;
        $profile_id = 0;

        if(isset($_POST))
        {
            $profile_id = $_POST['profile_id'];
        }
        if(!$profile_id)
        {
            $this->response(['status' => false, 'message' => 'Profile ID required.'], REST_Controller::HTTP_CONFLICT);
        }
        $r = $this->Profile_model->like_profile($user_id,$profile_id);
        if($r)
        {
            $this->response([
            'status' => true,
            'message' => 'Action perform successfully.',
        ], REST_Controller::HTTP_CREATED);

        }
        else
        {
            $this->response(['status' => false, 'message' => 'Server error try again'], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
    public function update_profile_post() {

    	$data = $_POST;
        $json = file_get_contents('php://input');

        if(!$_POST && $json)
        {
            $data = json_decode($json,true);
        }
    if ($this->profile->id) {
    	        $result = $this->Profile_model->updateFamilyProfile($this->profile->id,$data);

                if($result)
                {
                    $sur = array();
                    foreach($data as $k=> $v)
                    {

                        if (strpos($k, 'survey') !== false) {
                            $exp = explode('_',$k);
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
