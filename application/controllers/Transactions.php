<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Transactions
 *
 * @author Milan Soni
 */
ini_set('precision', '15');

class Transactions extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->model("transaction_model");
        $this->load->model("customer_model");
        $this->load->library("auth_lib");
        $this->load->library("Datatables");
        $this->load->library("session");
        $this->load->library("form_validation");
        $this->load->database();
    }

    function index() {
        if ($this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "society") {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
        $data['transaction'] = $this->transaction_model->get_transactions();
//        echo "<pre>";
//        print_r($data['transaction']);exit;
        $this->load->view("common/header", $data);
        $this->load->view("transactions/index", $data);
        $this->load->view("common/footer");
    }

    function import_txn() {
        if ($this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "admin") {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        if ($this->input->post()) {
            $csv = $_FILES['transaction']['tmp_name'];
            if (($getfile = fopen($csv, "r")) !== FALSE) {
                $data = fgetcsv($getfile, 1000, ",");
//                echo "<pre>";
                $i = 0;
                while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
//                    print_r($data);
                    if ($i > 0) {
//                        // check_exist machine id
                        $stat = $this->transaction_model->exist_machine($data[13]);
                        if ($stat === FALSE) {
                            $this->session->set_flashdata("message", "Device ID does not exist in the system.");
                            redirect("/", "refresh");
                        }
                        $society = $this->transaction_model->get_society_id($data[13])->society_id;
                        $dairy = $this->transaction_model->get_dairy_id($data[13])->dairy_id;
                        $machine_id = $this->transaction_model->get_machine_id($data[13])->mid;
                        if ($data[7] == "") {
                            $this->session->set_flashdata("message", "Line:$i Adhar no required");
                            $i++;
                            continue;
                        }
                        if ($this->customer_model->check_exist_adhar($data[7]) === FALSE) {
//                            echo "Hello";exit;
//                            $this->session->set_flashdata("message","Line:$i Adhar no not exist");
//                            $i++;
//                            continue;
                            $customer_data = array(
                                "adhar_no" => $data[7],
//                                "society_id"=>$society,
//                                "dairy_id"=>$dairy,
//                                "machine_id"=>$machine_id,
                            );
//                            echo "<pre>";
//                            print_r($customer_data);exit;
                            $this->customer_model->add_customer($customer_data, $data[13], $society);
                            $cid = $this->db->insert_id();
//                            $adhar = $this->transaction_model->get_adhar($cid);
                            $transaction_single = array(
                                "dairy_id" => $dairy,
                                "society_id" => $society,
                                "deviceid" => $stat,
                                "sampleid" => $data[12],
                                "ismanual" => $data[9],
                                "type" => $data[8],
                                "cid" => $cid,
                                "netamt" => $data[6],
                                "rate" => $data[5],
                                "weight" => $data[4],
                                "snf" => $data[3],
                                "clr" => $data[2],
                                "fat" => $data[1],
                                "memcode" => $data[0],
                                "date" => date("Y-d-m", strtotime($data[14])),
                                "shift" => $data[15],
                                "dockno" => $data[10],
                                "soccode" => $data[11]
                            );
//                            print_r($transaction_single);exit;
                            $this->transaction_model->insert_single($transaction_single);
                            $i++;
                            continue;
                        }
                        $cid = $this->transaction_model->get_cid($data[7]);
                        $t_date = str_replace('/', '-', $data[14]);
                        $trans = array(
                            "dairy_id" => $dairy,
                            "society_id" => $society,
                            "deviceid" => $stat,
                            "sampleid" => $data[12],
                            "ismanual" => $data[9],
                            "type" => $data[8],
                            "cid" => $cid,
                            "netamt" => $data[6],
                            "rate" => $data[5],
                            "weight" => $data[4],
                            "snf" => $data[3],
                            "clr" => $data[2],
                            "fat" => $data[1],
                            "memcode" => $data[0],
                            "date" => date("Y-m-d", strtotime($t_date)),
                            "shift" => $data[15],
                            "dockno" => $data[10],
                            "soccode" => $data[11]
                        );
//                        echo "<pre>";
//                        print_r($trans);exit;
                        $this->transaction_model->insert_single($trans);
                    } else {
                        $i++;
                        continue;
                    }
                }
//                exit;
//                if(!empty($trans) && $this->transaction_model->import_txn($trans)){
                $this->session->set_flashdata("success", "Success");
                redirect("transactions/daily", "refresh");
            }
        } else {
            $this->load->view("common/header");
            $this->load->view("transactions/import");
            $this->load->view("common/footer");
        }
    }

    function test_json() {
//        echo "<pre>";
//        print_r($_FILES);exit;
        $json_file = file_get_contents($_FILES['json']['tmp_name']);
        $json = json_decode($json_file);
//        print_r($json);
        foreach ($json->transaction as $row) {
            $machine_id = $row->deviceid;
            $soc_id = $this->transaction_model->get_society_id($machine_id)->society_id;
            $dairy_id = $this->transaction_model->get_dairy_id($machine_id)->dairy_id;

            $trans_arr = array(
                "machine_id" => $row->deviceid,
                "society_id" => $soc_id,
                "dairy_id" => $dairy_id,
                "sample_id" => $row->sampleid,
                "soccode" => $row->soccode,
                "dockno" => $row->dockno,
                "fat" => $row->fat,
                "snf" => $row->snf,
                "rate" => $row->rate,
                "weight" => $row->weight,
                "totalamt" => $row->totalamt,
                "type" => $row->type,
                "clr" => $row->clr,
                "dumptime" => $row->dumptime,
                "ismanual" => $row->ismanual,
                "netamt" => $row->netamt,
                "shift" => $row->shift,
                "date" => $row->date,
                "mem_code" => $row->memcode,
                "adhar" => $row->adhar,
            );
            $this->transaction_model->insert_transaction($trans_arr);
            continue;
        }
//        $this->session->set_flashdata("success","Transaction uploaded successfully.");
//        redirect("/","refresh");
        exit;
    }

    function test_text() {
//        echo "<pre>";
//        print_r($_FILES);exit;
        $json_file = fopen($_FILES['json']['tmp_name'], "r") or die("Unable to open file!");
        $data = fread($json_file, filesize($_FILES['json']['tmp_name']));
        $data1 = explode(",", $data);
        $trans_array = array();
        if (!empty($data1)) {
            foreach ($data1 as $row) {
                $inner_data = explode(":", $row);
                $trans_array[str_replace('"', '', $inner_data[0])] = trim($inner_data[1], '"');
                if (strpos($inner_data[0], 'deviceid') !== false) {
                    $trans_array["society_id"] = $this->transaction_model->get_society_id($inner_data[1])->society_id;
                    $trans_array["dairy_id"] = $this->transaction_model->get_dairy_id($inner_data[1])->dairy_id;
                }
            }
        }
//        echo "<pre>";
//        print_r($trans_array);exit;
        $this->transaction_model->insert_transaction($trans_array);
        fclose($json_file);
        exit;
    }

    function daily() {
        // validation
        $this->form_validation->set_rules("date", "Date", "trim|required|callback_check_future");
        $this->form_validation->set_rules("to_date", "Date", "trim|required|callback_check_dates");
        if ($this->form_validation->run() == TRUE) {
//            $data['transactions'] = $this->transaction_model->get_transactions($this->input->post("date"), $this->input->post("to_date"));
            $data['customers'] = $this->customer_model->get_customer();
            $this->load->view("common/header");
            $this->load->view("transactions/daily", $data);
            $this->load->view("common/footer");
        } else {
            $data['errors'] = $this->form_validation->error_array();
//            $data['transactions'] = $this->transaction_model->get_transactions(date("Y-m-d"), date("Y-m-d"));
            $data['customers'] = $this->customer_model->get_customer();
            $this->load->view("common/header");
            $this->load->view("transactions/daily", $data);
            $this->load->view("common/footer");
        }
    }

    function get_daily_transaction() {
        $this->datatables->select("c.customer_name as customer_name,t.fat,t.clr,t.snf,t.weight,t.rate,t.netamt,t.date")
                ->from("transactions t")
                ->join("machines m", "m.machine_id = t.deviceid", "LEFT")
                ->join("users s", "s.id = m.society_id", "LEFT")
                ->join("users d", "d.id = m.dairy_id", "LEFT")
                ->join("customers c", "c.id = t.cid", "LEFT")
                ->where("t.type", "C")
                ->where("t.date", date("Y-m-d"));
        if ($this->session->userdata("group") == "admin") {
            echo $this->datatables->generate();
        } else if ($this->session->userdata("group") == "dairy") {
            $id = $this->session->userdata("id");
            $this->datatables->where("t.dairy_id", $id);
            echo $this->datatables->generate();
        } else {
            $id = $this->session->userdata("id");
            $this->db->where("t.society_id", $id);
            echo $this->datatables->generate();
        }
    }

    function get_daily_Buff_transaction() {
        $this->datatables->select("c.customer_name as customer_name,t.fat,t.clr,t.snf,t.weight,t.rate,t.netamt,t.date")
                ->from("transactions t")
                ->join("machines m", "m.machine_id = t.deviceid", "LEFT")
                ->join("users s", "s.id = m.society_id", "LEFT")
                ->join("users d", "d.id = m.dairy_id", "LEFT")
                ->join("customers c", "c.id = t.cid", "LEFT")
                ->where("t.type", "B")
                ->where("t.date", date("Y-m-d"));
        if ($this->session->userdata("group") == "admin") {
            echo $this->datatables->generate();
        } else if ($this->session->userdata("group") == "dairy") {
            $id = $this->session->userdata("id");
            $this->datatables->where("t.dairy_id", $id);
            echo $this->datatables->generate();
        } else {
            $id = $this->session->userdata("id");
            $this->db->where("t.society_id", $id);
            echo $this->datatables->generate();
        }
    }

    function get_daily_transaction_post($from = NULL, $to = NULL, $shift = NULL, $customer = NULL) {
        $this->datatables->select("c.customer_name as customer_name,t.fat,t.clr,t.snf,t.weight,t.rate,t.netamt,t.date")
                ->from("transactions t")
                ->join("machines m", "m.machine_id = t.deviceid", "LEFT")
                ->join("users s", "s.id = t.society_id", "LEFT")
                ->join("users d", "d.id = t.dairy_id", "LEFT")
                ->join("customers c", "c.id = t.cid", "LEFT")
                ->where("t.type", "C")
                ->where('date BETWEEN "' . date('Y-m-d', strtotime($from)) . '" and "' . date('Y-m-d', strtotime($to)) . '"');
        if ($customer != "") {
            $this->datatables->where("t.cid", $customer);
        }
        if ($shift != "All") {
            $this->datatables->where("t.shift", $shift);
        }
        echo $this->datatables->generate();
    }

    function get_daily_buff_transaction_post($from = NULL, $to = NULL, $shift = NULL, $customer = NULL) {
        $this->datatables->select("c.customer_name as customer_name,t.fat,t.clr,t.snf,t.weight,t.rate,t.netamt,t.date")
                ->from("transactions t")
                ->join("machines m", "m.machine_id = t.deviceid", "LEFT")
//        ->join("society_machine_map smm","smm.machine_id = m.id","LEFT")
                ->join("users s", "s.id = t.society_id", "LEFT")
//        ->join("dairy_machine_map dmm","dmm.machine_id = m.id","LEFT")
                ->join("users d", "d.id = t.dairy_id", "LEFT")
                ->join("customers c", "c.id = t.cid", "LEFT")
                ->where("t.type", "B")
                ->where('date BETWEEN "' . date('Y-m-d', strtotime($from)) . '" and "' . date('Y-m-d', strtotime($to)) . '"');
        if ($customer != "") {
            $this->datatables->where("t.cid", $customer);
        }
        if ($shift != "All") {
            $this->datatables->where("t.shift", $shift);
        }
        echo $this->datatables->generate();
    }

    function daily_txn() {
        $this->form_validation->set_rules("date", "Date", "trim|required|callback_check_future");
        $this->form_validation->set_rules("to_date", "Date", "trim|required|callback_check_dates");

        if ($this->form_validation->run() == TRUE) {
            $this->load->view("common/header");
            $this->load->view("transactions/dairy_txn");
            $this->load->view("common/footer");
        } else {
            $this->load->view("common/header");
            $this->load->view("transactions/dairy_txn");
            $this->load->view("common/footer");
        }
    }

    function dairy_txn_datatable() {
        $id = $this->session->userdata("id");
        $this->datatables->select("s.name, ROUND(AVG(t.fat), 2) as fat, ROUND(AVG(t.clr), 2) as clr, ROUND(AVG(t.snf), 2) as snf, ROUND(AVG(t.weight), 2) as weight, ROUND(SUM(t.netamt), 2) as netamt")
                ->from("transactions t")
//            ->join("machines m","m.machine_id = t.deviceid","LEFT")
//            ->join("society_machine_map smm","smm.machine_id = m.id","LEFT")
                ->join("users s", "s.id = t.society_id", "LEFT")
//            ->join("dairy_machine_map dmm","dmm.machine_id = m.id","LEFT")
                ->join("users d", "d.id = t.dairy_id", "LEFT")
                ->where("t.dairy_id", $id);
        $this->datatables->group_by("t.society_id");
        echo $this->datatables->generate();
    }

    function daily_admin() {
        // validation
        $this->form_validation->set_rules("date", "Date", "trim|required|callback_check_future");
        $this->form_validation->set_rules("to_date", "Date", "trim|required|callback_check_dates");

        if ($this->form_validation->run() == TRUE) {
            $this->load->view("common/header");
            $this->load->view("transactions/daily_admin");
            $this->load->view("common/footer");
        } else {
            $this->load->view("common/header");
            $this->load->view("transactions/daily_admin");
            $this->load->view("common/footer");
        }
    }

    function dairy_admin_txn_datatable($from = NULL, $to = NULL) {
        $id = $this->session->userdata("id");
        $this->datatables->select("d.name, ROUND(AVG(t.fat), 2) as fat, ROUND(AVG(t.clr), 2) as clr, ROUND(AVG(t.snf), 2) as snf, ROUND(AVG(t.weight), 2) as weight, ROUND(AVG(t.rate), 2) as rate, ROUND(SUM(t.netamt), 2) as netamt, t.date")
                ->from("transactions t")
                ->join("machines m", "m.machine_id = t.deviceid", "LEFT")
                ->join("society_machine_map smm", "smm.machine_id = m.id", "LEFT")
                ->join("users s", "s.id = smm.society_id", "LEFT")
                ->join("dairy_machine_map dmm", "dmm.machine_id = m.id", "LEFT")
                ->join("users d", "d.id = dmm.dairy_id", "LEFT");
        if ($from && $to) {
            $this->datatables->where('t.date BETWEEN "' . date('Y-m-d', strtotime($from)) . '" and "' . date('Y-m-d', strtotime($to)) . '"');
        } else {
            $this->datatables->where("t.date", date("Y-m-d"));
        }
        $this->datatables->group_by("t.dairy_id");
        echo $this->datatables->generate();
    }

    function monthly() {
        // validation
        $this->form_validation->set_rules("date", "Month", "trim|required");

        if ($this->form_validation->run() == TRUE) {
            $data['transactions'] = $this->transaction_model->get_monthly_transaction($this->input->post("date"));
            $this->load->view("common/header");
            $this->load->view("transactions/monthly", $data);
            $this->load->view("common/footer");
        } else {
            $data['errors'] = $this->form_validation->error_array();
            $data['transactions'] = $this->transaction_model->get_monthly_transaction(date("Y-m"));
            $this->load->view("common/header");
            $this->load->view("transactions/monthly", $data);
            $this->load->view("common/footer");
        }
    }

    function customer() {
        if ($this->input->post("submit")) {
//            echo "Hello";exit;
            $data['transactions'] = $this->transaction_model->get_customer_transaction($this->input->post("customer"));
            $data['customers'] = $this->customer_model->get_customer_txn();
            $this->load->view("common/header");
            $this->load->view("transactions/customer", $data);
            $this->load->view("common/footer");
        } else {
            $data['customers'] = $this->customer_model->get_customer_txn();
            $this->load->view("common/header");
            $this->load->view("transactions/customer", $data);
            $this->load->view("common/footer");
        }
    }

    function check_future() {
        if ($this->input->post("date")) {
            $cur_date = date("Y-m-d");
            $mydate = $this->input->post("date");
            if ($mydate > $cur_date) {
                $this->form_validation->set_message('check_future', "'$mydate' is larger than '$cur_date'");
                return FALSE;
            }
            return TRUE;
        }
    }

    function check_dates() {
        $from = $this->input->post("date");
        $to = $this->input->post("to_date");
        if ($from > $to) {
            $this->form_validation->set_message("check_dates", "From date greater than to date");
            return FALSE;
        }
        return TRUE;
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
                $response['error'] = FALSE;
                $response['message'] = "No data found";
                http_response_code(200);
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
                $response['error'] = FALSE;
                $response['message'] = "No data found";
                http_response_code(200);
                echo json_encode($response);
            }
        }else{
            $response['error'] = TRUE;
            $response['message'] = "Please try again letter";
            http_response_code(400);
            echo json_encode($response);
        }
    }

}

/** application/controllers/Transactions.php */
