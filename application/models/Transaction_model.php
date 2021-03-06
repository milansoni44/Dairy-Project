<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Transaction_model
 *
 * @author Milan Soni
 */
class Transaction_model extends CI_Model {

    //put your code here
    function __construct()
	{
		parent::__construct();
    }
    
    function get_societies()
	{
        $id = $this->session->userdata("id");
        $q = $this->db->query("SELECT id, name FROM users WHERE dairy_id = '$id'");
        if($q->num_rows() > 0)
		{
            foreach($q->result() as $row)
			{
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

    function get_transaction_by_id($id = NULL)
    {
        $q = $this->db->query("SELECT t.*,m.machine_id, s.name  AS society_name, d.name AS dairy_name, c.customer_name FROM transactions t
                                LEFT JOIN machines m ON m.id = t.deviceid
                                LEFT JOIN users s ON s.id = t.society_id
                                LEFT JOIN users d ON d.id = t.dairy_id
                                LEFT JOIN customers c ON c.id = t.cid WHERE t.id = '$id'");
        return ($q->num_rows() > 0) ? $q->row() : FALSE;
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
        /*echo $this->db->last_query();exit;*/
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
        $q = $this->db->query("SELECT t.id, t.fat, t.snf, t.rate, t.weight AS litre, t.type, t.clr, t.netamt, t.shift, t.date, t.memcode, m.machine_id, CONCAT_WS('-', c.customer_name,c.adhar_no) AS customer, d.name AS dairy_name, s.name AS soc_name FROM transactions t
                            LEFT JOIN machines m ON m.id = t.deviceid
                            LEFT JOIN customers c ON c.id = t.cid
                            LEFT JOIN users d ON d.id = t.dairy_id
                            LEFT JOIN users s ON s.id = t.society_id
                            WHERE s.id = '$id' AND `t`.`date` = CURDATE()");
        /*echo $this->db->last_query();exit;*/
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

    function get_cow_soc_weekly_transaction($sid = NULL, $date_start = NULL, $date_end = NULL)
    {
        $date_end = (isset($date_end)) ? $date_end : date('Y-m-d');
        $date_start = (isset($date_start)) ? $date_start : date('Y-m-d', strtotime('-16 days')); // TODO neeed change to 7 days

        $q = $this->db->query("SELECT 
                                ROUND(SUM(`t`.`weight`), 2) AS `litre`, 
                                (SELECT CONCAT_WS('-',machine_name, machine_id) FROM machines WHERE `machines`.`id` = `t`.`deviceid`) AS machine,`t`.`type`, `s`.`name` AS `society_name`, `d`.`name` AS `dairy_name`, 
                                ROUND(AVG(`t`.`fat`), 2) AS `fat`, 
                                ROUND(AVG(t.clr), 2) AS `clr`, 
                                ROUND(AVG(t.rate), 2) AS `rate`, 
                                ROUND(SUM(t.netamt), 2) AS `netamt`, `t`.`shift`, 
                                ROUND(AVG(`t`.`snf`), 2) AS `snf` FROM transactions t
								LEFT JOIN `users` s ON s.id = t.society_id
								LEFT JOIN `users` d ON d.id = t.dairy_id
								WHERE `t`.`society_id` = '$sid' AND `t`.`type` = 'C' AND `t`.`date` BETWEEN '$date_start' AND '$date_end' GROUP BY `t`.`shift` ORDER BY `t`.`date` DESC");
         /*echo $this->db->last_query();exit;*/
        if($q->num_rows() > 0)
        {
            return $q->result_array();
        }
        return FALSE;
    }

    function get_buff_soc_weekly_transaction($sid = NULL, $date_start = NULL, $date_end = NULL)
    {
        $date_end = (isset($date_end)) ? $date_end : date('Y-m-d');
        $date_start = (isset($date_start)) ? $date_start : date('Y-m-d', strtotime('-16 days')); // TODO neeed change to 7 days

        $q = $this->db->query("SELECT  
									ROUND(SUM(`t`.`weight`), 2) AS `litre`,
									`t`.`type`,
									`s`.`name` AS `society_name`,
									`d`.`name` AS `dairy_name`, 
									ROUND(AVG(`t`.`fat`), 2) AS `fat`, 
									ROUND(AVG(t.clr), 2) AS `clr`, 
									ROUND(AVG(`t`.`snf`), 2) AS `snf`, 
									ROUND(AVG(`t`.`rate`), 2) AS `rate`, 
									ROUND(SUM(`t`.`netamt`), 2) AS `netamt`, 
									`t`.`shift`, 
									`t`.`snf` 
								FROM transactions t
                                LEFT JOIN `users` s ON s.id = t.society_id
                                LEFT JOIN `users` d ON d.id = t.dairy_id
                                WHERE `t`.`society_id` = '$sid'
                                AND `t`.`type` = 'B'
                                AND (`t`.`date` BETWEEN '$date_start' AND '$date_end') GROUP BY `t`.`shift`");

        /*echo $this->db->last_query();exit;*/
        if($q->num_rows() > 0)
        {
            return $q->result_array();
        }
        return FALSE;
    }
	
	function get_weekly_transaction($cid = NULL, $sid = NULL)
	{
		$date_end = date('Y-m-d');
		$date_start = date('Y-m-d', strtotime('-7 days'));
		
		$q = $this->db->query("SELECT DATE_FORMAT(`t`.`date`, '%d-%M-%Y') AS `date`, `t`.`weight` AS `litre`, (SELECT CONCAT_WS('-',machine_name, machine_id) FROM machines WHERE `machines`.`id` = `t`.`deviceid`) AS machine,`t`.`type`, `s`.`name` AS `society_name`, `d`.`name` AS `dairy_name`, `t`.`fat`, t.clr, t.rate, t.netamt, `t`.`shift`, `t`.`snf` FROM transactions t
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
									ROUND(SUM(`t`.`weight`), 2) AS `litre`, 
									(SELECT CONCAT_WS('-',machine_name, machine_id) 
										FROM machines `m`
										WHERE `m`.`id` = `t`.`deviceid`) AS machine,
									`t`.`type`,
									`s`.`name` AS `society_name`,
									`d`.`name` AS `dairy_name`, 
									ROUND(AVG(`t`.`fat`), 2) AS `fat`, 
									ROUND(AVG(t.clr), 2) AS `clr`, 
									ROUND(AVG(`t`.`snf`), 2) AS `snf`, 
									ROUND(AVG(`t`.`rate`), 2) AS `rate`, 
									ROUND(SUM(`t`.`netamt`), 2) AS `netamt`, 
									`t`.`shift`, 
									`t`.`snf` 
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

        $q = $this->db->query("SELECT 
									`t`.`society_id`, 
									ROUND(SUM(`t`.`weight`), 2) AS `litre`, 
									(SELECT CONCAT_WS('-',machine_name, machine_id) 
										FROM machines `m`
										WHERE `m`.`id` = `t`.`deviceid`) AS machine,
									`t`.`type`,
									`s`.`name` AS `society_name`,
									`d`.`name` AS `dairy_name`, 
									ROUND(AVG(`t`.`fat`), 2) AS `fat`, 
									ROUND(AVG(t.clr), 2) AS `clr`, 
									ROUND(AVG(`t`.`snf`), 2) AS `snf`, 
									ROUND(AVG(`t`.`rate`), 2) AS `rate`, 
									ROUND(SUM(`t`.`netamt`), 2) AS `netamt`, 
									`t`.`shift`, 
									`t`.`snf`  
								FROM transactions t
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

    function get_monthly_buff_txn($cid = NULL)
    {
        $month = date('Y-m', strtotime("-1 month"));
        $month_year = explode('-', $month);

        $q = $this->db->query("SELECT 
                                    `t`.`society_id`, 
                                    ROUND(SUM(`t`.`weight`), 2) AS `litre`, 
                                    (SELECT CONCAT_WS('-',machine_name, machine_id) 
                                        FROM machines `m`
                                        WHERE `m`.`id` = `t`.`deviceid`) AS machine,
                                    `t`.`type`,
                                    `s`.`name` AS `society_name`,
                                    `d`.`name` AS `dairy_name`, 
                                    ROUND(AVG(`t`.`fat`), 2) AS `fat`, 
                                    ROUND(AVG(t.clr), 2) AS `clr`, 
                                    ROUND(AVG(`t`.`snf`), 2) AS `snf`, 
                                    ROUND(AVG(`t`.`rate`), 2) AS `rate`, 
                                    ROUND(SUM(`t`.`netamt`), 2) AS `netamt`, 
                                    `t`.`shift`, 
									`t`.`snf`  
                                FROM transactions t
                                LEFT JOIN `users` s ON s.id = t.society_id
                                LEFT JOIN `users` d ON d.id = t.dairy_id
                                WHERE t.cid = '$cid'
                                AND MONTH(`t`.`date`) = '".$month_year[1]."'
                                AND YEAR(`t`.`date`) = '".$month_year[0]."'
                                AND `t`.`type` = 'B'
                                GROUP BY `t`.`society_id`, `t`.`type`");
        /*echo $this->db->last_query();exit;*/
        if($q->num_rows() > 0)
        {
            return $q->result_array();
        }
        return FALSE;
    }

    function get_monthly_cow_txn($cid = NULL)
    {
        $month = date('Y-m', strtotime("-1 month"));
        $month_year = explode('-', $month);
        /*$year = date('Y');*/
        $q = $this->db->query("SELECT 
                                    `t`.`society_id`, 
                                    ROUND(SUM(`t`.`weight`), 2) AS `litre`, 
                                    (SELECT CONCAT_WS('-',machine_name, machine_id) 
                                        FROM machines `m`
                                        WHERE `m`.`id` = `t`.`deviceid`) AS machine,
                                    `t`.`type`,
                                    `s`.`name` AS `society_name`,
                                    `d`.`name` AS `dairy_name`, 
                                    ROUND(AVG(`t`.`fat`), 2) AS `fat`, 
                                    ROUND(AVG(t.clr), 2) AS `clr`, 
                                    ROUND(AVG(`t`.`snf`), 2) AS `snf`, 
                                    ROUND(AVG(`t`.`rate`), 2) AS `rate`, 
                                    ROUND(SUM(`t`.`netamt`), 2) AS `netamt`, 
                                    `t`.`shift`, 
									`t`.`snf`  
                                FROM transactions t
                                LEFT JOIN `users` s ON s.id = t.society_id
                                LEFT JOIN `users` d ON d.id = t.dairy_id
                                WHERE t.cid = '$cid'
                                AND MONTH(`t`.`date`) = '".$month_year[0]."'
                                AND YEAR(`t`.`date`) = '".$month_year[1]."'
                                AND `t`.`type` = 'C'
                                GROUP BY `t`.`society_id`, `t`.`type`");
        /*echo $this->db->last_query();exit;*/
        if($q->num_rows() > 0)
        {
            return $q->result_array();
        }
        return FALSE;
    }

    function get_yearly_buff_txn($cid = NULL)
    {
        $year = date("Y",strtotime("-1 year"));
        $q = $this->db->query("SELECT 
                                    `t`.`society_id`, 
                                    ROUND(SUM(`t`.`weight`), 2) AS `litre`, 
                                    (SELECT CONCAT_WS('-',machine_name, machine_id) 
                                        FROM machines `m`
                                        WHERE `m`.`id` = `t`.`deviceid`) AS machine,
                                    `t`.`type`,
                                    `s`.`name` AS `society_name`,
                                    `d`.`name` AS `dairy_name`, 
                                    ROUND(AVG(`t`.`fat`), 2) AS `fat`, 
                                    ROUND(AVG(t.clr), 2) AS `clr`, 
                                    ROUND(AVG(`t`.`snf`), 2) AS `snf`, 
                                    ROUND(AVG(`t`.`rate`), 2) AS `rate`, 
                                    ROUND(SUM(`t`.`netamt`), 2) AS `netamt`, 
                                    `t`.`shift`, 
									`t`.`snf`  
                                FROM transactions t
                                LEFT JOIN `users` s ON s.id = t.society_id
                                LEFT JOIN `users` d ON d.id = t.dairy_id
                                WHERE t.cid = '$cid'
                                AND YEAR(`t`.`date`) = '$year'
                                AND `t`.`type` = 'B'
                                GROUP BY `t`.`society_id`, `t`.`type`");
        /*echo $this->db->last_query();exit;*/
        if($q->num_rows() > 0)
        {
            return $q->result_array();
        }
        return FALSE;
    }

    function get_yearly_cow_txn($cid = NULL)
    {
        $year = date("Y",strtotime("-1 year"));
        $q = $this->db->query("SELECT 
                                    `t`.`society_id`, 
                                    ROUND(SUM(`t`.`weight`), 2) AS `litre`, 
                                    (SELECT CONCAT_WS('-',machine_name, machine_id) 
                                        FROM machines `m`
                                        WHERE `m`.`id` = `t`.`deviceid`) AS machine,
                                    `t`.`type`,
                                    `s`.`name` AS `society_name`,
                                    `d`.`name` AS `dairy_name`, 
                                    ROUND(AVG(`t`.`fat`), 2) AS `fat`, 
                                    ROUND(AVG(t.clr), 2) AS `clr`, 
                                    ROUND(AVG(`t`.`snf`), 2) AS `snf`, 
                                    ROUND(AVG(`t`.`rate`), 2) AS `rate`, 
                                    ROUND(SUM(`t`.`netamt`), 2) AS `netamt`, 
                                    `t`.`shift`, 
									`t`.`snf`  
                                FROM transactions t
                                LEFT JOIN `users` s ON s.id = t.society_id
                                LEFT JOIN `users` d ON d.id = t.dairy_id
                                WHERE t.cid = '$cid'
                                AND YEAR(`t`.`date`) = '$year'
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
		$q = $this->db->query("SELECT DATE_FORMAT(`t`.`date`, '%d-%M-%Y') AS `date`, `t`.`weight` AS `litre`, (SELECT CONCAT_WS('-',machine_name, machine_id) FROM machines WHERE `machines`.`id` = `t`.`deviceid`) AS machine,`t`.`type`, `s`.`name` AS `society_name`, `d`.`name` AS `dairy_name`, `t`.`fat`, t.clr, t.rate, t.netamt, `t`.`shift`, `t`.`snf` FROM transactions t
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
	
	public function transaction_report($data)
	{
		$start = isset($data['start']) ? $data['start'] : 0;
		$limit = isset($data['limit']) ? $data['limit'] : 10;
		$result = $this->db->query(" SELECT 
							`c`.`customer_name` as `customer_name`, 
							`t`.`fat`, 
							`t`.`clr`, 
							`t`.`snf`, 
							`t`.`weight`, 
							`t`.`rate`, 
							`t`.`netamt`, 
							`t`.`date` 
						FROM `transactions` `t` 
						LEFT JOIN `machines` `m` ON `m`.`machine_id` = `t`.`deviceid` 
						LEFT JOIN `users` `s` ON `s`.`id` = `t`.`society_id` 
						LEFT JOIN `users` `d` ON `d`.`id` = `t`.`dairy_id` 
						LEFT JOIN `customers` `c` ON `c`.`id` = `t`.`cid` 
						WHERE `t`.`type` = 'C' 
						AND (`date` BETWEEN '".$data['start_date']."' AND '".$data['to_date']."')
						LIMIT ".$start.", ".$limit);
		return $result ? $result->result_array() : FALSE;
	}
	
	public function transaction_report_count($data)
	{
		$result = $this->db->query(" SELECT 
							COUNT(*) AS `total`
						FROM `transactions` `t` 
						LEFT JOIN `machines` `m` ON `m`.`machine_id` = `t`.`deviceid` 
						LEFT JOIN `users` `s` ON `s`.`id` = `t`.`society_id` 
						LEFT JOIN `users` `d` ON `d`.`id` = `t`.`dairy_id` 
						LEFT JOIN `customers` `c` ON `c`.`id` = `t`.`cid` 
						WHERE `t`.`type` = 'C' 
						AND (`date` BETWEEN '".$data['start_date']."' AND '".$data['to_date']."')");
		return $result ? $result->row('total') : FALSE;
	}

	function custom_transactions_cow($data = array())
    {
        $q = $this->db->select("CONCAT_WS(' ',c.customer_name, c.adhar_no) AS customer,ROUND(t.fat,2) AS fat,ROUND(t.clr,2) AS clr,ROUND(t.snf,2) AS snf,ROUND(t.weight, 2) AS weight,t.rate,ROUND(t.netamt, 2) AS netamt,t.date, t.shift")
            ->from("transactions t")
            ->join("machines m", "m.id = t.deviceid", "LEFT")
            ->join("users s", "s.id = m.society_id", "LEFT")
            ->join("users d", "d.id = m.dairy_id", "LEFT")
            ->join("customers c", "c.id = t.cid", "LEFT")
            ->where("t.type", "C")
            ->where("m.status", 1)
            ->where("t.society_id", $this->session->userdata("id"));
        if($data['period_word'] == "Last Month"){
            $date = date("Y-m-d", strtotime("-1 months"));
            $this->db->where("MONTH(t.date)", date('m', strtotime($date)));
            $this->db->where("YEAR(t.date)", date('Y', strtotime($date)));
        }else{
            $date_end = date("Y-m-d");
            $date_start = date("Y-m-d", strtotime("-7 Days"));
            $this->db->where('t.date BETWEEN "'. date('Y-m-d', strtotime($date_start)). '" and "'. date('Y-m-d', strtotime($date_end)).'"');
        }

        if($data['shift'] == "M"){
            $this->db->where("t.shift", "M");
        }else if($data['shift'] == "E"){
            $this->db->where("t.shift", "E");
        }else{
            // no code to display all shift
        }
        $this->db->order_by("t.date", "DESC");
            $q = $this->db->get();
        /*echo $this->db->last_query();exit;*/
        if($q->num_rows() > 0){
            return $q->result_array();
        }
        return FALSE;
    }

    function custom_transactions_buff($data = array())
    {
        /*print "<pre>";
        print_r($data);exit;*/
        $q = $this->db->select("CONCAT_WS(' ',c.customer_name, c.adhar_no) AS customer,ROUND(t.fat, 2) AS fat,ROUND(t.clr, 2) AS clr,ROUND(t.snf, 2) AS snf,ROUND(t.weight, 2) AS weight,t.rate,ROUND(t.netamt, 2) AS netamt,t.date,t.shift")
            ->from("transactions t")
            ->join("machines m", "m.id = t.deviceid", "LEFT")
            ->join("users s", "s.id = m.society_id", "LEFT")
            ->join("users d", "d.id = m.dairy_id", "LEFT")
            ->join("customers c", "c.id = t.cid", "LEFT")
            ->where("t.type", "B")
            ->where("m.status", 1)
            ->where("t.society_id", $this->session->userdata("id"));

        // period condition
        if($data['period_word'] == "Last Month"){
            $date = date("Y-m-d", strtotime("-1 months"));
            $this->db->where("MONTH(t.date)", date('m', strtotime($date)));
            $this->db->where("YEAR(t.date)", date('Y', strtotime($date)));
        }else{
            $date_end = date("Y-m-d");
            $date_start = date("Y-m-d", strtotime("-7 Days"));
            $this->db->where('t.date BETWEEN "'. date('Y-m-d', strtotime($date_start)). '" and "'. date('Y-m-d', strtotime($date_end)).'"');
        }
        // shift condition
        if($data['shift'] == "M"){
            $this->db->where("t.shift", "M");
        }else if($data['shift'] == "E"){
            $this->db->where("t.shift", "E");
        }else{
            // no code to display all shift
        }
        $q = $this->db->get();
        /*echo $this->db->last_query();exit;*/
        if($q->num_rows() > 0){
            return $q->result_array();
        }
        return FALSE;
    }

    public function custom_transactions_cow_summary($data = array())
    {
        /*print "<pre>";
        print_r($data);exit;*/
        /*Array
        (
            [id] => 1
            [report_name] => Silicon Dairy Report
            [period] => 1
            [shift] => All
            [machine_type] => USB
            [society_id] => 21,22 // TODO check multiple society transaction group by society
            [user_id] => 19
            [period_word] => Last 7 Days
            [shift_word] => All
        )*/
        $m_soc = explode(",", $data['society_id']);
        $where = " WHERE `t`.`type` = 'C' AND `m`.`status` = 1 AND (CURDATE() BETWEEN `m`.`from_date` AND `m`.`to_date`) AND";
        $sql = "SELECT `s`.`name` AS `society_name`,ROUND(AVG(`t`.`fat`), 2) AS fat,ROUND(AVG(`t`.`clr`), 2) AS clr,ROUND(AVG(`t`.`snf`), 2) AS snf,ROUND(SUM(`t`.`weight`), 2) AS weight,ROUND(AVG(`t`.`rate`), 2) AS rate,ROUND(SUM(`t`.`netamt`), 2) AS netamt,`t`.`shift` FROM `transactions` `t` LEFT JOIN `machines` `m` ON `m`.`id` = `t`.`deviceid` LEFT JOIN `users` `s` ON `s`.`id` = `t`.`society_id` LEFT JOIN `users` `d` ON `d`.`id` = `t`.`dairy_id` LEFT JOIN `customers` `c` ON `c`.`id` = `t`.`cid`";

        if($data['shift'] == "M" || $data['shift'] == "E"){
            $where.= " `t`.`shift` = '".$data['shift']."' AND";
        }else{
            // no code for all shift
        }

        if($data['period'] == 1) {
            // period 1 = Last 7 Days
            $date_end = date("Y-m-d");
            $date_start = date("Y-m-d", strtotime("-7 Days"));
            $where.= " (`t`.`date` BETWEEN '".$date_start."' AND '".$date_end."') AND";
        }else{
            // period 2 = Last Month
            $date = date("Y-m-d", strtotime("-1 months"));
            $where.= " MONTH(`t`.`date`) = '".date('m', strtotime($date))."' AND YEAR(`t`.`date`) = '".date('Y', strtotime($date))."' AND";
        }
        /*$cnt = count($m_soc);
        $i = 1;
        foreach($m_soc as $row){
            if($i > $cnt) {
                $where .= " `t`.`society_id` = '$row' AND";
            }else{
                $where .= " `t`.`society_id` = '$row'";
            }
        }*/
        $in_arr = array();
        foreach($m_soc as $row){
            array_push($in_arr, $row);
        }
        if(!empty($in_arr)){
            $soc_ids = implode(',', $in_arr);
            $where .= " `t`.`society_id` IN ($soc_ids)";
        }
        $group_by = " GROUP BY `t`.`society_id`";
        $new_sql = $sql.$where.$group_by;

        $query = $this->db->query($new_sql);
        /*echo $this->db->last_query();exit;*/

        return ($query->num_rows() > 0) ? $query->result_array() : FALSE;
    }

    public function custom_transactions_buff_summary($data = array())
    {
        $where = " WHERE `t`.`type` = 'B' AND `m`.`status` = 1 AND (CURDATE() BETWEEN `m`.`from_date` AND `m`.`to_date`) AND";
        $sql = "SELECT `s`.`name` AS `society_name`,ROUND(AVG(`t`.`fat`), 2) AS fat,ROUND(AVG(`t`.`clr`), 2) AS clr,ROUND(AVG(`t`.`snf`), 2) AS snf,ROUND(SUM(`t`.`weight`), 2) AS weight,ROUND(AVG(`t`.`rate`), 2) AS rate,ROUND(SUM(`t`.`netamt`), 2) AS netamt,`t`.`shift` FROM `transactions` `t` LEFT JOIN `machines` `m` ON `m`.`id` = `t`.`deviceid` LEFT JOIN `users` `s` ON `s`.`id` = `t`.`society_id` LEFT JOIN `users` `d` ON `d`.`id` = `t`.`dairy_id` LEFT JOIN `customers` `c` ON `c`.`id` = `t`.`cid`";

        if($data['shift'] == "M" || $data['shift'] == "E"){
            $where.= " `t`.`shift` = '".$data['shift']."' AND";
        }else{
            // no code for all shift
        }

        $m_soc = explode(",", $data['society_id']);
        if($data['period'] == 1) {
            // period 1 = Last 7 Days
            $date_end = date("Y-m-d");
            $date_start = date("Y-m-d", strtotime("-7 Days"));
            $where.= " (`t`.`date` BETWEEN '".$date_start."' AND '".$date_end."') AND";
        }else{
            $date = date("Y-m-d", strtotime("-1 months"));
            $where.= " MONTH(`t`.`date`) = '".date('m', strtotime($date))."' AND YEAR(`t`.`date`) = '".date('Y', strtotime($date))."' AND";
        }

        $in_arr = array();
        foreach($m_soc as $row){
            array_push($in_arr, $row);
        }
        if(!empty($in_arr)){
            $soc_ids = implode(',', $in_arr);
            $where .= " `t`.`society_id` IN ($soc_ids)";
        }

        $group_by = " GROUP BY `t`.`society_id`";
        $new_sql = $sql.$where.$group_by;

        $query = $this->db->query($new_sql);
        /*echo $this->db->last_query();exit;*/

        return ($query->num_rows() > 0) ? $query->result_array() : FALSE;
    }
	
	function get_society_summary($shift = NULL)
	{
		$id = $this->session->userdata("id");
		$date = date('Y-m-d');
		$q = $this->db->query("SELECT ROUND(SUM(weight), 2) AS litre, `type` FROM transactions
								WHERE society_id = '$id'
								AND `date` = '$date'
								AND `shift` = '$shift'
								GROUP BY `type`");
								
								// TODO change date from static date to current date
								
		if($q->num_rows() > 0)
		{
			return $q->result_array();
		}
		return FALSE;
	}
	
	function get_monthly_milk_collection($shift = NULL, $type = NULL)
	{
		$id = $this->session->userdata("id");
		$date = date('m');
		for($i = 1; $i<= date('t'); $i++)
		{
			$date = date('Y-m').'-'.$i;
			$date_arr[] = array($i);
		//	$data[] = array("date"=>$date, "litre"=>"","type"=>"");
			$sql_cow = "SELECT ROUND(SUM(weight), 2) AS litre FROM transactions
					WHERE `date` = '$date'
					AND society_id = '$id'
					AND `shift` = '$shift' AND `type` = '$type'";
					
			$q = $this->db->query($sql_cow);
			
			if($q->num_rows() > 0)
			{
				$data[] = (float) $q->row()->litre;
			}
		}
		return $data;
	}

	public function export_cow($data = array())
    {
        /*Array
        (
            [id] => 24
            [start_date] => 2017-01-01
            [end_date] => 2017-04-07
            [shift] =>
            [cid] =>
        )*/
        $this->db->select("c.customer_name,c.adhar_no,m.machine_id,t.fat,t.clr,t.snf,t.weight,t.rate,t.netamt,t.shift,t.date")
            ->from("transactions t")
            ->join("customers c", "c.id = t.cid", "LEFT")
            ->join("machines m", "m.id = t.deviceid", "LEFT")
            ->where("t.society_id", $data['id'])
            ->where("t.date >=", $data['start_date'])
            ->where("t.date <=", $data['end_date'])
            ->where("t.type", "C");
        if($data['shift']){
            $this->db->where("t.shift", $data['shift']);
        }

        if($data['cid']){
            $this->db->where("t.cid", $data['cid']);
        }
        $this->db->where("CURDATE() BETWEEN m.from_date AND m.to_date");
        $q = $this->db->get();
        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            return $q->result_array();
        }
        return FALSE;
    }

    public function export_buff($data = array())
    {
        /*Array
        (
            [id] => 24
            [start_date] => 2017-01-01
            [end_date] => 2017-04-07
            [shift] =>
            [cid] =>
        )*/
        $this->db->select("c.customer_name,c.adhar_no,m.machine_id,t.fat,t.clr,t.snf,t.weight,t.rate,t.netamt,t.shift,t.date")
            ->from("transactions t")
            ->join("customers c", "c.id = t.cid", "LEFT")
            ->join("machines m", "m.id = t.deviceid", "LEFT")
            ->where("t.society_id", $data['id'])
            ->where("t.date >=", $data['start_date'])
            ->where("t.date <=", $data['end_date'])
            ->where("t.type", "B");
        if($data['shift']){
            $this->db->where("t.shift", $data['shift']);
        }

        if($data['cid']){
            $this->db->where("t.cid", $data['cid']);
        }
        $this->db->where("CURDATE() BETWEEN m.from_date AND m.to_date");
        $q = $this->db->get();
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            return $q->result_array();
        }
        return FALSE;
    }
}

/** application/Models/Transaction_model.php */