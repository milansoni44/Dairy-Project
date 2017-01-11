<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model{
    //put your code here
    public function __construct() {
        parent::__construct();
    }
    
    public function check_login($data = array()){
//        print_r($data);exit;
        $q = $this->db->select("*")
                ->from("users")
                ->where("username",$data['username'])
                ->where("password",$data['password'])
                ->get();
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            return $q->row();
        }
        return false;
    }
    
    public function get_user_group($id = NULL){
        $q = $this->db->select("group_id,groups.name")
                ->from("user_groups")
                ->join("groups","groups.id = user_groups.group_id")
                ->where("user_groups.user_id",$id)
                ->get();
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
    
    public function insertToken($user_id)
    {   
        $token = substr(sha1(rand()), 0, 30); 
        $date = date('Y-m-d');
        
        $string = array(
                'token'=> $token,
                'user_id'=>$user_id,
                'created'=>$date
            );
        $query = $this->db->insert_string('tokens',$string);
        $this->db->query($query);
        return $token . $user_id;
        
    }
    
    public function check_email_exist($email = NULL){
        $q = $this->db->get_where("users",array("email"=>$email));
        if($q->num_rows() > 0){
            return TRUE;
        }
        return FALSE;
    }
    
    public function getUserInfoByEmail($email = NULL){
        $q = $this->db->get_where("users",array("email"=>$email));
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
    
    public function getUserInfo($id)
    {
        $q = $this->db->get_where('users', array('id' => $id), 1);  
        if($this->db->affected_rows() > 0){
            $row = $q->row();
            return $row;
        }else{
            error_log('no user found getUserInfo('.$id.')');
            return false;
        }
    }
    
    public function isTokenValid($token)
    {
       $tkn = substr($token,0,30);
       $uid = substr($token,30);      
       
        $q = $this->db->get_where('tokens', array(
            'tokens.token' => $tkn, 
            'tokens.user_id' => $uid), 1);       
        if($this->db->affected_rows() > 0){
            $row = $q->row();             
            
            $created = $row->created;
            $createdTS = strtotime($created);
            $today = date('Y-m-d'); 
            $todayTS = strtotime($today);
            
            if($createdTS != $todayTS){
                return false;
            }
            
            $user_info = $this->getUserInfo($row->user_id);
            return $user_info;
            
        }else{
            return false;
        }
        
    }   
    
    public function updatePassword($data = array()){
        $this->db->where("id",$data['user_id']);
        if($this->db->update('users',array('password'=>$data['password']))){
            return TRUE;
        }
        return FALSE;
    }
    
    function get_dairy($id = NULL){
        $q = $this->db->query("SELECT d.* FROM users d LEFT JOIN users s ON s.dairy_id = d.id WHERE s.id = '$id'");
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
}

/** application/Models/Auth_model.php */