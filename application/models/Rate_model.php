<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Rate_model
 *
 * @author Intel
 */
class Rate_model extends CI_Model{
    //put your code here
    function __construct() {
        parent::__construct();
    }
    
    function get_bufallo_rate(){
        if($this->session->userdata("group") == "dairy"){
            $this->db->where("dairy_id", $this->session->userdata("id"));
            $q = $this->db->get("buffalo_fat");
        }else if($this->session->userdata("group") == "admin"){
            $q = $this->db->get("buffalo_fat");
        }
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }
    
    function get_cow_fatrate(){
        if($this->session->userdata("group") == "dairy"){
            $this->db->where("dairy_id", $this->session->userdata("id"));
            $q = $this->db->get("cow_fat");
        }else if($this->session->userdata("group") == "admin"){
            $q = $this->db->get("cow_fat");
        }
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }
    
    function insert_bfat_data($data = array()){
        if($this->db->insert_batch("buffalo_fat", $data)){
            $notify_data = array(
                "dairy_id"=>$this->session->userdata("id"),
                "type"=>"rate",
                "message"=>"Buffalo FAT updated successfully",
                "tbl_name"=>"buffalo_fat",
                "is_read"=>0
            );
            $this->db->insert("notification", $notify_data);
            return TRUE;
        }
        return FALSE;
    }
    
    function insert_cfat_data($data = array()){
        if($this->db->insert_batch("cow_fat", $data)){
            $notify_data = array(
                "dairy_id"=>$this->session->userdata("id"),
                "type"=>"rate",
                "message"=>"Cow FAT updated successfully",
                "tbl_name"=>"cow_fat",
                "is_read"=>0
            );
            $this->db->insert("notification", $notify_data);
            return TRUE;
        }
        return FALSE;
    }
    
    function read_notification(){
        $id = $this->session->userdata("id");
        $this->db->where("dairy_id", $id);
        $this->db->where("type", "rate");
        $this->db->where("tbl_name", "buffalo_fat");
        $this->db->where("is_read",0);
        $q = $this->db->get("notification");
        if($q->num_rows() > 0){
            $this->db->update("notification", array("is_read"=>1));
            return TRUE;
        }
        return FALSE;
    }
    
    function get_bfat(){
        if($this->session->userdata("group") == "dairy"){
            $id = $this->session->userdata("id");
        }else if($this->session->userdata("group") == "society"){
            $id = $this->session->userdata("id");
        }
        $q = $this->db->get_where("buffalo_fat", array("dairy_id"=>$id));
        if($q->num_rows() > 0){
            return $q->result_array();
        }
        return FALSE;
    }
    
    function get_cfat(){
        if($this->session->userdata("group") == "dairy"){
            $id = $this->session->userdata("id");
        }else if($this->session->userdata("group") == "society"){
            $id = $this->session->userdata("id");
        }
        $q = $this->db->get_where("cow_fat", array("dairy_id"=>$id));
        if($q->num_rows() > 0){
            return $q->result_array();
        }
        return FALSE;
    }
}
