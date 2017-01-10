<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Machine_model
 *
 * @author Intel
 */
class Machine_model extends CI_Model{
    //put your code here
    
    function __construct() {
        parent::__construct();
    }
    /**
     * insert machine
     * @param type $data
     * @return boolean
     */
    function add_machine($data = array()){
        if($this->db->insert_batch("machines",$data)){
            return TRUE;
        }
        return FALSE;
    }
    
    function edit_machine($data = array(), $id = NULL){
        $this->db->where("id", $id);
        if($this->db->update("machines",$data)){
            return TRUE;
        }
        return FALSE;
    }
    /**
     * get all the machines
     * @return boolean
     */
    function get_machines(){
//        $q = $this->db->get("machines");
        $q = $this->db->query("SELECT m.*, d.name as dairy_name, s.name as society_name FROM machines m
LEFT JOIN dairy_machine_map dmm
ON dmm.machine_id = m.id
LEFT JOIN society_machine_map smm
ON smm.machine_id = m.id
LEFT JOIN users d
ON d.id = dmm.dairy_id
LEFT JOIN users s
ON s.id = smm.society_id");
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }
    
    function get_machine_by_id($id = NULL){
        $q = $this->db->get_where("machines", array("id"=>$id));
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
    /**
     * get all available machines for dairy
     * @param type $id
     * @return boolean
     */
    function allocated_dairy_machine($id = NULL){
        if(!$id){
            $q1 = $this->db->get("dairy_machine_map");
        }else{
            $q1 = $this->db->query("SELECT * FROM dairy_machine_map WHERE id NOT IN($id)");
        }
//        echo $this->db->last_query();exit;
        if($q1->num_rows() > 0){
            foreach($q1->result() as $row){
                $row1[] = $row->id;
            }
            $machine_ids = implode("','", $row1);
        }
        
        if(!empty($machine_ids)){
            $q = $this->db->query("SELECT * FROM machines WHERE id NOT IN ('".$machine_ids."')");
        }else{
            $q = $this->db->query("SELECT * FROM machines");
        }
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            foreach($q->result() as $m){
                $m1[] = $m;
            }
            return $m1;
        }
        return FALSE;
    }
    /**
     * get all available machines for society
     * @param type $id
     * @return boolean
     */
    function allocated_soc_machine($id = NULL){
        if(!$id){
            $q = $this->db->get("society_machine_map");
        }else{
            $q = $this->db->query("SELECT * FROM society_machine_map WHERE id NOT IN($id)");
        }
//        echo $this->db->last_query();
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row->machine_id;
            }
            $machine_ids = implode("','",$row1);
        }
        
        if(!empty($machine_ids)){
            $q1 = $this->db->query("SELECT * FROM machines WHERE id NOT IN ('".$machine_ids."')");
        }else{
            $q1 = $this->db->query("SELECT * FROM machines");
        }
//        echo $this->db->last_query();
        if($q1->num_rows() > 0){
            foreach($q1->result() as $m){
                $m1[] = $m;
            }
            return $m1;
        }
        return FALSE;
    }
    
    function map_society_machine($data = array()){
        if($this->db->insert("society_machine_map",$data)){
            $notification = array(
                "type"=>"machine",
                "machine_id"=>$data['machine_id'],
                "society_id"=>$data['society_id'],
                "dairy_id"=>$this->session->userdata("id"),
                "message"=>$data['machine_id']." successfully added.",
                "is_read"=>0
            );
            $this->db->insert("notification", $notification);
            return TRUE;
        }
        return FALSE;
    }
    
    function map_dairy_machine($data = array()){
        if($this->db->insert("dairy_machine_map",$data)){
            $notification = array(
                "type"=>"machine",
                "machine_id"=>$data['machine_id'],
                "dairy_id"=>$data['dairy_id'],
                "message"=>$data['machine_id']." successfully added.",
                "is_read"=>0
            );
            $this->db->insert("notification", $notification);
            return TRUE;
        }
        return FALSE;
    }
    
    function mapped_machine(){
                $this->db->select("dairy_machine_map.id,users.name,machines.machine_id")
                ->from("dairy_machine_map")
                ->join("machines","machines.id = dairy_machine_map.machine_id")
                ->join("users","users.id = dairy_machine_map.dairy_id","LEFT")
                ->join("user_groups","user_groups.user_id = users.id")
                ->join("groups","groups.id = user_groups.group_id")
                ->where("groups.name","dairy");
        if($this->session->userdata("group") == "dairy"){
            $this->db->where("users.id",$this->session->userdata("id"));
            $q = $this->db->get();
        }else{
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
    
    function mapped_society_machine(){
        $this->db->select("society_machine_map.id,users.name,machines.machine_id")
            ->from("users")
            ->join("user_groups","user_groups.user_id = users.id","LEFT")
            ->join("groups","groups.id = user_groups.group_id","LEFT")
            ->join("society_machine_map","society_machine_map.society_id = users.id")
            ->join("machines","machines.id = society_machine_map.machine_id")
            ->where("groups.name","society");
        if($this->session->userdata("group") == "society"){
            $this->db->where("users.id",$this->session->userdata("id"));
            $q = $this->db->get();
        }else{
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
    
    function get_dairyMachine_by_id($id = NULL){
        $q = $this->db->get_where("dairy_machine_map", array("id"=>$id));
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
    
    function edit_dairy_machine($data = array(), $id = NULL){
        $this->db->where('id',$id);
        if($this->db->update("dairy_machine_map",$data)){
            return TRUE;
        }
        return FALSE;
    }
    
    function get_societyMachine_by_id($id = NULL){
        $q = $this->db->get_where("society_machine_map", array("id"=>$id));
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
    
    function edit_society_machine($data = array(), $id = NULL){
        $this->db->where("id", $id);
        if($this->db->update("society_machine_map", $data)){
            return TRUE;
        }
        return FALSE;
    }
    
    function count_machines($type, $id = NULL){
        if($type == "dairy"){
            $q = $this->db->query("SELECT COUNT(*) as num FROM notification WHERE dairy_id = '$id' AND is_read = '0'");
        }else if($type == "society"){
            $q = $this->db->query("SELECT COUNT(*) as num FROM notification WHERE society_id = '$id' AND is_read = '0'");
        }else if($type == "admin"){
            $q = $this->db->query("SELECT COUNT(*) as num FROM notification WHERE is_read = '0'");
        }
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
    
    function get_available_machines(){
//        $q = $this->db->query("");
    }
}