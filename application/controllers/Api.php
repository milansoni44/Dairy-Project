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
class Api extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("auth_model");
        $this->load->model("society_model");
        $this->load->model("machine_model");
        $this->load->model("dairy_model");
        $this->load->model("transaction_model");
        $this->load->model("customer_model");
        $this->load->database();
    }

    /**
     * Header authentication for society
     * @return mixed
     */
    public function check_header_authentication_for_society()
    {
        $headers = getallheaders();
        if (isset($headers['Authorization-Key'])) {
            $api_key = $headers['Authorization-Key'];
            $id = $this->society_model->get_society_id($api_key);
            return $id;
        } else {
            $response['error'] = TRUE;
            $response['message'] = "Api key is missing";
            http_response_code(401);
            echo json_encode($response);
            exit;
        }
    }

    /**
     * Header authentication for customers
     * @return mixed
     */
    public function check_header_authentication_for_customer()
    {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $api_key = $headers['Authorization'];
            $id = $this->customer_model->get_customer_id($api_key);
            return $id;
        } else {
            $response['error'] = TRUE;
            $response['message'] = "Api key is missing";
            http_response_code(401);
            echo json_encode($response);
            exit;
        }
    }

    /* customer login by otp * Start */

    public function generateOTP()
    {
        return substr(str_shuffle("123456789"), 0, 4);
    }

    public function custoerOtpLogin()
    {
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

                if ($result->row('id')) {
                    $otp = (int)$this->generateOTP();
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
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
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

    public function customerLoginOtpSubmit()
    {
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
                    $result2 = $this->db->query("SELECT *,
												(CASE `type` 
												 WHEN 'C' THEN 'Cow' 
												 WHEN 'B' THEN 'Buffalo' 
												 ELSE NULL 
												 END) AS `type_word`
												FROM `customers` WHERE `id`=" . $customer_id);
                    $customer_info = $result2->row_array();

                    $result_meta = $this->db->query("SELECT `society_id`,
( SELECT `u`.`name` FROM `users` `u` WHERE `u`.`id`=`customer_machine`.`society_id` ) AS `society_name`,
( SELECT `u`.`dairy_id` FROM `users` `u` WHERE `u`.`id`=`customer_machine`.`society_id` ) AS `dairy_id`,
( SELECT `u`.`name` FROM `users` `u` 
WHERE `u`.`id`=( SELECT `ud`.`dairy_id` FROM `users` `ud` WHERE `ud`.`id`=`customer_machine`.`society_id` ) ) AS `dairy_name`
					FROM `customer_machine` WHERE `cid`=" . $customer_id);
                    $customer_meta_info = $result_meta->result_array();

                    $http_response_code = 200;
                    $response = array(
                        'error' => FALSE,
                        'customer_info' => $customer_info,
                        'customer_meta_info' => $customer_meta_info,
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

    function customer_weekly_transaction()
    {
        $http_response_code = 401;
        $society_arr = array();
        $response = array();
        $data['cow'] = array();
        $data['buffalo'] = array();
        $data['society'] = array();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $cid = $this->input->post("cid");
            $sid = $this->input->post("society_id");
            $society_list = $this->customer_model->get_customer_society($cid);
            if (!empty($society_list)) {
                foreach ($society_list as $rw_soc) {
                    $data['society'][] = $rw_soc;
                }
            }
            $transaction = $this->transaction_model->get_weekly_transaction($cid, $sid);
            if (!empty($transaction)) {
                foreach ($transaction as $rw_txn) {
                    if ($rw_txn['type'] == 'B') {
                        $data['buffalo'][] = $rw_txn;
                    } else {
                        $data['cow'][] = $rw_txn;
                    }
                }
                $http_response_code = 200;
                $response['error'] = FALSE;
                $response['message'] = "Transaction Found";
                $response['data'] = $data;
            } else {
                $response['error'] = TRUE;
                $response['message'] = "No transaction found";
            }

        } else {
            $response['error'] = TRUE;
            $response['message'] = "Invalid method";
        }

        http_response_code($http_response_code);
        echo json_encode($response);
    }

    function customer_range_transaction()
    {
        $http_response_code = 401;
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $society = $this->input->post("society_id");
            $from_date = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post("from_date"))));
            $to_date = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post("to_date"))));
            $cid = $this->input->post("cid");
            $type = $this->input->post("type");

            $data_arr = array(
                "society" => $society,
                "from_date" => $from_date,
                "to_date" => $to_date,
                "cid" => $cid,
                "type" => $type
            );
            $transaction = $this->transaction_model->get_customRangeTxn($data_arr);
            if (!empty($transaction)) {
                $http_response_code = 200;
                $response['error'] = FALSE;
                $response['message'] = "data found";
                $response['data'] = $transaction;
            } else {
                $response['error'] = TRUE;
                $response['message'] = "No data found";
            }
        } else {
            $response['error'] = TRUE;
            $response['message'] = "Invalid method";
        }
        http_response_code($http_response_code);
        echo json_encode($response);
    }

    function weekly_txn_summary()
    {
        $http_response_code = 401;
        $cow_array = array();
        $buff_array = array();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            // parameter may be exist
            $cid = $this->input->post("cid");
            $buff_array = $this->transaction_model->get_weekly_buff_txn($cid);
            if (!$buff_array) {
                $buff_array = array();
            }
            $cow_array = $this->transaction_model->get_weekly_cow_txn($cid);
            if (!$cow_array) {
                $cow_array = array();
            }
            /*echo "<pre>";
			print_r($transaction_buff);exit;*/
            if (!empty($buff_array) || !empty($cow_array)) {
                $http_response_code = 200;
                $response['error'] = FALSE;
                $response['message'] = "Data found";
                $response['data'] = array("cow" => $cow_array, "buffalo" => $buff_array);
            } else {
                $response['error'] = TRUE;
                $response['message'] = "No transaction found";
            }
        } else {
            $response['error'] = TRUE;
            $response['message'] = "Invalid method";
        }
        http_response_code($http_response_code);
        echo json_encode($response);
    }

    function monthly_txn_summary()
    {
        $http_response_code = 401;
        $cow_array = array();
        $buff_array = array();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            // parameters
            $cid = $this->input->post("cid");
            $buff_array = $this->transaction_model->get_monthly_buff_txn($cid);
            if (!$buff_array) {
                $buff_array = array();
            }
            $cow_array = $this->transaction_model->get_monthly_cow_txn($cid);
            if (!$cow_array) {
                $cow_array = array();
            }
            /*print_r($cow_array);exit;*/
            if (!empty($buff_array) || !empty($cow_array)) {
                $http_response_code = 200;
                $response['error'] = FALSE;
                $response['message'] = "Data found";
                $response['data'] = array("cow" => $cow_array, "buffalo" => $buff_array);
            } else {
                $response['error'] = TRUE;
                $response['message'] = "No transaction found";
            }
        } else {
            $response['error'] = TRUE;
            $response['message'] = "Invalid method";
        }
        http_response_code($http_response_code);
        echo json_encode($response);
    }

    function yearly_txn_summary()
    {
        $http_response_code = 401;
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $cid = $this->input->post("cid");
            $buff_array = $this->transaction_model->get_yearly_buff_txn($cid);
            if (!$buff_array) {
                $buff_array = array();
            }
            $cow_array = $this->transaction_model->get_yearly_cow_txn($cid);
            if (!$cow_array) {
                $cow_array = array();
            }
            /*print_r($cow_array);exit;*/
            if (!empty($buff_array) || !empty($cow_array)) {
                $http_response_code = 200;
                $response['error'] = FALSE;
                $response['message'] = "Data found";
                $response['data'] = array("cow" => $cow_array, "buffalo" => $buff_array);
            } else {
                $response['error'] = TRUE;
                $response['message'] = "No transaction found";
            }
        } else {
            $response['error'] = TRUE;
            $response['message'] = "Invalid method";
        }
        http_response_code($http_response_code);
        echo json_encode($response);
    }

    /* customer login by otp * end */


    /* society app webservice * Start */

    function society_login()
    {
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

                    http_response_code(200);
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

    function login()
    {
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
                    $response['data'] = $data;;
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

    public function societyCustomerList()
    {
        $response = array();
        $http_response_code = 401;

        if ($this->input->server("REQUEST_METHOD") === "POST") {
            $society_id = $this->check_header_authentication_for_society();

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

    /**
     * society weekly transaction summary
     * Method: GET
     * Headers: Authorization: api_key
     * response type json
     */
    function society_weekly_transaction()
    {
        $http_response_code = 401;
        $society_arr = array();
        $response = array();
        $data['cow'] = array();
        $data['buffalo'] = array();
        $data['society'] = array();
        if ($this->input->server('REQUEST_METHOD') == 'GET') {
            $sid = $this->check_header_authentication_for_society();

            $buff_array = $this->transaction_model->get_buff_soc_weekly_transaction($sid);

            if (!$buff_array) {
                $buff_array = array();
            }
            $cow_array = $this->transaction_model->get_cow_soc_weekly_transaction($sid);
            if (!$cow_array) {
                $cow_array = array();
            }

            if (!empty($buff_array) || !empty($cow_array)) {
                $http_response_code = 200;
                $response['error'] = FALSE;
                $response['message'] = "Data found";
                $response['data'] = array("cow" => $cow_array, "buffalo" => $buff_array);
            } else {
                $response['error'] = TRUE;
                $response['message'] = "No transaction found";
            }
        } else {
            $response['error'] = TRUE;
            $response['message'] = "Invalid method";
        }
        http_response_code($http_response_code);
        echo json_encode($response);
    }

    /**
     * society transaction summary by date & type filter
     * Method: POST
     * Headers: Authorization: api_key
     * Params:  from_date, to_date, type
     * response type json
     */
    public function societyTransactionSummary()
    {
        $response = array();
        $http_response_code = 401;

        if ($this->input->server("REQUEST_METHOD") === "POST") {
            $society_id = $this->check_header_authentication_for_society();
            $from_date = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post("from_date"))));     // format dd-mm-yyyy
            $to_date = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post("to_date"))));         // format dd-mm-yyyy
            $type = ucfirst($this->input->post("type"));

            if($type == 'B') {
                $buff_array = $this->transaction_model->get_buff_soc_weekly_transaction($society_id, $from_date, $to_date);
                if (!$buff_array) {
                    $buff_array = array();
                }
            }else {
                $cow_array = $this->transaction_model->get_cow_soc_weekly_transaction($society_id, $from_date, $to_date);
                if (!$cow_array) {
                    $cow_array = array();
                }
            }

            if (!empty($buff_array) || !empty($cow_array)) {
                $http_response_code = 200;
                $response['error'] = FALSE;
                $response['message'] = "Data found";
                if($type == 'C') {
                    $response['data'] = array("cow" => $cow_array);
                }else{
                    $response['data'] = array("buffalo" => $buff_array);
                }
            } else {
                $response['error'] = TRUE;
                $response['message'] = "No transaction found";
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
    /**
     * transaction import by society
     * Method: POST
     * Headers: Authorization: api_key
     * Params:  society_json
     * response type json
     */
    function import_json()
    {
        $response = array();
        $validation_error = array();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $data = json_decode($this->input->post("society_json"))->transaction;
            $i = 0;
            foreach ($data as $row) {
                $society = $this->transaction_model->get_society_id($row->deviceid)->society_id;
                $dairy = $this->transaction_model->get_dairy_id($row->deviceid)->dairy_id;
                $machine_id = $this->transaction_model->get_machine_id($row->deviceid)->mid;
                $valid_society_machine = $this->transaction_model->check_mapped_society_machine($machine_id, $society);

                if ($valid_society_machine === FALSE) {
                    http_response_code(401);
                    $response['error'] = TRUE;
                    $response['message'] = "Machine is not allocate to society";
                    echo json_encode($response);
                    exit;
                }
                if ($row->aadhar == "") {
                    $validation_error[] = array("message" => "Line:$i Adhar no required");
                    $i++;
                    continue;
                }

                if ($this->customer_model->check_exist_adhar($row->aadhar) === FALSE) {
                    $customer_data = array(
                        "adhar_no" => $row->aadhar,
                    );
                    $cid = $this->customer_model->add_customer($customer_data, $machine_id, $society);
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
            $response['message'] = "Invalid Method";
            http_response_code(401);
            echo json_encode($response);
        }
    }

    function list_transaction()
    {
        $response = array();
        if ($this->input->server('REQUEST_METHOD') == 'GET') {
            $sid = $this->check_header_authentication_for_society();
            $txn_list = $this->transaction_model->get_txn_list($sid);
            $buff_txn = array();
            $cow_txn = array();
            if(!empty($txn_list)){
                foreach($txn_list as $row){
                    if($row['type'] == "B"){
                        $buff_txn[] = $row;
                    }
                    if($row['type'] == "C"){
                        $cow_txn[] = $row;
                    }
                }
                $response['error'] = FALSE;
                $response['message'] = "Data loaded successfully";
                $response['data'] = array("Cow"=>$cow_txn, "Buffalo"=> $buff_txn);
                http_response_code(200);
                echo json_encode($response);
            } else {
                $response['error'] = TRUE;
                $response['message'] = "No data found";
                http_response_code(200);
                echo json_encode($response);
            }
        } else {
            $response['error'] = TRUE;
            $response['message'] = "Please try again letter";
            http_response_code(401);
            echo json_encode($response);
        }
    }

    public function view_transaction(){
        $response = array();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $sid = $this->check_header_authentication_for_society();
            $txn_id = $this->input->post("transaction_id");
            $transaction = $this->transaction_model->get_transaction_by_id($txn_id);
            /*print_r($transaction);exit;*/
            if($transaction){
                $response['error'] = FALSE;
                $response['message'] = "Data loaded successfully";
                $response['data'] = (array) $transaction;
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

    function search_txn()
    {
        $response = array();
        if ($this->input->post()) {
            $sid = $this->check_header_authentication_for_society();
            $str = $this->input->post("search");
            if ($txn_list = $this->transaction_model->search_txn($sid, $str)) {
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

    /**
     * get society machines
     * Method: POST
     * Headers: Authorization: api_key
     * response type json
     */
    public function get_society_machine()
    {
        $http_response_code = 401;
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $society_id = $this->check_header_authentication_for_society();
            $machines = $this->machine_model->allocated_soc_machine($society_id);
            if (!empty($machines)) {
                foreach ($machines as $row) {
                    $data['machines'][] = array('machine_id' => $row->machine_id, 'id' => $row->id);
                }
            }
            $http_response_code = 200;
            $response['error'] = FALSE;
            $response['data'] = $data;
        } else {
            $response['error'] = TRUE;
            $response['message'] = "Invalid method";
        }
        http_response_code($http_response_code);
        echo json_encode($response);
    }

    /**
     * customer list by society
     * Method: POST
     * Headers: Authorization: api_key
     * response type json
     */
    public function customer_list()
    {
        $http_response_code = 401;
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $society = $this->check_header_authentication_for_society();
            $customers = $this->customer_model->get_society_customer($society);
            if (!empty($customers)) {
                foreach ($customers as $row) {
                    $data[] = (array)$row;
                }
                $http_response_code = 200;
                $response['error'] = FALSE;
                $response['data'] = $data;
            } else {
                $response['error'] = TRUE;
                $response['message'] = "No customers found";
            }
        } else {
            $response['error'] = TRUE;
            $response['message'] = "Invalid method";
        }
        http_response_code($http_response_code);
        echo json_encode($response);
    }

    /**
     * customer filter by search string
     * Method: POST
     * Headers: Authorization: api_key
     * Params:  search_key
     * response type json
     */
    public function customer_filter()
    {
        $http_response_code = 401;
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $society = $this->check_header_authentication_for_society();
            $string = $this->input->post("search");
            $customers = $this->customer_model->search_customer($string, $society);
            if (!empty($customers)) {
                $http_response_code = 200;
                $response['message'] = "Customer found";
                $response['data'] = $customers;
            } else {
                $response['error'] = TRUE;
                $response['message'] = "No customers found";
            }
        } else {
            $response['error'] = TRUE;
            $response['message'] = "Invalid method";
        }
        http_response_code($http_response_code);
        echo json_encode($response);
    }

    /**
     * customer import api
     * Method: POST
     * Headers: Authorization: api_key
     * Params:  customer_json, machine_id
     * response type json
     */
    public function import_customer_json()
    {
        $http_response_code = 401;
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $society_id = $this->check_header_authentication_for_society();
            $data = json_decode($this->input->post("customer_json"))->Customer;
            $machine_id = $this->input->post("machine_id");
            if (!empty($data)) {
                $i = 2;
                foreach ($data as $row) {
                    $col1 = $row->member_code;  // member code
                    $col2 = $row->name;  // customer name
                    $col3 = $row->mobile_number;  // customer mobile
                    $col4 = $row->aadhar;  // customer aadhar
                    $col5 = $row->type;  // customer type COW | BUFFALO
                    if ($col1 == "" || $col2 == "" || $col3 == "" || $col4 == "" || $col4 == "") {
                        $data_validate[] = array("Error" => "Please fill all the fileds", "Line" => $i);
                        $i++;
                        continue;
                    }
                    $cid = $this->customer_model->check_exist($col4, "adhar_no");
                    if ($cid === FALSE) {
                        $customer_data = array(
                            "customer_name" => $col2,
                            "mem_code" => $col1,
                            "mobile" => $col3,
                            "adhar_no" => $col4,
                            "type" => $col5,
                            "created_at" => date("Y-m-d"),
                        );
                        $this->customer_model->add_customer($customer_data, $machine_id, $society_id);
                        $http_response_code = 200;
                        $response['error'] = FALSE;
                        $response['message'] = "Customer import successfully.";
                    } else {
                        $data_validate = array();
                        $exist_cust = $this->customer_model->get_customer_api_id($cid);
                        // check blank fields
                        if ($col1 != "") {
                            $cust_data = array("mem_code" => $col1);
                            $this->customer_model->update_single($cust_data, $cid);
                            $http_response_code = 200;
                            $response['error'] = FALSE;
                            $response['message'] = "Customer member code updated successfully on line $i";
                        }

                        if ($exist_cust->customer_name == "" && $col2 != "") {
                            $cust_data = array("customer_name" => $col2);
                            $this->customer_model->update_single($cust_data, $cid);
                            $http_response_code = 200;
                            $response['error'] = FALSE;
                            $response['message'] = "Customer name updated successfully on line $i";
                        }

                        if ($exist_cust->mobile == "" && $col3 != "") {
                            $cust_data = array("mobile" => $col3);
                            $this->customer_model->update_single($cust_data, $cid);
                            $http_response_code = 200;
                            $response['error'] = FALSE;
                            $response['message'] = "Customer mobile updated successfully on line $i";
                        }
                        if ($this->customer_model->check_exist_customer_machine($cid, $machine_id, $society_id)) {
                            $data_validate = array("Error" => "Customer already exist", "Line" => $i);
                            $http_response_code = 200;
                            $response['exist'][] = $data_validate;
                            $response['error'] = TRUE;
                            $response['message'] = "Customer already exist";
                            $i++;
                            continue;
                        } else {
                            $cust_machine = array(
                                "cid" => $cid,
                                "machine_id" => $machine_id,
                                "society_id" => $society_id
                            );
                            $this->customer_model->insert_customer_machine($cust_machine);
                            $http_response_code = 200;
                            $response['error'] = FALSE;
                            $response['message'] = "Customer successfully imported";
                            $i++;
                            continue;
                        }
                    }
                }
            } else {
                $response['error'] = TRUE;
                $response['message'] = "Please try again letter";
            }
        } else {
            $response['error'] = TRUE;
            $response['message'] = "Invalid method";
        }
        http_response_code($http_response_code);
        echo json_encode($response);
    }
    /* society app webservice * End */
}
