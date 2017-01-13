<?php defined('BASEPATH') OR exit('No direct script access allowed');
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
    
    function not_allocated_machines(){
        $q = $this->db->query("SELECT * FROM machines WHERE society_id IS NULL AND dairy_id IS NULL");
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }
    
    function not_allocated_soc_machines(){
//        $q = $this->db->select("machine_id")->get("society_machine_map");
        /*$id = $this->session->userdata("id");
        $q = $this->db->query("SELECT machine_id FROM dairy_machine_map WHERE dairy_id = '$id'");
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            foreach($q->result() as $rw){
                $rw1[] = $rw->machine_id;
            }
        }
        if(!empty($rw)){
            $ex = implode("','", $rw1);
            $q = $this->db->query("SELECT machine_id FROM society_machine_map WHERE machine_id IN('$ex')");
        }else{
            $q = $this->db->get("machines");
        }
        foreach($q->result() as $rw1){
            $rw2[] = $rw1->machine_id;
        }
        $ex1 = implode("','", $rw2);
//        echo $this->db->last_query();exit;
        $q1 = $this->db->query("SELECT m.machine_id, m.id as mo_id FROM dairy_machine_map dmm LEFT JOIN machines m ON m.id = dmm.machine_id WHERE dmm.dairy_id = '$id' AND dmm.machine_id NOT IN('$ex1')");
        echo $this->db->last_query();exit;
        if($q1->num_rows() > 0){
            foreach($q1->result() as $row12){
                $row111[] = $row12;
            }
            return $row111;
        }
        return FALSE;*/
        $id = $this->session->userdata("id");
        $q = $this->db->query("SELECT id as mo_id, machine_id FROM machines WHERE society_id IS NULL AND dairy_id = '$id'");
        if($q->num_rows() > 0){
            foreach($q->result() as $rw){
                $rw1[] = $rw;
            }
            return $rw1;
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
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row->machine_id;
            }
            $machine_ids = implode("','",$row1);
        }
        
        if(!empty($machine_ids)){
            $q1 = $this->db->query("SELECT dmm.*, m.machine_id AS mid FROM dairy_machine_map dmm LEFT JOIN machines m ON m.id = dmm.machine_id WHERE dmm.machine_id NOT IN ('".$machine_ids."')");
        }else{
            $q1 = $this->db->query("SELECT dmm.*, m.machine_id AS mid FROM dairy_machine_map dmm LEFT JOIN machines m ON m.id = dmm.machine_id");
        }
//        echo $this->db->last_query();exit;
        if($q1->num_rows() > 0){
            foreach($q1->result() as $m){
                $m1[] = $m;
            }
            return $m1;
        }
        return FALSE;
    }
    
    function map_society_machine($data = array()){
        $this->db->where("id", $data['machine_id']);
        if($this->db->update("machines", array("society_id"=>$data['society_id']))){
            $q = $this->db->get_where("machines", array("id"=>$data['machine_id']));
            $machine = $q->row()->machine_id;
            $notification = array(
                "society_id"=>$data['society_id'],
                "message"=>$machine." successfully added.",
            );
            $this->db->insert("notification",$notification);
            $nid = $this->db->insert_id();
            $notification_read = array(
                "notification_id"=>$nid,
                "society_id"=>$data['society_id'],
                "is_read"=>0
            );
            $this->db->insert("notification_read", $notification_read);
            return TRUE;
        }
        return FALSE;
        /*if($this->db->insert("society_machine_map",$data)){
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
        return FALSE;*/
    }
    
    function map_dairy_machine($data = array()){
        $this->db->where("id", $data['machine_id']);
        if($this->db->update("machines", array("dairy_id"=>$data['dairy_id']))){
            $q = $this->db->get_where("machines", array("id"=>$data['machine_id']));
            $machine = $q->row()->machine_id;
            $notification = array(
                "dairy_id"=>$data['dairy_id'],
                "message"=>$machine." successfully added."
            );
            $this->db->insert("notification",$notification);
            $nid = $this->db->insert_id();
            $notification_read = array(
                "notification_id"=>$nid,
                "dairy_id"=>$data['dairy_id'],
                "is_read"=>0
            );
            $this->db->insert("notification_read", $notification_read);
            return TRUE;
        }
        return FALSE;
        /*if($this->db->insert("dairy_machine_map",$data)){
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
        return FALSE;*/
    }
    
    function mapped_machine(){
        if($this->session->userdata("group") == "admin"){
            $q = $this->db->query("SELECT m.id, m.machine_id, d.name FROM machines m
                                LEFT JOIN users d ON d.id = m.dairy_id");
        }else if($this->session->userdata("group") == "dairy"){
            $id = $this->session->userdata("id");
            $q = $this->db->query("SELECT m.id, m.machine_id, d.name FROM machines m
                                LEFT JOIN users d ON d.id = m.dairy_id
                                WHERE m.dairy_id = '$id'");
        }
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
                /*$this->db->select("dairy_machine_map.id,users.name,machines.machine_id")
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
        return FALSE;*/
    }
    
    function mapped_society_machine(){
        if($this->session->userdata("group") == "society"){
            $id = $this->session->userdata("id");
            $q = $this->db->query("SELECT m.id, m.machine_id, s.name FROM machines m
                                LEFT JOIN users s ON s.id = m.society_id
                                WHERE society_id = '$id'");
        }else if($this->session->userdata("group") == "dairy"){
            $id = $this->session->userdata("id");
            $q = $this->db->query("SELECT m.id, m.machine_id, s.name FROM machines m
                                LEFT JOIN users d ON d.id = m.dairy_id
                                LEFT JOIN users s ON s.dairy_id = m.dairy_id
                                WHERE d.id = '$id' AND m.society_id IS NOT NULL");
        }else{
            $q = $this->db->query("SELECT m.id, m.machine_id, s.name FROM machines m
                                LEFT JOIN users d ON d.id = m.dairy_id
                                LEFT JOIN users s ON s.dairy_id = m.dairy_id WHERE m.society_id IS NOT NULL");
        }
        /*$this->db->select("society_machine_map.id,users.name,machines.machine_id")
            ->from("users")
            ->join("user_groups","user_groups.user_id = users.id","LEFT")
            ->join("groups","groups.id = user_groups.group_id","LEFT")
            ->join("society_machine_map","society_machine_map.society_id = users.id")
            ->join("machines","machines.id = society_machine_map.machine_id")
            ->where("groups.name","society");
        if($this->session->userdata("group") == "society"){
            $this->db->where("users.id",$this->session->userdata("id"));
            $q = $this->db->get();
        }else if($this->session->userdata("group") == "dairy"){
            $this->db->where("users.dairy_id", $this->session->userdata("id"));
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
        return FALSE;*/
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
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
//        echo $type;exit;
        if($type == "dairy"){
            $q = $this->db->query("SELECT COUNT(*) AS num FROM notification n
                                LEFT JOIN notification_read nr ON nr.notification_id = n.id
                                WHERE n.dairy_id = '$id' AND nr.is_read = '0'");
        }else if($type == "society"){
            $q = $this->db->query("SELECT COUNT(*) AS num FROM notification n
                                LEFT JOIN notification_read nr ON nr.notification_id = n.id
                                WHERE n.society_id = '$id' AND nr.is_read = '0'");
        }
//        echo $this->db->last_query();exit;
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
}

/** application/Models/Machine_model.php */