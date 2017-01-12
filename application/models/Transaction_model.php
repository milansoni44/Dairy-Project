<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Transaction_model
 *
 * @author Milan Soni
 */
class Transaction_model extends CI_Model{
    //put your code here
    function __construct() {
        parent::__construct();
    }
    
    function get_society_id($machine_id = NULL){
        $machine_id = trim($machine_id,'"');
        $q = $this->db->query("SELECT m.machine_id, u.name, u.id AS society_id FROM `machines` m LEFT JOIN society_machine_map smm ON smm.machine_id = m.id LEFT JOIN users u ON u.id = smm.society_id WHERE m.machine_id = '$machine_id'");
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
    
    function get_dairy_id($machine_id = NULL){
        $machine_id = trim($machine_id,'"');
        $q = $this->db->query("SELECT m.machine_id, u.name, u.id AS dairy_id FROM `machines` m LEFT JOIN dairy_machine_map smm ON smm.machine_id = m.id LEFT JOIN users u ON u.id = smm.dairy_id WHERE m.machine_id = '$machine_id'");
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
    
    function get_machine_id($machine_id = NULL){
        $machine_id = trim($machine_id,'"');
        $q = $this->db->query("SELECT m.id as mid, m.machine_id, u.name, u.id AS dairy_id FROM `machines` m LEFT JOIN dairy_machine_map smm ON smm.machine_id = m.id LEFT JOIN users u ON u.id = smm.dairy_id WHERE m.machine_id = '$machine_id'");
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
    
    function insert_transaction($data = array()){
        if($this->db->insert("transactions", $data)){
            return TRUE;
        }
        return FALSE;
    }
    
    function get_transactions($date = NULL, $to_date = NULL){
        $this->db->select("t.*,s.name AS society_name,d.name AS dairy_name, c.customer_name as customer_name")
                ->from("transactions t")
                ->join("machines m","m.machine_id = t.deviceid","LEFT")
                ->join("society_machine_map smm","smm.machine_id = m.id","LEFT")
                ->join("users s","s.id = smm.society_id","LEFT")
                ->join("dairy_machine_map dmm","dmm.machine_id = m.id","LEFT")
                ->join("users d","d.id = dmm.dairy_id","LEFT")
                ->join("customers c","c.adhar_no = t.adhar");
        if(!$date){
            if($this->session->userdata("group") == "admin"){
                $q = $this->db->get();
            }else if($this->session->userdata("group") == "dairy"){
                $id = $this->session->userdata("id");
                $this->db->where("t.dairy_id",$id);
                $q = $this->db->get();
            }else{
                $id = $this->session->userdata("id");
                $this->db->where("t.society_id",$id);
                $q = $this->db->get();
            }
        }else{
            if($this->session->userdata("group") == "admin"){
//                $this->db->where("date", $date);
                $this->db->where('date BETWEEN "'. date('Y-m-d', strtotime($date)). '" and "'. date('Y-m-d', strtotime($to_date)).'"');
                $q = $this->db->get();
            }else if($this->session->userdata("group") == "dairy"){
                $id = $this->session->userdata("id");
//                $this->db->where("date", $date);
                $this->db->where('date BETWEEN "'. date('Y-m-d', strtotime($date)). '" and "'. date('Y-m-d', strtotime($to_date)).'"');
                $this->db->where("t.dairy_id",$id);
                $q = $this->db->get();
            }else{
                $id = $this->session->userdata("id");
//                $this->db->where("date", $date);
                $this->db->where('date BETWEEN "'. date('Y-m-d', strtotime($date)). '" and "'. date('Y-m-d', strtotime($to_date)).'"');
                $this->db->where("t.society_id",$id);
                $q = $this->db->get();
            }
        }
//        echo $this->db->last_query();
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }
    
    function get_monthly_transaction($month = NULL){
        //select `employee_id`,`date`,`checkin`,`checkout` from `attendence` where DATE_FORMAT(date, "%m-%Y") = "10-2012"
        $this->db->select("t.*,s.name AS society_name,d.name AS dairy_name, c.customer_name as customer_name")
                ->from("transactions t")
                ->join("machines m","m.machine_id = t.deviceid","LEFT")
                ->join("society_machine_map smm","smm.machine_id = m.id","LEFT")
                ->join("users s","s.id = smm.society_id","LEFT")
                ->join("dairy_machine_map dmm","dmm.machine_id = m.id","LEFT")
                ->join("users d","d.id = dmm.dairy_id","LEFT")
                ->join("customers c","c.adhar_no = t.adhar");
        if($this->session->userdata("group") == "admin"){
            $this->db->where("DATE_FORMAT(date,'%Y-%m')",$month);
            $q = $this->db->get();
        }else if($this->session->userdata("group") == "dairy"){
            $this->db->where("DATE_FORMAT(date,'%Y-%m')",$month);
            $id = $this->session->userdata("id");
            $this->db->where("t.dairy_id",$id);
            $q = $this->db->get();
        }else{
            $this->db->where("DATE_FORMAT(date,'%Y-%m')",$month);
            $id = $this->session->userdata("id");
            $this->db->where("t.society_id",$id);
            $q = $this->db->get();
        }
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }
    
    function get_customer_transaction($adhar = NULL){
        $this->db->select("t.*,s.name AS society_name,d.name AS dairy_name, c.customer_name as customer_name")
                ->from("transactions t")
                ->join("machines m","m.machine_id = t.deviceid","LEFT")
                ->join("society_machine_map smm","smm.machine_id = m.id","LEFT")
                ->join("users s","s.id = smm.society_id","LEFT")
                ->join("dairy_machine_map dmm","dmm.machine_id = m.id","LEFT")
                ->join("users d","d.id = dmm.dairy_id","LEFT")
                ->join("customers c","c.adhar_no = t.adhar");
        if($this->session->userdata("group") == "admin"){
            $this->db->where("t.adhar",$adhar);
            $q = $this->db->get();
        }else if($this->session->userdata("group") == "dairy"){
            $this->db->where("t.adhar",$adhar);
            $id = $this->session->userdata("id");
            $this->db->where("t.dairy_id",$id);
            $q = $this->db->get();
        }else{
            $this->db->where("t.adhar",$adhar);
            $id = $this->session->userdata("id");
            $this->db->where("t.society_id",$id);
            $q = $this->db->get();
        }
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }
    
    function import_txn($data = array()){
        if($this->db->insert_batch("transactions", $data)){
            return TRUE;
        }
        return FALSE;
    }
    
    function exist_machine($device = NULL){
        $q = $this->db->get_where("machines", array("machine_id"=>$device));
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            return $q->row()->id;
        }
        return FALSE;
    }
    
    function get_cid($adhar = NULL){
        $q = $this->db->get_where("customers", array("adhar_no"=>$adhar));
        if($q->num_rows() > 0){
            return $q->row()->id;
        }
        return FALSE;
    }
    
    function insert_single($data = array()){
        $this->db->insert("transactions", $data);
        return TRUE;
    }
}

/** application/Models/Transaction_model.php */