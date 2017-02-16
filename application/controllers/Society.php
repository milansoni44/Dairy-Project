<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Society
 *
 * @author Milan Soni
 */
class Society extends MY_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->library("auth_lib");
        $this->load->model("auth_model");
        $this->load->model("society_model");
        $this->load->model("dairy_model");
        $this->load->library("session");
        $this->load->library("form_validation");
        $this->load->library("upload");
        $this->load->helper("security");
        $this->load->database();
        if(!$this->auth_lib->is_logged_in()){
            redirect("auth/login","refresh");
        }
    }
    
    public function index()
    {
        if($this->session->userdata("group") != "dairy" && $this->session->userdata("group") != "admin"){
            $this->session->set_flashdata("message","Access Denied");
            redirect("/","refresh");
        }
        //$data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
        $data['society'] = $this->society_model->get_society();
        $this->load->view('common/header', $this->data);
        $this->load->view('society/index',$data);
        $this->load->view('common/footer');
    }
    
    function add()
    {
        if($this->session->userdata("group") == "admin" || $this->session->userdata("group") == "society"){
            $this->session->set_flashdata("message","Access Denied");
            redirect("/","refresh");
        }
        
        // validation for society
        $this->form_validation->set_rules("name","Name","trim|required|xss_clean");
        $this->form_validation->set_rules("username","Username","trim|required|is_unique[`users`.`username`]");
        $this->form_validation->set_rules("email","Email","trim|xss_clean|valid_email|is_unique[`users`.`email`]");
        $this->form_validation->set_rules("password","Password","trim|required|xss_clean");
        $this->form_validation->set_rules("mobile","Mobile","trim|required|xss_clean");
        $this->form_validation->set_rules("address","Address","trim|xss_clean");
        $this->form_validation->set_rules("area","Area","trim|xss_clean");
        $this->form_validation->set_rules("street","Street","trim|xss_clean");
        $this->form_validation->set_rules("contact_person","Contact Person","trim|xss_clean");
        $this->form_validation->set_rules("pincode","Pincode","trim|xss_clean");
        $this->form_validation->set_rules("state","State","trim|xss_clean");
        $this->form_validation->set_rules("city","City","trim|xss_clean");
        $this->form_validation->set_rules('logo', 'Logo', 'callback_image_upload');
        
        if($this->form_validation->run() == TRUE){
            $img = $this->data['image_name'];
            $soc_data = array(
                "dairy_id"=> $this->session->userdata("id"),
                "name"=> ucfirst($this->input->post("name")),
                "username"=> $this->input->post("username"),
                "email"=> $this->input->post("email"),
                "password"=> md5($this->input->post("password")),
                "mobile"=> $this->input->post("mobile"),
                "address"=> $this->input->post("address"),
                "area"=> $this->input->post("area"),
                "street"=> $this->input->post("street"),
                "contact_person"=> $this->input->post("contact_person"),
                "pincode"=> $this->input->post("pincode"),
                "state"=> $this->input->post("state"),
                "city"=> $this->input->post("city"),
                "acc_no"=> $this->input->post("ac_no"),
                "bank_name"=> $this->input->post("bank_name"),
                "acc_type"=> $this->input->post("acc_type"),
                "ifsc"=> $this->input->post("ifsc"),
                'token'=>md5($this->input->post("username").$this->session->userdata("id")),
                "photo"=>$img
            );
//            echo "<pre>";
//            print_r($soc_data);exit;
            if($this->society_model->add_society($soc_data))
            {
                $this->session->set_flashdata("success","Society data inserted successfully.");
            }
            else
            {
                $this->session->set_flashdata("success","Society data has not been inserted.");
            }
            redirect("society",'refresh');
        }
        /*if(($this->form_validation->run() == TRUE) && $this->society_model->add_society($soc_data)){
            $this->session->set_flashdata("success","Society data inserted successfully.");
            redirect("society",'refresh');
        }*/else{
            $data['errors'] = $this->form_validation->error_array();
            $data['states'] = $this->dairy_model->get_states();
            $this->load->view("common/header", $this->data);
            $this->load->view("society/add",$data);
            $this->load->view("common/footer");
        }
    }
    
    function image_upload(){
        if($_FILES['logo']['size'] != 0){
            $upload_dir = APPPATH.'../assets/uploads/';
            if (!is_dir($upload_dir)) {
                 mkdir($upload_dir);
            }	
            $config['upload_path']   = $upload_dir;
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['file_name']     = 'userimage_'.substr(md5(rand()),0,7);
            $config['overwrite']     = false;
            $config['max_size']	 = '5120';

            $this->upload->initialize($config);
            if (!$this->upload->do_upload('logo')){
                $this->form_validation->set_message('image_upload', $this->upload->display_errors());
                return FALSE;
            }	
            else{
                $this->upload_data['file'] =  $this->upload->data();
                return $this->data['image_name'] = $this->upload_data['file']['file_name'];
            }	
        }	
        else{
//            $this->form_validation->set_message('image_upload', "No file selected");
            return $this->data['image_name'] = "default.jpg";
        }
    }
    
    function edit($id = NULl){
//        if($this->session->userdata("group") != "dairy"){
//            $this->session->set_flashdata("message","Access Denied");
//            redirect("/","refresh");
//        }
        // validation for society
        $this->form_validation->set_rules("name","Name","trim|required|xss_clean");
        $this->form_validation->set_rules("username","Username","trim|required|xss_clean|callback_check_username");
        $this->form_validation->set_rules("email","Email","trim|xss_clean|valid_email|callback_check_email");
        $this->form_validation->set_rules("password","Password","trim|xss_clean");
        $this->form_validation->set_rules("mobile","Mobile","trim|required|xss_clean");
        $this->form_validation->set_rules("address","Address","trim|xss_clean");
        $this->form_validation->set_rules("area","Area","trim|xss_clean");
        $this->form_validation->set_rules("street","Street","trim|xss_clean");
        $this->form_validation->set_rules("contact_person","Contact Person","trim|xss_clean");
        $this->form_validation->set_rules("pincode","Pincode","trim|xss_clean");
        $this->form_validation->set_rules("state","State","trim|xss_clean");
        $this->form_validation->set_rules("city","City","trim|xss_clean");
        $this->form_validation->set_rules('logo', 'Logo', 'callback_image_upload');

        if($this->form_validation->run() == TRUE){
//            $img = ($this->data['image_name'] == 'default.jpg') ? '' : $soc_data[''];
            $soc_data = array(
                "dairy_id"=> $this->session->userdata("id"),
                "name"=> ucfirst($this->input->post("name")),
                "username"=> $this->input->post("username"),
                "email"=> $this->input->post("email"),
//                "password"=> md5($this->input->post("password")),
                "mobile"=> $this->input->post("mobile"),
                "address"=> $this->input->post("address"),
                "area"=> $this->input->post("area"),
                "street"=> $this->input->post("street"),
                "contact_person"=> $this->input->post("contact_person"),
                "pincode"=> $this->input->post("pincode"),
                "state"=> $this->input->post("state"),
                "city"=> $this->input->post("city"),
                "acc_no"=> $this->input->post("ac_no"),
                "bank_name"=> $this->input->post("bank_name"),
                "acc_type"=> $this->input->post("acc_type"),
                "ifsc"=> $this->input->post("ifsc"),
            );
            if($this->input->post("password") != ""){
                $soc_data["password"] = md5($this->input->post("password"));
            }
            if($this->data['image_name'] != 'default.jpg'){
                $soc_data['photo'] = $this->data['image_name'];
            }
            if($this->society_model->edit_society($soc_data, $id))
            {
                $this->session->set_flashdata("success","Society data updated successfully.");
            }else{
                $this->session->set_flashdata("success","Society data has not been updated.");
            }
            redirect("society",'refresh');
        }
        /*if(($this->form_validation->run() == TRUE) && $this->society_model->edit_society($soc_data, $id)){
            $this->session->set_flashdata("success","Society data updated successfully.");
            redirect("society",'refresh');
        }*/else{
            $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['id'] = $id;
            $data['errors'] = $this->form_validation->error_array();
            $data['society'] = $this->society_model->get_society_by_id($id);
            $data['states'] = $this->dairy_model->get_states();
            $this->load->view("common/header", $this->data);
            $this->load->view("society/edit",$data);
            $this->load->view("common/footer");
        }
    }
    
    function change_status($id = NULL){
        if($this->society_model->change_status($id)){
            $this->session->set_flashdata("success", "Status changed successfully");
            redirect("society", "refresh");
        }
    }
    
    function check_username($str){
        if($str == $this->input->post("username_edit")){
            return TRUE;
        }else{
            if($this->dairy_model->check_username($str)){
                $this->form_validation->set_message("check_username","The username is already exist.");
                return FALSE;
            }
            return TRUE;
        }
        return TRUE;
    }
    
    function check_email($str){
        if($str == $this->input->post("email_edit")){
            return TRUE;
        }else{
            if($this->dairy_model->check_email($str)){
                $this->form_validation->set_message("check_email","The email is already exist.");
                return FALSE;
            }
            return TRUE;
        }
        return TRUE;
    }
}

/** application/controllers/Society.php */