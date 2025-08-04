<?php

require APPPATH . 'core/API_Controller.php';

class Message extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    /**
     * Send message API endpoint
     * POST /api/message/send_msg
     */
    public function send_msg_post()
    {
        // Get input data
        $data = $_POST;
        $json = file_get_contents('php://input');
        
        if (!$_POST && $json) {
            $data = json_decode($json, true);
        }

        // Set validation rules
        $this->form_validation->set_rules('message', 'Message', 'required|trim|max_length[1000]');
        $this->form_validation->set_rules('receiver_id', 'Receiver ID', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('sender_id', 'Sender ID', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('msg_type', 'Message Type', 'trim|in_list[txt,img,video,audio,file]');
        
        // Run validation
        if ($this->form_validation->run() === FALSE) {
            $errors = $this->form_validation->error_array();
            $this->response([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Get validated data
        $message = $this->input->post('message');
        $receiver_id = $this->input->post('receiver_id');
        $sender_id = $this->input->post('sender_id');
        $msg_type = $this->input->post('msg_type') ?: 'txt';
        
        // Get username from profiles table
        $sender_profile = $this->getShortProfile($sender_id);
        $username = $sender_profile->full_name ?? 'Unknown User';
        
        // Validate receiver exists
        $receiver_exists = $this->getShortProfile($receiver_id);
        if (!$receiver_exists) {
            $this->response([
                'status' => false,
                'message' => 'Receiver not found'
            ], REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        // Prepare message data
        $message_data = [
            'username' => $username,
            'message' => $message,
            'receiver_id' => $receiver_id,
            'sender_id' => $sender_id,
            'msg_type' => $msg_type,
            'createdAt' => date('Y-m-d H:i:s')
        ];

        // Insert message into database
        $inserted = $this->db->insert('messages', $message_data);
        
        if ($inserted) {
            $message_id = $this->db->insert_id();
            
            // Get the inserted message with additional info
            $inserted_message = $this->db->where('id', $message_id)->get('messages')->row();
            
            $this->response([
                'status' => true,
                'message' => 'Message sent successfully',
                'data' => [
                    'id' => $inserted_message->id,
                    'username' => $inserted_message->username,
                    'message' => $inserted_message->message,
                    'receiver_id' => $inserted_message->receiver_id,
                    'sender_id' => $inserted_message->sender_id,
                    'msg_type' => $inserted_message->msg_type,
                    'createdAt' => $inserted_message->createdAt
                ]
            ], REST_Controller::HTTP_CREATED);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Failed to send message'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get chat between two users
     * GET /api/message/chat?login_id=LOGIN_USER_ID&user_id=OTHER_USER_ID
     */
    public function chat_get()
    {
        $login_id = $this->input->get('login_id') ?: $this->input->post('login_id');
        $user_id = $this->input->get('user_id') ?: $this->input->post('user_id');

        if (!$login_id || !$user_id || !is_numeric($login_id) || !is_numeric($user_id)) {
            $this->response([
                'status' => false,
                'message' => 'Both login_id and user_id are required and must be numeric.'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Get all messages between these two users
        $messages = $this->db
            ->where("(sender_id = $login_id AND receiver_id = $user_id) OR (sender_id = $user_id AND receiver_id = $login_id)")
            ->order_by('createdAt', 'ASC')
            ->get('messages')
            ->result_array();

        // Get other user's profile details
        $other_user_profile = $this->getShortProfile($user_id);
        $user_info = [
            'user_id' => $user_id,
            'user_name' => $other_user_profile->full_name ?? 'Unknown User',
            'user_avatar' => $other_user_profile->profile_pic ?? null,
            'country' => $other_user_profile->country ?? null,
            'city' => $other_user_profile->city ?? null
        ];

        $this->response([
            'status' => true,
            'user_info' => $user_info,
            'data' => $messages
        ], REST_Controller::HTTP_OK);
    }

    /**
     * Get list of all users with whom login user has had conversations
     * GET /api/message/conversations_list?login_id=LOGIN_USER_ID
     */
    public function conversations_list_get()
    {
        $login_id = $this->input->get('login_id') ?: $this->input->post('login_id');

        if (!$login_id || !is_numeric($login_id)) {
            $this->response([
                'status' => false,
                'message' => 'login_id is required and must be numeric.'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Get unique conversations with last message time
        $conversations = $this->db->select('
                CASE 
                    WHEN sender_id = ' . $login_id . ' THEN receiver_id 
                    ELSE sender_id 
                END as other_user_id,
                MAX(createdAt) as last_message_time,
                COUNT(*) as message_count', false)
                ->where("sender_id = $login_id OR receiver_id = $login_id")
                ->group_by('other_user_id')
                ->order_by('last_message_time', 'DESC')
                ->get('messages')
                ->result_array();

        // Get user details for each conversation
        $formatted_conversations = [];
        foreach ($conversations as $conv) {
            $user_profile = $this->getShortProfile($conv['other_user_id']);
            
            // Get the last message content
            $last_message = $this->db->where("(sender_id = $login_id AND receiver_id = {$conv['other_user_id']}) OR (sender_id = {$conv['other_user_id']} AND receiver_id = $login_id)")
                                    ->order_by('createdAt', 'DESC')
                                    ->limit(1)
                                    ->get('messages')
                                    ->row();
            
            $formatted_conversations[] = [
                'id' => $conv['other_user_id'],
                'name' => $user_profile->full_name ?? 'Unknown User',
                'avatar' => $user_profile->profile_pic ?? null,
                'slug' => $user_profile->slug ?? null,
                'last_message' => $last_message ? $last_message->message : '',
                'type' => $last_message ? $last_message->msg_type : '',
                'createdAt' => $last_message ? $last_message->createdAt : $conv['last_message_time']
            ];
        }

        $this->response([
            'status' => true,
            'data' => $formatted_conversations
        ], REST_Controller::HTTP_OK);
    }
}
