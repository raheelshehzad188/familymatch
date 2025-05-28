<?php
require APPPATH . 'libraries/REST_Controller.php';
class API_Controller extends REST_Controller {

    public function __construct() 
    {
        parent::__construct();
        // Add your custom logic here
        // For example: load a common model, helper, etc.
        $this->load->helper('url');
        // CORS headers

        $this->example_api();



    }
    private $key = '';
    private $host = '';
    protected $user_id = '';
    protected $profile = '';
    public function example_api()
{

    $user_id = $this->session->userdata('user_id') ?? null;
    $request_url = current_url();
    $request_method = $this->input->method(TRUE);
    $headers = json_encode($this->input->request_headers());
     $client_ip = $this->input->ip_address(); // ✅ IP Address capture



    // Request data handle karna GET ya POST dono ke liye
    if ($request_method === 'GET') {
        $request_data = json_encode($this->input->get());
    } else {
        $request_data = json_encode($this->input->post());
        $json = file_get_contents('php://input');

        if(!$this->input->post() && $json)
        {
            $request_data = $json;
        }
    }


    $response = ['status' => 'success', 'message' => 'API called successfully'];

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));

    $log_data = [
        'user_id' => $user_id,
        'request_url' => $request_url,
        'client_ip' => $client_ip,
        'request_method' => $request_method,
        'request_data' => $request_data,
        'request_headers' => $headers,
        'created_at' => date('Y-m-d H:i:s')
    ];
    $r = $this->insert_log($log_data);
}
    public function insert_log($data)
    {
        $this->db->insert('api_logs', $data);
        return $this->db->insert_id();
    }


    public function authenticate()
    {
        $api_key = $this->input->get_request_header('X-API-KEY');
        $this->key = $api_key;
        $host = $_SERVER['HTTP_HOST'];
        $this->host = $host;
        $this->authenticate_key();

    }
    public function generate_token($user_id) {
        $filter = ($_GET)?$_GET:array();
        // Example of creating a token. You can replace this with JWT logic.
        $payload = [
            'user_id' => $user_id,
            'timestamp' => time()
        ];
        return base64_encode(json_encode($payload)); // Just a placeholder for the token
    }
    public function validate_token()
    {
        $headers = apache_request_headers();
    $auth_header = isset($headers['Authorization']) ? $headers['Authorization'] : null;
    if(!$auth_header && isset($headers['authorization']))
    {
        $auth_header = $headers['authorization'];
    }
    if (!$auth_header || !preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        exit();
    }
    $token = $matches[1];
    $token = base64_decode($token);
    $arr = json_decode($token,true);
    if($auth_header && $arr && isset($arr['user_id']))
    {
        $this->user_id = $arr['user_id'];
        $this->profile = $this->getProfile($this->user_id);
        if(!$this->profile || !$this->user_id)
        {
            $this->response(['status' => false, 'message' => 'Unauthorized'], REST_Controller::HTTP_UNAUTHORIZED);
            return false;
        }
        return $this->user_id;



    }
    else
    {
        $this->response(['status' => false, 'message' => 'Unauthorized'], REST_Controller::HTTP_UNAUTHORIZED);
            return false;
    }

    }
    public function get_age_in_years($dob) {
    $birthDate = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y; // 'y' means full years only
    return $age;
}

    public function getsProfile($user_id)
    {
        $this->db->select('p.*, g.name as gender, r.name as reffer,b.name as body,pp.thumb_path as img,c.name as country,s.name as state,ci.name as city,rl.name as religion,ms.name as marital_status_name,ql.name as qualification');
        $this->db->from('profiles p');
        $this->db->join('genders g', 'p.gender = g.id', 'left');
        // $this->db->join('marital_statuses m', 'p.marital_status = m.id', 'left');
        // $this->db->join('education_levels e', 'p.education_level = e.id', 'left');
        $this->db->join('referrals r', 'p.reffer_id = r.id', 'left'); // optional
        $this->db->join('body_types b', 'p.body_type = b.id', 'left'); // optional
        $this->db->join('countries c', 'p.country_id = c.id', 'left'); // optional
        $this->db->join('states s', 'p.state_id = s.id', 'left'); // optional
        $this->db->join('cities ci', 'p.city_id = ci.id', 'left'); // optional
        $this->db->join('religions rl', 'p.religion_id = rl.id', 'left'); // optional
        $this->db->join('media pp', 'p.profile_pic = pp.id', 'left'); // optional
        $this->db->join('marital_status ms', 'p.marital_status = ms.id', 'left'); // optional
        $this->db->join('qualifications ql', 'p.qualification_id = ql.id', 'left'); // optional

        $this->db->where('p.user_id', $user_id);
        $query = $this->db->get();
        $profile = $query->row();
        $profile->age = $this->get_age_in_years($profile->dob);
        $profile->img = base_url($profile->img);
        $profile_id =         $profile->id;
    }
    public function getProfile($user_id)
    {
        $this->db->select('p.*, g.name as gender, r.name as reffer,b.name as body,pp.thumb_path as img,c.name as country,s.name as state,ci.name as city,rl.name as religion,ms.name as marital_status_name,ql.name as qualification');
        $this->db->from('profiles p');
        $this->db->join('genders g', 'p.gender = g.id', 'left');
        // $this->db->join('marital_statuses m', 'p.marital_status = m.id', 'left');
        // $this->db->join('education_levels e', 'p.education_level = e.id', 'left');
        $this->db->join('referrals r', 'p.reffer_id = r.id', 'left'); // optional
        $this->db->join('body_types b', 'p.body_type = b.id', 'left'); // optional
        $this->db->join('countries c', 'p.country_id = c.id', 'left'); // optional
        $this->db->join('states s', 'p.state_id = s.id', 'left'); // optional
        $this->db->join('cities ci', 'p.city_id = ci.id', 'left'); // optional
        $this->db->join('religions rl', 'p.religion_id = rl.id', 'left'); // optional
        $this->db->join('media pp', 'p.profile_pic = pp.id', 'left'); // optional
        $this->db->join('marital_status ms', 'p.marital_status = ms.id', 'left'); // optional
        $this->db->join('qualifications ql', 'p.qualification_id = ql.id', 'left'); // optional

        $this->db->where('p.user_id', $user_id);
        $query = $this->db->get();
        $profile = $query->row();
        if(!$profile)
        {
            return (object)array();

        }
            $profile->age = '';
            if($profile->dob)
            $profile->age = $this->get_age_in_years($profile->dob);
            $profile->img = base_url($profile->img);

            $profile_id =         $profile->id;
        //get interests
        $this->db->select('i.id,i.title,i.image');
        $this->db->from('profile_intersts pi');
        $this->db->join('interests i', 'pi.interest_id = i.id');
        $this->db->where('pi.profile_id', $profile_id);

         $query = $this->db->get();

        $profile->interests = $query->result();
        foreach ($profile->interests as $key => $value) {
        $profile->interests[$key]->image = base_url('uploads/interests/'.$profile->interests[$key]->image);
    }
        //get ethnicities
        $this->db->select('e.id,e.name');
    $this->db->from('profile_ethnic pi');
    $this->db->join('ethnicities e', 'pi.ethnic_id = e.id');  // Note: column name ethinc_id
    $this->db->where('pi.profile_id', $profile_id);
    $query = $this->db->get();
    $profile->ethnicities =  $query->result();
    //get values 
    $this->db->select('e.id,e.name,e.img');
    $this->db->from('profile_cvalues pi');
    $this->db->join('core_values e', 'pi.val_id = e.id');  // Note: column name ethinc_id
    $this->db->where('pi.profile_id', $profile_id);
    $query = $this->db->get();
    $profile->values = $query->result();
    foreach ($profile->values as $key => $value) {
        $profile->values[$key]->img = base_url($profile->values[$key]->img);
    }
        return $profile;

    }

    public function authenticate_key()
    {
        $api_key = $this->key;
        $r =$this->db->where('key',$this->key)->where('host',$this->host)->get('keys')->row();

        if (!$r) {
            $this->response(['status' => false, 'message' => 'Invalid API Key'], REST_Controller::HTTP_UNAUTHORIZED);
            exit();
        }
        return true;


    }
}
