<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
         
    public function __construct() {
        parent::__construct();
        $this->load->helper("url");
        $this->load->library("auth_lib");
        $this->load->library("session");
        $this->load->library('email');
        $this->load->model("dairy_model");
        $this->load->model("society_model");
        $this->load->model("machine_model");
        $this->load->model("transaction_model");
        $this->load->database();
        if(!$this->auth_lib->is_logged_in()){
            redirect("auth/login","refresh");
        }
    }
    
    public function index()
    {
		/* Start Morning & Evening Total Cow & Buffalo Litre */
		$data['morning'] = json_encode(array());
		$data['evening'] = json_encode(array());
		$morning_summary = $this->transaction_model->get_society_summary('M');
		if(!empty($morning_summary))
		{
			foreach($morning_summary as $m_row)
			{	
				$data_morning[] = array("name"=>($m_row['type'] == "C") ? "Cow" : "Buffalo", "y"=> (float) $m_row['litre']);
			}
			$data['morning'] = json_encode($data_morning);
		}
		
		$evening_summary = $this->transaction_model->get_society_summary('E');
		if(!empty($evening_summary))
		{
			foreach($evening_summary as $e_row)
			{	
				$data_evening[] = array("name"=>($e_row['type'] == "C") ? "Cow" : "Buffalo" , "y"=> (float) $e_row['litre']);
			}
			$data['evening'] = json_encode($data_evening);
		}
		/* END Morning & Evening Total Cow & Buffalo Litre */
		
		/* Start Monthly milk collection date wise */
		$date = date('m');
		for($i = 1; $i<= date('t'); $i++)
		{
			$date_arr[] = date('Y-m').'-'.$i;
		}
		$morning_cow_month = $this->transaction_model->get_monthly_milk_collection('M', 'C');
		$morning_buff_month = $this->transaction_model->get_monthly_milk_collection('M', 'B');

        $eve_cow_month = $this->transaction_model->get_monthly_milk_collection('E', 'C');
        $eve_buff_month = $this->transaction_model->get_monthly_milk_collection('E', 'B');
		
		$data['monthly_cow_summary'] = $morning_cow_month;
		$data['monthly_buff_summary'] = $morning_buff_month;
        $data['monthly_cow_summary_eve'] = $eve_cow_month;
		$data['monthly_buff_summary_eve'] = $eve_buff_month;
		$data['month_dates'] = $date_arr;
		/* End Monthly milk collection date wise */
        $this->load->view('common/header', $this->data);
		if($this->session->userdata("group") == "society"){
			$this->load->view('welcome_message_society', $data);
		}else if($this->session->userdata("group") == "dairy"){
			$this->load->view('welcome_message_dairy', $data);
		}else{
			$this->load->view('welcome_message_admin', $data);
		}
        $this->load->view('common/footer');
    }
}

/** application/controllers/Welcome.php */