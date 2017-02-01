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
    function add_machine($data = array())
	{
		$from_to_data_str = '';
		if( isset($data['from_date']) && isset($data['to_date']) )
		{
		$from_to_data_str = ' `from_date`="'.$data['from_date'].'", `to_date`="'.$data['to_date'].'",';
		}
		
		$result = $this->db->query(" INSERT INTO `machines` SET 
								`machine_id`='".htmlentities($data['machine_id'], ENT_QUOTES)."',
								`machine_name`='".htmlentities($data['machine_name'], ENT_QUOTES)."',
								`machine_type`='".$data['machine_type']."',
								`validity`='".$data['validity']."',
								".$from_to_data_str."
								`start_validity_from`='".$data['start_validity_from']."',
								`dairy_id`='".$data['dairy_id']."'	");
		return $result ? TRUE : FALSE;
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
        $q = $this->db->query("SELECT machines.*, d.name AS dairy_name, s.name AS society_name FROM machines
                            LEFT JOIN users d ON d.id = machines.dairy_id
                            LEFT JOIN users s ON s.id = machines.society_id");
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
    function allocated_dairy_machine(){
        $id = $this->session->userdata("id");
        $q = $this->db->query("SELECT m.*, u.name FROM machines m
LEFT JOIN users u ON u.id = m.society_id
WHERE m.dairy_id = '$id'");
        /*echo $this->db->last_query();exit;*/
        if($q->num_rows() > 0){
            foreach($q->result() as $m){
                $m1[] = $m;
            }
            return $m1;
        }
        return FALSE;
    }
    
    function allocated_soc_machine($id = NULL){
        if(!$id){
            
        }else{
            $q1 = $this->db->query("SELECT machine_id,id FROM machines WHERE society_id = '$id'");
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
    
    function map_society_machine($data = array(), $id = NULL)
	{
        $this->db->where("id", $id);
        if($this->db->update("machines", $data)){
            return TRUE;
        }
        return FALSE;
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
        $id = $this->session->userdata("id");
        $q = $this->db->query("SELECT * FROM machines WHERE society_id = '$id'");
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
    }
    
    function get_dairyMachine_by_id($id = NULL){
//        $q = $this->db->get_where("dairy_machine_map", array("id"=>$id));
        $q = $this->db->query("SELECT * FROM machines WHERE id = '$id'");
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
    
    function edit_dairy_machine($data = array(), $id = NULL){
        $this->db->where('id',$id);
        if($this->db->update("machines",$data)){
            return TRUE;
        }
        return FALSE;
    }
    
    function get_societyMachine_by_id($id = NULL){
//        $q = $this->db->get_where("society_machine_map", array("id"=>$id));
        $q = $this->db->query("SELECT * FROM machines WHERE id = '$id'");
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
    
    function totalCount(){
        if($this->session->userdata("group") == "admin"){
            $q = $this->db->query("SELECT COUNT(*) AS total_machine, (SELECT COUNT(*) FROM machines m_in WHERE m_in.dairy_id IS NOT NULL) AS total_allocated FROM machines");
        }else if($this->session->userdata("group") == "dairy"){
            $id = $this->session->userdata("id");
            $q = $this->db->query("SELECT COUNT(*) AS total_machine, (SELECT COUNT(*) FROM machines m_in WHERE m_in.society_id IS NOT NULL) AS total_allocated FROM machines WHERE dairy_id = '$id'");
        }
//        echo $this->db->last_query();
        return $q->row();
    }
    
    function get_validity($id = NULL){
        $q = $this->db->get_where("machines", array("id"=>$id));
        if($q->num_rows() > 0){
            return $q->row()->validity;
        }
        return FALSE;
    }
    
    function change_status($id = NULL){
        //UPDATE machines SET `status` = NOT `status` WHERE id = '16'
        if($this->db->query("UPDATE machines SET `status` = NOT `status` WHERE id = '$id'")){
            return TRUE;
        }
        return FALSE;
    }
}

/** application/Models/Machine_model.php */