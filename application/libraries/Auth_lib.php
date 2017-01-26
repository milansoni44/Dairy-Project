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
			$query = " SELECT * 
						FROM `notification` 
						WHERE `id` NOT IN ( SELECT `notification_id` FROM `notification_read` WHERE `dairy_id`=".$id." AND `is_read`=1 )";
		}
		else if( $type === "society" )
		{
			$query = " SELECT * 
						FROM `notification` 
						WHERE `id` NOT IN ( SELECT `notification_id` FROM `notification_read` WHERE `society_id`=".$id." AND `is_read`=1 )";
		}
		
		if( $query != '' )
		{
			$result = $this->CI->db->query( $query );
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
