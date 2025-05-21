<?php
require APPPATH . 'core/API_Controller.php';

class MediaController extends API_Controller
{
    public function upload_post()
	{

		$this->validate_token();

	    // Load libraries
	    $this->load->library('upload');
	    $this->load->library('image_lib');

	    // Define folders
	    $folders = ['uploads/original', 'uploads/thumb', 'uploads/medium', 'uploads/large'];

	    // Create folders if not exist
	    foreach ($folders as $folder) {
	        if (!is_dir($folder)) {
	            mkdir($folder, 0777, true);
	        }
	    }

	    // Upload config
	    $config['upload_path']   = './uploads/original/';
	    $config['allowed_types'] = 'jpg|jpeg|png|webp';
	    $config['max_size']      = 2048; // 2MB
	    $config['encrypt_name']  = TRUE;

	    $this->upload->initialize($config);

	    if (!$this->upload->do_upload('image')) {
	    	$this->response(['status' => false, 'message' => strip_tags($this->upload->display_errors())], REST_Controller::HTTP_BAD_REQUEST);
	        return;
	    }

	    $upload_data = $this->upload->data(); // Uploaded file info
	    $file_name = $upload_data['file_name'];
	    $source_path = './uploads/original/' . $file_name;

	    // Resize image to multiple sizes
	    $sizes = [
	        'thumb'  => [150, 150],
	        'medium' => [300, 300],
	        'large'  => [800, 800],
	    ];

	    foreach ($sizes as $folder => $dims) {
	        $this->resize_image($source_path, './uploads/' . $folder . '/' . $file_name, $dims[0], $dims[1]);
	    }

	    // Save to DB
	    $data = [
	        'file_name'     => $file_name,
	        'user_id'     => $this->user_id,
	        'original_path' => 'uploads/original/' . $file_name,
	        'thumb_path'    => 'uploads/thumb/' . $file_name,
	        'medium_path'   => 'uploads/medium/' . $file_name,
	        'large_path'    => 'uploads/large/' . $file_name,
	        'uploaded_at'   => date('Y-m-d H:i:s')
	    ];
	    $this->db->insert('media', $data);
	    $media_id = $this->db->insert_id();
	    if(isset($_POST['type']) && $media_id)
	    {
	    	$type = $_POST['type'];
	    	if($type == 'profile' ||$type == 'Profile')
	    	{
	    		$up = array('profile_pic'=>$media_id);
	    		$this->db->where('id',$this->profile->id)->update('profiles',$up); 
	    	}
	    }
	    $this->response([
            'status' => true,
            'media_id' => $media_id
        ], REST_Controller::HTTP_OK);
	}
	private function resize_image($source_path, $target_path, $width, $height)
	{
	    $config['image_library']  = 'gd2';
	    $config['source_image']   = $source_path;
	    $config['new_image']      = $target_path;
	    $config['maintain_ratio'] = TRUE;
	    $config['width']          = $width;
	    $config['height']         = $height;

	    $this->image_lib->initialize($config);
	    $this->image_lib->resize();
	    $this->image_lib->clear();
	}


}



?>