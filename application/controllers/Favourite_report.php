<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Favourite_report extends MY_Controller
{
	public function __construct() {
        parent::__construct();
        $this->load->model("favourite_report_model");
        $this->load->model("notification_model");
        $this->load->helper("form");
        $this->load->library("auth_lib");
        $this->load->library("session");
        if($this->session->userdata("group") == "admin")
		{
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
    }
	
	function index()
	{
		$this->data['all_favourite_report'] = $this->favourite_report_model->get_favourite_report();
		$this->load->view("common/header", $this->data);
		$this->load->view("favourite_report/index", $this->data);
		$this->load->view("common/footer");
	}
	
	public function insert()
	{
		if( $this->input->server("REQUEST_METHOD") === "POST" )
		{
			$result = $this->favourite_report_model->FavouriteReportAddUpdate( $this->input->post() );
			if( $result )
			{
				$this->session->set_flashdata("success","A Favourite report has been inserted successfully.");
			}
			else
			{
				$this->session->set_flashdata("success","An error in inserting favourite report.");
			}
			redirect("favourite_report",'location');
		}
	}
	
	public function update()
	{
		$favourite_report_id = $this->uri->segment(3);
		if( $this->input->server("REQUEST_METHOD") === "POST" )
		{
			$data = $this->input->post();
			$favourite_report_id = $this->input->post('favourite_report_id');
			$result = $this->favourite_report_model->FavouriteReportAddUpdate( $data, $favourite_report_id );
			if( $result )
			{
				$this->session->set_flashdata("success","A Favourite report has been updated successfully.");
			}
			else
			{
				$this->session->set_flashdata("success","An error in updating favourite report.");
			}
			redirect("favourite_report",'location');
		}
		else if( $favourite_report_id )
		{
			$tmp['favourite_report_id'] = $favourite_report_id;
			$this->add($tmp);
		}
	}
	
	public function add($tmp = array())
	{
		if(!empty($tmp))
		{
			$this->data['action'] = "edit";
			$fav_report_info = $this->favourite_report_model->get_favourite_report($tmp['favourite_report_id']);
			
			$this->data['favourite_report_id']          = $fav_report_info['id']         ;
			$this->data['report_name']                  = $fav_report_info['report_name'];
			$this->data['period']                       = $fav_report_info['period']     ;
			$this->data['shift']                        = $fav_report_info['shift']      ;
		//	$this->data['type']                         = $fav_report_info['type']       ;
			$this->data['user_id']                      = $fav_report_info['user_id']    ;
			$this->data['period_word']                  = $fav_report_info['period_word'];
			$this->data['shift_word']                   = $fav_report_info['shift_word'] ;
			$this->data['type_word']                    = $fav_report_info['type_word']  ;
		}
		else
		{
			$this->data['action'] = "add";
			
			$this->data['favourite_report_id']          = 0;
			$this->data['report_name']                  = '';
			$this->data['period']                       = '';
			$this->data['shift']                        = '';
		//	$this->data['type']                         = '';
			$this->data['user_id']                      = '';
			$this->data['period_word']                  = '';
			$this->data['shift_word']                   = '';
			$this->data['type_word']                    = '';
		}
		
		$this->load->view("common/header", $this->data);
		$this->load->view("favourite_report/add", $this->data);
		$this->load->view("common/footer");
	}
	
	public function delete()
	{
		$favourite_report_id = $this->uri->segment(3);
		if( $favourite_report_id )
		{
			$result = $this->favourite_report_model->delete_favourite_report($favourite_report_id);
			if( $result )
			{
				$this->session->set_flashdata("success","A Favourite report has been deleted successfully.");
			}
			else
			{
				$this->session->set_flashdata("success","An error in deleting favourite report.");
			}
			redirect("favourite_report",'location');
		}
	}
}
?>