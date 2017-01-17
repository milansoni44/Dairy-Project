<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Society
 *
 * @author Milan Soni
 */
class Society extends CI_Controller
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
    }
    
    public function index()
    {
        if($this->session->userdata("group") != "dairy" && $this->session->userdata("group") != "admin"){
            $this->session->set_flashdata("message","Access Denied");
            redirect("/","refresh");
        }
        $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
        $data['society'] = $this->society_model->get_society();
        $this->load->view('common/header', $data);
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
        $this->form_validation->set_rules("email","Email","trim|required|valid_email");
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
            $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['states'] = $this->dairy_model->get_states();
            $this->load->view("common/header", $data);
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
            $this->load->view("common/header", $data);
            $this->load->view("society/edit",$data);
            $this->load->view("common/footer");
        }
    }
    
    // For mobile webservices
    
    
    function login(){
        $response = array();
        if($this->input->post()){
            $array = array(
                "username"=>$this->input->post("username"),
                "password"=>md5($this->input->post("password")),
            );
            $data = $this->auth_model->check_login($array);
            if($data){
                if($this->auth_model->check_userType($data->id) == "society"){
                    $dairy = $this->auth_model->get_dairy($data->id);
                    $response['error'] = FALSE;
                    $response['dairy'] = $dairy->name;
                    $response['data'] = $data;

                    http_response_code(200);
                    echo json_encode($response);
                }else{
                    $response['error'] = TRUE;
                    $response['message'] = "Username or password is invalid";
                    http_response_code(401);
                    echo json_encode($response);
                }
            }else{
                $response['error'] = TRUE;
                $response['message'] = "Username or password is invalid";
                http_response_code(401);
                echo json_encode($response);
            }
        }else{
            $response['error'] = TRUE;
            $response['message'] = "Please try again letter";
            http_response_code(401);
            echo json_encode($response);
        }
    }
}

/** application/controllers/Society.php */