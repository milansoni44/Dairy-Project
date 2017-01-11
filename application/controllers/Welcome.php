<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
         
    public function __construct() {
        parent::__construct();
        $this->load->helper("url");
        $this->load->library("auth_lib");
        $this->load->library("session");
        $this->load->library('email');
        $this->load->model("dairy_model");
        $this->load->model("society_model");
        $this->load->model("machine_model");
        $this->load->database();
        if(!$this->auth_lib->is_logged_in()){
            redirect("auth/login","refresh");
        }
    }
    
    public function index()
    {
        $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
        $this->load->view('common/header', $data);
        $this->load->view('welcome_message');
        $this->load->view('common/footer');
    }
}

/** application/controllers/Welcome.php */