<?php
class Survey_model extends CI_Model {

    public function insert_question_with_options($question, $options = []) {
        $this->db->insert('survey_questions', ['question' => $question,'question_type' => ($options)?'multiple_choice':'text',]);
        $question_id = $this->db->insert_id();

        foreach ($options as $option) {
            $this->db->insert('survey_options', [
                'question_id' => $question_id,
                'option_text' => $option
            ]);
        }

        return $question_id;
    }
        public function get_all_questions_with_options() {


        $this->db->select('q.id, q.question, q.created_at, o.option_text');
        $this->db->from('survey_questions q');
        $this->db->join('survey_options o', 'o.question_id = q.id');
        $this->db->order_by('q.id', 'DESC');
        $query = $this->db->get();

        $questions = [];
        foreach ($query->result() as $row) {
            $questions[$row->id]['id'] = $row->id;
            $questions[$row->id]['question'] = $row->question;
            $questions[$row->id]['created_at'] = $row->created_at;
            $questions[$row->id]['options'][] = $row->option_text;
        }
        return $questions;
    }
    public function get_question_with_options($question_id) {
    $this->db->select('q.id, q.question, o.id as option_id, o.option_text');
    $this->db->from('survey_questions q');
    $this->db->join('survey_options o', 'o.question_id = q.id');
    $this->db->where('q.id', $question_id);
    $query = $this->db->get();

    $result = [];
    foreach ($query->result() as $row) {
        $result['id'] = $row->id;
        $result['question'] = $row->question;
        $result['options'][] = [
            'option_id' => $row->option_id,
            'option_text' => $row->option_text
        ];
    }
    return $result;
}
public function delete_question($id) {
    // Delete options first
    $this->db->where('question_id', $id);
    $this->db->delete('survey_options');

    // Then delete the question
    $this->db->where('id', $id);
    $this->db->delete('survey_questions');

    return true;
}
public function get_all_responses()
    {
        $this->db->select('
            u.id as user_id,
            u.name as user_name,
            q.id as question_id,
            q.question,
            o.id as option_id,
            o.option_text,
            r.created_at
        ');
        $this->db->from('responses r');
        $this->db->join('users u', 'u.id = r.user_id');
        $this->db->join('survey_questions q', 'q.id = r.question_id');
        $this->db->join('survey_options o', 'o.id = r.option_id');
        $this->db->order_by('r.user_id, q.id');
        $query = $this->db->get();

        return $query->result_array ();
    }


public function update_question($question_id, $question_text, $options) {
    // Update question
    $this->db->where('id', $question_id)->update('survey_questions', ['question' => $question_text]);

    // Remove all old options

    // Insert new options
    foreach ($options as $opt) {
        $al = $this->db->where([
                'question_id' => $question_id,
                'option_text' => $opt
            ])->get('survey_options')->row();
        if (!empty($opt) && !$al) {
            $this->db->insert('survey_options', [
                'question_id' => $question_id,
                'option_text' => $opt
            ]);
        }
    }
    return true;
}


}
