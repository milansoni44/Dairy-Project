<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Auth_lib
 *
 * @author Milan Soni(Ehealthsource)
 */
class Auth_lib {
    //put your code here
    var $CI;
    public function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->helper('url');
        $this->CI->load->library('session');
        $this->CI->load->database();
    }
    
    public function set_session_data($data = array()){
        $this->CI->session->set_userdata($data);
    }
	
	public function get_notification($type=NULL, $id=NULL)
	{
		$query = '';
		if( $type === "dairy" )
		{
			$query_total = " SELECT COUNT(*) AS `total` 
						FROM `notification` 
						WHERE `for_whom`=1 AND `dairy_id`=".$id." AND `id` NOT IN ( SELECT `notification_id` FROM `notification_read` WHERE `dairy_id`=".$id." AND `is_read`=1 )";
			
			$query = " SELECT * 
						FROM `notification` 
						WHERE `for_whom`=1 AND `dairy_id`=".$id." AND `id` NOT IN ( SELECT `notification_id` FROM `notification_read` WHERE `dairy_id`=".$id." AND `is_read`=1 ) LIMIT 10";
		}
		else if( $type === "society" )
		{
			$query_total = "SELECT COUNT(*) AS `total` FROM (
						SELECT * 
						FROM `notification` 
						WHERE 
						`for_whom`=2 
						AND `society_id`=".$id." 
						AND `id` NOT IN ( SELECT `notification_id` FROM `notification_read` WHERE `society_id`=".$id." AND `is_read`=1 )

						UNION

						SELECT * 
						FROM `notification` 
						WHERE 
						`for_whom`=2 
						AND `id` NOT IN ( SELECT `notification_id` FROM `notification_read` WHERE `society_id`=".$id." AND `is_read`=1 )
						AND `dairy_id`=( SELECT `u`.`dairy_id` FROM `users` `u` WHERE `u`.`id`=".$id." AND `notification`.`for_whom`=2 )
					) AS `tmp`";
					
			$query = "SELECT * FROM (
						SELECT * 
						FROM `notification` 
						WHERE 
						`for_whom`=2 
						AND `society_id`=".$id." 
						AND `id` NOT IN ( SELECT `notification_id` FROM `notification_read` WHERE `society_id`=".$id." AND `is_read`=1 )

						UNION

						SELECT * 
						FROM `notification` 
						WHERE 
						`for_whom`=2 
						AND `id` NOT IN ( SELECT `notification_id` FROM `notification_read` WHERE `society_id`=".$id." AND `is_read`=1 )
						AND `dairy_id`=( SELECT `u`.`dairy_id` FROM `users` `u` WHERE `u`.`id`=".$id." AND `notification`.`for_whom`=2 )
					) AS `tmp`
					ORDER BY `created_at` DESC LIMIT 10";
		}
		
		if( $query != '' )
		{
			$result = $this->CI->db->query( $query );
			$this->CI->data['notifications'] = $result->result_array();
			
			$result_total = $this->CI->db->query( $query_total );
			$this->CI->data['total_notification'] = $result_total->row("total");
		//	print "<pre>"; print_r( $result->result_array() ); exit;
		}
	}
    
    public function get_machines($type = NULL, $id = NULL)
	{
        if($type == "dairy"){
            $q = $this->CI->db->query("SELECT n.message FROM notification n
                                LEFT JOIN notification_read nr ON nr.notification_id = n.id
                                WHERE n.dairy_id = '$id' AND nr.is_read = '0'");
        }else if($type == "society"){
            $q = $this->CI->db->query("SELECT n.message FROM notification n
                                LEFT JOIN notification_read nr ON nr.notification_id = n.id
                                WHERE n.society_id = '$id' AND nr.is_read = '0'");
        }else{
            $q = $this->CI->db->query("SELECT n.message AS num FROM notification n
                                LEFT JOIN notification_read nr ON nr.notification_id = n.id");
        }
//        echo $this->CI->db->last_query();exit;
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }
    
    public function sess_destroy(){
        $this->CI->session->sess_destroy(); 
    }
    
    public function logout(){
        $this->sess_destroy();
    }
    
    public function is_logged_in(){
        if($this->CI->session->userdata('username')){
            return TRUE;
        }
        return FALSE;
    }
}
