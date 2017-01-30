<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Favourite_report extends MY_Controller
{
	public function __construct() {
        parent::__construct();
        $this->load->model("notification_model");
        $this->load->library("auth_lib");
        $this->load->library("session");
        if($this->session->userdata("group") == "admin"){
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
    }

    /**
     * display all notification
     */
    function index() {
        $this->data['all_notification'] = $this->notification_model->get_all_notification();
        $notification_ids = array_column($this->data['all_notification'], 'id');

        $type = $this->session->userdata("group");
        $id = $this->session->userdata("id");
        $query_str = '';
        if ($type == "dairy") {
            $query_str = ", `dairy_id`=" . $id;
        } else if ($type == "society") {
            $query_str = ", `society_id`=" . $id;
        }

        for ($i = 0; $i < count($notification_ids); $i++) {
            $str = ($query_str != '') ? str_replace(",", '', $query_str) : "1";
            $result_total = $this->db->query("SELECT COUNT(*) AS `total` 
					FROM `notification_read` 
					WHERE `notification_id`=" . $notification_ids[$i] . " AND " . $str);
            if ($result_total->row('total') == 0) {
                $this->db->query("INSERT INTO `notification_read` SET 
													`notification_id`=" . $notification_ids[$i] . ",
													`is_read`=1
													" . $query_str . "
													");
            }
        }

        $type = $this->session->userdata("group");
        $id = $this->session->userdata("id");
        //    $this->data['notifications'] = $this->auth_lib->get_machines($type, $id);
        //$this->data['notifications'] = $this->auth_lib->get_notification($type, $id);
        $this->auth_lib->get_notification($type, $id);

        $this->load->view("common/header", $this->data);
        $this->load->view("favourite_report/index", $this->data);
        $this->load->view("common/footer");
    }
}
?>