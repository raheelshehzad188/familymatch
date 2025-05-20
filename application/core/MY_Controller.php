<?php
// application/core/MY_Controller.php
class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        die('OKK');
        // Add your custom logic here
        // For example: load a common model, helper, etc.
        $this->load->helper('url');
    }
}
