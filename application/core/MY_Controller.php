<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MY_Controller
 *
 * @author Intel
 */
class MY_Controller extends CI_Controller{
    //put your code here
    public $data;
    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model("machine_model");
        $this->load->library("auth_lib");
        $type = $this->session->userdata("group");
        $id = $this->session->userdata("id");
        $this->data['notifications'] = $this->auth_lib->get_machines($type, $id);
        $this->data['machine_count'] = $this->machine_model->totalCount();
    }
}
