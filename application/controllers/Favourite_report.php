<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Customers
 *
 * @author Abhay Bhalala & Milan Soni
 */
class Favourite_report extends MY_Controller
{
	public function __construct() {
        parent::__construct();
        $this->load->model("favourite_report_model");
        $this->load->model("notification_model");
        $this->load->model("society_model");
        $this->load->model("transaction_model");
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
		    $data = array(
		        "report_name"=>$this->input->post("report_name"),
		        "period"=>$this->input->post("period"),
		        "shift"=>$this->input->post("shift"),
		        "machine_type"=>$this->input->post("machine_type"),
		        "society"=>($this->session->userdata("group") == "dairy") ? implode(",", $this->input->post("society")) : NULL,
		        "favourite_report_id"=>$this->input->post("favourite_report_id"),
            );
            /*print "<pre>";
            var_dump($data);exit;*/
			$result = $this->favourite_report_model->FavouriteReportAddUpdate( $data );
            if( $result )
			{
				$this->session->set_flashdata("success","A Favourite report has been inserted successfully.");
			}
			else
			{
				$this->session->set_flashdata("success","An error in inserting favourite report.");
			}
			redirect("/",'location');
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
			redirect("/",'location');
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

			$this->data['favourite_report_id']          = $fav_report_info['id'];
			$this->data['report_name']                  = $fav_report_info['report_name'];
			$this->data['period']                       = $fav_report_info['period'];
			$this->data['shift']                        = $fav_report_info['shift'];
			$this->data['machine_type']                 = $fav_report_info['machine_type']       ;
			$this->data['user_id']                      = $fav_report_info['user_id'];
			$this->data['period_word']                  = $fav_report_info['period_word'];
			$this->data['shift_word']                   = $fav_report_info['shift_word'];
			$this->data['society']                      = $fav_report_info['society_id'];
		}
		else
		{
			$this->data['action'] = "add";
			
			$this->data['favourite_report_id']          = 0;
			$this->data['report_name']                  = '';
			$this->data['period']                       = '';
			$this->data['shift']                        = '';
			$this->data['machine_type']                 = '';
			$this->data['user_id']                      = '';
			$this->data['period_word']                  = '';
			$this->data['shift_word']                   = '';
            $this->data['society']                      = '';
		}

		$this->data['society_info'] = $this->society_model->get_society();
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
			redirect("/",'location');
		}
	}

    /**
     * @param null $id
     */
    public function run($id = NULL)
    {
        if(!$id){
            show_404();
        }
        $fav_report_info = $this->favourite_report_model->get_favourite_report($id);
        /*print "<pre>";
        print_r($fav_report_info);exit;*/
        /*Array
        (
            [id] => 1
            [report_name] => Silicon Dairy Report
            [period] => 1
            [shift] => All
            [machine_type] => USB
            [society_id] => 21,22
            [user_id] => 19
            [period_word] => Last 7 Days
            [shift_word] => All
        )*/
        if($this->session->userdata("group") == "society") {
            $data['transactions_cow'] = $this->transaction_model->custom_transactions_cow($fav_report_info);
            $data['transactions_buff'] = $this->transaction_model->custom_transactions_buff($fav_report_info);
        }

        if($this->session->userdata("group") == "dairy"){
            $data['transactions_cow'] = $this->transaction_model->custom_transactions_cow_summary($fav_report_info);
            $data['transactions_buff'] = $this->transaction_model->custom_transactions_buff_summary($fav_report_info);
        }
        $data['id'] = $fav_report_info['id'];
        $this->load->view("common/header", $this->data);
        $this->load->view("favourite_report/run", $data);
        $this->load->view("common/footer", $this->data);
    }

    public function download_cow($id = NULL){
        $transactions_cow = array();
        /*$transactions_buff = array();*/
        $fav_report_info = $this->favourite_report_model->get_favourite_report($id);
        $file_name = $fav_report_info['report_name'];
        if($this->session->userdata("group") == "society") {
            $transactions_cow = $this->transaction_model->custom_transactions_cow($fav_report_info);
        }else{
            $transactions_cow = $this->transaction_model->custom_transactions_cow_summary($fav_report_info);
        }


        if(!empty($transactions_cow))
        {
            /*print "<pre>";
            print_r($transactions_cow);exit;*/
            foreach($transactions_cow as $cow){
                $data_final[] = array_values($cow);
            }

            $fp = fopen('php://output', 'w');

            if ($fp && $transactions_cow) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="'.$file_name.'.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');
                if($this->session->userdata("group") == "society") {
                    fputcsv($fp, array("Customer", "FAT", "CLR", "SNF", "Litre", "Rate/Ltr", "Net Amount", "Date", "Shift"));
                }else{
                    fputcsv($fp, array("Customer", "FAT", "CLR", "SNF", "Litre", "Rate/Ltr", "Net Amount", "Shift"));
                }
                foreach($data_final as $rr){
                    fputcsv($fp, $rr);
                }
                die;
            }
        }else{
            $this->session->set_flashdata("message", "No Cow Data Found");
            redirect("favourite_report/run/".$id, "refresh");
        }
    }

    public function download_buff($id = NULL)
    {
        $transactions_buff = array();
        /*$transactions_buff = array();*/
        $fav_report_info = $this->favourite_report_model->get_favourite_report($id);
        $file_name = $fav_report_info['report_name'];
        if($this->session->userdata("group") == "society") {
            $transactions_buff = $this->transaction_model->custom_transactions_buff($fav_report_info);
        }else{
            $transactions_buff = $this->transaction_model->custom_transactions_buff_summary($fav_report_info);
        }


        if(!empty($transactions_buff))
        {
            /*print "<pre>";
            print_r($transactions_cow);exit;*/
            foreach($transactions_buff as $buff){
                $data_final[] = array_values($buff);
            }

            $fp = fopen('php://output', 'w');

            if ($fp && $transactions_buff) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="'.$file_name.'.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');
                if($this->session->userdata("group") == "society") {
                    fputcsv($fp, array("Customer", "FAT", "CLR", "SNF", "Litre", "Rate/Ltr", "Net Amount", "Date", "Shift"));
                }else{
                    fputcsv($fp, array("Customer", "FAT", "CLR", "SNF", "Litre", "Rate/Ltr", "Net Amount", "Shift"));
                }
                foreach($data_final as $rr){
                    fputcsv($fp, $rr);
                }
                die;
            }
        }else{
            $this->session->set_flashdata("message", "No Buffalo Data Found");
            redirect("favourite_report/run/".$id, "refresh");
        }
    }
}
?>