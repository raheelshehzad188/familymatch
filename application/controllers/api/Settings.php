<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'core/API_Controller.php');

class Settings extends API_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all settings
     * GET /api/settings
     */
    public function index_get() {
        try {
            $settings = $this->get_all_settings();
            
            $this->response([
                'status' => true,
                'message' => 'Settings retrieved successfully',
                'data' => $settings
            ], 200);
            
        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'message' => 'Error retrieving settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific setting by key
     * GET /api/settings/{key}
     */
    public function setting_get($key = null) {
        if (!$key) {
            $this->response([
                'status' => false,
                'message' => 'Setting key is required'
            ], 400);
        }

        try {
            $setting = $this->get_setting_by_key($key);
            
            if ($setting) {
                $this->response([
                    'status' => true,
                    'message' => 'Setting retrieved successfully',
                    'data' => $setting
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Setting not found'
                ], 404);
            }
            
        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'message' => 'Error retrieving setting: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update setting
     * POST /api/settings/update
     */
    public function update_post() {
        $key = $this->input->post('key');
        $value = $this->input->post('value');
        $field_type = $this->input->post('field_type') ?: 'text';

        if (!$key) {
            $this->response([
                'status' => false,
                'message' => 'Setting key is required'
            ], 400);
        }

        try {
            // Handle image upload
            if ($field_type == 'image' && isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_path = './uploads/';
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0755, true);
                }
                
                $file_name = time() . '_' . $_FILES['image']['name'];
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path . $file_name)) {
                    $value = $file_name;
                } else {
                    $this->response([
                        'status' => false,
                        'message' => 'Image upload failed'
                    ], 400);
                    return;
                }
            }

            $result = $this->update_setting($key, $value, $field_type);
            
            $this->response([
                'status' => true,
                'message' => 'Setting updated successfully',
                'data' => [
                    'key' => $key,
                    'value' => $value,
                    'field_type' => $field_type,
                    'action' => $result
                ]
            ], 200);
            
        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'message' => 'Error updating setting: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new setting
     * POST /api/settings/create
     */
    public function create_post() {
        $key = $this->input->post('key');
        $value = $this->input->post('value');
        $field_type = $this->input->post('field_type') ?: 'text';

        if (!$key) {
            $this->response([
                'status' => false,
                'message' => 'Setting key is required'
            ], 400);
        }

        try {
            // Handle image upload for new setting
            if ($field_type == 'image' && isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_path = './uploads/';
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0755, true);
                }
                
                $file_name = time() . '_' . $_FILES['image']['name'];
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path . $file_name)) {
                    $value = $file_name;
                } else {
                    $this->response([
                        'status' => false,
                        'message' => 'Image upload failed'
                    ], 400);
                    return;
                }
            }

            $result = $this->create_setting($key, $value, $field_type);
            
            $this->response([
                'status' => true,
                'message' => 'Setting created successfully',
                'data' => [
                    'key' => $key,
                    'value' => $value,
                    'field_type' => $field_type,
                    'action' => $result
                ]
            ], 201);
            
        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'message' => 'Error creating setting: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete setting
     * DELETE /api/settings/delete/{key}
     */
    public function delete_delete($key = null) {
        if (!$key) {
            $this->response([
                'status' => false,
                'message' => 'Setting key is required'
            ], 400);
        }

        try {
            $result = $this->delete_setting($key);
            
            if ($result) {
                $this->response([
                    'status' => true,
                    'message' => 'Setting deleted successfully'
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Setting not found or could not be deleted'
                ], 404);
            }
            
        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'message' => 'Error deleting setting: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get settings by group
     * GET /api/settings/group/{group}
     */
    public function group_get($group = null) {
        if (!$group) {
            $this->response([
                'status' => false,
                'message' => 'Group parameter is required'
            ], 400);
        }

        try {
            $settings = $this->get_settings_by_group($group);
            
            $this->response([
                'status' => true,
                'message' => 'Group settings retrieved successfully',
                'data' => $settings
            ], 200);
            
        } catch (Exception $e) {
            $this->response([
                'status' => false,
                'message' => 'Error retrieving group settings: ' . $e->getMessage()
            ], 500);
        }
    }

    // Private helper methods

    private function get_all_settings() {
        $settings = [];
        $result = $this->db->get('settings')->result();
        
        foreach ($result as $row) {
            $setting_value = $row->meta_value;
            
            // Handle image paths
            if (isset($row->field_type) && $row->field_type == 'image' && !empty($setting_value)) {
                // Check if path already includes uploads/
                if (strpos($setting_value, 'uploads/') === 0) {
                    $setting_value = base_url($setting_value);
                } else {
                    $setting_value = base_url('uploads/' . $setting_value);
                }
            }
            
            $settings[$row->meta_key] = [
                'value' => $setting_value,
                'field_type' => isset($row->field_type) ? $row->field_type : 'text',
                'created_at' => isset($row->created_at) ? $row->created_at : null,
                'updated_at' => isset($row->updated_at) ? $row->updated_at : null
            ];
        }
        
        return $settings;
    }

    private function get_setting_by_key($key) {
        $result = $this->db->where('meta_key', $key)->get('settings')->row();
        
        if ($result) {
            $setting_value = $result->meta_value;
            
            // Handle image paths
            if (isset($result->field_type) && $result->field_type == 'image' && !empty($setting_value)) {
                if (strpos($setting_value, 'uploads/') === 0) {
                    $setting_value = base_url($setting_value);
                } else {
                    $setting_value = base_url('uploads/' . $setting_value);
                }
            }
            
            return [
                'key' => $result->meta_key,
                'value' => $setting_value,
                'field_type' => isset($result->field_type) ? $result->field_type : 'text',
                'created_at' => isset($result->created_at) ? $result->created_at : null,
                'updated_at' => isset($result->updated_at) ? $result->updated_at : null
            ];
        }
        
        return null;
    }

    private function update_setting($key, $value, $field_type = 'text') {
        $existing = $this->db->where('meta_key', $key)->get('settings')->row();
        
        if ($existing) {
            $this->db->where('meta_key', $key)
                     ->update('settings', [
                         'meta_value' => $value,
                         'field_type' => $field_type,
                         'updated_at' => date('Y-m-d H:i:s')
                     ]);
            return 'updated';
        } else {
            $this->db->insert('settings', [
                'meta_key' => $key,
                'meta_value' => $value,
                'field_type' => $field_type,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            return 'created';
        }
    }

    private function create_setting($key, $value, $field_type = 'text') {
        $this->db->insert('settings', [
            'meta_key' => $key,
            'meta_value' => $value,
            'field_type' => $field_type,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return 'created';
    }

    private function delete_setting($key) {
        return $this->db->where('meta_key', $key)->delete('settings');
    }

    private function get_settings_by_group($group) {
        // This method can be extended based on your group structure
        // For now, it returns all settings
        return $this->get_all_settings();
    }
} 