<?php

require APPPATH . 'core/API_Controller.php';
class Events extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user/Events_model');
        $this->load->helper('url');
    }

    // Get all events (public and user's private events)
    public function index_get()
    {
        $this->validate_token();
        $filters = ($_GET) ? $_GET : [];
        $limit = (isset($_GET['per_page'])) ? $_GET['per_page'] : 10;
        $page = $this->input->get('page') ?? 1;
        $page = max(1, (int)$page);

        $offset = ($page - 1) * $limit;
        
        $events = $this->Events_model->get_events($this->user_id, $limit, $offset, $filters);
        
        
        $this->response([
            'status' => true,
            'data' => $events
        ], REST_Controller::HTTP_OK);
    }

    // Get single event by ID
    public function event_get($event_id)
    {
        $this->validate_token();
        $event = $this->Events_model->get_event_by_id($event_id, $this->user_id);
        
        if (!$event) {
            $this->response([
                'status' => false,
                'message' => 'Event not found or access denied'
            ], REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        $this->response([
            'status' => true,
            'data' => $event
        ], REST_Controller::HTTP_OK);
    }

    // Create new event
    public function create_post()
    {
        $this->validate_token();
        
        $data = $_POST;
        $json = file_get_contents('php://input');

        if (!$_POST && $json) {
            $data = json_decode($json, true);
        }

        // Required fields validation
        $required_fields = ['title', 'description', 'event_date', 'event_time', 'location', 'event_type'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                $this->response([
                    'status' => false,
                    'message' => ucfirst($field) . ' is required'
                ], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }
        }

        // Validate event_type
        if (!in_array($data['event_type'], ['public', 'private'])) {
            $this->response([
                'status' => false,
                'message' => 'Event type must be either "public" or "private"'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Prepare event data
        $event_data = [
            'user_id' => $this->user_id,
            'title' => $data['title'],
            'description' => $data['description'],
            'event_date' => date('Y-m-d', strtotime($data['event_date'])),
            'event_time' => $data['event_time'],
            'location' => $data['location'],
            'event_type' => $data['event_type'],
            'max_participants' => isset($data['max_participants']) ? (int)$data['max_participants'] : null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $event_id = $this->Events_model->create_event($event_data);
        
        if ($event_id) {
            $event = $this->Events_model->get_event_by_id($event_id, $this->user_id);
            $this->response([
                'status' => true,
                'message' => 'Event created successfully',
                'data' => $event
            ], REST_Controller::HTTP_CREATED);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Failed to create event'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Update event
    public function update_post($event_id)
    {
        $this->validate_token();
        
        // Check if user owns the event
        $existing_event = $this->Events_model->get_event_by_id($event_id, $this->user_id);
        if (!$existing_event || $existing_event->user_id != $this->user_id) {
            $this->response([
                'status' => false,
                'message' => 'Event not found or you do not have permission to edit it'
            ], REST_Controller::HTTP_FORBIDDEN);
            return;
        }

        $data = $_POST;
        $json = file_get_contents('php://input');

        if (!$_POST && $json) {
            $data = json_decode($json, true);
        }

        // Validate event_type if provided
        if (isset($data['event_type']) && !in_array($data['event_type'], ['public', 'private'])) {
            $this->response([
                'status' => false,
                'message' => 'Event type must be either "public" or "private"'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Prepare update data
        $update_data = [];
        $allowed_fields = ['title', 'description', 'event_date', 'event_time', 'location', 'event_type', 'max_participants'];
        
        foreach ($allowed_fields as $field) {
            if (isset($data[$field])) {
                if ($field === 'event_date') {
                    $update_data[$field] = date('Y-m-d', strtotime($data[$field]));
                } elseif ($field === 'max_participants') {
                    $update_data[$field] = (int)$data[$field];
                } else {
                    $update_data[$field] = $data[$field];
                }
            }
        }

        if (empty($update_data)) {
            $this->response([
                'status' => false,
                'message' => 'No valid fields to update'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $update_data['updated_at'] = date('Y-m-d H:i:s');

        $result = $this->Events_model->update_event($event_id, $update_data);
        
        if ($result) {
            $event = $this->Events_model->get_event_by_id($event_id, $this->user_id);
            $this->response([
                'status' => true,
                'message' => 'Event updated successfully',
                'data' => $event
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Failed to update event'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Delete event
    public function delete_post($event_id)
    {
        $this->validate_token();
        
        // Check if user owns the event
        $existing_event = $this->Events_model->get_event_by_id($event_id, $this->user_id);
        if (!$existing_event || $existing_event->user_id != $this->user_id) {
            $this->response([
                'status' => false,
                'message' => 'Event not found or you do not have permission to delete it'
            ], REST_Controller::HTTP_FORBIDDEN);
            return;
        }

        $result = $this->Events_model->delete_event($event_id);
        
        if ($result) {
            $this->response([
                'status' => true,
                'message' => 'Event deleted successfully'
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Failed to delete event'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Join event
    public function join_post($event_id)
    {
        $this->validate_token();
        
        // Check if event exists and is accessible
        $event = $this->Events_model->get_event_by_id($event_id, $this->user_id);
        if (!$event) {
            $this->response([
                'status' => false,
                'message' => 'Event not found or access denied'
            ], REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        // Check if user is not the event creator
        if ($event->user_id == $this->user_id) {
            $this->response([
                'status' => false,
                'message' => 'You cannot join your own event'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Check if event is full
        if ($event->max_participants && $event->current_participants >= $event->max_participants) {
            $this->response([
                'status' => false,
                'message' => 'Event is full'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $result = $this->Events_model->join_event($event_id, $this->user_id);
        
        if ($result) {
            $this->response([
                'status' => true,
                'message' => 'Successfully joined the event'
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Failed to join event or already joined'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    // Leave event
    public function leave_post($event_id)
    {
        $this->validate_token();
        
        $result = $this->Events_model->leave_event($event_id, $this->user_id);
        
        if ($result) {
            $this->response([
                'status' => true,
                'message' => 'Successfully left the event'
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Failed to leave event or not joined'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    // Get user's created events
    public function my_events_get()
    {
        $this->validate_token();
        $limit = (isset($_GET['per_page'])) ? $_GET['per_page'] : 10;
        $page = $this->input->get('page') ?? 1;
        $page = max(1, (int)$page);

        $offset = ($page - 1) * $limit;
        $events = $this->Events_model->get_user_events($this->user_id, $limit, $offset);
        
        $this->response([
            'status' => true,
            'data' => $events
        ], REST_Controller::HTTP_OK);
    }

    // Get user's joined events
    public function joined_events_get()
    {
        $this->validate_token();
        $limit = (isset($_GET['per_page'])) ? $_GET['per_page'] : 10;
        $page = $this->input->get('page') ?? 1;
        $page = max(1, (int)$page);

        $offset = ($page - 1) * $limit;
        $events = $this->Events_model->get_joined_events($this->user_id, $limit, $offset);
        
        $this->response([
            'status' => true,
            'data' => $events
        ], REST_Controller::HTTP_OK);
    }

    // Get event participants
    public function participants_get($event_id)
    {
        $this->validate_token();
        
        // Check if event exists and is accessible
        $event = $this->Events_model->get_event_by_id($event_id, $this->user_id);
        if (!$event) {
            $this->response([
                'status' => false,
                'message' => 'Event not found or access denied'
            ], REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        $participants = $this->Events_model->get_event_participants($event_id);
        
        $this->response([
            'status' => true,
            'data' => $participants
        ], REST_Controller::HTTP_OK);
    }
} 