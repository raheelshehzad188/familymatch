<?php

require APPPATH . 'core/API_Controller.php';
class Profile extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user/Profile_model');
        $this->load->helper('url');

    }
    public function matches_get()
    {
        $this->validate_token();
        $filters = ($_GET) ? $_GET : [];
        $limit = (isset($_GET['per_page'])) ? $_GET['per_page'] : 5;
        $page = $this->input->get('page') ?? 1;
        $page = max(1, (int)$page); // Avoid page 0 or negative

        $offset = ($page - 1) * $limit;

        $matches = $this->Profile_model->get_sql_matched_profiles($this->user_id, $limit, $offset, $filters);
        $this->response([
                'status' => true,
                'data' => $matches
            ], REST_Controller::HTTP_OK);
    }

    public function user_matches_get($profile_id)
    {
        $this->validate_token();
        $user_id = $this->Profile_model->get_user_id($profile_id);
        $filters = ($_GET) ? $_GET : [];
        $limit = (isset($_GET['per_page'])) ? $_GET['per_page'] : 5;
        $page = $this->input->get('page') ?? 1;
        $page = max(1, (int)$page); // Avoid page 0 or negative

        $offset = ($page - 1) * $limit;

        $matches = $this->Profile_model->get_sql_matched_profiles($user_id, $limit, $offset, $filters);
        $this->response([
                'status' => true,
                'data' => $matches
            ], REST_Controller::HTTP_OK);
    }
    public function user_profile_get($id = 0)
    {
        $this->response([
                'status' => true,
                'data' => $this->getProfile($id),
            ], REST_Controller::HTTP_OK);
    }
    public function results_login_get()
    {
        $this->validate_token();

        $this->load->model('user/Gernal_model');
        $filters = ($_GET) ? $_GET : [];
        $filters['user_id'] = $this->user_id;
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
    public function user_about_get($id = 0)
    {
        $p = (array) $this->getProfile($id);
        $sur = $this->Profile_model->get_user_survey($id);
        $s = [];
        foreach ($sur as $k => $v) {
            $s[$v->question_id] = $v->option_text;
        }
        $sur = $s;
        $admin_aassets = $this->config->item('admin_assets');
        $about = [];

        if (isset($p['marital_status_name']) && $p['marital_status_name']) {
            $about[] = ['img' => $admin_aassets.'/img/aboutdivorce.png','val' => $p['marital_status_name']];
        }

        if (isset($p['qualification']) && $p['qualification']) {
            $about[] = ['img' => $admin_aassets.'/img/qualification.jpg','val' => $p['qualification']];
        }
        if (isset($p['religion']) && $p['religion']) {
            $about[] = ['img' => $admin_aassets.'/img/religion.png','val' => $p['religion']];
        }
        if (isset($sur[16]) && $sur[16]) {
            $about[] = ['img' => $admin_aassets.'/img/kids.png','val' => $sur[16]];
        }
        if (isset($sur[17]) && $sur[17]) {
            $about[] = ['img' => $admin_aassets.'/img/baby.png','val' => $sur[17]];
        }

        if (isset($p['height']) && $p['height']) {
            $about[] = ['img' => $admin_aassets.'/img/height.png','val' => $p['height']];
        }
        $this->response([
                'status' => true,
                'data' => $about,
            ], REST_Controller::HTTP_OK);
    }
    public function index_get($id = 0)
    {

        $this->validate_token();
        $this->response([
                'status' => true,
                'data' => $this->profile
            ], REST_Controller::HTTP_OK);
    }
    public function likes_get()
    {
        $this->validate_token();
        $likes = $this->Profile_model->get_likes($this->user_id);
        $n = [];
        foreach ($likes as $key => $value) {
            $n [] = $this->getShortProfile($this->Profile_model->get_user_id($value['profile_id']));
        }
        $this->response([
                'status' => true,
                'data' => $n
            ], REST_Controller::HTTP_OK);
    }
    public function winks_get()
    {
        $this->validate_token();
        $likes = $this->Profile_model->get_winks($this->user_id);
        $n = [];
        foreach ($likes as $key => $value) {
            $n [] = $this->getProfile($this->Profile_model->get_user_id($value['profile_id']));
        }
        $this->response([
                'status' => true,
                'data' => $n
            ], REST_Controller::HTTP_OK);
    }

    // Update family profile
    public function ignore_profile_post()
    {
        $this->validate_token();
        $user_id = $this->user_id;
        $profile_id = 0;

        if (isset($_POST)) {
            $profile_id = $_POST['profile_id'];
        }
        if (!$profile_id) {
            $this->response(['status' => false, 'message' => 'Profile ID required.'], REST_Controller::HTTP_CONFLICT);
        }
        $r = $this->Profile_model->ignore_profile($user_id, $profile_id);
        if ($r) {
            $this->response([
            'status' => true,
            'is_ignored' => $this->is_ignored($user_id, $profile_id),
            'message' => 'Action perform successfully.',
        ], REST_Controller::HTTP_CREATED);

        } else {
            $this->response(['status' => false, 'message' => 'Server error try again'], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function ignores_get()
    {
        $this->validate_token();
        $ignores = $this->Profile_model->get_ignores($this->user_id);
        $n = [];
        foreach ($ignores as $key => $value) {
            $n [] = $this->getShortProfile($this->Profile_model->get_user_id($value['profile_id']));
        }
        $this->response([
            'status' => true,
            'data' => $n
        ], REST_Controller::HTTP_OK);
    }

    public function like_profile_post()
    {
        $this->validate_token();
        $user_id = $this->user_id;
        $profile_id = 0;

        if (isset($_POST)) {
            $profile_id = $_POST['profile_id'];
        }
        if (!$profile_id) {
            $this->response(['status' => false, 'message' => 'Profile ID required.'], REST_Controller::HTTP_CONFLICT);
        }
        $r = $this->Profile_model->like_profile($user_id, $profile_id);
        if ($r) {
            $this->response([
            'status' => true,
            'is_like' => $this->is_like($user_id, $profile_id),
            'message' => 'Action perform successfully.',
        ], REST_Controller::HTTP_CREATED);

        } else {
            $this->response(['status' => false, 'message' => 'Server error try again'], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
    public function wink_profile_post()
    {
        $this->validate_token();
        $user_id = $this->user_id;
        $profile_id = 0;

        if (isset($_POST)) {
            $profile_id = $_POST['profile_id'];
        }
        if (!$profile_id) {
            $this->response(['status' => false, 'message' => 'Profile ID required.'], REST_Controller::HTTP_CONFLICT);
        }
        $r = $this->Profile_model->wink_profile($user_id, $profile_id);
        if ($r) {
            $this->response([
            'status' => true,
            'message' => 'Action perform successfully.',
        ], REST_Controller::HTTP_CREATED);

        } else {
            $this->response(['status' => false, 'message' => 'Server error try again'], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
    public function update_profile_post()
    {

        $data = $_POST;
        $json = file_get_contents('php://input');

        if (!$_POST && $json) {
            $data = json_decode($json, true);
        }
        if ($this->profile->id) {
            $result = $this->Profile_model->updateFamilyProfile($this->profile->id, $data);

            if ($result) {
                $sur = [];
                foreach ($data as $k => $v) {

                    if (strpos($k, 'survey') !== false) {
                        $exp = explode('_', $k);
                        $sur[$exp[1]] = $v;
                    }

                }
                $user_id = $this->user_id;

                foreach ($sur as $q => $answer) {
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

    public function favorite_profile_post()
    {
        $this->validate_token();
        $user_id = $this->user_id;
        $profile_id = 0;

        if (isset($_POST)) {
            $profile_id = $_POST['profile_id'];
        }
        if (!$profile_id) {
            $this->response(['status' => false, 'message' => 'Profile ID required.'], REST_Controller::HTTP_CONFLICT);
        }
        $r = $this->Profile_model->favorite_profile($user_id, $profile_id);
        if ($r) {
            $this->response([
            'status' => true,
            'is_favorite' => $this->is_favorite($user_id, $profile_id),
            'message' => 'Action perform successfully.',
        ], REST_Controller::HTTP_CREATED);

        } else {
            $this->response(['status' => false, 'message' => 'Server error try again'], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function favorites_get()
    {
        $this->validate_token();
        $favorites = $this->Profile_model->get_favorites($this->user_id);
        $n = [];
        foreach ($favorites as $key => $value) {
            $n [] = $this->getShortProfile($this->Profile_model->get_user_id($value['profile_id']));
        }
        $this->response([
            'status' => true,
            'data' => $n
        ], REST_Controller::HTTP_OK);
    }

    public function change_password_post()
    {
        $this->validate_token();
        $user_id = $this->user_id;
        $old_password = $this->post('old_password');
        $new_password = $this->post('new_password');
        $confirm_password = $this->post('confirm_password');

        if (!$old_password || !$new_password || !$confirm_password) {
            $this->response(['status' => false, 'message' => 'All fields are required.'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        if ($new_password !== $confirm_password) {
            $this->response(['status' => false, 'message' => 'New and confirm password do not match.'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        // Get user row
        $user = $this->db->where('id', $user_id)->get('users')->row();
        if (!$user) {
            $this->response(['status' => false, 'message' => 'User not found.'], REST_Controller::HTTP_NOT_FOUND);
            return;
        }
        // Verify old password
        if (!password_verify($old_password, $user->password)) {
            $this->response(['status' => false, 'message' => 'Old password is incorrect.'], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        // Update password
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $this->db->where('id', $user_id)->update('users', ['password' => $hashed]);
        $this->response(['status' => true, 'message' => 'Password changed successfully.'], REST_Controller::HTTP_OK);
    }

    public function forgot_password_post()
    {
        $email = $this->post('email');
        if (!$email) {
            $this->response(['status' => false, 'message' => 'Email is required.'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $user = $this->db->where('email', $email)->get('users')->row();
        if (!$user) {
            $this->response(['status' => false, 'message' => 'Email not found.'], REST_Controller::HTTP_NOT_FOUND);
            return;
        }
        $token = bin2hex(random_bytes(32));
        $this->load->model('user/User_model');
        $this->User_model->set_reset_token($email, $token);

        $reset_link = base_url("reset-password?token=$token");

        $this->load->library('email');
        // No need to initialize config, it will use application/config/email.php
        $this->email->from('your_email@gmail.com', 'Your App Name'); // <-- change this
        $this->email->to($email);
        $this->email->subject('Password Reset Request');
        $this->email->message("Click the link to reset your password: $reset_link");

        if ($this->email->send()) {
            $this->response(['status' => true, 'message' => 'Password reset link sent to your email.'], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Failed to send email.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function reset_password_post()
    {
        $token = $this->post('token');
        $new_password = $this->post('new_password');
        $confirm_password = $this->post('confirm_password');

        if (!$token || !$new_password || !$confirm_password) {
            $this->response(['status' => false, 'message' => 'All fields are required.'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        if ($new_password !== $confirm_password) {
            $this->response(['status' => false, 'message' => 'Passwords do not match.'], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $this->load->model('user/User_model');
        $user = $this->User_model->get_user_by_token($token);
        if (!$user) {
            $this->response(['status' => false, 'message' => 'Invalid or expired token.'], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $this->db->where('id', $user->id)->update('users', ['password' => $hashed]);
        $this->User_model->clear_reset_token($user->id);

        $this->response(['status' => true, 'message' => 'Password reset successfully.'], REST_Controller::HTTP_OK);
    }

}
