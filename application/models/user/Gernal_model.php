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
