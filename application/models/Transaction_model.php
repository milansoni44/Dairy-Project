<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Transaction_model
 *
 * @author Milan Soni
 */
class Transaction_model extends CI_Model {

    //put your code here
    function __construct() {
        parent::__construct();
    }
    
    function get_societies(){
        $id = $this->session->userdata("id");
        $q = $this->db->query("SELECT id, name FROM users WHERE dairy_id = '$id'");
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }
    
    function get_society_txn($id = NULL){
        $q = $this->db->query("SELECT AVG(fat), AVG(clr), AVG(snf), SUM(weight), SUM(netamt) FROM transaction WHERE society_id = '$id'");
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }

    function get_society_id($machine_id = NULL) {
        $machine_id = trim($machine_id, '"');
        $q = $this->db->query("SELECT society_id FROM machines WHERE machine_id = '$machine_id'");
//        echo $this->db->last_query();exit;
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    function get_dairy_id($machine_id = NULL) {
        $machine_id = trim($machine_id, '"');
        $q = $this->db->query("SELECT dairy_id FROM machines WHERE machine_id = '$machine_id'");
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    function get_machine_id($machine_id = NULL) {
        $machine_id = trim($machine_id, '"');
        $q = $this->db->query("SELECT id as mid FROM machines WHERE machine_id = '$machine_id'");
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    function insert_transaction($data = array()) {
        if ($this->db->insert("transactions", $data)) {
            return TRUE;
        }
        return FALSE;
    }

    function get_transactions($date = NULL, $to_date = NULL) {
        $this->db->select("t.*,s.name AS society_name,d.name AS dairy_name, c.customer_name as customer_name")
                ->from("transactions t")
                ->join("machines m", "m.machine_id = t.deviceid", "LEFT")
                ->join("society_machine_map smm", "smm.machine_id = m.id", "LEFT")
                ->join("users s", "s.id = smm.society_id", "LEFT")
                ->join("dairy_machine_map dmm", "dmm.machine_id = m.id", "LEFT")
                ->join("users d", "d.id = dmm.dairy_id", "LEFT")
                ->join("customers c", "c.adhar_no = t.adhar");
        if (!$date) {
            if ($this->session->userdata("group") == "admin") {
                $q = $this->db->get();
            } else if ($this->session->userdata("group") == "dairy") {
                $id = $this->session->userdata("id");
                $this->db->where("t.dairy_id", $id);
                $q = $this->db->get();
            } else {
                $id = $this->session->userdata("id");
                $this->db->where("t.society_id", $id);
                $q = $this->db->get();
            }
        } else {
            if ($this->session->userdata("group") == "admin") {
//                $this->db->where("date", $date);
                $this->db->where('date BETWEEN "' . date('Y-m-d', strtotime($date)) . '" and "' . date('Y-m-d', strtotime($to_date)) . '"');
                $q = $this->db->get();
            } else if ($this->session->userdata("group") == "dairy") {
                $id = $this->session->userdata("id");
//                $this->db->where("date", $date);
                $this->db->where('date BETWEEN "' . date('Y-m-d', strtotime($date)) . '" and "' . date('Y-m-d', strtotime($to_date)) . '"');
                $this->db->where("t.dairy_id", $id);
                $q = $this->db->get();
            } else {
                $id = $this->session->userdata("id");
//                $this->db->where("date", $date);
                $this->db->where('date BETWEEN "' . date('Y-m-d', strtotime($date)) . '" and "' . date('Y-m-d', strtotime($to_date)) . '"');
                $this->db->where("t.society_id", $id);
                $q = $this->db->get();
            }
        }
//        echo $this->db->last_query();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }

    function get_monthly_transaction($month = NULL) {
        //select `employee_id`,`date`,`checkin`,`checkout` from `attendence` where DATE_FORMAT(date, "%m-%Y") = "10-2012"
        $this->db->select("t.*,s.name AS society_name,d.name AS dairy_name, c.customer_name as customer_name")
                ->from("transactions t")
                ->join("machines m", "m.machine_id = t.deviceid", "LEFT")
                ->join("society_machine_map smm", "smm.machine_id = m.id", "LEFT")
                ->join("users s", "s.id = smm.society_id", "LEFT")
                ->join("dairy_machine_map dmm", "dmm.machine_id = m.id", "LEFT")
                ->join("users d", "d.id = dmm.dairy_id", "LEFT")
                ->join("customers c", "c.adhar_no = t.adhar");
        if ($this->session->userdata("group") == "admin") {
            $this->db->where("DATE_FORMAT(date,'%Y-%m')", $month);
            $q = $this->db->get();
        } else if ($this->session->userdata("group") == "dairy") {
            $this->db->where("DATE_FORMAT(date,'%Y-%m')", $month);
            $id = $this->session->userdata("id");
            $this->db->where("t.dairy_id", $id);
            $q = $this->db->get();
        } else {
            $this->db->where("DATE_FORMAT(date,'%Y-%m')", $month);
            $id = $this->session->userdata("id");
            $this->db->where("t.society_id", $id);
            $q = $this->db->get();
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

    function get_customer_transaction($adhar = NULL) {
        $this->db->select("t.*,s.name AS society_name,d.name AS dairy_name, c.customer_name as customer_name, m.machine_id as machine_id")
                ->from("transactions t")
                ->join("machines m", "m.id = t.deviceid", "LEFT")
//                ->join("society_machine_map smm","smm.machine_id = m.id","LEFT")
                ->join("users s", "s.id = t.society_id", "LEFT")
//                ->join("dairy_machine_map dmm","dmm.machine_id = m.id","LEFT")
                ->join("users d", "d.id = t.dairy_id", "LEFT")
                ->join("customers c", "c.id = t.cid");
        if ($this->session->userdata("group") == "admin") {
            $this->db->where("t.adhar", $adhar);
            $q = $this->db->get();
        } else if ($this->session->userdata("group") == "dairy") {
            $this->db->where("c.adhar_no", $adhar);
            $id = $this->session->userdata("id");
            $this->db->where("t.dairy_id", $id);
            $q = $this->db->get();
        } else {
            $this->db->where("c.adhar_no", $adhar);
            $id = $this->session->userdata("id");
            $this->db->where("t.society_id", $id);
            $q = $this->db->get();
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

    function import_txn($data = array()) {
        if ($this->db->insert_batch("transactions", $data)) {
            return TRUE;
        }
        return FALSE;
    }

    function exist_machine($device = NULL) {
        $q = $this->db->get_where("machines", array("machine_id" => $device));
//        echo $this->db->last_query();exit;
        if ($q->num_rows() > 0) {
            return $q->row()->id;
        }
        return FALSE;
    }

    function get_cid($adhar = NULL) {
        $q = $this->db->get_where("customers", array("adhar_no" => $adhar));
        if ($q->num_rows() > 0) {
            return $q->row()->id;
        }
        return FALSE;
    }

    function insert_single($data = array()) {
        $this->db->insert("transactions", $data);
        return TRUE;
    }

    function get_txn_list($id = NULL) {
        $q = $this->db->query("SELECT t.fat, t.snf, t.rate, t.weight, t.type, t.clr, t.netamt, t.shift, t.date, t.memcode, m.machine_id, CONCAT_WS('-', c.customer_name,c.adhar_no) AS customer, d.name AS dairy_name, s.name AS soc_name FROM transactions t
                            LEFT JOIN machines m ON m.id = t.deviceid
                            LEFT JOIN customers c ON c.id = t.cid
                            LEFT JOIN users d ON d.id = t.dairy_id
                            LEFT JOIN users s ON s.id = t.society_id
                            WHERE s.id = '$id'");
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
    }
    
    function search_txn($id = NULL, $str = NULL){
        $q = $this->db->query("SELECT t.fat, t.snf, t.rate, t.weight, t.type, t.clr, t.netamt, t.shift, t.date, t.memcode, m.machine_id, CONCAT_WS('-', c.customer_name,c.adhar_no) AS customer, d.name AS dairy_name, s.name AS soc_name FROM transactions t
                            LEFT JOIN machines m ON m.id = t.deviceid
                            LEFT JOIN customers c ON c.id = t.cid
                            LEFT JOIN users d ON d.id = t.dairy_id
                            LEFT JOIN users s ON s.id = t.society_id
                            WHERE s.id = '$id' AND c.adhar_no LIKE '%$str%' OR customer_name LIKE '%$str%'");
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            return $q->result_array();
        }
        return FALSE;
    }
    
    function check_mapped_society_machine($machine_id = NULL, $soc_id){
        $q = $this->db->query("SELECT * FROM machines WHERE society_id = '$soc_id'");
        if($q->num_rows() > 0){
            return TRUE;
        }else{
            return FALSE;
        }
    }
	
	function get_weekly_transaction($cid = NULL, $sid = NULL)
	{
		$date_end = date('Y-m-d');
		$date_start = date('Y-m-d', strtotime('-7 days'));
		
		$q = $this->db->query("SELECT DATE_FORMAT(`t`.`date`, '%d-%M-%Y') AS `date`, `t`.`weight` AS `litre`, (SELECT CONCAT_WS('-',machine_name, machine_id) FROM machines WHERE `machines`.`id` = `t`.`deviceid`) AS machine,`t`.`type`, `s`.`name` AS `society_name`, `d`.`name` AS `dairy_name`, `t`.`fat`, t.clr, t.rate, t.netamt, `t`.`shift` FROM transactions t
								LEFT JOIN `users` s ON s.id = t.society_id
								LEFT JOIN `users` d ON d.id = t.dairy_id
								WHERE t.cid = '$cid'
								AND `t`.`date` BETWEEN '$date_start' AND '$date_end' ORDER BY `t`.`date` DESC");
		/* echo $this->db->last_query();exit; */
		if($q->num_rows() > 0)
		{
			return $q->result_array();
		}
		return FALSE;		
	}
	
	function get_weekly_buff_txn($cid = NULL)
	{
		$date_end = date('Y-m-d');
		$date_start = date('Y-m-d', strtotime('-7 days'));
		
		$q = $this->db->query("SELECT 
									`t`.`society_id`, 
									SUM(`t`.`weight`) AS `litre`, 
									(SELECT CONCAT_WS('-',machine_name, machine_id) 
										FROM machines `m`
										WHERE `m`.`id` = `t`.`deviceid`) AS machine,
									`t`.`type`,
									`s`.`name` AS `society_name`,
									`d`.`name` AS `dairy_name`, 
									AVG(`t`.`fat`) AS `fat`, 
									AVG(t.clr) AS `clr`, 
									AVG(`t`.`snf`) AS `snf`, 
									AVG(`t`.`rate`) AS `rate`, 
									SUM(`t`.`netamt`) AS `netamt`, 
									`t`.`shift` 
								FROM transactions t
                                LEFT JOIN `users` s ON s.id = t.society_id
                                LEFT JOIN `users` d ON d.id = t.dairy_id
                                WHERE t.cid = '$cid'
                                AND `t`.`date` 
                                BETWEEN '$date_start' AND '$date_end'
                                 AND `t`.`type` = 'B'
                                GROUP BY `t`.`society_id`, `t`.`type`");
		
		 /*echo $this->db->last_query();exit;*/
		if($q->num_rows() > 0)
		{
			return $q->result_array();
		}
		return FALSE;
	}

    function get_weekly_cow_txn($cid = NULL)
    {
        $date_end = date('Y-m-d');
        $date_start = date('Y-m-d', strtotime('-7 days'));

        $q = $this->db->query("SELECT `t`.`society_id`, SUM(`t`.`weight`) AS `litre`, (SELECT CONCAT_WS('-',machine_name, machine_id) FROM machines WHERE `machines`.`id` = `t`.`deviceid`) AS machine,`t`.`type`, `s`.`name` AS `society_name`, `d`.`name` AS `dairy_name`, AVG(`t`.`fat`) AS `fat`, AVG(t.clr) AS `clr`, AVG(`t`.`snf`) AS `snf` ,AVG(`t`.`rate`) AS `rate`, SUM(`t`.`netamt`) AS netamt, `t`.`shift` FROM transactions t
                                LEFT JOIN `users` s ON s.id = t.society_id
                                LEFT JOIN `users` d ON d.id = t.dairy_id
                                WHERE t.cid = '$cid'
                                AND `t`.`date` 
                                BETWEEN '$date_start' AND '$date_end'
                                 AND `t`.`type` = 'C'
                                GROUP BY `t`.`society_id`, `t`.`type`");

        /*echo $this->db->last_query();exit;*/
        if($q->num_rows() > 0)
        {
            return $q->result_array();
        }
        return FALSE;
    }
	
	function get_customRangeTxn($data = array())
	{
		$q = $this->db->query("SELECT DATE_FORMAT(`t`.`date`, '%d-%M-%Y') AS `date`, `t`.`weight` AS `litre`, (SELECT CONCAT_WS('-',machine_name, machine_id) FROM machines WHERE `machines`.`id` = `t`.`deviceid`) AS machine,`t`.`type`, `s`.`name` AS `society_name`, `d`.`name` AS `dairy_name`, `t`.`fat`, t.clr, t.rate, t.netamt, `t`.`shift` FROM transactions t
							LEFT JOIN users s ON s.id = t.society_id
							LEFT JOIN users d ON d.id = t.dairy_id
							WHERE t.type = '".$data['type']."'
							AND t.cid = '".$data['cid']."'
							AND t.date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."' AND t.society_id = '".$data['society']."' ORDER BY `t`.`date` DESC");
		// echo $this->db->last_query();exit;
		
		if($q->num_rows() > 0)
		{
			return $q->result_array();
		}
		return FALSE;
	}
}

/** application/Models/Transaction_model.php */