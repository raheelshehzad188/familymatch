<?php

class Gernal_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Update family profile
    public function get_countries() {
        return $this->db->get('countries')->result_array();
    }
    public function get_all_refferals() {
        return $this->db->get('referrals')->result_array();
    }
    public function get_all_data($tbl) {
        return $this->db->get($tbl)->result_array();
    }
    public function get_all_body_types() {
        return $this->db->get('body_types')->result_array();
    }
public function get_guest_profiles($limit = 10, $offset = 0, $filters = []) {
    $params = [];

    // Insert user_id as first param for the subquery
    $user_id = !empty($filters['user_id']) ? (int)$filters['user_id'] : 0;
    $params[] = $user_id;

    $sql = "
        SELECT 
            p.*,
            g.name AS gender,
            r.name AS reffer,
            b.name AS body,
            pp.thumb_path AS img,
            c.name AS country,
            s.name AS state,
            ci.name AS city,
            rl.name AS religion,
            ms.name AS ms_name,
            qf.name AS qualification,
            m.thumb_path AS profile_image,
            TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS age,
            (
                SELECT 1
                FROM profile_like pl
                WHERE pl.user_id = ? AND pl.profile_id = p.id
                LIMIT 1
            ) AS is_like
        FROM profiles p
        LEFT JOIN genders g ON p.gender = g.id
        LEFT JOIN referrals r ON p.reffer_id = r.id
        LEFT JOIN body_types b ON p.body_type = b.id
        LEFT JOIN media pp ON p.profile_pic = pp.id
        LEFT JOIN countries c ON p.country_id = c.id
        LEFT JOIN states s ON p.state_id = s.id
        LEFT JOIN cities ci ON p.city_id = ci.id
        LEFT JOIN religions rl ON p.religion_id = rl.id
        LEFT JOIN qualifications qf ON p.qualification_id = qf.id
        LEFT JOIN marital_status ms ON p.marital_status = ms.id
        LEFT JOIN media m ON m.id = p.profile_pic
        WHERE 1=1
    ";

    // Filters (appended after user_id)
    if (!empty($filters['gender'])) {
        $sql .= " AND p.gender = ? ";
        $params[] = $filters['gender'];
    }
    if (!empty($filters['religion_id'])) {
        $sql .= " AND p.religion_id = ? ";
        $params[] = $filters['religion_id'];
    }
    if (!empty($filters['country_id'])) {
        $sql .= " AND p.country_id = ? ";
        $params[] = $filters['country_id'];
    }
    if (!empty($filters['state_id'])) {
        $sql .= " AND p.state_id = ? ";
        $params[] = $filters['state_id'];
    }
    if (!empty($filters['marital_status'])) {
        $sql .= " AND p.marital_status = ? ";
        $params[] = $filters['marital_status'];
    }
    if (!empty($filters['body_type'])) {
        $sql .= " AND p.body_type = ? ";
        $params[] = $filters['body_type'];
    }
    if (!empty($filters['qualification_id'])) {
        $sql .= " AND p.qualification_id = ? ";
        $params[] = $filters['qualification_id'];
    }
    if (!empty($filters['city_id'])) {
        $sql .= " AND p.city_id = ? ";
        $params[] = $filters['city_id'];
    }
    if (!empty($filters['min_age'])) {
        $sql .= " AND TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) >= ? ";
        $params[] = (int)$filters['min_age'];
    }
    if (!empty($filters['max_age'])) {
        $sql .= " AND TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) <= ? ";
        $params[] = (int)$filters['max_age'];
    }

    // Pagination
    $sql .= " ORDER BY p.id DESC LIMIT ? OFFSET ? ";
    $params[] = (int)$limit;
    $params[] = (int)$offset;

    // Run query
    $query = $this->db->query($sql, $params);

    $base_upload_url = base_url();

    foreach ($query->result() as $row) {
        if ($row->profile_image) {
            $row->profile_image = $base_upload_url . $row->profile_image;
        }
        $row->is_liked = $row->is_liked ? 1 : 0;
    }

    return $query->result();
}




    public function get_all_genders() {
        return $this->db->get('genders')->result_array();
    }
    public function get_all_ethnicities() {
        return $this->db->get('ethnicities')->result_array();
    }
    public function get_all_interests() {
        return $this->db->get('interests')->result_array();
    }

    // Update family profile
    public function get_states($data) {
        if(isset($data['country_id']))
        {
            $this->where('country_id',$data['country_id']);
        }
        return $this->db->get('states')->result_array();
    }
    public function get_options($id) {
        return $op = $this->db->where('question_id',$id)->get('survey_options')->result_array();
    }
    public function get_cities($data) {
        if(isset($data['state_id']))
        {
            $this->where('state_id',$data['state_id']);
        }
        return $this->db->get('states')->result_array();
    }
public function get_all_questions_with_options() {
    $question = $this->db ->get('survey_questions')->result_array();
    $awnsers = array();
    foreach($question as $k=> $v)
    {
        $awnsers[] = $this->get_options($v['id']);
        $question_type= $v['question_type'];
        unset($question[$k]['question_type']);
        unset($question[$k]['created_at']);
        $question[$k]['type'] = $question_type;
        $question[$k]['apiname'] = $v['id'];
    }
    return array('questions'=>$question,'options'=>$awnsers);

    $this->db->select('q.id as question_id, q.question, o.id as option_id, o.option_text');
    $this->db->from('survey_questions q');
    $this->db->join('survey_options o', 'o.question_id = q.id');
    $query = $this->db->get();

    $result = [];
    foreach ($query->result() as $row) {
        $qid = $row->question_id;
        if (!isset($result[$qid])) {
            $result[$qid] = [
                'id' => $qid,
                'question' => $row->question,
                'options' => []
            ];
        }
        $result[$qid]['options'][] = [
            'id' => $row->option_id,
            'text' => $row->option_text
        ];
    }

    return array_values($result);
}


}