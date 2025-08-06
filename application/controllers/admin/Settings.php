<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'core/Admin_Controller.php');

class Settings extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('admin/Crud_model');
        $this->load->database();
    }

    public function index() {
        $data['title'] = 'Settings';
        $data['page'] = 'settings';
        
        // Get current settings
        $data['settings'] = $this->get_settings();
        
        $this->admin('admin/settings', $data);
    }

    public function save() {
        if ($this->input->method() === 'post') {
            $settings = $this->input->post('settings');
            $field_types = $this->input->post('field_types');
            $existing_values = $this->input->post('existing_values');
            
            $up = array();
            foreach ($settings as $key => $value) {
                $up[$key] = $value;
            }
            foreach ($_FILES as $key => $value) {
            // Check if this key is in $up (i.e., in settings array)
            if (isset($field_types[$key]) && $field_types[$key] == 'image') {
                if (isset($_FILES[$key]) && $_FILES[$key]['error'] == 0 && !empty($_FILES[$key]['name'])) {
                    $upload_path = './uploads/';
                    if (!is_dir($upload_path)) {
                        mkdir($upload_path, 0755, true);
                    }
                    $file_name = time() . '_' . $_FILES[$key]['name'];
                    if (move_uploaded_file($_FILES[$key]['tmp_name'], $upload_path . $file_name)) {
                        $up[$key] = $file_name;
                        
                    } else {
                        
                    }
                } else {
                    // No new file uploaded, keep existing value if available
                    if (isset($existing_values[$key])) {
                        //$up[$key] = $existing_values[$key];
                    }
                }
            }
            }
            foreach ($up as $key => $value) {
                $this->update_setting($key, $value, $field_types[$key]);
            }
            $this->session->set_flashdata('success', 'Settings updated successfully!');
        }
        
        redirect('admin/settings');
    }

    public function add_new() {
        if ($this->input->method() === 'post') {
            $new_setting_key = $this->input->post('new_setting_key');
            $new_setting_type = $this->input->post('new_setting_type');
            $option_group = $this->input->post('option_group');
            $option_description = $this->input->post('option_description');
            
            // Handle different field types
            $meta_value = '';
            
            if ($new_setting_type == 'image') {
                // Handle image upload for new setting
                if (isset($_FILES['new_setting_image']) && $_FILES['new_setting_image']['error'] == 0) {
                    $upload_path = './uploads/';
                    $file_name = time() . '_' . $_FILES['new_setting_image']['name'];
                    
                    if (move_uploaded_file($_FILES['new_setting_image']['tmp_name'], $upload_path . $file_name)) {
                        $meta_value = $file_name;
                    } else {
                        $this->session->set_flashdata('error', 'Image upload failed!');
                        redirect('admin/settings');
                        return;
                    }
                } else {
                    $this->session->set_flashdata('error', 'Please select an image file!');
                    redirect('admin/settings');
                    return;
                }
            } elseif ($new_setting_type == 'text') {
                $meta_value = $this->input->post('new_setting_value');
            } elseif ($new_setting_type == 'textarea') {
                $meta_value = $this->input->post('new_setting_textarea');
            } elseif ($new_setting_type == 'select') {
                $meta_value = $this->input->post('new_setting_select_options');
            } elseif ($new_setting_type == 'checkbox') {
                $meta_value = $this->input->post('new_setting_checkbox_value');
            }
            
            // Insert new setting into the 'settings' table
            $data = array(
                'meta_key' => $new_setting_key,
                'meta_value' => $meta_value,
                'field_type' => $new_setting_type,
            );
            $r = $this->db->insert('settings', $data);
            if ($r) {
                $this->session->set_flashdata('success', 'Option inserted successfully!');
            }
            else {
                $this->session->set_flashdata('error', 'Something went wrong!');
            }
        }
        
        redirect('admin/settings');
    }

    private function get_settings() {
        $settings = [];
        $result = $this->db->get('settings')->result();
        
        foreach ($result as $row) {
            $settings[$row->meta_key] = [
                'value' => $row->meta_value,
                'field_type' => isset($row->field_type) ? $row->field_type : 'text'
            ];
        }
        
        return $settings;
    }

    private function update_setting($key, $value, $field_type = 'text') {
        $existing = $this->db->where('meta_key', $key)->get('settings')->row();
        
        if ($existing) {
            // Update existing row
            $this->db->where('meta_key', $key)
                     ->update('settings', [
                         'meta_value' => $value,
                         'field_type' => $field_type,
                         'updated_at' => date('Y-m-d H:i:s')
                     ]);
            return 'updated';
        } else {
            // Create NEW ROW in settings table
            $insert_data = [
                'meta_key' => $key,
                'meta_value' => $value,
                'field_type' => $field_type,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->insert('settings', $insert_data);
            return 'created';
        }
    }

    public function api_settings() {
        $data['title'] = 'API Settings';
        $data['page'] = 'api_settings';
        
        // Get API related settings
        $data['api_settings'] = $this->get_api_settings();
        
        $this->admin('admin/api_settings', $data);
    }

    public function save_api_settings() {
        if ($this->input->method() === 'post') {
            $api_settings = $this->input->post('api_settings');
            $field_types = $this->input->post('field_types');
            $existing_values = $this->input->post('existing_values');
            
            if ($api_settings) {
                foreach ($api_settings as $key => $value) {
                    $field_type = isset($field_types[$key]) ? $field_types[$key] : 'text';
                    
                    if ($field_type == 'image') {
                        if (isset($_FILES['api_settings']['name'][$key]) && $_FILES['api_settings']['error'][$key] == 0) {
                            // Validate file type
                            $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');
                            $file_extension = strtolower(pathinfo($_FILES['api_settings']['name'][$key], PATHINFO_EXTENSION));
                            
                            if (in_array($file_extension, $allowed_types)) {
                                $config['upload_path'] = './uploads/settings/';
                                $config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
                                $config['max_size'] = 2048;
                                $config['file_name'] = uniqid('apiimg_') . '_' . $_FILES['api_settings']['name'][$key];

                                // Make sure the upload path exists
                                if (!is_dir($config['upload_path'])) {
                                    mkdir($config['upload_path'], 0755, true);
                                }

                                $this->load->library('upload', $config);

                                // Prepare $_FILES array for this file
                                $fileArray = [
                                    'name' => $_FILES['api_settings']['name'][$key],
                                    'type' => $_FILES['api_settings']['type'][$key],
                                    'tmp_name' => $_FILES['api_settings']['tmp_name'][$key],
                                    'error' => $_FILES['api_settings']['error'][$key],
                                    'size' => $_FILES['api_settings']['size'][$key]
                                ];

                                $_FILES['single_api_img'] = $fileArray;

                                if ($this->upload->do_upload('single_api_img')) {
                                    $uploadData = $this->upload->data();
                                    $value = 'uploads/settings/' . $uploadData['file_name'];
                                } else {
                                    $this->session->set_flashdata('error', 'Image upload failed for ' . $key . ': ' . $this->upload->display_errors('', ''));
                                    // Keep existing value if upload failed
                                    $value = isset($existing_values[$key]) ? $existing_values[$key] : '';
                                    continue;
                                }
                            } else {
                                $this->session->set_flashdata('error', 'Invalid file type for ' . $key . '. Allowed: JPG, PNG, GIF, WEBP');
                                $value = isset($existing_values[$key]) ? $existing_values[$key] : '';
                            }
                        } else {
                            // If no new image uploaded, keep the old value
                            $value = isset($existing_values[$key]) ? $existing_values[$key] : '';
                        }
                    }
                    
                    $this->update_setting('api_' . $key, $value, $field_type);
                }
                
                $this->session->set_flashdata('success', 'API Settings updated successfully!');
            }
        }
        
        redirect('admin/settings/api_settings');
    }

    private function get_api_settings() {
        $settings = [];
        $result = $this->db->like('meta_key', 'api_')->get('settings')->result();
        
        foreach ($result as $row) {
            $key = str_replace('api_', '', $row->meta_key);
            $settings[$key] = [
                'value' => $row->meta_value,
                'field_type' => isset($row->field_type) ? $row->field_type : 'text'
            ];
        }
        
        return $settings;
    }

    public function view_all() {
        $data['title'] = 'All Settings in Database';
        $data['page'] = 'view_settings';
        
        // Get all settings from database with full details
        $data['all_settings'] = $this->db->get('settings')->result();
        
        $this->admin('admin/view_all_settings', $data);
    }
} 