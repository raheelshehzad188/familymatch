<?php

class Setting_model extends CI_Model
{
    public function get_all_settings()
    {
        $query = $this->db->get('settings');
        $data = [];
        foreach ($query->result() as $row) {
            $data[$row->meta_key] = [
                'value' => $row->meta_value,
                'field_type' => isset($row->field_type) ? $row->field_type : 'text'
            ];
        }
        return $data;
    }

    public function update_setting($key, $value, $field_type = 'text')
    {
        $exists = $this->db->get_where('settings', ['meta_key' => $key])->row();

        if ($exists) {
            $this->db->where('meta_key', $key)->update('settings', [
                'meta_value' => $value,
                'field_type' => $field_type
            ]);
        } else {
            $this->db->insert('settings', [
                'meta_key' => $key, 
                'meta_value' => $value,
                'field_type' => $field_type
            ]);
        }
    }

    public function update_settings_batch($data)
    {
        foreach ($data as $key => $value) {
            $this->update_setting($key, $value);
        }
    }

    public function get_setting_by_key($key)
    {
        $row = $this->db->get_where('settings', ['meta_key' => $key])->row();
        if ($row) {
            return [
                'value' => $row->meta_value,
                'field_type' => isset($row->field_type) ? $row->field_type : 'text'
            ];
        }
        return null;
    }
}
