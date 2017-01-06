<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Setting_model
 *
 * @author Milan Soni
 */
class Setting_model extends CI_Model{
    //put your code here
    function __construct() {
        parent::__construct();
    }
    
    function get_config($type = NULL, $limit = NULL){
        $this->db->where("config_name", $limit);
        $this->db->where("config_category",$type);
        $q = $this->db->get("CONFIG_DATA");
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
    
    function delete_bf_data($table = NULL, $dairy_id = NULL){
        if($this->db->delete($table, array("dairy_id"=>$dairy_id))){
            return TRUE;
        }
        return FALSE;
    }
    
    function delete_c_data($table = NULL, $dairy_id = NULL){
        if($this->db->delete($table, array("dairy_id"=>$dairy_id))){
            return TRUE;
        }
        return FALSE;
    }
}
