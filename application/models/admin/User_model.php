<?php
class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all_users() {
        
        return $this->db->order_by('id', 'DESC')->get('users')->result(); // Table name is 'users'
    }
    public function get_user_by_id($id) {
        return $this->db->get_where('users', ['id' => $id])->row();
    }

    public function update_user($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }
    public function get_user_profile($id)
    {
        $this->db->select('p.*, g.name as gender, r.name as reffer,b.name as body,pp.thumb_path as img');
        $this->db->from('profiles p');
        $this->db->join('genders g', 'p.gender = g.id', 'left');
        // $this->db->join('marital_statuses m', 'p.marital_status = m.id', 'left');
        // $this->db->join('education_levels e', 'p.education_level = e.id', 'left');
        $this->db->join('referrals r', 'p.reffer_id = r.id', 'left'); // optional
        $this->db->join('body_types b', 'p.body_type = b.id', 'left'); // optional
        $this->db->join('media pp', 'p.profile_pic = pp.id', 'left'); // optional

        $this->db->where('p.user_id', $id);
        $query = $this->db->get();
        $row = $query->row();

        return $row;
    }
    public function get_profile_id_by_user($user_id)
    {
        $this->db->select('id');
        $this->db->from('profiles');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->id;
        }
        return null;
    }
    public function get_profile_interests($profile_id)
    {

        $this->db->select('i.title,i.image');
        $this->db->from('profile_intersts pi');
        $this->db->join('interests i', 'pi.interest_id = i.id');
        $this->db->where('pi.profile_id', $profile_id);

        $query = $this->db->get();
        return $query->result();
    }
    public function get_profile_ethnicities($profile_id)
{
    $this->db->select('e.name');
    $this->db->from('profile_ethnic pi');
    $this->db->join('ethnicities e', 'pi.ethnic_id = e.id');  // Note: column name ethinc_id
    $this->db->where('pi.profile_id', $profile_id);
    $query = $this->db->get();
    return $query->result();
}





    public function get_user_images($user_id)
    {
        return $this->db->get_where('media', ['user_id' => $user_id])->result();
    }
    public function get_user_survey($user_id)
    {
        $this->db->where('u.id', $user_id);
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

        return $query->result ();
    }

}
