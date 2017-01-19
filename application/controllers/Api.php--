<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Api
 *
 * @author Intel
 */
class Api extends CI_Controller{
    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->model("auth_model");
        $this->load->model("society_model");
        $this->load->model("dairy_model");
        $this->load->database();
    }
    
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
