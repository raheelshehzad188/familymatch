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
        return;
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
    private function getProfile($user_id)
    {
        return $profile = $this->db->where('user_id',$user_id)->get('profiles')->row();

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
