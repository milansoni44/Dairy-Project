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
    
    public function get_machines($type = NULL, $id = NULL){
        /*if($type == "dairy"){
            $q = $this->CI->db->query("SELECT m.machine_type,dmp.id, m.from_date, m.to_date, m.machine_id, m.validity FROM dairy_machine_map dmp
LEFT JOIN machines m ON m.id = dmp.machine_id
LEFT JOIN users u ON u.id = dmp.dairy_id
WHERE u.id = '$id'");
        }else if($type == "society"){
            $q = $this->CI->db->query("SELECT m.machine_type,dmp.id, m.from_date, m.to_date, m.machine_id, m.validity FROM society_machine_map dmp
LEFT JOIN machines m ON m.id = dmp.machine_id
LEFT JOIN users u ON u.id = dmp.society_id
WHERE u.id = '$id'");
        }else{
            $q = $this->CI->db->query("SELECT * FROM machines");
        }
//        echo $this->CI->db->last_query();exit;
         */
        if($type == "dairy" || $type == "society"){
            $q = $this->CI->db->query("SELECT * FROM notification WHERE dairy_id = '$id' AND is_read = '0'");
        }else{
            $q = $this->CI->db->query("SELECT * FROM notification");
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
