<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Customer_model
 *
 * @author Milan Soni
 */
class Customer_model extends CI_Model{
    //put your code here
    function __construct() {
        parent::__construct();
    }
    
    function get_customer(){
        if($this->session->userdata("group") == "admin"){
            $q = $this->db->query("SELECT c.*,u.name FROM customers c LEFT JOIN users u ON u.id = c.society_id");
        }else if($this->session->userdata("group") == "society"){
            $id = $this->session->userdata("id");
            $q = $this->db->query("SELECT c.*,u.name FROM customers c LEFT JOIN users u ON u.id = c.society_id WHERE c.society_id = '$id'");
        }else if($this->session->userdata("group") == "dairy"){
            $id = $this->session->userdata("id");
            $q = $this->db->query("SELECT c.*,u.name FROM customers c LEFT JOIN users u ON u.id = c.society_id LEFT JOIN user_groups ug ON ug.user_id = c.society_id LEFT JOIN groups g ON g.id = ug.group_id WHERE g.name = 'society' AND u.id = (SELECT users.id FROM users WHERE users.dairy_id = '$id')");
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
    
    function add_customer($data = array()){
        if($this->db->insert("customers",$data)){
            return TRUE;
        }
        return FALSE;
    }
    
    function edit_customer($data = array(), $id = NULL ){
        $this->db->where("id", $id);
        if($this->db->update("customers", $data)){
            return TRUE;
        }
        return FALSE;
    }
    
    function get_customer_by_id($id = NULL){
        $q = $this->db->query("SELECT * FROM customers WHERE id = '$id'");
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
    
    function get_society_customer($soc_id = NULL){
        $q = $this->db->query("SELECT customers.*, users.name FROM customers JOIN users ON users.id = customers.society_id WHERE society_id = '$soc_id'");
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }
    
    function check_exist_adhar($adhar = NULL){
        $q = $this->db->get_where("customers", array("adhar_no"=>$adhar));
        if($q->num_rows() > 0){
            return TRUE;
        }
        return FALSE;
    }
    
    function check_exist($col = NULL, $col_name = NULL, $id = NULL){
        // change as per expiry '0000-00-00' if not then member not exist
//        if($col_name == "mem_code"){
//            $q = $this->db->query("SELECT * FROM customers WHERE $col_name = '$col' AND expiry = '0000-00-00'");
//            echo $this->db->last_query();exit;
//        }else{
//            $q = $this->db->query("SELECT * FROM customers WHERE $col_name = '$col' AND expiry = '0000-00-00'");
//        }
//        echo $this->db->last_query();
//        echo "<br>";
        if(!$id){
            $soc_id = $this->session->userdata("id");
            $q = $this->db->query("SELECT * FROM customers WHERE $col_name = '$col' AND expiry = '0000-00-00' AND society_id = $soc_id");
        }else{
            $soc_id = $this->session->userdata("id");
            $q = $this->db->query("SELECT * FROM customers WHERE $col_name = '$col' AND expiry = '0000-00-00' AND id NOT IN('$id') AND society_id = $soc_id");
        }
//        echo $this->db->last_query();
        if($q->num_rows() > 0){
            return FALSE;
        }
        return TRUE;
    }
    
    function batch_insert_customer($data = array()){
        if(!empty($data)){
           $this->db->insert("customers", $data);
        }
        return TRUE;
    }
    
    function batch_insert_tmp_customer($data = array()){
        
    }
    
    function update_expiry($col = NULL){
        $this->db->where("mem_code", $col);
        $this->db->where("expiry",'0000-00-00');
        $this->db->where("society_id", $this->session->userdata("id"));
        if($this->db->update("customers", array("expiry"=> date("Y-m-d")))){
//            echo $this->db->last_query();exit;
            return TRUE;
        }
        return FALSE;
    }
    
    function get_tmp_data(){
        $q = $this->db->get_where("tmp_customers", array("society_id"=> $this->session->userdata("id")));
//        echo $this->db->last_query();
        if($q->num_rows() > 0){
            return $q->num_rows();
        }
        return FALSE;
    }
    
    function get_tmpData(){
        $q = $this->db->get_where("tmp_customers", array("society_id"=> $this->session->userdata("id")));
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }
    
    function get_tmpCustomer_by_id($id = NULL){
        $q = $this->db->get_where("tmp_customers", array("id"=> $id));
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
    
    function edit_Correctcustomer($data = array(), $id = NULL){
        if($this->db->insert("customers", $data)){
            $this->db->where("id", $id);
            $this->db->delete("tmp_customers");
            return TRUE;
        }
        return FALSE;
    }
}

/** application/Models/Customer_model.php */