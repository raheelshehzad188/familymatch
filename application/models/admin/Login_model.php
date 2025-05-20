<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model {

    // Thresholds
    private $max_attempts = 5;
    private $block_duration = '1 hour';

    public function __construct() {
        parent::__construct();
    }

    // Verify admin credentials
    public function verify_admin($email, $password) {
        $this->db->where('email', $email);
        $admin = $this->db->get('admins')->row();
        $hashedPassword = password_hash('admin', PASSWORD_BCRYPT);

        if ($admin && password_verify($password, $admin->password)) {
            return $admin;
        }
        return false;
    }

    // Fetch attempt record
    public function get_attempts($ip) {
        $this->db->where('ip_address', $ip);
        return $this->db->get('login_attempts')->row();
    }

    // Check if IP is currently blocked
    public function is_blocked($ip) {
        $rec = $this->get_attempts($ip);
        if ($rec && $rec->blocked_until !== null) {
            // agar abhi bhi block time future me hai
            if (strtotime($rec->blocked_until) > time()) {
                return true;
            }
            // block expired, reset record
            $this->reset_attempts($ip);
        }
        return false;
    }

    // Add a failed attempt, and block if threshold reached
    public function add_attempt($ip) {
        $now = date('Y-m-d H:i:s');
        $rec = $this->get_attempts($ip);

        if (!$rec) {
            // pehli bar
            $this->db->insert('login_attempts', [
                'ip_address'    => $ip,
                'attempts'      => 1,
                'attempt_time'  => $now,
                'blocked_until' => null
            ]);
            return;
        }

        // agar pehle block ho chuka tha aur block expired, record reset ho ja chuka
        $newCount = $rec->attempts + 1;

        if ($newCount >= $this->max_attempts) {
            // block laga do
            $blockedUntil = date('Y-m-d H:i:s', strtotime("+{$this->block_duration}"));
            $this->db->where('ip_address', $ip)
                     ->update('login_attempts', [
                         'attempts'      => $newCount,
                         'attempt_time'  => $now,
                         'blocked_until' => $blockedUntil
                     ]);
        } else {
            // sirf attempts update karo
            $this->db->where('ip_address', $ip)
                     ->update('login_attempts', [
                         'attempts'     => $newCount,
                         'attempt_time' => $now
                     ]);
        }
    }

    // Reset/delete the record so that IP is unblocked and count zero ho
    public function reset_attempts($ip) {
        $this->db->where('ip_address', $ip)
                 ->delete('login_attempts');
    }
}
