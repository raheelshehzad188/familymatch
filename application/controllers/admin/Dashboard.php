<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'core/Admin_Controller.php');

class Dashboard extends Admin_Controller {

    public function index() {
        $this->admin('admin/dashboard');
    }
}
