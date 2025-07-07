<?php

class Events_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // Create new event
    public function create_event($data)
    {
        $this->db->insert('events', $data);
        return $this->db->insert_id();
    }

    // Get event by ID with access control
    public function get_event_by_id($event_id, $user_id)
    {
        $this->db->select('e.*, u.name as creator_name, u.email as creator_email, 
                          (SELECT COUNT(*) FROM event_participants ep WHERE ep.event_id = e.id) as current_participants');
        $this->db->from('events e');
        $this->db->join('users u', 'u.id = e.user_id');
        $this->db->where('e.id', $event_id);
        
        // Show event if it's public OR if user is the creator OR if user has joined it
        $this->db->where('(e.event_type = "public" OR e.user_id = ' . $user_id . ' OR 
                          EXISTS(SELECT 1 FROM event_participants ep WHERE ep.event_id = e.id AND ep.user_id = ' . $user_id . '))');
        
        $query = $this->db->get();
        return $query->row();
    }

    // Get all events (public and user's private events)
    public function get_events($user_id, $limit = 10, $offset = 0, $filters = [])
    {
        $this->db->select('e.*, u.name as creator_name, u.email as creator_email,
                          (SELECT COUNT(*) FROM event_participants ep WHERE ep.event_id = e.id) as current_participants,
                          (SELECT 1 FROM event_participants ep WHERE ep.event_id = e.id AND ep.user_id = ' . $user_id . ') as is_joined');
        $this->db->from('events e');
        $this->db->join('users u', 'u.id = e.user_id');
        
        // Show events that are public OR created by user OR joined by user
        $this->db->where('(e.event_type = "public" OR e.user_id = ' . $user_id . ' OR 
                          EXISTS(SELECT 1 FROM event_participants ep WHERE ep.event_id = e.id AND ep.user_id = ' . $user_id . '))');

        // Apply filters
        if (!empty($filters['event_type'])) {
            $this->db->where('e.event_type', $filters['event_type']);
        }
        
        if (!empty($filters['event_date'])) {
            $this->db->where('e.event_date', $filters['event_date']);
        }
        
        if (!empty($filters['location'])) {
            $this->db->like('e.location', $filters['location']);
        }
        
        if (!empty($filters['title'])) {
            $this->db->like('e.title', $filters['title']);
        }

        // Only show future events by default
        if (!isset($filters['include_past']) || !$filters['include_past']) {
            $this->db->where('e.event_date >=', date('Y-m-d'));
        }

        $this->db->order_by('e.event_date ASC, e.event_time ASC');
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get();
        $events = $query->result();

        // Process results
        foreach ($events as $event) {
            $event->is_joined = $event->is_joined ? 1 : 0;
            $event->current_participants = (int)$event->current_participants;
            $event->max_participants = $event->max_participants ? (int)$event->max_participants : null;
        }

        return $events;
    }

    // Update event
    public function update_event($event_id, $data)
    {
        $this->db->where('id', $event_id);
        return $this->db->update('events', $data);
    }

    // Delete event
    public function delete_event($event_id)
    {
        // First delete all participants
        $this->db->where('event_id', $event_id);
        $this->db->delete('event_participants');
        
        // Then delete the event
        $this->db->where('id', $event_id);
        return $this->db->delete('events');
    }

    // Join event
    public function join_event($event_id, $user_id)
    {
        // Check if already joined
        $existing = $this->db->where('event_id', $event_id)
                            ->where('user_id', $user_id)
                            ->get('event_participants')
                            ->row();
        
        if ($existing) {
            return false; // Already joined
        }

        $data = [
            'event_id' => $event_id,
            'user_id' => $user_id,
            'joined_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('event_participants', $data);
    }

    // Leave event
    public function leave_event($event_id, $user_id)
    {
        $this->db->where('event_id', $event_id);
        $this->db->where('user_id', $user_id);
        return $this->db->delete('event_participants');
    }

    // Get user's created events
    public function get_user_events($user_id, $limit = 10, $offset = 0)
    {
        $this->db->select('e.*, u.name as creator_name, u.email as creator_email,
                          (SELECT COUNT(*) FROM event_participants ep WHERE ep.event_id = e.id) as current_participants');
        $this->db->from('events e');
        $this->db->join('users u', 'u.id = e.user_id');
        $this->db->where('e.user_id', $user_id);
        $this->db->order_by('e.event_date DESC, e.event_time DESC');
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get();
        $events = $query->result();

        // Process results
        foreach ($events as $event) {
            $event->current_participants = (int)$event->current_participants;
            $event->max_participants = $event->max_participants ? (int)$event->max_participants : null;
        }

        return $events;
    }

    // Get user's joined events
    public function get_joined_events($user_id, $limit = 10, $offset = 0)
    {
        $this->db->select('e.*, u.name as creator_name, u.email as creator_email,
                          (SELECT COUNT(*) FROM event_participants ep WHERE ep.event_id = e.id) as current_participants,
                          ep.joined_at');
        $this->db->from('event_participants ep');
        $this->db->join('events e', 'e.id = ep.event_id');
        $this->db->join('users u', 'u.id = e.user_id');
        $this->db->where('ep.user_id', $user_id);
        $this->db->order_by('e.event_date ASC, e.event_time ASC');
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get();
        $events = $query->result();

        // Process results
        foreach ($events as $event) {
            $event->current_participants = (int)$event->current_participants;
            $event->max_participants = $event->max_participants ? (int)$event->max_participants : null;
        }

        return $events;
    }

    // Get event participants
    public function get_event_participants($event_id)
    {
        $this->db->select('ep.*, u.name, u.email, p.full_name, p.profile_pic, m.thumb_path as profile_image');
        $this->db->from('event_participants ep');
        $this->db->join('users u', 'u.id = ep.user_id');
        $this->db->join('profiles p', 'p.user_id = u.id', 'left');
        $this->db->join('media m', 'm.id = p.profile_pic', 'left');
        $this->db->where('ep.event_id', $event_id);
        $this->db->order_by('ep.joined_at ASC');
        
        $query = $this->db->get();
        $participants = $query->result();

        // Process profile images
        $base_url = base_url();
        foreach ($participants as $participant) {
            if ($participant->profile_image) {
                $participant->profile_image = $base_url . $participant->profile_image;
            }
        }

        return $participants;
    }

    // Check if user is joined to event
    public function is_user_joined($event_id, $user_id)
    {
        $this->db->where('event_id', $event_id);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('event_participants');
        return $query->num_rows() > 0;
    }

    // Get event statistics
    public function get_event_stats($event_id)
    {
        $stats = [
            'total_participants' => 0,
            'male_participants' => 0,
            'female_participants' => 0,
            'age_groups' => [
                '18-25' => 0,
                '26-35' => 0,
                '36-45' => 0,
                '46+' => 0
            ]
        ];

        // Get participants with profile data
        $this->db->select('p.gender, p.dob');
        $this->db->from('event_participants ep');
        $this->db->join('profiles p', 'p.user_id = ep.user_id');
        $this->db->where('ep.event_id', $event_id);
        
        $query = $this->db->get();
        $participants = $query->result();

        $stats['total_participants'] = count($participants);

        foreach ($participants as $participant) {
            // Count by gender
            if ($participant->gender == 1) { // Assuming 1 = male, 2 = female
                $stats['male_participants']++;
            } elseif ($participant->gender == 2) {
                $stats['female_participants']++;
            }

            // Count by age group
            if ($participant->dob) {
                $age = date_diff(date_create($participant->dob), date_create('today'))->y;
                
                if ($age >= 18 && $age <= 25) {
                    $stats['age_groups']['18-25']++;
                } elseif ($age >= 26 && $age <= 35) {
                    $stats['age_groups']['26-35']++;
                } elseif ($age >= 36 && $age <= 45) {
                    $stats['age_groups']['36-45']++;
                } elseif ($age >= 46) {
                    $stats['age_groups']['46+']++;
                }
            }
        }

        return $stats;
    }
} 