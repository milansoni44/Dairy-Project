<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Api
 *
 * @author Milan & Abhay
 */
class Api extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->model("auth_model");
        $this->load->model("society_model");
        $this->load->model("dairy_model");
        $this->load->database();
    }
    
    function society_login(){
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

    function login() {
        $response = array();
        if ($this->input->post()) {
            $array = array(
                "username" => $this->input->post("username"),
                "password" => md5($this->input->post("password")),
            );
            $data = $this->auth_model->check_login($array);
            if ($data) {
                if ($this->auth_model->check_userType($data->id) == "society") {
                    $dairy = $this->auth_model->get_dairy($data->id);
                    $response['error'] = FALSE;
                    $response['dairy'] = $dairy->name;
                    $response['data'] = $data;

                    ;
                    echo json_encode($response);
                } else {
                    $response['error'] = TRUE;
                    $response['message'] = "Username or password is invalid";
                    http_response_code(401);
                    echo json_encode($response);
                }
            } else {
                $response['error'] = TRUE;
                $response['message'] = "Username or password is invalid";
                http_response_code(401);
                echo json_encode($response);
            }
        } else {
            $response['error'] = TRUE;
            $response['message'] = "Please try again letter";
            http_response_code(401);
            echo json_encode($response);
        }
    }

    /* customer login by otp * Start */

    public function generateOTP() {
        return substr(str_shuffle("0123456789"), 0, 4);
    }

    public function custoerOtpLogin() {
        /*
          param = mobile

          return data:
          {
          error: true/false,
          customer_id: numeric,
          message: 'this is message'
          }

         */
        $response = array();
        $http_response_code = 401;

        if ($this->input->server("REQUEST_METHOD") === "POST") {
            $mobile = $this->input->post('mobile');
            if ($mobile && $mobile != "" && $mobile != NULL) {
                $result = $this->db->query(" SELECT `id` FROM `customers` WHERE `mobile`=" . $mobile);

                if ($result) {
                    $otp = (int) $this->generateOTP();
                    $customer_id = $result->row('id');
                    $result2 = $this->db->query(" UPDATE `customers` SET `otp`=" . $otp . " WHERE `id`=" . $customer_id);

                    if ($result2) {
                        /* Message to client start */
                        //$otp = "454545";
                        $ch = curl_init();
                        //$mobile="4654654654";
                        $msg = $otp . " is your one time password (OTP) at Dairysuite App";
                        //$msg = "Onetime Password (OTP) for your login is ".$otp;
                        $msg = urlencode($msg);

                        curl_setopt($ch, CURLOPT_URL, "http://ip.shreesms.net/smsserver/SMS10N.aspx?Userid=BPUNGI&UserPassword=12345&PhoneNumber=$mobile&Text=$msg");
                        $output = curl_exec($ch);

                        curl_close($ch);
                        /* Message to client end */
                        $http_response_code = 200;
                        $response = array(
                            'error' => FALSE,
                            'customer_id' => $customer_id,
                            'message' => "OTP has been sent to given mobile number"
                        );
                    } else {
                        $response = array(
                            'error' => TRUE,
                            'message' => "error in saving OTP"
                        );
                    }
                } else {
                    $response = array(
                        'error' => TRUE,
                        'message' => "Your mobile number is not saved in our system"
                    );
                }
            } else {
                $response = array(
                    'error' => TRUE,
                    'message' => "Mobile number required"
                );
            }
        } else {
            $response = array(
                'error' => TRUE,
                'message' => "Your method is invalid."
            );
        }
        //header("Content-type: application/json");
        http_response_code($http_response_code);
        echo json_encode($response);
    }

    public function customerLoginOtpSubmit() {
        /*
          param = customer_id, otp

          return data:
          {
          error: true/false,
          customer_info: json_array,
          message: 'this is message'
          }

         */
        $response = array();
        $http_response_code = 401;
        if ($this->input->server("REQUEST_METHOD") === "POST") {
            $customer_id = $this->input->post('customer_id');
            $otp = $this->input->post('otp');

            if ($otp && $otp != '' && $customer_id && $customer_id != '') {
                $result = $this->db->query("SELECT COUNT(*) AS `total` FROM `customers` WHERE `id`=" . $customer_id . " AND `otp`=" . $otp);

                if ($result->row('total') > 0) {
                    $this->db->query(" UPDATE `customers` SET `otp`=NULL WHERE `id`=" . $customer_id);
                    $result2 = $this->db->query("SELECT * FROM `customers` WHERE `id`=" . $customer_id);

                    $http_response_code = 200;
                    $response = array(
                        'error' => FALSE,
                        'customer_info' => $result2->row_array(),
                        'message' => "Login success"
                    );
                } else {
                    $response = array(
                        'error' => TRUE,
                        'message' => "Invalid otp or maybe expired otp"
                    );
                }
            } else {
                $response = array(
                    'error' => TRUE,
                    'message' => "provide otp and customer_id"
                );
            }
        } else {
            $response = array(
                'error' => TRUE,
                'message' => "Your method is invalid."
            );
        }
        http_response_code($http_response_code);
        echo json_encode($response);
    }

    /* customer login by otp * end */



    /* society app webservice * Start */

    public function societyCustomerList() {
        /*
          param = society_id

          return data:
          {
          error: true/false,
          customer_list: json_array,
          message: 'this is message'
          }

         */

        $response = array();
        $http_response_code = 401;

        if ($this->input->server("REQUEST_METHOD") === "POST") {
            $society_id = $this->input->post('society_id');

            if ($society_id && $society_id != '') {
                $result = $this->db->query("SELECT * FROM `customers` WHERE `id` IN (
		SELECT `cid` FROM `customer_machine` WHERE `society_id`=" . $society_id . ")");

                if ($result->num_rows() > 0) {
                    $http_response_code = 200;
                    $response = array(
                        'error' => FALSE,
                        'customer_list' => $result->result_array()
                    );
                } else {
                    $response = array(
                        'error' => TRUE,
                        'message' => "There is no customers as of now."
                    );
                }
            } else {
                $response = array(
                    'error' => TRUE,
                    'message' => "Provide society_id"
                );
            }
        } else {
            $response = array(
                'error' => TRUE,
                'message' => "Your method is invalid."
            );
        }
        http_response_code($http_response_code);
        echo json_encode($response);
    }

    public function societyTransactionSummary() {
        /*
          param = society_id=11, date = Y-m-d, shift= E|M

          return data:
          {
          error: true/false,
          customer_list: json_array,
          message: 'this is message'
          }

         */

        $response = array();
        $http_response_code = 401;

        if ($this->input->server("REQUEST_METHOD") === "POST") {
            $society_id = $this->input->post('society_id');
            $date = $this->input->post('date') ? date('Y-m-d', strtotime($this->input->post('date'))) : date('Y-m-d');
            $shift = $this->input->post('shift');

            if ($society_id && $society_id != '' && $shift && $shift != '') {
                $result_cnt = $this->db->query("SELECT 
					COUNT(*) AS total
		FROM `transactions` WHERE `society_id`=" . $society_id . " AND `date`='" . $date . "' ");
                if($result_cnt->row("total") > 0){
                    $result_cow = $this->db->query("SELECT 
                                            SUM(`weight`) AS `total_litre`, 
                                            AVG(`fat`) AS `avg_fat`, 
                                            AVG(`clr`) AS `avg_clr`, 
                                            AVG(`snf`) AS `avg_snf`, 
                                            SUM(`netamt`) AS `total_amount`, 
                                            COUNT(`cid`) AS `producer`,
                                            (SELECT `machine_id` FROM `machines` `m` WHERE `m`.`id` = `transactions`.`deviceid` ) AS `machine_code`
                    FROM `transactions` WHERE `society_id`=" . $society_id . " AND shift='$shift' AND `type`='C' AND `date`='" . $date . "' ");

                    $result_buf = $this->db->query("SELECT 
                                            SUM(`weight`) AS `total_litre`, 
                                            AVG(`fat`) AS `avg_fat`, 
                                            AVG(`clr`) AS `avg_clr`, 
                                            AVG(`snf`) AS `avg_snf`, 
                                            SUM(`netamt`) AS `total_amount`, 
                                            COUNT(`cid`) AS `producer`,
                                            (SELECT `machine_id` FROM `machines` `m` WHERE `m`.`id` = `transactions`.`deviceid` ) AS `machine_code`
                    FROM `transactions` WHERE `society_id`=" . $society_id . " AND shift='$shift' AND `type`='B' AND `date`='" . $date . "' ");
                    
                    $http_response_code = 200;
                    $response = array(
                        'error' => FALSE,
                        'data' => array(
                            'date_shift' => date("d-M-Y", strtotime($date))." ( ". ($shift=="E" ? "Evening" : "Morning" ) ." )",
                            'cow' => $result_cow->row_array(),
                            'buf' => $result_buf->row_array()
                        )
                    );
                }else{
                    $response = array(
                        'error' => TRUE,
                        'message' => "There is no transactions as of now."
                    );
                }
            } else {
                $response = array(
                    'error' => TRUE,
                    'message' => "Please provide society_id & shift"
                );
            }
        } else {
            $response = array(
                'error' => TRUE,
                'message' => "Your method is invalid."
            );
        }
        http_response_code($http_response_code);
        echo json_encode($response);
    }
    
    function import_json() {
        $response = array();
        $validation_error = array();
        if ($this->input->post()) {
            $data = json_decode($this->input->post("society_json"))->transaction;
//            print_r($data);exit;
            $i = 0;
            foreach ($data as $row) {
                // for temporary purpose
                $stat = $this->transaction_model->exist_machine("IDF000001");
                if ($stat === FALSE) {
                    http_response_code(400);
                    $response['error'] = TRUE;
                    $response['message'] = "Machine not exist in the system.";
                    echo json_encode($response);
                    exit;
                }

                $society = $this->transaction_model->get_society_id("IDF000001")->society_id;
                $dairy = $this->transaction_model->get_dairy_id("IDF000001")->dairy_id;
                $machine_id = $this->transaction_model->get_machine_id("IDF000001")->mid;

                if ($row->aadhar == "") {
//                    $this->session->set_flashdata("message","Line:$i Adhar no required");
                    $validation_error[] = array("message" => "Line:$i Adhar no required");
                    $i++;
                    continue;
                }

                if ($this->customer_model->check_exist_adhar($row->aadhar) === FALSE) {
                    $customer_data = array(
                        "adhar_no" => $row->aadhar,
                    );
                    $cid = $this->customer_model->add_customer($customer_data, /* $row->deviceid */ $machine_id, $society);
//                    echo  = $this->db->insert_id();exit;
                    $date = str_replace('/', '-', $row->date);
                    $transaction_single = array(
                        "dairy_id" => $dairy,
                        "society_id" => $society,
                        "deviceid" => $machine_id, //$row->deviceid,
                        "sampleid" => $row->sampleid,
                        "ismanual" => $row->manual,
                        "type" => $row->type,
                        "cid" => $cid,
                        "netamt" => $row->rupee,
                        "rate" => $row->rate,
                        "weight" => $row->ltr,
                        "snf" => $row->snf,
                        "clr" => $row->clr,
                        "fat" => $row->fat,
                        "memcode" => $row->member_code,
                        "date" => date('Y-m-d', strtotime($date)),
                        "shift" => $row->shift,
                        "dockno" => trim($row->dockno),
                        "soccode" => trim($row->soccode)
                    );
                    $this->transaction_model->insert_single($transaction_single);
                    $i++;
                    continue;
                }
                $cid = $this->transaction_model->get_cid($row->aadhar);
                $t_date = str_replace('/', '-', $row->date);
                $trans = array(
                    "dairy_id" => $dairy,
                    "society_id" => $society,
                    "deviceid" => $machine_id, //$row->deviceid,
                    "sampleid" => $row->sampleid,
                    "ismanual" => $row->manual,
                    "type" => $row->type,
                    "cid" => $cid,
                    "netamt" => $row->rupee,
                    "rate" => $row->rate,
                    "weight" => $row->ltr,
                    "snf" => $row->snf,
                    "clr" => $row->clr,
                    "fat" => $row->fat,
                    "memcode" => $row->member_code,
                    "date" => date('Y-m-d', strtotime($t_date)),
                    "shift" => $row->shift,
                    "dockno" => trim($row->dockno),
                    "soccode" => trim($row->soccode)
                );
                $this->transaction_model->insert_single($trans);
                $i++;
                continue;
            }
            http_response_code(200);
            $response['error'] = FALSE;
            $response['message'] = "Transaction data successfully uploaded";
            $response['validate_error'] = $validation_error;
            echo json_encode($response);
        } else {
            $response['error'] = TRUE;
            $response['message'] = "Please try again letter";
            http_response_code(400);
            echo json_encode($response);
        }
    }

    function list_transaction() {
        $response = array();
        if ($this->input->post()) {
            $sid = $this->input->post("sid");
            if ($txn_list = $this->transaction_model->get_txn_list($sid)) {
                $response['error'] = FALSE;
                $response['message'] = "Data loaded successfully";
                $response['data'] = $txn_list;
                http_response_code(200);
                echo json_encode($response);
            } else {
                $response['error'] = TRUE;
                $response['message'] = "No data found";
                http_response_code(404);
                echo json_encode($response);
            }
        } else {
            $response['error'] = TRUE;
            $response['message'] = "Please try again letter";
            http_response_code(400);
            echo json_encode($response);
        }
    }
    
    function search_txn(){
        $response = array();
        if($this->input->post()){
            $sid = $this->input->post("sid");
            $str = $this->input->post("search");
            if($txn_list = $this->transaction_model->search_txn($sid, $str)){
                $response['error'] = FALSE;
                $response['message'] = "Data loaded successfully";
                $response['data'] = $txn_list;
                http_response_code(200);
                echo json_encode($response);
            }else{
                $response['error'] = TRUE;
                $response['message'] = "No data found";
                http_response_code(404);
                echo json_encode($response);
            }
        }else{
            $response['error'] = TRUE;
            $response['message'] = "Please try again letter";
            http_response_code(400);
            echo json_encode($response);
        }
    }
    /* society app webservice * End */
}