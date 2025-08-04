<?php

require APPPATH . 'core/API_Controller.php';

class Invitations extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    /**
     * Send invitation request
     * POST /api/invitations/send
     */
    public function send_post()
    {
        // Validate token and get user ID
        $this->validate_token();
        $from_user_id = $this->user_id;
        
        // Debug: Check if user_id is set
        if (!$from_user_id) {
            $this->response([
                'status' => false,
                'message' => 'Token validation failed - user_id not found',
                'debug' => ['user_id' => $from_user_id]
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        
        // Get input data
        $data = $_POST;
        $json = file_get_contents('php://input');
        
        if (!$_POST && $json) {
            $data = json_decode($json, true);
        }

        // Set validation rules
        $this->form_validation->set_rules('to_user_id', 'To User ID', 'trim|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('invitation_type', 'Invitation Type', 'required|in_list[meeting,friend,marriage]');
        $this->form_validation->set_rules('message', 'Message', 'required|trim|max_length[500]');
        $this->form_validation->set_rules('meeting_date', 'Meeting Date', 'trim');
        $this->form_validation->set_rules('meeting_location', 'Meeting Location', 'trim|max_length[200]');
        
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

        // Get validated data - handle both JSON and form data
        $to_user_id = $this->input->post('to_user_id');
        $invitation_type = $this->input->post('invitation_type');
        $message = $this->input->post('message');
        $meeting_date = $this->input->post('meeting_date');
        $meeting_location = $this->input->post('meeting_location');
        
        // If JSON data is provided, use it
        if ($json && !$_POST) {
            $json_data = json_decode($json, true);
            if ($json_data) {
                $to_user_id = $json_data['to_user_id'] ?? $to_user_id;
                $invitation_type = $json_data['invitation_type'] ?? $invitation_type;
                $message = $json_data['message'] ?? $message;
                $meeting_date = $json_data['meeting_date'] ?? $meeting_date;
                $meeting_location = $json_data['meeting_location'] ?? $meeting_location;
            }
        }
        
        // Get user profiles
        $from_user_profile = $this->getShortProfile($from_user_id);
        
        // Debug: Check from user profile
        if (!$from_user_profile) {
            $this->response([
                'status' => false,
                'message' => 'From user profile not found',
                'debug' => ['from_user_id' => $from_user_id]
            ], REST_Controller::HTTP_NOT_FOUND);
            return;
        }
        
        // Check to_user_id if provided
        if ($to_user_id) {
            $to_user_profile = $this->getShortProfile($to_user_id);
            if (!$to_user_profile) {
                $this->response([
                    'status' => false,
                    'message' => 'To user profile not found',
                    'debug' => ['to_user_id' => $to_user_id]
                ], REST_Controller::HTTP_NOT_FOUND);
                return;
            }
        }

        // Check if invitation already exists (only if to_user_id is provided)
        if ($to_user_id) {
            $existing_invitation = $this->db->where('from_user_id', $from_user_id)
                                          ->where('to_user_id', $to_user_id)
                                          ->where('invitation_type', $invitation_type)
                                          ->where('status', 'pending')
                                          ->get('invitations')
                                          ->row();
            
            if ($existing_invitation) {
                $this->response([
                    'status' => false,
                    'message' => 'Invitation already sent'
                ], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }
        }

        // Prepare invitation data
        $invitation_data = [
            'from_user_id' => $from_user_id,
            'to_user_id' => $to_user_id ?: null,
            'from_user_name' => $from_user_profile->full_name ?: 'Unknown User',
            'invitation_type' => $invitation_type,
            'message' => $message,
            'meeting_date' => $meeting_date,
            'meeting_location' => $meeting_location,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Debug: Check if invitations table exists
        if (!$this->db->table_exists('invitations')) {
            $this->response([
                'status' => false,
                'message' => 'Invitations table does not exist',
                'debug' => ['table_exists' => $this->db->table_exists('invitations')]
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }
        
        // Insert invitation
        $inserted = $this->db->insert('invitations', $invitation_data);
        
        if ($inserted) {
            $invitation_id = $this->db->insert_id();
            
            // Send notification to recipient (only if to_user_id is provided)
            if ($to_user_id) {
                $notification_data = [
                    'user_id' => $to_user_id,
                    'from_user_id' => $from_user_id,
                    'type' => 'invitation',
                    'title' => 'New Invitation',
                    'message' => $from_user_profile->full_name . ' sent you an invitation',
                    'reference_id' => $invitation_id
                ];
                $this->db->insert('notifications', $notification_data);
            }
            
            // Get the inserted invitation
            $inserted_invitation = $this->db->where('id', $invitation_id)->get('invitations')->row();
            
            $this->response([
                'status' => true,
                'message' => 'Invitation sent successfully',
                'data' => [
                    'id' => $inserted_invitation->id,
                    'from_user_id' => $inserted_invitation->from_user_id,
                    'to_user_id' => $inserted_invitation->to_user_id,
                    'invitation_type' => $inserted_invitation->invitation_type,
                    'message' => $inserted_invitation->message,
                    'status' => $inserted_invitation->status,
                    'created_at' => $inserted_invitation->created_at
                ]
            ], REST_Controller::HTTP_CREATED);
        } else {
            dd($this->db->last_query());
            $this->response([
                'status' => false,
                'message' => 'Failed to send invitation'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Approve/Reject invitation
     * POST /api/invitations/respond
     */
    public function respond_post()
    {
        // Validate token and get user ID
        $this->validate_token();
        $user_id = $this->user_id;
        
        // Set validation rules
        $this->form_validation->set_rules('invitation_id', 'Invitation ID', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('action', 'Action', 'required|in_list[approve,reject]');
        $this->form_validation->set_rules('response_message', 'Response Message', 'trim|max_length[500]');
        
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
        $invitation_id = $this->input->post('invitation_id');
        $action = $this->input->post('action');
        $response_message = $this->input->post('response_message');
        
        // Get invitation
        $invitation = $this->db->where('id', $invitation_id)
                              ->where('to_user_id', $user_id)
                              ->where('status', 'pending')
                              ->get('invitations')
                              ->row();
        
        if (!$invitation) {
            $this->response([
                'status' => false,
                'message' => 'Invitation not found or already processed'
            ], REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        // Update invitation status
        $status = ($action == 'approve') ? 'approved' : 'rejected';
        $updated = $this->db->where('id', $invitation_id)
                           ->update('invitations', [
                               'status' => $status,
                               'response_message' => $response_message,
                               'responded_at' => date('Y-m-d H:i:s')
                           ]);
        
        if ($updated) {
            // Send notification to sender
            $notification_data = [
                'user_id' => $invitation->from_user_id,
                'from_user_id' => $user_id,
                'type' => $action,
                'title' => 'Invitation ' . ucfirst($action),
                'message' => 'Your invitation has been ' . $action,
                'reference_id' => $invitation_id
            ];
            $this->db->insert('notifications', $notification_data);
            
            // If approved, create friendship for all invitation types
            if ($action == 'approve') {
                // Check if friendship already exists
                $existing_friendship = $this->db->where('user_id', $invitation->from_user_id)
                                              ->where('friend_id', $invitation->to_user_id)
                                              ->get('friendships')
                                              ->row();
                
                if (!$existing_friendship) {
                    // Add to friends table (bidirectional friendship)
                    $friendship_data1 = [
                        'user_id' => $invitation->from_user_id,
                        'friend_id' => $invitation->to_user_id,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('friendships', $friendship_data1);
                    
                    // Also add reverse friendship
                    $friendship_data2 = [
                        'user_id' => $invitation->to_user_id,
                        'friend_id' => $invitation->from_user_id,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('friendships', $friendship_data2);
                }
            }
            
            $this->response([
                'status' => true,
                'message' => 'Invitation ' . $action . ' successfully',
                'data' => [
                    'invitation_id' => $invitation_id,
                    'status' => $status,
                    'response_message' => $response_message
                ]
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Failed to process invitation'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get invitations for a user (sent and received)
     * GET /api/invitations/list?type=sent|received
     */
    public function list_get()
    {
        // Validate token and get user ID
        $this->validate_token();
        $user_id = $this->user_id;
        $type = $this->input->get('type') ?: 'received'; // sent or received

        if ($type == 'sent') {
            $this->db->where('from_user_id', $user_id);
        } else {
            $this->db->where('to_user_id', $user_id);
        }

        // Get invitations
        $invitations = $this->db->order_by('created_at', 'DESC')
                               ->get('invitations')
                               ->result_array();

        // Format invitations
        $formatted_invitations = [];
        foreach ($invitations as $invitation) {
            $other_user_id = ($type == 'sent') ? $invitation['to_user_id'] : $invitation['from_user_id'];
            $other_user_profile = $this->getShortProfile($other_user_id);
            
            $formatted_invitations[] = [
                'id' => $invitation['id'],
                'invitation_type' => $invitation['invitation_type'],
                'message' => $invitation['message'],
                'status' => $invitation['status'],
                'meeting_date' => $invitation['meeting_date'],
                'meeting_location' => $invitation['meeting_location'],
                'response_message' => $invitation['response_message'],
                'other_user' => [
                    'id' => $other_user_id,
                    'name' => $other_user_profile->full_name ?? 'Unknown User',
                    'avatar' => $other_user_profile->profile_pic ?? null,
                    'slug' => $other_user_profile->slug ?? null
                ],
                'created_at' => $invitation['created_at'],
                'responded_at' => $invitation['responded_at']
            ];
        }

        $this->response([
            'status' => true,
            'data' => $formatted_invitations
        ], REST_Controller::HTTP_OK);
    }

    /**
     * Send friend request
     * POST /api/invitations/friend_request
     */
    public function friend_request_post()
    {
        // Validate token and get user ID
        $this->validate_token();
        $from_user_id = $this->user_id;
        
        // Set validation rules
        $this->form_validation->set_rules('to_user_id', 'To User ID', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('message', 'Message', 'trim|max_length[500]');
        
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
        $to_user_id = $this->input->post('to_user_id');
        $message = $this->input->post('message') ?: 'I would like to be your friend';
        
        // Check if friend request already exists
        $existing_request = $this->db->where('from_user_id', $from_user_id)
                                   ->where('to_user_id', $to_user_id)
                                   ->where('invitation_type', 'friend')
                                   ->where('status', 'pending')
                                   ->get('invitations')
                                   ->row();
        
        if ($existing_request) {
            $this->response([
                'status' => false,
                'message' => 'Friend request already sent'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Send friend request using the same invitation system
        $invitation_data = [
            'from_user_id' => $from_user_id,
            'to_user_id' => $to_user_id,
            'invitation_type' => 'friend',
            'message' => $message,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $inserted = $this->db->insert('invitations', $invitation_data);
        
        if ($inserted) {
            $invitation_id = $this->db->insert_id();
            
            // Send notification
            $from_user_profile = $this->getShortProfile($from_user_id);
            $notification_data = [
                'user_id' => $to_user_id,
                'from_user_id' => $from_user_id,
                'type' => 'request',
                'title' => 'New Friend Request',
                'message' => $from_user_profile->full_name . ' sent you a friend request',
                'reference_id' => $invitation_id
            ];
            $this->db->insert('notifications', $notification_data);
            
            $this->response([
                'status' => true,
                'message' => 'Friend request sent successfully',
                'data' => [
                    'invitation_id' => $invitation_id,
                    'message' => $message
                ]
            ], REST_Controller::HTTP_CREATED);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Failed to send friend request'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get friends list
     * GET /api/invitations/friends
     */
    public function friends_get()
    {
        // Validate token and get user ID
        $this->validate_token();
        $user_id = $this->user_id;

        // Get friends from friendships table
        $friends = $this->db->select('f.*, u.id as friend_id')
                           ->from('friendships f')
                           ->join('users u', 'u.id = f.friend_id')
                           ->where('f.user_id', $user_id)
                           ->get()
                           ->result_array();

        // Format friends list
        $formatted_friends = [];
        foreach ($friends as $friend) {
            $friend_profile = $this->getShortProfile($friend['friend_id']);
            
            $formatted_friends[] = [
                'id' => $friend['friend_id'],
                'name' => $friend_profile->full_name ?? 'Unknown User',
                'avatar' => $friend_profile->profile_pic ?? null,
                'slug' => $friend_profile->slug ?? null,
                'friendship_date' => $friend['created_at']
            ];
        }

        $this->response([
            'status' => true,
            'data' => $formatted_friends
        ], REST_Controller::HTTP_OK);
    }
    
    /**
     * Check if two users are friends
     * GET /api/invitations/check_friendship?user_id=USER_ID
     */
    public function check_friendship_get()
    {
        // Validate token and get user ID
        $this->validate_token();
        $current_user_id = $this->user_id;
        
        $other_user_id = $this->input->get('user_id');
        
        if (!$other_user_id || !is_numeric($other_user_id)) {
            $this->response([
                'status' => false,
                'message' => 'user_id is required and must be numeric.'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        
        // Check if friendship exists
        $friendship = $this->db->where('user_id', $current_user_id)
                              ->where('friend_id', $other_user_id)
                              ->get('friendships')
                              ->row();
        
        $this->response([
            'status' => true,
            'data' => [
                'are_friends' => $friendship ? true : false,
                'friendship_date' => $friendship ? $friendship->created_at : null
            ]
        ], REST_Controller::HTTP_OK);
    }
    
    /**
     * Get count of received invitations
     * GET /api/invitations/received_count
     */
    public function received_count_get()
    {
        // Validate token and get user ID
        $this->validate_token();
        $user_id = $this->user_id;

        // Count received invitations (pending, approved, rejected)
        $total_count = $this->db->where('to_user_id', $user_id)
                               ->count_all_results('invitations');
        
        // Count pending invitations
        $pending_count = $this->db->where('to_user_id', $user_id)
                                 ->where('status', 'pending')
                                 ->count_all_results('invitations');
        
        // Count approved invitations
        $approved_count = $this->db->where('to_user_id', $user_id)
                                  ->where('status', 'approved')
                                  ->count_all_results('invitations');
        
        // Count rejected invitations
        $rejected_count = $this->db->where('to_user_id', $user_id)
                                  ->where('status', 'rejected')
                                  ->count_all_results('invitations');

        $this->response([
            'status' => true,
            'data' => [
                'total_received' => $total_count,
                'pending_count' => $pending_count,
                'approved_count' => $approved_count,
                'rejected_count' => $rejected_count
            ]
        ], REST_Controller::HTTP_OK);
    }
    
    /**
     * Get list of received invitations with detailed information
     * GET /api/invitations/received_list
     */
    public function received_list_get()
    {
        // Validate token and get user ID
        $this->validate_token();
        $user_id = $this->user_id;

        // Get received invitations with sender details
        $invitations = $this->db->select('i.*, u.full_name as sender_name, u.profile_pic as sender_avatar, u.slug as sender_slug')
                               ->from('invitations i')
                               ->join('users u', 'u.id = i.from_user_id')
                               ->where('i.to_user_id', $user_id)
                               ->order_by('i.created_at', 'DESC')
                               ->get()
                               ->result_array();

        // Format invitations with additional details
        $formatted_invitations = [];
        foreach ($invitations as $invitation) {
            $formatted_invitations[] = [
                'id' => $invitation['id'],
                'invitation_type' => $invitation['invitation_type'],
                'message' => $invitation['message'],
                'status' => $invitation['status'],
                'meeting_date' => $invitation['meeting_date'],
                'meeting_location' => $invitation['meeting_location'],
                'response_message' => $invitation['response_message'],
                'sender' => [
                    'id' => $invitation['from_user_id'],
                    'name' => $invitation['sender_name'] ?? 'Unknown User',
                    'avatar' => $invitation['sender_avatar'] ?? null,
                    'slug' => $invitation['sender_slug'] ?? null
                ],
                'created_at' => $invitation['created_at'],
                'responded_at' => $invitation['responded_at'],
                'days_ago' => $this->getDaysAgo($invitation['created_at'])
            ];
        }

        $this->response([
            'status' => true,
            'data' => [
                'invitations' => $formatted_invitations,
                'total_count' => count($formatted_invitations)
            ]
        ], REST_Controller::HTTP_OK);
    }
    
    /**
     * Helper function to calculate days ago
     */
    private function getDaysAgo($date)
    {
        $created = new DateTime($date);
        $now = new DateTime();
        $diff = $now->diff($created);
        
        if ($diff->days == 0) {
            return 'Today';
        } elseif ($diff->days == 1) {
            return 'Yesterday';
        } else {
            return $diff->days . ' days ago';
        }
    }
    
    /**
     * Test method to check if API is working
     * GET /api/invitations/test
     */
    public function test_get()
    {
        $this->response([
            'status' => true,
            'message' => 'Invitations API is working',
            'timestamp' => date('Y-m-d H:i:s')
        ], REST_Controller::HTTP_OK);
    }
} 