<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'core/Admin_Controller.php');

class Crud extends Admin_Controller {
    public function get_file_type($file_path) {
    // First check if file exists
    if (!file_exists($file_path)) {
        return 'invalid'; // Or return false
    }

    // Get MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file_path);
    finfo_close($finfo);

    // Check if it's an image
    if (strpos($mime_type, 'image/') === 0) {
        return 'image';
    } else {
        return 'other';
    }
}

    public function __construct() {

        parent::__construct();
        $this->load->model('admin/Crud_model');
        
        $this->dir = 'admin/'.$this->route;
    }
    public $dir = '';
    public $route = 'crud';
    public $sing = 'Body Type';
    public $multi = 'Body Types';
    public $label ='Type';
    public $form_type ='full';
    public $tbl = '';
    public $fields = [];
    public function set_data($tbl)
    {
        // $this->route= $this->route.'/'.$tbl;
        $this->para1 = $tbl;

        $row = $this->db->where('slug',$tbl)->get('crud')->row();
        $this->db->order_by('list_sort','ASC');
        $this->fields = $this->db->where('crud_id',$row->id)->get('crud_fields')->result_array();
        
        $this->tbl = $row->db_tble;
        $this->slug = $row->slug;
        $this->Crud_model->tbl = $row->db_tble;
        $this->Crud_model->key = $row->tbl_key;
        $this->label = $row->single;
        $this->sing = $row->single;
        $this->multi = $row->multi;
        $this->form_type = $row->form_type;

    }

    public function all($tbl) {
         $this->set_data($tbl);


        $js = array();
            $js[] = 'https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap.min.js';
            $data['js'] = $js;
            $data['dtable']  = 'admin/'.$this->route.'/get_json/'.$this->slug;
            $data['add_link']  = 'admin/'.$this->route.'/add/'.$this->slug;
            $data['label']  = $this->label;
            $data['heading']  = $this->multi;
            $data['fields']  = $this->fields;
        $this->admin($this->dir.'/list', $data);
    }
    public function get_json($tbl){
        

        $this->set_data($tbl);
        $users = $this->Crud_model->get_all_data();
        $data = array();

        foreach ($users as $k=>$user) {
            $action = '<a class="edit-btn   btn" href="'.$this->admin_url.$this->route.'/edit/'.$tbl.'/'.$user[$this->Crud_model->key].'" ><i class="fa-solid fa-pen-to-square"></i> Edit</a><a class="del-btn btn btn-danger" href="'.$this->admin_url.$this->route.'/delete/'.$tbl.'/'.$user[$this->Crud_model->key].'" ><i class="fa-solid fa-trash"></i> Delete</a>';
                $sing = array();
                $sing[] = $user[$this->Crud_model->key];
                
                foreach ($this->fields as $key => $value) {
                    $lv = $user[$value['db_column']];
                    if($value['is_list'])
                    {
                        if($value['type'] !== 'file')
                        {
                            $sing[] = $lv;
                        }
                        elseif($value['type'] === 'file')
                        {
                            if($lv)
                            {
                                if($this->get_file_type($lv) == 'image')
                                {
                                    $sing[] = '<a href="'.base_url($lv).'" target="_blank"><img src="'.base_url($lv).'" height="100" width="100" /></a>';
                                }
                                else
                                {
                                    $sing[] = '<a href="'.base_url($lv).'" target="_blank">View</a>';
                                }
                            }
                            else
                            {
                                $sing [] = '-';
                            }
                        }
                    }
                }
             $sing[] = date('Y-m-d', strtotime($user['created_at']));
            $sing[] = $action;
            $data[]= $sing;
            
        }

        echo json_encode([
            "data" => $data
        ]);
    }
    public function add_column_if_not_exists($table_name, $column_name)
{
    $CI =& get_instance();
    $CI->load->database();
    $CI->load->dbforge();
    $fields = $CI->db->list_fields($table_name);
    if (!in_array($column_name, $fields)) {
        
        // Define new column structure
        $new_field = [
            $column_name => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE
            ]
        ];

        // Add column
        if ($CI->dbforge->add_column($table_name, $new_field)) {
            return true;
        } else {
            return false;
        }

    } else {
        return false;
    }
}

public function delete_table_if_exists($table_name)
{
    $CI =& get_instance();
    $CI->load->dbforge();

    // Check if table exists
    if ($CI->db->table_exists($table_name)) {

        // Drop the table
        if ($CI->dbforge->drop_table($table_name)) {
            return true;
        } else {
            return false;
        }

    } else {
        return false;
    }
}


    public function create_table_if_not_exists($table_name, $primary_key)
{
    $CI =& get_instance();
    $CI->load->dbforge();

    // Check if table exists
    if (!$CI->db->table_exists($table_name)) {

        // Define fields
        $fields = [
            $primary_key => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ]
        ];

        // Add fields to dbforge
        $CI->dbforge->add_field($fields);

        // Set primary key
        $CI->dbforge->add_key($primary_key, TRUE);

        // Create table
        if ($CI->dbforge->create_table($table_name)) {
            return true;
        } else {
            return false;
        }

    } else {
        return false;
    }
}


    public function add($tbl) {
        $this->set_data($tbl);


        if ($this->input->post()) {

    // Load form validation library
    $this->load->library('form_validation');

    foreach ($this->fields as $key => $value) {
            if ($value['type'] != 'file' && $value['is_required']) {
                // Set rule for required fields
                $this->form_validation->set_rules(
                    $value['db_column'],
                    ucfirst($value['label']), // Human-readable label
                    'required'
                );
            }
            elseif ($value['type'] == 'file' && $value['is_required'] && !isset($_FILES[$value['db_column']])) {
            $this->form_validation->set_rules(
                $value['db_column'],
                ucfirst($value['label']), // Human-readable label
                'required'
            );
            }
        }//foreach end

    if ($this->form_validation->run() == FALSE) {
        // Validation failed - reload form with errors
        $_SESSION['error'] = 'Fill required field';
        redirect('admin/crud/add/' . $this->tbl);
        
    } else {
        //make array to insert
        $in = array();
        foreach ($this->fields as $key => $value) {
            $col = $value['db_column'];
            if($value['type'] == 'file')
            {
                $in[$col] = $this->_upload_image($col);
            }
            else
            {
                $in[$col] = $this->input->post($col);
            }
        }
        if($tbl == 'crud')
        {
            
        $r = $this->create_table_if_not_exists($in['db_tble'], $in['tbl_key']);
        if(!$r)
        {
        //     $_SESSION['error'] = 'Srver error on tbl creation';
        // redirect('admin/crud/add/' . $this->tbl);
        }
        }
        elseif($tbl == 'crud_fields')
        {
            $this->set_data('crud');
            $row = $this->Crud_model->tbl = 'crud';
            $row = $this->Crud_model->get_data_by_id($in['crud_id']);
            $this->set_data($tbl);
            if($row)
            {
        $r = $this->add_column_if_not_exists($row['db_tble'], $in['db_column']);
        // if(!$r)
        // {
        //     $_SESSION['error'] = 'Srver error on column creation';
        // redirect('admin/crud/add/' . $this->tbl);
        // }
            }
            $this->set_data($tbl);
        }
        $this->Crud_model->insert_data($in);
        $this->session->set_flashdata('success', $this->sing . ' added successfully.');
        redirect('admin/crud/all/' . $this->tbl);
    }
}
        // dd( $this->fields);
        $data['fields']  = $this->fields;
        $data['label']  = $this->label;
        $data['heading']  = $this->multi;
        $data['form_type']  = $this->form_type;
        $this->admin($this->dir.'/add',$data);
    }
    private function _upload_image($file) {


    if (!empty($_FILES[$file]['name'])) {
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);
        
        // ✅ Fixed the syntax error below:
        if ($this->upload->do_upload($file)) {
            $upload_data = $this->upload->data();
            $source_path = 'uploads/'.$upload_data['file_name'];
            
            return $source_path;

        } else {
            $error = $this->upload->display_errors('<p class="text-danger">', '</p>');
            echo $error;
            die(); // ❗ You may remove this in production
            return null;
        }
    }

    return null;
}


    public function edit($tbl,$id) {

                $this->set_data($tbl);

        $data['row'] = $row = $this->Crud_model->get_data_by_id($id);
                if ($this->input->post()) {


    // Load form validation library
    $this->load->library('form_validation');

    foreach ($this->fields as $key => $value) {
            if ($value['type'] != 'file' && $value['is_required']) {
                // Set rule for required fields
                if(!isset($_FILES[$value['db_column']]['name']))
                {
                    $this->form_validation->set_rules(
                        $value['db_column'],
                        ucfirst($value['label']), // Human-readable label
                        'required'
                    );
                }
            }
            elseif ($value['type'] == 'file' && $value['is_required'] && !isset($_FILES[$value['db_column']])) {
            $this->form_validation->set_rules(
                $value['db_column'],
                ucfirst($value['label']), // Human-readable label
                'required'
            );
            }
        }//foreach end


    if ($this->form_validation->run() == FALSE) {
        // Validation failed - reload form with errors
        $_SESSION['error'] = 'Fill required field';
        redirect('admin/crud/edit/' . $this->slug.'/'.$id);
        
    } else {
        //make array to insert
        $in = array();
        foreach ($this->fields as $key => $value) {
            $col = $value['db_column'];
            if($value['type'] == 'file')
            {
                $in[$col] = $this->_upload_image($col);
                $in[$col] = ($in[$col])?$in[$col]:$row[$col];
            }
            else
            {
                $in[$col] = $this->input->post($col);
            }
        }
        $r = $this->Crud_model->update_data($id,$in);
        $this->session->set_flashdata('success', $this->sing . ' added successfully.');
        redirect('admin/crud/all/' . $this->slug);
    }
}
        $data['label']  = $this->label;
        $data['heading']  = $this->multi;
        $data['fields']  = $this->fields;
        $this->admin($this->dir.'/edit', $data);
    }

    public function delete($tbl, $id) {
        
        $this->set_data($tbl);
        if($tbl == 'crud')
        {
            $row = $this->Crud_model->get_data_by_id($id);
            $r = $this->delete_table_if_exists($row['db_tble']);
            
        }
        $this->Crud_model->delete_data($id);
        $this->session->set_flashdata('success', $this->sing.' deleted successfully.');
        redirect('admin/'.$this->route.'/all/'.$tbl);
    }

}
