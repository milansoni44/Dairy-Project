<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Transactions
 *
 * @author Milan Soni
 */
class Transactions extends CI_Controller{
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
    
    function index(){
        $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
        $data['transaction'] = $this->transaction_model->get_transactions();
//        echo "<pre>";
//        print_r($data['transaction']);exit;
        $this->load->view("common/header", $data);
        $this->load->view("transactions/index", $data);
        $this->load->view("common/footer");
    }
    
    function test_json(){
//        echo "<pre>";
//        print_r($_FILES);exit;
        $json_file = file_get_contents($_FILES['json']['tmp_name']);
        $json = json_decode($json_file);
//        print_r($json);
        foreach($json->transaction as $row){
            $machine_id = $row->deviceid;
            $soc_id = $this->transaction_model->get_society_id($machine_id)->society_id;
            $dairy_id = $this->transaction_model->get_dairy_id($machine_id)->dairy_id;
            
            $trans_arr = array(
                "machine_id"=>$row->deviceid,
                "society_id"=>$soc_id,
                "dairy_id"=>$dairy_id,
                "sample_id"=>$row->sampleid,
                "soccode"=>$row->soccode,
                "dockno"=>$row->dockno,
                "fat"=>$row->fat,
                "snf"=>$row->snf,
                "rate"=>$row->rate,
                "weight"=>$row->weight,
                "totalamt"=>$row->totalamt,
                "type"=>$row->type,
                "clr"=>$row->clr,
                "dumptime"=>$row->dumptime,
                "ismanual"=>$row->ismanual,
                "netamt"=>$row->netamt,
                "shift"=>$row->shift,
                "date"=>$row->date,
                "mem_code"=>$row->memcode,
                "adhar"=>$row->adhar,
            );
            $this->transaction_model->insert_transaction($trans_arr);
            continue;
        }
//        $this->session->set_flashdata("success","Transaction uploaded successfully.");
//        redirect("/","refresh");
        exit;
    }
    
    function test_text(){
//        echo "<pre>";
//        print_r($_FILES);exit;
        $json_file = fopen($_FILES['json']['tmp_name'],"r") or die("Unable to open file!");
        $data = fread($json_file, filesize($_FILES['json']['tmp_name']));
        $data1 = explode(",", $data);
        $trans_array = array();
        if(!empty($data1)){
            foreach($data1 as $row){
                $inner_data = explode(":", $row);
                $trans_array[str_replace('"', '', $inner_data[0])] = trim($inner_data[1],'"');
                if(strpos($inner_data[0], 'deviceid') !== false){
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
    
    /*function daily(){
        // validation
        $this->form_validation->set_rules("date","Date","trim|required|callback_check_future");
        $this->form_validation->set_rules("to_date","Date","trim|required|callback_check_dates");
        if($this->form_validation->run() == TRUE){
            $data['transactions'] = $this->transaction_model->get_transactions($this->input->post("date"), $this->input->post("to_date"));
            $data['customers'] = $this->customer_model->get_customer();
            $this->load->view("common/header");
            $this->load->view("transactions/daily", $data);
            $this->load->view("common/footer");
        }else{
            $data['errors'] = $this->form_validation->error_array();
            $data['transactions'] = $this->transaction_model->get_transactions(date("Y-m-d"), date("Y-m-d"));
            $data['customers'] = $this->customer_model->get_customer();
            $this->load->view("common/header");
            $this->load->view("transactions/daily", $data);
            $this->load->view("common/footer");
        }
    }*/
    
    function daily(){
        $data['customers'] = $this->customer_model->get_customer();
        $this->load->view("common/header");
        $this->load->view("transactions/daily", $data);
        $this->load->view("common/footer");
    }
    
    function get_daily_transaction(){
        $this->datatables->select("c.customer_name as customer_name,t.type,t.fat,t.clr,t.snf,t.weight,t.rate,t.netamt,t.date")
            ->from("transactions t")
            ->join("machines m","m.machine_id = t.deviceid","LEFT")
            ->join("society_machine_map smm","smm.machine_id = m.id","LEFT")
            ->join("users s","s.id = smm.society_id","LEFT")
            ->join("dairy_machine_map dmm","dmm.machine_id = m.id","LEFT")
            ->join("users d","d.id = dmm.dairy_id","LEFT")
            ->join("customers c","c.adhar_no = t.adhar","LEFT")
            ->where("t.date", date("Y-m-d"));
        if($this->session->userdata("group") == "admin"){
            echo $this->datatables->generate();
        }else if($this->session->userdata("group") == "dairy"){
            $id = $this->session->userdata("id");
            $this->datatables->where("t.dairy_id",$id);
            echo $this->datatables->generate();
        }else{
            $id = $this->session->userdata("id");
            $this->db->where("t.society_id",$id);
            echo $this->datatables->generate();
        }
    }
    
    function get_daily_transaction_post($from = NULL, $to = NULL, $type = NULL, $customer = NULL){
        $this->datatables->select("c.customer_name as customer_name,t.type,t.fat,t.clr,t.snf,t.weight,t.rate,t.netamt,t.date")
        ->from("transactions t")
        ->join("machines m","m.machine_id = t.deviceid","LEFT")
        ->join("society_machine_map smm","smm.machine_id = m.id","LEFT")
        ->join("users s","s.id = smm.society_id","LEFT")
        ->join("dairy_machine_map dmm","dmm.machine_id = m.id","LEFT")
        ->join("users d","d.id = dmm.dairy_id","LEFT")
        ->join("customers c","c.adhar_no = t.adhar","LEFT")
        ->where('date BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');
        if($type != ""){
            $this->datatables->where("t.type", $type);
        }
        if($customer != ""){
            $this->datatables->where("t.adhar", $customer);
        }
        echo $this->datatables->generate();
    }
    
    function dairy_txn(){
        $this->load->view("common/header");
        $this->load->view("transactions/dairy_txn");
        $this->load->view("common/footer");
    }
    
    function dairy_txn_datatable(){
        $id = $this->session->userdata("id");
        $this->datatables->select("s.name, ROUND(AVG(t.fat), 2) as fat, ROUND(AVG(t.clr), 2) as clr, ROUND(AVG(t.snf), 2) as snf, ROUND(AVG(t.weight), 2) as weight, ROUND(AVG(t.rate), 2) as rate, ROUND(SUM(t.netamt), 2) as netamt, t.date")
            ->from("transactions t")
            ->join("machines m","m.machine_id = t.deviceid","LEFT")
            ->join("society_machine_map smm","smm.machine_id = m.id","LEFT")
            ->join("users s","s.id = smm.society_id","LEFT")
            ->join("dairy_machine_map dmm","dmm.machine_id = m.id","LEFT")
            ->join("users d","d.id = dmm.dairy_id","LEFT")
            ->where("t.dairy_id", $id);
            $this->datatables->group_by("t.society_id");
            echo $this->datatables->generate();
    }
    
    function daily_admin(){
        $this->load->view("common/header");
        $this->load->view("transactions/dairy_admin");
        $this->load->view("common/footer");
    }
    
    function dairy_admin_txn_datatable(){
        
    }
    
    function monthly(){
        // validation
        $this->form_validation->set_rules("date","Month","trim|required");
        
        if($this->form_validation->run() == TRUE){
            $data['transactions'] = $this->transaction_model->get_monthly_transaction($this->input->post("date"));
            $this->load->view("common/header");
            $this->load->view("transactions/monthly", $data);
            $this->load->view("common/footer");
        }else{
            $data['errors'] = $this->form_validation->error_array();
            $data['transactions'] = $this->transaction_model->get_monthly_transaction(date("Y-m"));
            $this->load->view("common/header");
            $this->load->view("transactions/monthly", $data);
            $this->load->view("common/footer");
        }
    }
    
    function customer(){
        if($this->input->post("submit")){
            $data['transactions'] = $this->transaction_model->get_customer_transaction($this->input->post("customer"));
            $data['customers'] = $this->customer_model->get_customer();
            $this->load->view("common/header");
            $this->load->view("transactions/customer", $data);
            $this->load->view("common/footer");
        }else{
            $data['customers'] = $this->customer_model->get_customer();
            $this->load->view("common/header");
            $this->load->view("transactions/customer", $data);
            $this->load->view("common/footer");
        }
    }
    
    function check_future(){
        if($this->input->post("date")){
            $cur_date = date("Y-m-d");
            $mydate = $this->input->post("date");
            if($mydate > $cur_date){
                $this->form_validation->set_message('check_future', "'$mydate' is larger than '$cur_date'");
                return FALSE;
            }
            return TRUE;
        }
    }
    
    function check_dates(){
        $from = $this->input->post("date");
        $to = $this->input->post("to_date");
        if($from > $to){
            $this->form_validation->set_message("check_dates","From date greater than to date");
            return FALSE;
        }
        return TRUE;
    }
    
    function demo(){
        $this->load->view("common/header");
        $this->load->view("transactions/demo");
        $this->load->view("common/footer");
    }
    
    function datatable(){
        $this->datatables->select("v_name_lan1,v_url_lan2,v_url_lan3")
            ->from("video");
        
        echo $this->datatables->generate();
    }
}
