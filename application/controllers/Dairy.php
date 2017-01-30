<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Dairy
 *
 * @author Milan Soni
 */
class Dairy extends MY_Controller{
    //put your code here
    public function __construct() {
        parent::__construct();
        $this->load->library("auth_lib");
        $this->load->model("dairy_model");
        $this->load->helper('url');
        $this->load->helper('security');
        $this->load->library("form_validation");
        $this->load->helper("security");
        $this->load->library("session");
        $this->load->database();
        if(!$this->auth_lib->is_logged_in()){
            redirect("auth/login","refresh");
        }
    }
    
    public function index(){
        if($this->session->userdata("group") != "admin"){
            $this->session->set_falshdata("message","Access Denied");
            redirect("/","refresh");
        }
//        $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
        $data['dairies'] = $this->dairy_model->get_dairy();
        $this->load->view("common/header", $this->data);
        $this->load->view("dairy/index",$data);
        $this->load->view("common/footer");
    }
    
    public function add(){
        if($this->session->userdata("group") != "admin"){
            $this->session->set_falshdata("message","Access Denied");
            redirect("/","refresh");
        }
        // validation for dairy
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
        
        if($this->form_validation->run() == TRUE){
            $validity = $this->input->post("validity");
            $dd = explode("-", $validity);
//            $start_date = date("Y-m-d", strtotime($dd[0]));
//            $end_date = date("Y-m-d", strtotime($dd[1]));
            $data = array(
                "name"=>  ucfirst($this->input->post("name")),
                "username"=>  $this->input->post("username"),
                "password"=>  md5($this->input->post("password")),
                "email"=>  $this->input->post("email"),
                "address"=>  $this->input->post("address"),
                "area"=>  $this->input->post("area"),
                "contact_person"=>  $this->input->post("contact_person"),
                "mobile"=>  $this->input->post("mobile"),
                "street"=>  $this->input->post("street"),
                "pincode"=>  $this->input->post("pincode"),
                "state"=>  $this->input->post("state"),
                "city"=>  $this->input->post("city"),
//                "validity_start_date"=>$start_date,
//                "validity_end_date"=>$end_date,
            );
//            echo "<pre>";
//            print_r($data);exit;
        }
        if(($this->form_validation->run() == TRUE) && $this->dairy_model->add_dairy($data)){
            $this->session->set_flashdata("success","Dairy data inserted successfully.");
            redirect("dairy",'refresh');
        }else{
//            $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['errors'] = $this->form_validation->error_array();
            $data['states'] = $this->dairy_model->get_states();
            $this->load->view("common/header", $this->data);
            $this->load->view("dairy/add",$data);
            $this->load->view("common/footer");
        }
    }
    
    public function edit($id = NULL){
        if($this->session->userdata("group") != "admin"){
            $this->session->set_falshdata("message","Access Denied");
            redirect("/","refresh");
        }
        // validation for dairy
        $this->form_validation->set_rules("name","Name","trim|required|xss_clean");
        $this->form_validation->set_rules("username","Username","trim|required|xss_clean|callback_check_username");
        $this->form_validation->set_rules("password","Password","trim|xss_clean");
        $this->form_validation->set_rules("email","Email","trim|xss_clean|valid_email|callback_check_email");
        $this->form_validation->set_rules("mobile","Mobile","trim|required|xss_clean");
        $this->form_validation->set_rules("address","Address","trim|xss_clean");
        $this->form_validation->set_rules("area","Area","trim|xss_clean");
        $this->form_validation->set_rules("street","Street","trim|xss_clean");
        $this->form_validation->set_rules("contact_person","Contact Person","trim|xss_clean");
        $this->form_validation->set_rules("pincode","Pincode","trim|xss_clean");
        $this->form_validation->set_rules("state","State","trim|xss_clean");
        $this->form_validation->set_rules("city","City","trim|xss_clean");
        
        if($this->form_validation->run() == TRUE){
            $validity = $this->input->post("validity");
//            $dd = explode("-", $validity);
//            $start_date = date("Y-m-d", strtotime($dd[0]));
//            $end_date = date("Y-m-d", strtotime($dd[1]));
            $data = array(
                "name"=> ucfirst($this->input->post("name")),
                "username"=>  $this->input->post("username"),
                "email"=>  $this->input->post("email"),
                "address"=>  $this->input->post("address"),
                "area"=>  $this->input->post("area"),
                "contact_person"=>  $this->input->post("contact_person"),
                "mobile"=>  $this->input->post("mobile"),
                "street"=>  $this->input->post("street"),
                "pincode"=>  $this->input->post("pincode"),
                "state"=>  $this->input->post("state"),
                "city"=>  $this->input->post("city"),
//                "validity_start_date"=>$start_date,
//                "validity_end_date"=>$end_date,
            );
            if($this->input->post("password") != ""){
                $data["password"] = md5($this->input->post("password"));
            }
//            echo "<pre>";
//            print_r($data);exit;
        }
        if(($this->form_validation->run() == TRUE) && $this->dairy_model->update_dairy($data,$id)){
            $this->session->set_flashdata("success","Dairy data updated successfully.");
            redirect("dairy",'refresh');
        }else{
//            $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['errors'] = $this->form_validation->error_array();
            $data['dairy'] = $this->dairy_model->get_dairy_by_id($id);
            $data['states'] = $this->dairy_model->get_states();
            $data['cities'] = $this->dairy_model->get_cities_by_id($data['dairy']->state);
            $data['id'] = $id;
            $this->load->view("common/header", $this->data);
            $this->load->view("dairy/edit",$data);
            $this->load->view("common/footer");
        }
    }
    
    public function delete($id = NULL){
        if($this->session->userdata("group") != "admin"){
            $this->session->set_falshdata("message","Access Denied");
            redirect("/","refresh");
        }
        if($this->dairy_model->delete($id)){
            $this->session->set_flashdata("success","Dairy data deleted successfully.");
            redirect("dairy",'refresh');
        }
    }
    
    public function get_cities(){
        $id = $this->input->post('s_id');
        $data = $this->dairy_model->get_cities_by_id($id);
        echo json_encode($data);
        exit;
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
    
    function change_status($id = NULL){
        if($this->dairy_model->change_status($id)){
            $this->session->set_flashdata("success", "Status changed successfully");
            redirect("dairy", "refresh");
        }
    }
}

/** application/controllers/Dairy.php */