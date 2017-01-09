<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Diary
 *
 * @author Milan Soni(Ehealthsource)
 */
class Auth extends CI_Controller{
    //put your code here
    public function __construct() {
        parent::__construct();
        $this->load->library("form_validation");
        $this->load->helper("url");
        $this->load->library("session");
        $this->load->library("auth_lib");
        $this->load->helper('security');
        $this->load->helper('form');
        $this->load->model("auth_model");
        $this->load->model("machine_model");
        $this->load->database();
    }
    
    public function index(){
//        redirect("auth/login","refresh");
        $data['uname'] = array(
            "name"=> "uname",
            "id" => "uname",
            "type" => "text",
            "value"=>$this->form_validation->set_value("uname"),
            "placeholder"=>"Username",
            "class"=>"form-control"
        );
        $this->load->view("auth/login",$data);
    }
    
    public function login(){
        if($this->auth_lib->is_logged_in()){
            redirect("/","refresh");
        }
        // validation for login form
        $this->form_validation->set_rules("uname","Username","trim|required|xss_clean");
        $this->form_validation->set_rules("password","Password","trim|required|xss_clean");
        
        if($this->form_validation->run() == TRUE){
            $data = array(
                "username"=> $this->input->post("uname"),
                "password"=> md5($this->input->post("password"))
            );
            
//            print_r($data);exit;
        }
        
        if(($this->form_validation->run() == TRUE)){
            $notification_num = 0;
            $user_data = $this->auth_model->check_login($data);
            if(!empty($user_data)){
                // Todo
                $groups = $this->auth_model->get_user_group($user_data->id);
                $notification_num = $this->machine_model->count_machines($groups->name, $user_data->id)->num;
                if($groups->name == "society"){
                    $dairy = $this->auth_model->get_dairy($user_data->id)->name;
                }else{
                    $dairy = "";
                }
                $set_user_data = array(
                    "username"=>$user_data->username,
                    "name"=>$user_data->name,
                    "id"=> $user_data->id,
                    "group"=> $groups->name,
                    "machine_notify"=>$notification_num,
                    "dairy"=>$dairy
                );
                $this->auth_lib->set_session_data($set_user_data);
                $this->session->set_flashdata("success","Login successfull");
                redirect("/","refresh");
            }else{
                $this->session->set_flashdata('error','Incorrect Username or Password');
                $data['uname'] = array(
                    "name"=> "uname",
                    "id" => "uname",
                    "type" => "text",
                    "value"=>$this->form_validation->set_value("uname"),
                    "placeholder"=>"Username",
                    "class"=>"form-control"
                );
                $this->load->view("auth/login",$data);
            }
        }else{
            $data['errors'] = $this->form_validation->error_array();
            $data['uname'] = array(
                "name"=> "uname",
                "id" => "uname",
                "type" => "text",
                "value"=>$this->form_validation->set_value("uname"),
                "placeholder"=>"Username",
                "class"=>"form-control"
            );
            $this->load->view("auth/login", $data);
        }
    }
    
    public function logout(){
        try {
            $this->auth_lib->sess_destroy(); 
            $this->session->set_flashdata('success','Logged out successfully.');
            redirect("auth/login","refresh");
        } catch (Exception $ex) {
            echo "error";
        }
    }
    
    public function forgot(){
        $this->form_validation->set_rules("email","Email","trim|required|xss_clean|valid_email");
        
        if($this->form_validation->run() == TRUE){
            if($this->auth_model->check_email_exist($this->input->post('email'))){
                $userInfo = $this->auth_model->getUserInfoByEmail($this->input->post('email'));
                $token = $this->auth_model->insertToken($userInfo->id);
                $qstring = $this->base64url_encode($token);  
                $url = base_url() . 'index.php/auth/reset_password/token/' . $qstring;
                $link = '<a href="' . $url . '">' . $url . '</a>';
                $this->load->library('email');

                $subject = 'Password reset';
                $message = '<p>This message has been sent for reseting password.</p><br>'.$link;

                // Get full html:
                $body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=' . strtolower(config_item('charset')) . '" />
                    <title>' . html_escape($subject) . '</title>
                    <style type="text/css">
                        body {
                            font-family: Arial, Verdana, Helvetica, sans-serif;
                            font-size: 16px;
                        }
                    </style>
                </head>
                <body>
                ' . $message . '
                </body>
                </html>';
                // Also, for getting full html you may use the following internal method:
                //$body = $this->email->full_html($subject, $message);

                $result = $this->email
                        ->from('info@aurseekho.com')
                        ->reply_to('ehs.milan@gmail.com')    // Optional, an account where a human being reads.
                        ->to('ehs.milan@gmail.com')
                        ->subject($subject)
                        ->message($body)
                        ->send();

//                var_dump($result);
                echo '<br />';
                echo $this->email->print_debugger();

                exit;
            }else{
                $this->session->set_flashdata('error','Email is not exist');
                $data['forgot'] = array(
                    'id'=>'email',
                    'name'=>'email',
                    'type'=>'email',
                    'value'=>$this->form_validation->set_value('email'),
                    'class'=>'form-control',
                    'placeholder'=>'Email'
                );
                $this->load->view('auth/forgot',$data);
            }
        }else{
            $data['errors'] = $this->form_validation->error_array();
            $data['forgot'] = array(
                'id'=>'email',
                'name'=>'email',
                'type'=>'email',
                'value'=>$this->form_validation->set_value('email'),
                'class'=>'form-control',
                'placeholder'=>'Email'
            );
            $this->load->view('auth/forgot',$data);
        }
    }
    
    public function reset_password()
    {
        $token = $this->base64url_decode($this->uri->segment(4));
        $cleanToken = $this->security->xss_clean($token);

        $user_info = $this->auth_model->isTokenValid($cleanToken); //either false or array();               
        if(!$user_info){
            $this->session->set_flashdata('error', 'Token is invalid or expired');
            redirect('auth/login');
        }
        $data = array(
            'name'=> $user_info->name, 
            'email'=>$user_info->email, 
            'username'=>$user_info->username, 
            'token'=>$this->base64url_encode($token)
        );
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');              

        if ($this->form_validation->run() == FALSE) {
            $data['errors'] = $this->form_validation->error_array();
            $this->load->view('auth/reset_password', $data);
        }else{
//            $this->load->library('password');
            $post = $this->input->post(NULL, TRUE);
            $cleanPost = $this->security->xss_clean($post);
            $hashed = md5($cleanPost['password']);
            $cleanPost['password'] = $hashed;
            $cleanPost['user_id'] = $user_info->id;
            unset($cleanPost['passconf']);
            if(!$this->auth_model->updatePassword($cleanPost)){
                $this->session->set_flashdata('error', 'There was a problem updating your password');
            }else{
                $this->session->set_flashdata('success', 'Your password has been updated. You may now login');
            }
            redirect('auth/login','refresh');
        }
    }
    
    public function base64url_encode($data) {
      return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    public function base64url_decode($data) {
      return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}