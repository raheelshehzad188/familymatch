<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_Controller extends CI_Controller {
    public $exc_login = ['login'];

    public function __construct() {
        parent::__construct();

        // Load anything you need across all admin controllers
        $this->load->helper('url');
        $this->load->helper('my');
        $controller = $this->router->fetch_class();
        if(!in_array($controller, $this->exc_login))
        {
            if(!isset($_SESSION['admin']))
            {
                $_SESSION['error'] = 'Invalid request!';
        redirect('admin/login');
            }
        }
        $this->admin = $_SESSION['admin'];

        $this->assets_url = $this->config->item('admin_assets');
        $this->admin_url = base_url('admin/');


    }
    public $admin = array(); 
    public $assets_url;
    public $admin_url;
    public $para1 = '';
    public function full($view,$data = array())
    {
        if(!isset($data['assets_url']))
        {
            $data['assets_url'] = $this->assets_url;
        }


        $this->load->view($view,$data);
    }
    public function get_crud()
    {
        return$this->db->get('crud')->result_array();
    }
    public function get_side_bar()
    {
        $role_id = ($this->admin->role_id)?$this->admin->role_id:0;
        $role = $this->db->where('id',$role_id)->get('roles')->row();
        return (isset($role->slug))?'sidebar_'.$role->slug.'.php':'sidebar.php';
    }
    public function admin($view,$data = array())
    {
        if(!isset($data['assets_url']))
        {
            $data['assets_url'] = $this->assets_url;
        }
        if(!isset($data['title']))
        {
            $data['title'] = SITE_NAME;
        }
        else
        {
            $data['title'] = $data['title'].' > '.SITE_NAME;
        }
        $data['admin'] = $this->admin;
        $data['cruds'] = $this->get_crud();
        $data['side_bar'] = $this->get_side_bar();
        $data['controller'] = $this->router->fetch_class();
        $data['method'] = $this->router->fetch_method();
        $data['param1'] = $this->para1;
        $data['content'] = $this->load->view($view,$data,true);
        $this->load->view(ADMIN_LAYOUT,$data);
    }
}
