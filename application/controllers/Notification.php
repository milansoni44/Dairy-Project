<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Notification extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("notification_model");
	}
	
	/**
     * display all notification
     */
    function index()
	{
        $this->data['all_notification'] = $this->notification_model->get_all_notification();
        $this->load->view("common/header", $this->data);
        $this->load->view("notification/index", $this->data);
        $this->load->view("common/footer");
    }
}
?>