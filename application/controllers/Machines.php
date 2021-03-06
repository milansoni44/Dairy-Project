<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Machines
 *
 * @author Milan Soni
 */
class Machines extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library("auth_lib");
        $this->load->library("session");
        $this->load->library("form_validation");
        $this->load->model("machine_model");
        $this->load->model("dairy_model");
        $this->load->model("society_model");
        $this->load->database();
        if (!$this->auth_lib->is_logged_in()) {
            redirect("auth/login", "refresh");
        }
    }

    /**
     * display all the machines
     */
    function index()
	{
        if ($this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "society") {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        $data['machines'] = $this->machine_model->get_machines();
        $this->load->view("common/header", $this->data);
        $this->load->view("machines/index", $data);
        $this->load->view("common/footer");
    }

    /**
     * add new machine
     */
    function add()
	{
        if ($this->session->userdata("group") != "admin")
		{
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        // validation for machines

        if ($this->input->server("REQUEST_METHOD") === "POST")
		{
			$skiped_machine = array();
            $i = 0;
			$added = 0;
            foreach ($_POST['machine_id'] as $row)
			{
                $machine_data = array(
                    "machine_id" => $row,
                    "machine_name" => $_POST['machine_name'][$i],
                    "machine_type" => $_POST['type'][$i],
                    "validity" => $_POST['validity'][$i],
                    "dairy_id" => $_POST['dairy_id'][$i],
                    "start_validity_from" => $_POST['start_validity_from'][$i]
                );
				
				$dup_res = $this->db->query(" SELECT COUNT(*) AS `total` FROM `machines` WHERE `machine_id` = '".$machine_data['machine_id']."'");
				
				if($dup_res->row('total') == 0)
				{
					if( $machine_data['start_validity_from'] == 1 )
					{
						// if machine validity starts from today
						$machine_validity = $this->get_validity( $machine_data['validity'] );
						$date = explode("-", $machine_validity);
						$machine_data['from_date'] = date("Y-m-d", strtotime($date[0]));
						$machine_data['to_date'] = date("Y-m-d", strtotime($date[1]));
					}
					
					$result = $this->machine_model->add_machine($machine_data);
					
					if($result)
					{
						$notify_msg = $_POST['machine_name'][$i] . ' (' . $row . ') successfully allocated to {dairy_name}.';
						
						$this->db->query("INSERT INTO `notification` SET 
											`message`='" . htmlentities($notify_msg, ENT_QUOTES) . "',
											`for_whom`=1,
											`dairy_id`='" . $_POST['dairy_id'][$i] . "'
										");
						$added++;
					}
					
				}
				else
				{
					$skiped_machine[] = $machine_data['machine_id'];
				}
                $i++;
            }
			
			if( !empty($skiped_machine) )
			{
				$this->session->set_flashdata("message", implode(",", $skiped_machine)." is/are already inserted machine id(s).");
			}
			
			if($added)
			{
				$this->session->set_flashdata("success", $added." machines data inserted successfully.");
			}
            redirect("machines", 'refresh');
        }
		else
		{
            $data['dairy_info'] = $this->dairy_model->get_dairy();
            $this->load->view("common/header", $this->data);
            $this->load->view("machines/add", $data);
            $this->load->view("common/footer");
        }
    }

    function edit($id = NULL, $msg = NULL) {
        if ($this->session->userdata("group") != "admin") {
            $this->session->set_falshdata("message", "Access Denied");
            redirect("/", "refresh");
        }

        if (isset($_POST['submit'])) {
//            echo $msg;exit;
//            echo "<pre>";
//            print_r($_POST);exit;
//            var_dump($_POST['validity']);
            if($_POST['validity'] != ''){
                $date_arr = explode('-', $_POST['validity']);
//            print_r($date_arr);exit;
                $from_date = date('Y-m-d', strtotime($date_arr[0]));
                $to_date = date('Y-m-d', strtotime($date_arr[1]));
            }else{
                $from_date = NULL;
                $to_date = NULL;
            }
            if(!$msg){
                $machine_data = array(
//                "machine_id" => $_POST['machine_id'],
                    "machine_name" => htmlentities($_POST['machine_name'], ENT_QUOTES),
                    "machine_type" => $_POST['type'],
                    "dairy_id" => $_POST['dairy_id'],
                    "from_date"=> $from_date,
                    "to_date"=> $to_date
                );
            }else{
                $machine_data = array(
                    "from_date"=> $from_date,
                    "to_date"=> $to_date
                );
            }

//            echo "<pre>";
//            print_r($machine_data);exit;
        }

        if (!empty($machine_data) && $this->machine_model->edit_machine($machine_data, $id)) {
            $this->session->set_flashdata("success", "Machines data updated successfully.");
            redirect("machines", 'refresh');
        } else {
//            $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['machine'] = $this->machine_model->get_machine_by_id($id);
            $data['msg'] = $msg;
            $data['dairy_info'] = $this->dairy_model->get_dairy();
            $data['id'] = $id;
            $this->load->view("common/header", $this->data);
            $this->load->view("machines/edit", $data);
            $this->load->view("common/footer");
        }
    }

    /**
     * display dairy associated machines
     */
    function allocate() {
        if ($this->session->userdata("group") == "dairy") {
//            $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['allocated_machines'] = $this->machine_model->allocated_dairy_machine();
//            echo "<pre>";
//            print_r($data['allocated_machines']);exit;
            $this->load->view("common/header", $this->data);
            $this->load->view("machines/allocated_machines", $data);
            $this->load->view("common/footer");
        } else {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
    }

    /**
     * display society associated machines
     */
    function allocated_to_society() {
        if ($this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "admin" || $this->session->userdata("group") == "society") {
//            $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['allocated_machines'] = $this->machine_model->mapped_society_machine();
            $this->load->view("common/header", $this->data);
            $this->load->view("machines/allocated_to_society", $data);
            $this->load->view("common/footer");
        } else {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
    }

    /**
     * add new machine to dairy
     */
    function add_allocate() {
        if ($this->session->userdata("group") == "society" || $this->session->userdata("group") == "dairy") {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        if (isset($_POST['submit'])) {
            $map_data = array(
                "dairy_id" => $this->input->post("dairy"),
                "machine_id" => $this->input->post("machine"),
            );
        }
//        print_r($map_data);exit;
        if (!empty($map_data) && $this->machine_model->map_dairy_machine($map_data)) {
            $this->session->set_flashdata("success", "Machine added to dairy successfully.");
            redirect("machines", 'refresh');
        } else {
//            $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['dairy'] = $this->dairy_model->get_dairy();
            $data['machines'] = $this->machine_model->not_allocated_machines();
            $this->load->view("common/header", $this->data);
            $this->load->view("machines/allocate", $data);
            $this->load->view("common/footer");
        }
    }

    /**
     * update dairy machine
     * @param type $id
     */
    function edit_allocate($id = NULL) {
        if ($this->session->userdata("group") == "admin" || $this->session->userdata("group") == "society") {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        if (isset($_POST['submit'])) {
            $map_data = array(
                "machine_name"=>htmlentities($_POST['machine_name'], ENT_QUOTES),
                "society_id"=>$_POST['society'],
            );
//            print_r($map_data);exit;
        }

        if (!empty($map_data) && $this->machine_model->edit_dairy_machine($map_data, $id)) {
            $this->session->set_flashdata("success", "Machine updated to dairy successfully.");
            redirect("machines/allocate", 'refresh');
        } else {
            $data['society'] = $this->society_model->get_society();
            $data['machines'] = $this->machine_model->allocated_dairy_machine($id);
            $data['mapped_machine'] = $this->machine_model->get_dairyMachine_by_id($id);
            $data['id'] = $id;
            $this->load->view("common/header", $this->data);
            $this->load->view("machines/edit_allocate", $data);
            $this->load->view("common/footer");
        }
    }

    /**
     * add new machine to society
     */
    function allocate_to_soc()
	{
        if ($this->session->userdata("group") == "society" || $this->session->userdata("group") == "admin") {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        if (isset($_POST['submit']))
		{
            // get machine validity
            $machine_info = $this->machine_model->get_machine_by_id($this->input->post("machine"));
			
			if( $machine_info->start_validity_from == 2 )
			{
				$validity = $this->machine_model->get_validity($this->input->post("machine"));
				$machine_validity = $this->get_validity($validity);
				$date = explode("-", $machine_validity);
				$from_date = date("Y-m-d", strtotime($date[0]));
				$to_date = date("Y-m-d", strtotime($date[1]));
				$map_data = array(
					"society_id" => $this->input->post("society"),
					// "machine_id" => $this->input->post("machine"),
					"from_date" => $from_date,
					"to_date" => $to_date,
				);
			}
			else
			{
				$map_data = array(
					"society_id" => $this->input->post("society"),
					// "machine_id" => $this->input->post("machine")
				);
			}
			
			
        }
//        print_r($map_data);exit;
        if (!empty($map_data) && $this->machine_model->map_society_machine($map_data, $this->input->post("machine"))) {
//            $cnt = $this->db->query("SELECT * FROM notification WHERE dairy_id = '".$map_data['society_id']."' AND is_read = '0'");
//            $this->session->set_userdata('machine_notify', $cnt);
            $this->session->set_flashdata("success", "Machine added to society successfully.");

            $machine_info = $this->machine_model->get_machine_by_id($this->input->post("machine"));

            $notify_msg = $machine_info->machine_id . ' successfully allocated to {society_name}.';
            $this->db->query("INSERT INTO `notification` SET 
									`message`='" . $notify_msg . "',
									`society_id`='" . $map_data['society_id'] . "'
							");

            redirect("machines/allocate", 'refresh');
        } else {
//            $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['society'] = $this->society_model->get_society();
            $data['machines'] = $this->machine_model->not_allocated_soc_machines();
            $this->load->view("common/header", $this->data);
            $this->load->view("machines/allocate_to_soc", $data);
            $this->load->view("common/footer");
        }
    }
	
	// calculate machine validity
    function get_validity($string)
	{
//      $string = $this->input->post("validity");
        $index = substr($string, -1); // returns "m or y"
        $num = substr($string, 0, -1); // returns "numbers"
        $date = date('Y-m-d');
        if ($index == "m") {
            $m = '+' . $num . ' months';
            return $validity = (date('m/d/Y') . " - " . date('m/d/Y', strtotime($m, strtotime(date('Y-m-d')))));
        } else if ($index == "y") {
            // format 01/17/2017 - 01/17/2017
            $y = '+' . $num . ' years';
            return $validity = (date('m/d/Y') . " - " . date('m/d/Y', strtotime($y)));
        }
    }

    /**
     * update society machine
     * @param type $id
     */
    function edit_society_machine($id = NULL) {
        if ($this->session->userdata("group") == "admin" || $this->session->userdata("group") == "society") {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        if (isset($_POST['submit'])) {
            $map_data = array(
                "society_id" => $this->input->post("society"),
//                "machine_id"=>$this->input->post("machine"),
            );
        }
//        print_r($map_data);exit;
        if (!empty($map_data) && $this->machine_model->edit_society_machine($map_data, $id)) {
            $this->session->set_flashdata("success", "Machine updated to society successfully.");
            redirect("machines/allocated_to_society", 'refresh');
        } else {
//            $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['id'] = $id;
            $data['society_machine'] = $this->machine_model->get_societyMachine_by_id($id);
            $data['society'] = $this->society_model->get_society();
            $data['machines'] = $this->machine_model->allocated_soc_machine($id);
            $this->load->view("common/header", $this->data);
            $this->load->view("machines/edit_society_machine", $data);
            $this->load->view("common/footer");
        }
    }
    
    function change_status($id = NULL){
        if($this->machine_model->change_status($id)){
            if($this->session->userdata("group") == "admin"){
                $this->session->set_flashdata("success", "Status changed successfully");
                redirect("machines", "refresh");
            }else{
                $this->session->set_flashdata("success", "Status changed successfully");
                redirect("machines/allocate", "refresh");
            }
        }
    }

    public function renew($id = NULL)
    {
//        echo $id;exit;
        $data['id'] = $id;
        $this->load->view("common/header", $this->data);
        $this->load->view("machines/renew", $data);
        $this->load->view("common/footer");
    }

}

/** application/controllers/Machines.php */