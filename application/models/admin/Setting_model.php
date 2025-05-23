<?php
class Setting_model extends CI_Model {

    public function get_all_settings() {
        $query = $this->db->get('settings');
        $data = [];
        foreach ($query->result() as $row) {
            $data[$row->meta_key] = $row->meta_value;
        }
        return $data;
    }

    public function update_setting($key, $value) {
        $exists = $this->db->get_where('settings', ['meta_key' => $key])->row();

        if ($exists) {
            $this->db->where('meta_key', $key)->update('settings', ['meta_value' => $value]);
        } else {
            $this->db->insert('settings', ['meta_key' => $key, 'meta_value' => $value]);
        }
    }

    public function update_settings_batch($data) {
        foreach ($data as $key => $value) {
            $this->update_setting($key, $value);
        }
    }
}

?>