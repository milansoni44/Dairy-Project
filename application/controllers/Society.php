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
        $this->form_validation->set_rules("name","Name","trim|required");
        $this->form_validation->set_rules("username","Username","trim|required");
        $this->form_validation->set_rules("email","Email","trim|valid_email");
        $this->form_validation->set_rules("password","Password","trim|required");
        $this->form_validation->set_rules("mobile","Mobile","trim|required");
        
        if($this->form_validation->run() == TRUE){
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
            );
//            echo "<pre>";
//            print_r($soc_data);exit;
        }
        if(($this->form_validation->run() == TRUE) && $this->society_model->add_society($soc_data)){
            $this->session->set_flashdata("success","Society data inserted successfully.");
            redirect("society",'refresh');
        }else{
            echo validation_errors();exit;
            $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['states'] = $this->dairy_model->get_states();
            $this->load->view("common/header", $this->data);
            $this->load->view("society/add",$data);
            $this->load->view("common/footer");
        }
    }
    
    function edit($id = NULl){
//        if($this->session->userdata("group") != "dairy"){
//            $this->session->set_flashdata("message","Access Denied");
//            redirect("/","refresh");
//        }
        // validation for society
        $this->form_validation->set_rules("name","Name","trim|required");
        $this->form_validation->set_rules("username","Username","trim|required");
        $this->form_validation->set_rules("email","Email","trim|required|valid_email");
        $this->form_validation->set_rules("password","Password","trim");
        $this->form_validation->set_rules("mobile","Mobile","trim|required");
        
        if($this->form_validation->run() == TRUE){
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
                $data["password"] = md5($this->input->post("password"));
            }
        }
        if(($this->form_validation->run() == TRUE) && $this->society_model->edit_society($soc_data, $id)){
            $this->session->set_flashdata("success","Society data updated successfully.");
            redirect("society",'refresh');
        }else{
            $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['id'] = $id;
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
}

/** application/controllers/Society.php */