<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Dairy_model
 *
 * @author Intel
 */
class Dairy_model extends CI_Model{
    //put your code here
    public function __construct() {
        parent::__construct();
    }
    
    public function add_dairy($data = array()){
        if($this->db->insert("users",$data)){
            $id = $this->db->insert_id();
            $group_array = array(
                "user_id"=>$id,
                "group_id"=>2,
            );
            try {
                $this->db->insert("user_groups",$group_array);
                return TRUE;
            } catch (Exception $ex) {
                return FALSE;
            }
        }
        return FALSE;
    }
    
    public function get_dairy(){
        $q = $this->db->select("users.*")
                ->from("users")
                ->join("user_groups","user_groups.user_id = users.id","LEFT")
                ->join("groups","groups.id = user_groups.group_id","LEFT")
                ->where("groups.name","dairy")
                ->get();
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }
    
    public function get_dairy_by_id($id = NULL){
        $q = $this->db->select("users.*")
                ->from("users")
                ->join("user_groups","user_groups.user_id = users.id","LEFT")
                ->join("groups","groups.id = user_groups.group_id","LEFT")
                ->where("groups.name","dairy")
                ->where("users.id",$id)
                ->get();
        
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
    
    public function update_dairy($data = array(), $id = NULL){
        $this->db->where("id",$id);
        if($this->db->update("users",$data)){
            $group_array = array(
                "user_id"=>$id,
                "group_id"=>2,
            );
            try {
                $this->db->where("user_id",$id);
                $this->db->update("user_groups",$group_array);
                return TRUE;
            } catch (Exception $ex) {
                return 'Message: ' .$ex->getMessage();
            }
            return TRUE;
        }
        return FALSE;
    }
    
    public function delete($id = NULL){
        $this->db->where("id",$id);
        if($this->db->delete("users")){
            return TRUE;
        }
        return FALSE;
    }
    
    public function get_states(){
        $q = $this->db->get_where("states",array("country_id"=>101));
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }
    
    public function get_cities_by_id($id = NULL){
        $q = $this->db->get_where("cities",array("state_id"=>$id));
        if($q->num_rows() > 0){
            return $q->result_array();
        }
        return FALSE;
    }
    
    function check_username($username = NULL){
        $q = $this->db->get_where("users", array("username"=>$username));
        if($q->num_rows() > 0){
            return TRUE;
        }
        return FALSE;
    }
    
}
