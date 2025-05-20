<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'core/Admin_Controller.php');

class Interest extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/Interest_model');
        $this->dir = 'admin/'.$this->route;
    }
    public $dir = 'interest';
    public $route = 'interest';
    public $sing = 'Interest';
    public $multi = 'Interests';

    public function index() {
        $js = array();
            $js[] = 'https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap.min.js';
            
            $data['js'] = $js;
            $data['dtable']  = 'admin/'.$this->route.'/get_json';
        $this->admin($this->dir.'/list', $data);
    }
    public function get_json(){
        $users = $this->Interest_model->get_all_interests();
        $data = array();

        foreach ($users as $user) {
            $action = '<a href="'.$this->admin_url.$this->route.'/edit/'.$user['id'].'" class="btn">Edit</a>|<a href="'.$this->admin_url.$this->route.'/delete/'.$user['id'].'" class="btn">Delete</a>';
            $data[] = array(
                $user['id'],
                '<img src="'.base_url('uploads/interests/'.$user['image']).'" height="100" width="100" />',
                $user['title'],
                date('Y-m-d', strtotime($user['created_at'])),
                $action
            );
        }

        echo json_encode([
            "data" => $data
        ]);
    }

    public function add() {
        if ($this->input->post()) {
            $title = $this->input->post('title');
            $image = $this->_upload_image();

            $this->Interest_model->insert_interest($title, $image);
            $this->session->set_flashdata('success', $this->sing.' added successfully.');
            redirect('admin/'.$this->route);
        }
        $this->admin($this->dir.'/add');
    }
    private function _crop_image($source_path, $thumb_path, $width = 100, $height = 100)
{
    // Get original image size
    list($original_width, $original_height) = getimagesize($source_path);

    // Calculate cropping start point for center crop
    $x_axis = ($original_width / 2) - ($width / 2);
    $y_axis = ($original_height / 2) - ($height / 2);

    $config['image_library'] = 'gd2';
    $config['source_image'] = $source_path;
    $config['new_image'] = $thumb_path;
    $config['x_axis'] = $x_axis;
    $config['y_axis'] = $y_axis;
    $config['width'] = $width;
    $config['height'] = $height;
    $config['maintain_ratio'] = FALSE;

    $this->load->library('image_lib', $config);
    if (!$this->image_lib->crop()) {
        echo $this->image_lib->display_errors();
    }

    $this->image_lib->clear();
}

    private function _resize_image($path, $width, $height) {
    $config['image_library'] = 'gd2';
    $config['source_image'] = $path;
    $config['maintain_ratio'] = FALSE;  // FALSE means exact resize
    $config['width'] = $width;
    $config['height'] = $height;
    $config['new_image'] = $path; // overwrite original

    $this->load->library('image_lib', $config);

    if (!$this->image_lib->resize()) {
        echo $this->image_lib->display_errors();
    }

    $this->image_lib->clear(); // free memory
}

    private function _upload_image() {

        if (!empty($_FILES['image']['name'])) {
            $config['upload_path'] = './uploads/interests/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('image')) {
                 $upload_data = $this->upload->data();

            // Crop image after upload
            $source_path = $upload_data['full_path'];
            $thumb_path = $upload_data['file_path'] . 'thumb_' . $upload_data['file_name'];
            $this->_crop_image($source_path, $thumb_path, 100, 100);
            return 'thumb_' . $upload_data['file_name'];

            }
            else
            {
                 $error = $this->upload->display_errors('<p class="text-danger">', '</p>');
            echo $error;
            die();
            return null;
            }
        }
        return null;
    }

    public function edit($id) {
        $data['ethnicity'] = $this->Interest_model->get_interest_by_id($id);
        if ($this->input->post()) {
            $title = $this->input->post('title');
            $image = $this->_upload_image();

            $this->Interest_model->update_interest($id, $title, $image);
            $this->session->set_flashdata('success', $this->sing.' updated successfully.');
            redirect('admin/'.$this->route);
        }
        $this->admin($this->dir.'/edit', $data);
    }

    public function delete($id) {
        $this->Interest_model->delete_interest($id);
        $this->session->set_flashdata('success', $this->sing.' deleted successfully.');
        redirect('admin/'.$this->route);
    }
}
