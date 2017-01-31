<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Customer_model
 *
 * @author Milan Soni
 */
class Customer_model extends CI_Model {

    //put your code here
    function __construct() {
        parent::__construct();
    }

    function get_customer()
	{
        if ($this->session->userdata("group") == "admin") {
            $q = $this->db->query("SELECT * FROM customers c
LEFT JOIN customer_machine cs ON cs.cid = c.id");
        } else if ($this->session->userdata("group") == "society") {
            $id = $this->session->userdata("id");
            $q = $this->db->query("SELECT c.*, us.name FROM customers c
LEFT JOIN customer_machine cs ON cs.cid = c.id LEFT JOIN users us ON us.id = cs.society_id WHERE cs.society_id = '$id'");
        } else if ($this->session->userdata("group") == "dairy") {
            $id = $this->session->userdata("id");
            $q = $this->db->query("SELECT c.*, us.name FROM customers c
LEFT JOIN customer_machine cs ON cs.cid = c.id
LEFT JOIN users us ON us.id = cs.society_id
WHERE cs.society_id IN (SELECT GROUP_CONCAT(s.id) AS sid FROM users d LEFT JOIN users s ON s.dairy_id = d.id WHERE d.id = '$id')");
        }
//        echo $this->db->last_query();exit;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }
    
    function get_customer_by_machine($machine = NULL){
        $q = $this->db->query("SELECT c.* FROM customers c
                                LEFT JOIN customer_machine cm ON cm.cid = c.id
                                WHERE cm.machine_id = '$machine'");
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }

    function get_customer_txn() {
        if ($this->session->userdata("group") == "admin") {
            $q = $this->db->query("SELECT DISTINCT(c.id),c.*, s.name, m.machine_id FROM `transactions` t
LEFT JOIN users s ON s.id = t.society_id
LEFT JOIN users d ON d.id = t.dairy_id
LEFT JOIN machines m ON m.id = t.deviceid
LEFT JOIN customers c ON c.id = t.cid");
        } else if ($this->session->userdata("group") == "society") {
            $id = $this->session->userdata("id");
            $q = $this->db->query("SELECT DISTINCT(c.id),c.*, s.name, m.machine_id FROM `transactions` t
LEFT JOIN users s ON s.id = t.society_id
LEFT JOIN users d ON d.id = t.dairy_id
LEFT JOIN machines m ON m.id = t.deviceid
LEFT JOIN customers c ON c.id = t.cid WHERE t.society_id = '$id'");
        } else if ($this->session->userdata("group") == "dairy") {
            $id = $this->session->userdata("id");
            $q = $this->db->query("SELECT DISTINCT(c.id),c.*, s.name, m.machine_id FROM `transactions` t
LEFT JOIN users s ON s.id = t.society_id
LEFT JOIN users d ON d.id = t.dairy_id
LEFT JOIN machines m ON m.id = t.deviceid
LEFT JOIN customers c ON c.id = t.cid WHERE t.dairy_id = '$id'");
        }
//        echo $this->db->last_query();exit;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }

    function add_customer($data = array(), $machine = NULL, $society = NULL) {
        if ($this->db->insert("customers", $data)) {
            $id = $this->db->insert_id();
            if (!$society) {
                $customer_soc = array(
                    "cid" => $id,
                    "machine_id" => $machine,
                    "society_id" => $this->session->userdata("id")
                );
            } else {
                $customer_soc = array(
                    "cid" => $id,
                    "machine_id" => $machine,
                    "society_id" => $society
                );
            }
            $this->db->insert("customer_machine", $customer_soc);
            return $id;
        }
        return FALSE;
    }

    function edit_customer($data = array(), $id = NULL) {
        $this->db->where("id", $id);
        if ($this->db->update("customers", $data)) {
            return TRUE;
        }
        return FALSE;
    }

    function get_customer_by_id($id = NULL) {
        $q = $this->db->query("SELECT * FROM customers WHERE id = '$id'");
//        echo $this->db->last_query();exit;
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    function get_society_customer($soc_id = NULL) {
//        $q = $this->db->query("SELECT customers.*, users.name FROM customers LEFT JOIN users ON users.id = customers.society_id WHERE users.id = '$soc_id'");
        $q = $this->db->query("SELECT * FROM customers c
                                LEFT JOIN customer_machine cm ON cm.cid = c.id
                                LEFT JOIN users u ON u.id = cm.cid
                                WHERE cm.society_id = '$soc_id'");
//        echo $this->db->last_query();exit;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }

    function check_exist_adhar($adhar = NULL) {
        $q = $this->db->get_where("customers", array("adhar_no" => $adhar));
        if ($q->num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }
    
    function check_exist_customer_machine($cid = NULL, $machine = NULL){
        $id = $this->session->userdata("id");
        $q = $this->db->query("SELECT * FROM customer_machine WHERE machine_id = '$machine' AND cid = '$cid' AND society_id = '$id'");
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            return TRUE;
        }
        return FALSE;
    }
    
    function insert_customer_machine($data = array()){
        if($this->db->insert("customer_machine", $data)){
            return TRUE;
        }
        return FALSE;
    }

    function check_exist($col = NULL, $col_name = NULL, $id = NULL) {
        $soc_id = $this->session->userdata("id");
        $q = $this->db->query("SELECT * FROM customers c WHERE c.$col_name = '$col'");
//        echo $this->db->last_query();exit;
        if ($q->num_rows() > 0) {
            return $q->row()->id;
        }else{
            return FALSE;
        }
    }

    /* function batch_insert_customer($data = array()){
      if(!empty($data)){
      $this->db->insert("customers", $data);
      }
      return TRUE;
      } */

    function batch_insert_tmp_customer($data = array()) {
        
    }

    function update_expiry($col = NULL) {
        $this->db->where("mem_code", $col);
        $this->db->where("expiry", '0000-00-00');
        $this->db->where("society_id", $this->session->userdata("id"));
        if ($this->db->update("customers", array("expiry" => date("Y-m-d")))) {
//            echo $this->db->last_query();exit;
            return TRUE;
        }
        return FALSE;
    }

    function get_tmp_data() {
        $q = $this->db->get_where("tmp_customers", array("society_id" => $this->session->userdata("id")));
//        echo $this->db->last_query();
        if ($q->num_rows() > 0) {
            return $q->num_rows();
        }
        return FALSE;
    }

    function get_tmpData() {
        $q = $this->db->get_where("tmp_customers", array("society_id" => $this->session->userdata("id")));
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }

    function get_tmpCustomer_by_id($id = NULL) {
        $q = $this->db->get_where("tmp_customers", array("id" => $id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    function edit_Correctcustomer($data = array(), $id = NULL) {
        if ($this->db->insert("customers", $data)) {
            $this->db->where("id", $id);
            $this->db->delete("tmp_customers");
            return TRUE;
        }
        return FALSE;
    }
	
	function get_customer_society($cid = NULL)
	{
		$q = $this->db->query("SELECT cm.society_id AS society_id, s.name AS society_name FROM customers c
								LEFT JOIN customer_machine cm ON cm.cid = c.id
								LEFT JOIN users s ON s.id = cm.society_id
								WHERE c.id = '$cid'
								GROUP BY cm.society_id");
		if($q->num_rows() > 0)
		{
			return $q->result_array();
		}
		return FALSE;
	}
}

/** application/Models/Customer_model.php */