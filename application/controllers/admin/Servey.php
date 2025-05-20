<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'core/Admin_Controller.php');

class Servey extends Admin_Controller {
    public function __construct() {

            parent::__construct();
            $this->load->model('admin/Survey_model');
        }
        public function index() {


        // $data['questions'] = $this->Survey_model->get_all_questions_with_options();

        $js = array();
            $js[] = 'https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap.min.js';
            $js[] = $this->assets_url.'js/servy.js';
            $data['js'] = $js;

        $this->admin('admin/survey_questions_list', $data);
    }
    public function results_json()
    {

        $d= $this->Survey_model->get_all_responses();

        $data = array();

        foreach ($d as $user) {
            $data[] = array(
                $user['user_name'],
                $user['question'],
                $user['option_text'],
                date('Y-m-d', strtotime($user['created_at']))
            );
        }

        echo json_encode([
            "data" => $data
        ]);

    }
    public function results()
    {
        $data['responses'] = $this->Survey_model->get_all_responses();
        $js = array();
            $js[] = 'https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap.min.js';
            
            $data['js'] = $js;
            $data['dtable']  = 'admin/servey/results_json';
            $data['label']  = $this->label;
            $data['heading']  = $this->multi;
        $this->admin('admin/survey_results', $data);
    }


    public function edit($id) {
    $this->load->model('Survey_model');

    if ($this->input->post()) {
        $question = $this->input->post('question');
        $options = $this->input->post('options'); // array

        $this->Survey_model->update_question($id, $question, $options);
        $this->session->set_flashdata('success', 'Question updated successfully.');
        redirect('admin/servey');
    }

    $data['question'] = $this->Survey_model->get_question_with_options($id);
    $js = array();
            $js[] = $this->assets_url.'js/servy.js';
            $data['js'] = $js;
    $this->admin('admin/edit_question', $data);
}

public function delete($id) {
    $this->load->model('Survey_model');
    $this->Survey_model->delete_question($id);
    $this->session->set_flashdata('success', 'Question deleted successfully.');
    redirect('admin/servey');
}


    public function get_question(){
        $users = $this->Survey_model->get_all_questions_with_options();
        $data = array();

        foreach ($users as $user) {
            $action = '<a href="'.$this->admin_url.'servey/edit/'.$user['id'].'" class="btn">Edit</a>|<a href="'.$this->admin_url.'servey/delete/'.$user['id'].'" class="btn">Delete</a>';
            $data[] = array(
                $user['id'],
                $user['question'],
                date('Y-m-d', strtotime($user['created_at'])),
                $action
            );
        }

        echo json_encode([
            "data" => $data
        ]);
    }


    public function add_question()
    {
        if ($this->input->post()) {
            $question = $this->input->post('question');
            $options = $this->input->post('options'); // array

            $this->load->model('Survey_model');
            $this->Survey_model->insert_question_with_options($question, $options);

            $this->session->set_flashdata('success', 'Question and options added successfully.');
            redirect('admin/servey');
        }
        $js = array();
            $js[] = $this->assets_url.'js/servy.js';
            $data['js'] = $js;

        $this->admin('admin/servy_add',$data);
    }
}

