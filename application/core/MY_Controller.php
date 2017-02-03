<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MY_Controller
 *
 * @author Intel
 */
class MY_Controller extends CI_Controller{
    //put your code here
    public $data;
    public $CI = NULL;
    public function __construct() {
        parent::__construct();
        $this->CI = & get_instance();
        $this->load->library("session");
        $this->load->model("machine_model");
        $this->load->library("auth_lib");
        $type = $this->session->userdata("group");
        $id = $this->session->userdata("id");
    //    $this->data['notifications'] = $this->auth_lib->get_machines($type, $id);
        //$this->data['notifications'] = $this->auth_lib->get_notification($type, $id);
        $this->auth_lib->get_notification($type, $id);
		
		//print "<pre>";var_dump( $this->data['notifications'] );exit;
		//print "<pre>";var_dump( $id );exit;
		
        if($type == "dairy" || $type == "admin"){
            $this->data['machine_count'] = $this->machine_model->totalCount();
        }
    }
	
	public function pagination($data = array())
	{
		$this->load->library('pagination');

		$config['base_url'] = base_url().'index.php/'.$data['pagination_caller'];
	//	$config['num_links'] = 2;
		$config['total_rows'] = $data['total_records'];
		$config['per_page'] = $data['limit'];

		$this->pagination->initialize($config);

		return $this->pagination->create_links();
	}

	public function dynamic_report_menu()
    {
        $id = $this->session->userdata("id");
        $select_qry = " SELECT id, report_name FROM `favourite_report` ";
        $where_all = " WHERE `user_id`=".$id;
        $order_by = " ORDER BY id";
        $limit = " LIMIT 5";

       $select_qry .= $where_all.$order_by.$limit;

        $result = $this->db->query( $select_qry );
        /*echo $this->db->last_query();exit;*/
        if( $result )
        {
            return $result->result_array();
        }
        return FALSE;
    }
}
