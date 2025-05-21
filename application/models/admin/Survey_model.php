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
        $this->db->join('survey_options o', 'o.question_id = q.id','left');
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



public function update_question($question_id, $question_text, $options) {
    // Update question
    $this->db->where('id', $question_id)->update('survey_questions', ['question' => $question_text]);

    // Remove all old options
    $this->db->where('question_id', $question_id)->delete('survey_options');

    // Insert new options
    foreach ($options as $opt) {
        if (!empty($opt)) {
            $this->db->insert('survey_options', [
                'question_id' => $question_id,
                'option_text' => $opt
            ]);
        }
    }
    return true;
}


}
