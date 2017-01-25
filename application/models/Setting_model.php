<?php defined('BASEPATH') OR exit('No direct script access allowed');
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
        $q = $this->db->get("config_data");
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
    
    function delete_bf_data($table = NULL, $dairy_id = NULL){
        if($this->db->delete($table, array("dairy_id"=>$dairy_id))){
//            echo $this->db->last_query();exit;
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
    
    function insert_bfat_snf_data($data = array()){
        if($this->db->insert_batch('buffalo_fat_snf', $data)){
            return TRUE;
        }
        return FALSE;
    }
    
    function insert_bfat_clr_data($data = array()){
        if($this->db->insert_batch('buffalo_fat_clr', $data)){
            return TRUE;
        }
        return FALSE;
    }
}

/** application/Models/Setting_model.php */