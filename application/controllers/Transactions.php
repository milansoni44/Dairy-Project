<?php

defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('date.timezone', 'Asia/Kolkata');
/**
 * Description of Transactions
 *
 * @author Milan Soni
 */
ini_set('precision', '15');

class Transactions extends MY_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->model("transaction_model");
        $this->load->model("customer_model");
        $this->load->model("favourite_report_model");
        $this->load->library("auth_lib");
        $this->load->library("Datatables");
        $this->load->library("session");
        $this->load->library("form_validation");
        $this->load->database();
        if(!$this->auth_lib->is_logged_in()){
            redirect("auth/login","refresh");
        }
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
        $this->load->view("common/header", $this->data);
        $this->load->view("transactions/index", $data);
        $this->load->view("common/footer");
    }

    function import_txn() {
        if ($this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "admin") {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        if ($this->input->post()) {
            $ext = pathinfo($_FILES['transaction']['name'], PATHINFO_EXTENSION);
            if($ext != "csv"){
                $this->session->set_flashdata("message", "Only CSV file is accepted");
                redirect("transactions/import_txn", "refresh");
            }
            $csv = $_FILES['transaction']['tmp_name'];
            if (($getfile = fopen($csv, "r")) !== FALSE) {
                $data = fgetcsv($getfile, 1000, ",");
//                echo "<pre>";
                $i = 0;
                while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
//                    print_r($data);exit;
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
                        $valid_society_machine = $this->transaction_model->check_mapped_society_machine($machine_id, $this->session->userdata("id"));
                        if($valid_society_machine === FALSE){
                            $this->session->set_flashdata("message", "Machine is not allocated to current society.");
                            redirect("transactions/import_txn", "refresh");
                        }
                        if ($data[7] == "") {
                            $this->session->set_flashdata("message", "Line:$i Adhar no required");
                            $i++;
                            continue;
                        }
                        if ($this->customer_model->check_exist_adhar($data[7]) === FALSE) {
                            $customer_data = array(
                                "adhar_no" => $data[7],
                            );
//                            echo "<pre>";
//                            print_r($customer_data);exit;
                            $cid = $this->customer_model->add_customer($customer_data, $machine_id, $society);
                            
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
                        if($this->customer_model->check_exist_customer_machine($cid, $machine_id) === FALSE){
                            $cust_machine = array(
                                "cid"=>$cid,
                                "machine_id"=>$machine_id,
                                "society_id"=>$society
                            );
                            $this->customer_model->insert_customer_machine($cust_machine);
                        }
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
//                        echo $data[0];exit;
                        if($data[0] == "CUST" && $data[1] == "FAT" && $data[2] == "CLR" && $data[3] == "SNF"){
                            $i++;
                            continue;
                        }else{
                            $this->session->set_flashdata("message", "Invalid transaction file.");
                            redirect("transactions/import_txn", "refresh");
                        }
                    }
                }
//                exit;
//                if(!empty($trans) && $this->transaction_model->import_txn($trans)){
                $this->session->set_flashdata("success", "Success");
                redirect("transactions/daily", "refresh");
            }
        } else {
            $this->load->view("common/header", $this->data);
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

    function daily()
	{
        if ($this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "admin") {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        // validation
        $this->form_validation->set_rules("date", "Date", "trim|required|callback_check_future");
        $this->form_validation->set_rules("to_date", "Date", "trim|required|callback_check_dates");
        if ( $this->form_validation->run() == TRUE )
		{
//            $data['transactions'] = $this->transaction_model->get_transactions($this->input->post("date"), $this->input->post("to_date"));
            $data['customers'] = $this->customer_model->get_customer();
            $data['favourite_report'] = $this->favourite_report_model->get_favourite_report();
            $this->load->view("common/header", $this->data);
            $this->load->view("transactions/daily", $data);
            $this->load->view("common/footer");
        }
		else
		{
            $data['errors'] = $this->form_validation->error_array();
            $data['favourite_report'] = $this->favourite_report_model->get_favourite_report();
            $data['customers'] = $this->customer_model->get_customer();
            $this->load->view("common/header", $this->data);
            $this->load->view("transactions/daily", $data);
            $this->load->view("common/footer");
        }
    }

    function get_daily_transaction() {
        $this->datatables->select("t.id, CONCAT(c.customer_name, '(', c.adhar_no, ')'),m.machine_id,t.fat,t.clr,t.snf,t.weight,t.rate,t.netamt,t.date")
                ->unset_column('t.id')
                ->from("transactions t")
                ->join("machines m", "m.id = t.deviceid", "LEFT")
                ->join("users s", "s.id = m.society_id", "LEFT")
                ->join("users d", "d.id = m.dairy_id", "LEFT")
                ->join("customers c", "c.id = t.cid", "LEFT")
                ->where("t.type", "C")
                ->where("m.status", 1)
                ->where("CURDATE() BETWEEN m.from_date AND m.to_date")
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
            $this->datatables->add_column('Action','<a data-toggle="modal" data-target="#view-modal" data-id="$1" id="getUser" href="#">View</a>', 't.id');
            echo $this->datatables->generate();
        }
    }

    function get_daily_Buff_transaction() {
		$date = date('Y-m-d');
        $this->datatables->select("t.id, CONCAT(c.customer_name, '(', c.adhar_no, ')'),m.machine_id,t.fat,t.clr,t.snf,t.weight,t.rate,t.netamt,t.date")
                ->unset_column('t.id')
                ->from("transactions t")
                ->join("machines m", "m.id = t.deviceid", "LEFT")
                ->join("users s", "s.id = m.society_id", "LEFT")
                ->join("users d", "d.id = m.dairy_id", "LEFT")
                ->join("customers c", "c.id = t.cid", "LEFT")
                ->where("t.type", "B")
                ->where("m.status", 1)
                ->where("CURDATE() BETWEEN m.from_date AND m.to_date")
				->where("t.date", $date);
                // ->where('t.date1 BETWEEN "' . date('Y-m-d') . '" AND "' . date('Y-m-d') . '"');
        /* if ($this->session->userdata("group") == "dairy") {
            $id = $this->session->userdata("id");
            $this->datatables->where("t.dairy_id", $id);
            echo $this->datatables->generate();
        } else { */
            $id = $this->session->userdata("id");
            $this->db->where("t.society_id", $id);
        $this->datatables->add_column('Action','<a data-toggle="modal" data-target="#view-modal" data-id="$1" id="getUser" href="#">View</a>', 't.id');
            echo $this->datatables->generate();
        /* } */
    }

    function get_daily_transaction_post($from = NULL, $to = NULL, $shift = NULL, $customer = NULL, $report_id = NULL) {
		
        $this->datatables->select("t.id,CONCAT(c.customer_name, '(', c.adhar_no, ')'),m.machine_id,t.fat,t.clr,t.snf,t.weight,t.rate,t.netamt,t.date")
                ->unset_column('t.id')
                ->from("transactions t")
                ->join("machines m", "m.id = t.deviceid", "LEFT")
                ->join("users s", "s.id = t.society_id", "LEFT")
                ->join("users d", "d.id = t.dairy_id", "LEFT")
                ->join("customers c", "c.id = t.cid", "LEFT")
                ->where("t.type", "C")
                ->where("m.status", 1)
                ->where("CURDATE() BETWEEN m.from_date AND m.to_date")
                ->where('date BETWEEN "' . date('Y-m-d', strtotime($from)) . '" and "' . date('Y-m-d', strtotime($to)) . '"');
        if ($customer != "") {
            $this->datatables->where("t.cid", $customer);
        }
        if ($shift != "All") {
            $this->datatables->where("t.shift", $shift);
        }
        $this->datatables->where("t.society_id", $this->session->userdata("id"));
        $this->datatables->add_column('Action','<a data-toggle="modal" data-target="#view-modal" data-id="$1" id="getUser" href="#">View</a>', 't.id');
        echo $this->datatables->generate();
    }

    function get_daily_buff_transaction_post($from = NULL, $to = NULL, $shift = NULL, $customer = NULL) {
        $this->datatables->select("t.id, CONCAT(' ',c.customer_name, '(', c.adhar_no,')'),m.machine_id,t.fat,t.clr,t.snf,t.weight,t.rate,t.netamt,t.date")
                ->unset_column('t.id')
                ->from("transactions t")
                ->join("machines m", "m.id = t.deviceid", "LEFT")
                ->join("users s", "s.id = t.society_id", "LEFT")
                ->join("users d", "d.id = t.dairy_id", "LEFT")
                ->join("customers c", "c.id = t.cid", "LEFT")
                ->where("t.type", "B")
                ->where("m.status", 1)
                ->where("CURDATE() BETWEEN m.from_date AND m.to_date")
                ->where('date BETWEEN "' . date('Y-m-d', strtotime($from)) . '" and "' . date('Y-m-d', strtotime($to)) . '"');
        if ($customer != "") {
            $this->datatables->where("t.cid", $customer);
        }
        if ($shift != "All") {
            $this->datatables->where("t.shift", $shift);
        }
        $this->datatables->where("t.society_id", $this->session->userdata("id"));
        $this->datatables->add_column('Action','<a data-toggle="modal" data-target="#view-modal" data-id="$1" id="getUser" href="#">View</a>', 't.id');
        echo $this->datatables->generate();
    }

    function daily_txn() {
//        $this->form_validation->set_rules("date", "Date", "trim|required|callback_check_future");
//        $this->form_validation->set_rules("to_date", "Date", "trim|required|callback_check_dates");

        if ($this->form_validation->run() == TRUE) {
            $data['txn_society'] = $this->transaction_model->get_society_txn($this->input->post("society"));
            $data['society'] = $this->transaction_model->get_societies();
            $this->load->view("common/header", $this->data);
            $this->load->view("transactions/dairy_txn", $data);
            $this->load->view("common/footer");
        } else {
            $data['society'] = $this->transaction_model->get_societies();
            $this->load->view("common/header", $this->data);
            $this->load->view("transactions/dairy_txn", $data);
            $this->load->view("common/footer");
        }
    }

    function dairy_txn_datatable($id = NULL, $date_range = NULL) {
        if($this->session->userdata("group") == "society"){
            $id = $this->session->userdata("id");
        }
        $this->datatables->select("s.name, ROUND(AVG(t.fat), 2) as fat, ROUND(AVG(t.clr), 2) as clr, ROUND(AVG(t.snf), 2) as snf, ROUND(AVG(t.weight), 2) as weight, ROUND(SUM(t.netamt), 2) as netamt")
                ->from("transactions t")
                ->join("users s", "s.id = t.society_id", "LEFT")
                ->join("users d", "d.id = t.dairy_id", "LEFT")
                ->join("machines m", "m.id = t.deviceid", "LEFT");
        if(!$id){
            $date = explode('|', $date_range);
            $this->db->where('t.date BETWEEN "'. date('Y-m-d', strtotime($date[0])). '" and "'. date('Y-m-d', strtotime($date[1])).'"');
            $this->datatables->group_by("t.society_id");
            $this->datatables->where("t.society_id", $id);
        }else{
            $this->datatables->where("t.society_id", $id);
        }
        $this->datatables->where("m.status", 1)
        ->where("CURDATE() BETWEEN m.from_date AND m.to_date");
        echo $this->datatables->generate();
    }

    function daily_admin() {
        // validation
        $this->form_validation->set_rules("date", "Date", "trim|required|callback_check_future");
        $this->form_validation->set_rules("to_date", "Date", "trim|required|callback_check_dates");

        if ($this->form_validation->run() == TRUE) {
            $this->load->view("common/header", $this->data);
            $this->load->view("transactions/daily_admin");
            $this->load->view("common/footer");
        } else {
            $this->load->view("common/header", $this->data);
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
        $this->datatables->where("CURDATE() BETWEEN m.from_date AND m.to_date");
        $this->datatables->group_by("t.dairy_id");
        echo $this->datatables->generate();
    }

    function monthly() {
        // validation
        $this->form_validation->set_rules("date", "Month", "trim|required");

        if ($this->form_validation->run() == TRUE) {
            $data['transactions'] = $this->transaction_model->get_monthly_transaction($this->input->post("date"));
            $this->load->view("common/header", $this->data);
            $this->load->view("transactions/monthly", $data);
            $this->load->view("common/footer");
        } else {
            $data['errors'] = $this->form_validation->error_array();
            $data['transactions'] = $this->transaction_model->get_monthly_transaction(date("Y-m"));
            $this->load->view("common/header", $this->data);
            $this->load->view("transactions/monthly", $data);
            $this->load->view("common/footer");
        }
    }

    function customer() {
        if ($this->input->post("submit")) {
//            echo "Hello";exit;
            $data['transactions'] = $this->transaction_model->get_customer_transaction($this->input->post("customer"));
            $data['customers'] = $this->customer_model->get_customer_txn();
            $this->load->view("common/header", $this->data);
            $this->load->view("transactions/customer", $data);
            $this->load->view("common/footer");
        } else {
            $data['customers'] = $this->customer_model->get_customer_txn();
            $this->load->view("common/header", $this->data);
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

    public function edit($id = NULL)
    {
        $transaction_info = $this->transaction_model->get_transaction_by_id($id);
        print "<pre>";
        print_r($transaction_info);
    }

    public function view()
    {
        $tid = $this->input->post("id");
        $transaction_info = $this->transaction_model->get_transaction_by_id($tid);
        /*print "<pre>";
        print_r($transaction_info);exit;*/

        echo $html = '<div class="row">
                            <div class="col-md-12">
                            <table class="table table-striped table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Machine</th>
                                        <td>'.$transaction_info->machine_id.'</td>
                                        <th>Society</th>
                                        <td>'.$transaction_info->society_name.'</td>
                                    </tr>
                                    <tr>
                                        <th>Dairy</th>
                                        <td>'.$transaction_info->dairy_name.'</td>
                                        <th>Sample ID</th>
                                        <td>'.$transaction_info->sampleid.'</td>
                                    </tr>
                                    <tr>
                                        <th>Soccode</th>
                                        <td>'.$transaction_info->soccode.'</td>
                                        <th>Fat</th>
                                        <td>'.$transaction_info->fat.'</td>
                                    </tr>
                                    <tr>
                                        <th>SNF</th>
                                        <td>'.$transaction_info->snf.'</td>
                                        <th>Rate</th>
                                        <td>'.$transaction_info->rate.'</td>
                                    </tr>
                                    <tr>
                                        <th>Weight</th>
                                        <td>'.$transaction_info->weight.'</td>
                                        <th>Type</th>
                                        <td>'.$transaction_info->type.'</td>
                                    </tr>
                                    <tr>
                                        <th>CLR</th>
                                        <td>'.$transaction_info->clr.'</td>
                                        <th>IS MANUAL</th>
                                        <td>'.$transaction_info->ismanual.'</td>
                                    </tr>
                                    <tr>
                                        <th>Net Amount</th>
                                        <td>'.$transaction_info->netamt.'</td>
                                        <th>Shift</th>
                                        <td>'.$transaction_info->shift.'</td>
                                    </tr>
                                    <tr>
                                        <th>Date</th>
                                        <td>'.$transaction_info->date.'</td>
                                        <th>Customer Name</th>
                                        <td>'.$transaction_info->customer_name.'</td>
                                    </tr>
                                </tbody>
                            </table>';
    }

    public function export_cow($start_date = NULL, $end_date = NULL, $shift = 'All', $customer = 'All')
    {
        if ($this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "admin") {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        $id = $this->session->userdata("id");
        $start_date = !($start_date) ? date('Y-m-d') : $start_date;
        $end_date = !($end_date) ? date('Y-m-d') : $end_date;
        $shift = !($shift == "All") ? $shift : NULL;
        $customer = !($customer == "All") ? $customer : NULL;
//        $start_date = !($start_date) ? '2017-01-01' : $start_date;
//        $end_date = !($end_date) ? date('Y-m-d') : $end_date;
//        $shift = !($shift == "All") ? $shift : NULL;
//        $customer = !($customer == "All") ? $customer : NULL;

        $data = array(
            "id"=>$id,
            "start_date"=>$start_date,
            "end_date"=>$end_date,
            "shift"=>$shift,
            "cid"=>$customer
        );
        $cow_date = $this->transaction_model->export_cow($data);
//        echo "<pre>";
//        print_r($cow_date);exit;
        $file_name = "cow_transaction";
        if(!empty($cow_date))
        {
            /*print "<pre>";
            print_r($transactions_cow);exit;*/
            foreach($cow_date as $cow){
                $data_final[] = array_values($cow);
            }

            $fp = fopen('php://output', 'w');

            if ($fp && $cow_date) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $file_name . '.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');
                fputcsv($fp, array("customer", "adhar", "deviceid", "fat", "clr", "snf", "litre", "rate/litre", "amount", "shift", "date"));

                foreach($data_final as $rr){
                    fputcsv($fp, $rr);
                }
                die;
            }
        }else{
            $this->session->set_flashdata("message", "No Cow Data Found");
            redirect("/", "refresh");
        }
    }

    public function export_buff($start_date = NULL, $end_date = NULL, $shift = 'All', $customer = 'All')
    {
        if ($this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "admin") {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        $id = $this->session->userdata("id");
        $start_date = !($start_date) ? date('Y-m-d') : $start_date;
        $end_date = !($end_date) ? date('Y-m-d') : $end_date;
        $shift = !($shift == "All") ? $shift : NULL;
        $customer = !($customer == "All") ? $customer : NULL;
//        $start_date = !($start_date) ? '2017-01-01' : $start_date;
//        $end_date = !($end_date) ? date('Y-m-d') : $end_date;
//        $shift = !($shift == "All") ? $shift : NULL;
//        $customer = !($customer == "All") ? $customer : NULL;

        $data = array(
            "id"=>$id,
            "start_date"=>$start_date,
            "end_date"=>$end_date,
            "shift"=>$shift,
            "cid"=>$customer
        );
        $buff_date = $this->transaction_model->export_buff($data);
//        echo "<pre>";
//        print_r($cow_date);exit;
        $file_name = "cow_transaction";
        if(!empty($buff_date))
        {
            /*print "<pre>";
            print_r($transactions_cow);exit;*/
            foreach($buff_date as $cow){
                $data_final[] = array_values($cow);
            }

            $fp = fopen('php://output', 'w');

            if ($fp && $buff_date) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $file_name . '.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');
                fputcsv($fp, array("customer", "adhar", "deviceid", "fat", "clr", "snf", "litre", "rate/litre", "amount", "shift", "date"));

                foreach($data_final as $rr){
                    fputcsv($fp, $rr);
                }
                die;
            }
        }else{
            $this->session->set_flashdata("message", "No Buffalo Data Found");
            redirect("/", "refresh");
        }
    }
}

/** application/controllers/Transactions.php */
