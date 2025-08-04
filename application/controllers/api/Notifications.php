<?php

require APPPATH . 'core/API_Controller.php';

class Notifications extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    /**
     * Add new notification
     * POST /api/notifications/add
     */
    public function add_post()
    {
        // Get input data
        $data = $_POST;
        $json = file_get_contents('php://input');
        
        if (!$_POST && $json) {
            $data = json_decode($json, true);
        }

        // Set validation rules
        $this->form_validation->set_rules('user_id', 'User ID', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('from_user_id', 'From User ID', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('type', 'Notification Type', 'required|in_list[invitation,request,wink,like,message,visit,accept,reject]');
        $this->form_validation->set_rules('title', 'Title', 'trim|max_length[200]');
        $this->form_validation->set_rules('message', 'Message', 'trim|max_length[500]');
        $this->form_validation->set_rules('reference_id', 'Reference ID', 'trim|numeric');
        
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
        
        // Additional validation for non-wink types
        $type = $this->input->post('type');
        $title = $this->input->post('title');
        $message = $this->input->post('message');
        
        if ($type !== 'wink' && $type !== 'friend_request' && $type !== 'like') {
            if (empty($title)) {
                $this->response([
                    'status' => false,
                    'message' => 'Title is required for notifications other than wink, friend_request and like',
                    'errors' => ['title' => 'Title is required']
                ], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }
            
            if (empty($message)) {
                $this->response([
                    'status' => false,
                    'message' => 'Message is required for notifications other than wink, friend_request and like',
                    'errors' => ['message' => 'Message is required']
                ], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }
        }

        // Get validated data
        $user_id = $this->input->post('user_id');
        $from_user_id = $this->input->post('from_user_id');
        $type = $this->input->post('type');
        $title = $this->input->post('title');
        $message = $this->input->post('message');
        $reference_id = $this->input->post('reference_id');
        
        // Auto-generate message for wink type
        if ($type === 'wink') {
            $from_user_profile = $this->getShortProfile($from_user_id);
            $to_user_profile = $this->getShortProfile($user_id);
            $from_user_name = $from_user_profile->full_name ?? 'Someone';
            $to_user_name = $to_user_profile->full_name ?? 'you';
            $message = $from_user_name . ' sent wink to ' . $to_user_name . '! 😉';
            $title = 'New Wink';
        }
        
        // Auto-generate message for friend_request type
        if ($type === 'friend_request') {
            $from_user_profile = $this->getShortProfile($from_user_id);
            $to_user_profile = $this->getShortProfile($user_id);
            $from_user_name = $from_user_profile->full_name ?? 'Someone';
            $to_user_name = $to_user_profile->full_name ?? 'you';
            $message = $from_user_name . ' sent friend request to ' . $to_user_name . '! 👥';
            $title = 'New Friend Request';
        }
        
        // Auto-generate message for like type
        if ($type === 'like') {
            $from_user_profile = $this->getShortProfile($from_user_id);
            $to_user_profile = $this->getShortProfile($user_id);
            $from_user_name = $from_user_profile->full_name ?? 'Someone';
            $to_user_name = $to_user_profile->full_name ?? 'you';
            $message = $from_user_name . ' liked ' . $to_user_name . '! ❤️';
            $title = 'New Like';
        }
        
        // Get from user's name
        $from_user_profile = $this->getShortProfile($from_user_id);
        $from_user_name = $from_user_profile->full_name ?? 'Unknown User';
        
        // Validate user exists
        $user_exists = $this->getShortProfile($user_id);
        if (!$user_exists) {
            $this->response([
                'status' => false,
                'message' => 'User not found'
            ], REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        // Prepare notification data
        $notification_data = [
            'user_id' => $user_id,
            'from_user_id' => $from_user_id,
            'from_user_name' => $from_user_name,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'reference_id' => $reference_id,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Insert notification into database
        $inserted = $this->db->insert('notifications', $notification_data);
        
        if ($inserted) {
            $notification_id = $this->db->insert_id();
            
            // Get the inserted notification
            $inserted_notification = $this->db->where('id', $notification_id)->get('notifications')->row();
            
            $this->response([
                'status' => true,
                'message' => 'Notification added successfully',
                'data' => [
                    'id' => $inserted_notification->id,
                    'user_id' => $inserted_notification->user_id,
                    'from_user_id' => $inserted_notification->from_user_id,
                    'from_user_name' => $inserted_notification->from_user_name,
                    'type' => $inserted_notification->type,
                    'title' => $inserted_notification->title,
                    'message' => $inserted_notification->message,
                    'reference_id' => $inserted_notification->reference_id,
                    'is_read' => $inserted_notification->is_read,
                    'created_at' => $inserted_notification->created_at
                ]
            ], REST_Controller::HTTP_CREATED);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Failed to add notification'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get notifications list for a user
     * GET /api/notifications/list?user_id=USER_ID
     */
    public function list_get()
    {
        $user_id = $this->input->get('user_id') ?: $this->input->post('user_id');

        if (!$user_id || !is_numeric($user_id)) {
            $this->response([
                'status' => false,
                'message' => 'user_id is required and must be numeric.'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Get notifications for the user
        $notifications = $this->db->where('user_id', $user_id)
                                 ->order_by('created_at', 'DESC')
                                 ->get('notifications')
                                 ->result_array();

        // Format notifications
        $formatted_notifications = [];
        foreach ($notifications as $notification) {
            // Get from user's profile details
            $from_user_profile = $this->getShortProfile($notification['from_user_id']);
            
            $formatted_notifications[] = [
                'id' => $notification['id'],
                'type' => $notification['type'],
                'title' => $notification['title'],
                'message' => $notification['message'],
                'from_user' => [
                    'id' => $notification['from_user_id'],
                    'name' => $from_user_profile->full_name ?? 'Unknown User',
                    'avatar' => $from_user_profile->profile_pic ?? null,
                    'slug' => $from_user_profile->slug ?? null
                ],
                'reference_id' => $notification['reference_id'],
                'is_read' => (bool)$notification['is_read'],
                'created_at' => $notification['created_at']
            ];
        }

        $this->response([
            'status' => true,
            'data' => $formatted_notifications
        ], REST_Controller::HTTP_OK);
    }

    /**
     * Mark notification as read
     * POST /api/notifications/mark_read
     */
    public function mark_read_post()
    {
        $notification_id = $this->input->post('notification_id');
        $user_id = $this->input->post('user_id');

        if (!$notification_id || !$user_id) {
            $this->response([
                'status' => false,
                'message' => 'Both notification_id and user_id are required and must be numeric.'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Update notification as read
        $updated = $this->db->where('id', $notification_id)
                           ->where('user_id', $user_id)
                           ->update('notifications', ['is_read' => 1]);

        if ($updated) {
            $this->response([
                'status' => true,
                'message' => 'Notification marked as read'
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Failed to mark notification as read'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Mark all notifications as read for a user
     * POST /api/notifications/mark_all_read
     */
    public function mark_all_read_post()
    {
        $user_id = $this->input->post('user_id');

        if (!$user_id || !is_numeric($user_id)) {
            $this->response([
                'status' => false,
                'message' => 'user_id is required and must be numeric.'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Update all notifications as read for the user
        $updated = $this->db->where('user_id', $user_id)
                           ->update('notifications', ['is_read' => 1]);

        if ($updated) {
            $this->response([
                'status' => true,
                'message' => 'All notifications marked as read'
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Failed to mark notifications as read'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get unread notifications count
     * GET /api/notifications/unread_count?user_id=USER_ID
     */
    public function unread_count_get()
    {
        $user_id = $this->input->get('user_id') ?: $this->input->post('user_id');

        if (!$user_id || !is_numeric($user_id)) {
            $this->response([
                'status' => false,
                'message' => 'user_id is required and must be numeric.'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Count unread notifications
        $count = $this->db->where('user_id', $user_id)
                         ->where('is_read', 0)
                         ->count_all_results('notifications');

        $this->response([
            'status' => true,
            'data' => [
                'unread_count' => $count
            ]
        ], REST_Controller::HTTP_OK);
    }
} 