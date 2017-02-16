<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Society_model
 *
 * @author Milan Soni
 */
class Society_model extends CI_Model
{
    //put your code here
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function get_society()
    {
        $this->db->select("s.id, s.name, s.username, s.email, s.address, s.area, s.contact_person, s.mobile, s.street, s.pincode, s.state, s.city, s.acc_no, s.bank_name, s.acc_type, s.ifsc, s.status as society_status, d.name AS dairy_name")
        ->from("users s")
        ->join("user_groups ug","ug.user_id = s.id","LEFT")
        ->join("groups g","g.id = ug.group_id","LEFT")
        ->join("users d","d.id = s.dairy_id","LEFT")
        ->where("g.name","society");
        if($this->session->userdata("group") == "admin"){
            $q = $this->db->get();
        }else if($this->session->userdata("group") == "dairy"){
            $q = $this->db->where("s.dairy_id",$this->session->userdata("id"))
            ->get();
        }else{
            $q = $this->db->get();
        }
        /*echo $this->db->last_query();exit;*/
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $row1[] = $row;
            }
            return $row1;
        }
        return FALSE;
    }
    
    function add_society($data = array()){
        if($this->db->insert("users",$data)){
//            echo $this->db->last_query();exit;
            $id = $this->db->insert_id();
            $group_array = array(
                "user_id"=>$id,
                "group_id"=>3,
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
    
    function get_society_by_id($id = NULL){
        $this->db->select("users.*")
        ->from("users")
        ->join("user_groups","user_groups.user_id = users.id","LEFT")
        ->join("groups","groups.id = user_groups.group_id","LEFT")
        ->where("groups.name","society");
        if($this->session->userdata("group") == "admin"){
            $q = $this->db->get();
        }else if($this->session->userdata("group") == "dairy"){
            $this->db->where("users.dairy_id",$this->session->userdata("id"));
            $q = $this->db->where("users.id", $id)
            ->get();
        }
        
        if($q->num_rows() > 0){
            return $q->row();
        }
        return FALSE;
    }
    
    function edit_society($data = array(), $id = NULL){
        $this->db->where('id',$id);
        if($this->db->update("users", $data)){
            return TRUE;
        }
        return FALSE;
    }
    
    function change_status($id = NULL){
        //UPDATE machines SET `status` = NOT `status` WHERE id = '16'
        if($this->db->query("UPDATE users SET `status` = NOT `status` WHERE id = '$id'")){
            return TRUE;
        }
        return FALSE;
    }

    public function get_society_id($api_key = NULL)
    {
        $q = $this->db->get_where("users", array("token"=>$api_key));
        if($q->num_rows() > 0)
        {
            return $q->row()->id;
        }
        return FALSE;
    }
}

/** application/Models/Society_model.php */