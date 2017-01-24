<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Customers
 *
 * @author Milan Soni
 */
class Customers extends MY_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->library("auth_lib");
        $this->load->library('table');
        $this->load->library('datatables');
        $this->load->model("customer_model");
        $this->load->model("machine_model");
        $this->load->model("society_model");
        $this->load->database();
        if(!$this->auth_lib->is_logged_in()){
            redirect("auth/login","refresh");
        }
    }

    function index() {
//        $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
        $data['customers'] = $this->customer_model->get_customer();
        if ($this->session->userdata("group") == "dairy") {
            $data['society'] = $this->society_model->get_society();
        }
        $this->load->view("common/header", $this->data);
        $this->load->view("customers/index", $data);
        $this->load->view("common/footer");
    }

    function society_index() {
        if ($this->session->userdata("group") == "admin" || $this->session->userdata("group") == "society") {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }

        if ($this->input->post()) {
//            $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $id = $this->input->post("society");
            $data['customers'] = $this->customer_model->get_society_customer($id);
            if ($this->session->userdata("group") == "dairy") {
                $data['society'] = $this->society_model->get_society();
            }
            $this->load->view("common/header", $this->data);
            $this->load->view("customers/society_index", $data);
            $this->load->view("common/footer");
        } else {
//            $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['customers'] = $this->customer_model->get_customer();
            if ($this->session->userdata("group") == "dairy") {
                $data['society'] = $this->society_model->get_society();
            }
            $this->load->view("common/header", $this->data);
            $this->load->view("customers/society_index", $data);
            $this->load->view("common/footer");
        }
    }

    function getDatatableAjax() {
        $this->datatables->select("c.mem_code")
                ->from("customers c")
                ->join("users u", "u.id = c.society_id", "LEFT");
        echo $this->datatables->generate();
    }

    function add() {
        if ($this->session->userdata("group") != "society") {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }

        if (isset($_POST['submit'])) {
//            print_r($_POST);exit;
            if (!$this->customer_model->check_exist($_POST['mobile'], "mobile")) {
                $this->session->set_flashdata("message1", "This mobile already exist");
                redirect("customers", "refresh");
            }
            if (!$this->customer_model->check_exist($_POST['adhar_no'], "adhar_no")) {
                $this->session->set_flashdata("message1", "This adhar number already exist");
                redirect("customers", "refresh");
            }
            // member code is not required
//            if(!$this->customer_model->check_exist($_POST['member_code'],"mem_code")){
//                $this->customer_model->update_expiry($_POST['member_code']);
//            }
            $member_data = array(
                "customer_name" => $_POST['member_name'],
                "mobile" => $_POST['mobile'],
                "adhar_no" => $_POST['adhar_no'],
                "mem_code" => $_POST['member_code'],
                "type" => $_POST['type'],
//                "society_id"=>$this->session->userdata("id"),
//                "machine_id"=>$_POST['machine'],
                "ac_no" => $_POST['ac_no'],
                "bank_name" => $_POST['bank_name'],
                "ifsc" => $_POST['ifsc'],
                "ac_type" => $_POST['ac_type'],
                "created_at" => date("Y-m-d"),
            );
//            print_r($member_data);exit;
        }

        if (!empty($member_data) && $this->customer_model->add_customer($member_data)) {
            $this->session->set_flashdata("success", "Member added successfully");
            redirect("customers", "refresh");
        } else {
            //$data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['machine'] = $this->machine_model->allocated_soc_machine($this->session->userdata("id"));
            $this->load->view("common/header", $this->data);
            $this->load->view("customers/add", $data);
            $this->load->view("common/footer");
        }
    }

    function edit($id = NULL) {
        if ($this->session->userdata("group") != "society") {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }

        if (isset($_POST['submit'])) {
            if (!$this->customer_model->check_exist($_POST['mobile'], "mobile", $id)) {
                $this->session->set_flashdata("message", "This mobile already exist");
                redirect("customers", "refresh");
            }
            if (!$this->customer_model->check_exist($_POST['adhar_no'], "adhar_no", $id)) {
                $this->session->set_flashdata("message", "This adhar number already exist");
                redirect("customers", "refresh");
            }
            if (!$this->customer_model->check_exist($_POST['member_code'], "mem_code", $id)) {
                $this->customer_model->update_expiry($_POST['member_code']);
            }
            $member_data = array(
                "customer_name" => $_POST['member_name'],
                "mobile" => $_POST['mobile'],
                "adhar_no" => $_POST['adhar_no'],
                "mem_code" => $_POST['member_code'],
                "type" => $_POST['type'],
//                "society_id"=>$this->session->userdata("id"),
//                "machine_id"=>$_POST['machine'],
                "ac_no" => $_POST['ac_no'],
                "bank_name" => $_POST['bank_name'],
                "ifsc" => $_POST['ifsc'],
                "ac_type" => $_POST['ac_type'],
                "created_at" => date("Y-m-d"),
            );
//            print_r($member_data);exit;
        }

        if (!empty($member_data) && $this->customer_model->edit_customer($member_data, $id)) {
            $this->session->set_flashdata("success", "Member updated successfully");
            redirect("customers", "refresh");
        } else {
            //$data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['machine'] = $this->machine_model->allocated_soc_machine($this->session->userdata("id"));
            $data['member'] = $this->customer_model->get_customer_by_id($id);
            $data['id'] = $id;
            $this->load->view("common/header", $this->data);
            $this->load->view("customers/edit", $data);
            $this->load->view("common/footer");
        }
    }

    function import() {
        if ($this->session->userdata("group") != "society") {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }

        if (isset($_POST['submit'])) {
            $machine_id = $_POST['machine'];
            $data_validate = array();
            $name = $_FILES['import_member']['name'];
            $tmp = $_FILES['import_member']['tmp_name'];

            $csv_file = $tmp;

            if (($handle = fopen($csv_file, "r")) !== FALSE) {
                fgetcsv($handle);
                $i = 2;
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $num = count($data);
                    $all_data[] = $data;
                    for ($c = 0; $c < $num; $c++) {
                        $col[$c] = $data[$c];
                    }
                    $col1 = $col[0];
                    $col2 = $col[1];
                    $col3 = $col[2];
                    $col4 = $col[3];
                    $col5 = $col[4];
//                    echo $col3;exit;
                    //check blank fields
                    if ($col1 == "" || $col2 == "" || $col3 == "" || $col4 == "" || $col4 == "") {
                        $data_validate[] = array("Error" => "Please fill all the fileds", "Line" => $i);
                        $i++;
                        continue;
                    }
                    $cid = $this->customer_model->check_exist($col4, "adhar_no");
                    if ($cid === FALSE) {
                        $customer_data = array(
                            "customer_name" => $col[1],
                            "mem_code" => $col[0],
                            "mobile" => $col[2],
                            "adhar_no" => $col[3],
                            "type" => $col[4],
                            "created_at" => date("Y-m-d"),
                        );
                        $this->customer_model->add_customer($customer_data, $machine_id);
                    }else{
                        if($this->customer_model->check_exist_customer_machine($cid, $machine_id)){
                            $data_validate[] = array("Error" => "Customer already exist", "Line" => $i);
                            $i++;
                            continue;
                        }else{
                            $cust_machine = array(
                                "cid"=>$cid,
                                "machine_id"=>$machine_id,
                                "society_id"=>$this->session->userdata("id")
                            );
                            $this->customer_model->insert_customer_machine($cust_machine);
                            $i++;
                            continue;
                        }
                    }
                    // code change on 24-01-2017 due to customer_machine
                    /*if ($col1 == "" || $col2 == "" || $col3 == "" || $col4 == "" || $col4 == "") {
                        $data_validate[] = array("Error" => "Please fill all the fileds", "Line" => $i);
                        $i++;
                        continue;
                    } else if ($this->customer_model->check_exist($col3, "mobile") === FALSE && $this->customer_model->check_exist($col4, "adhar_no") === FALSE) {
                        $data_validate[] = array("Error" => "Adhar no: $col3 and Adhar: $col4 fields already exist", "Line" => $i);
                        $i++;
                        continue;
                    } else if ($this->customer_model->check_exist($col3, "mobile") === FALSE && $this->customer_model->check_exist($col4, "adhar_no")) {
                        $data_validate[] = array("Error" => "Adhar: $col3 field already exist", "Line" => $i);
                        $i++;
                        continue;
                    } else if ($this->customer_model->check_exist($col3, "mobile") && $this->customer_model->check_exist($col4, "adhar_no") === FALSE) {
                        $data_validate[] = array("Error" => "Mobile: $col4 field already exist", "Line" => $i);
                        $i++;
                        continue;
                    } else if (!$this->customer_model->check_exist($col1, "mem_code")) {            // check if same member code then update expiry of previous member
                        if ($this->customer_model->update_expiry($col1)) {      // update expiry
                            $customer_data = array(
                                "customer_name" => $col[1],
                                "mem_code" => $col[0],
                                "mobile" => $col[2],
                                "adhar_no" => $col[3],
                                "type" => $col[4],
//                                "society_id"=>$this->session->userdata("id"),
//                                "machine_id"=>$_POST['machine'],
                                "created_at" => date("Y-m-d"),
                            );
                            $this->customer_model->add_customer($customer_data, $machine_id);
                        }
                        $i++;
                        continue;
                    } else {
                        $customer_data = array(
                            "customer_name" => $col[1],
                            "mem_code" => $col[0],
                            "mobile" => $col[2],
                            "adhar_no" => $col[3],
                            "type" => $col[4],
//                            "society_id"=>$this->session->userdata("id"),
//                            "machine_id"=>$_POST['machine'],
                            "created_at" => date("Y-m-d"),
                        );
                        $this->customer_model->add_customer($customer_data, $machine_id);
                        $i++;
                        continue;
                    }*/
                }
            }
//            exit;
            $this->session->set_flashdata("message", $data_validate);
            redirect("customers", "refresh");
        } else {
            //$data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['machine'] = $this->machine_model->allocated_soc_machine($this->session->userdata("id"));
//            $data['tmp'] = $this->customer_model->get_tmp_data();
            $this->load->view("common/header", $this->data);
            $this->load->view("customers/import", $data);
            $this->load->view("common/footer");
        }
    }

    function correct() {
        //$data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
        $data['tmp'] = $this->customer_model->get_tmpData();
        $this->load->view("common/header", $this->data);
        $this->load->view("customers/correct", $data);
        $this->load->view("common/footer");
    }

    function edit_correct($id = NULL) {
        if ($this->session->userdata("group") != "society") {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }

        if (isset($_POST['submit'])) {
            if (!$this->customer_model->check_exist($_POST['mobile'], "mobile")) {
                $this->session->set_flashdata("message", "This mobile already exist");
                redirect("customers", "refresh");
            }
            if (!$this->customer_model->check_exist($_POST['adhar_no'], "adhar_no")) {
                $this->session->set_flashdata("message", "This adhar number already exist");
                redirect("customers", "refresh");
            }
            if (!$this->customer_model->check_exist($_POST['member_code'], "mem_code")) {
                $this->customer_model->update_expiry($_POST['member_code']);
            }
            $member_data = array(
                "customer_name" => $_POST['member_name'],
                "mobile" => $_POST['mobile'],
                "adhar_no" => $_POST['adhar_no'],
                "mem_code" => $_POST['member_code'],
                "society_id" => $this->session->userdata("id"),
                "created_at" => date("Y-m-d"),
            );
//            print_r($member_data);exit;
        }

        if (!empty($member_data) && $this->customer_model->edit_Correctcustomer($member_data, $id)) {
            $this->session->set_flashdata("success", "Member added successfully");
            redirect("customers", "refresh");
        } else {
//            $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['machine'] = $this->machine_model->allocated_soc_machine($this->session->userdata("id"));
            $data['member'] = $this->customer_model->get_tmpCustomer_by_id($id);
            $data['id'] = $id;
            $this->load->view("common/header", $this->data);
            $this->load->view("customers/edit_correct", $data);
            $this->load->view("common/footer");
        }
    }

    function import_test() {
        if ($this->session->userdata("group") != "society") {
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }

        if (isset($_POST['submit'])) {
            $data_validate = array();
            $name = $_FILES['import_member']['name'];
            $tmp = $_FILES['import_member']['tmp_name'];

            $csv_file = $tmp;

            if (($handle = fopen($csv_file, "r")) !== FALSE) {
                fgetcsv($handle);
                $i = 2;
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $num = count($data);
                    $all_data[] = $data;
                    for ($c = 0; $c < $num; $c++) {
                        $col[$c] = $data[$c];
                    }
                    $col1 = $col[0];
                    $col2 = $col[1];
                    $col3 = $col[2];
                    $col4 = $col[3];
                    $col5 = $col[4];
//                    echo $col3;exit;
                    //check blank fields
                    if ($col1 == "" || $col2 == "" || $col3 == "" || $col4 == "" || $col4 == "") {
                        $data_validate[] = array("Error" => "Please fill all the fileds", "Line" => $i);
                        $i++;
                        continue;
                    } else if ($this->customer_model->check_exist($col3, "mobile") === FALSE && $this->customer_model->check_exist($col4, "adhar_no") === FALSE) {
                        $data_validate[] = array("Error" => "Mobile: $col3 and Adhar: $col4 fields already exist", "Line" => $i);
                        $i++;
                        continue;
                    } else if ($this->customer_model->check_exist($col3, "mobile") === FALSE && $this->customer_model->check_exist($col4, "adhar_no")) {
                        $data_validate[] = array("Error" => "Mobile: $col3 field already exist", "Line" => $i);
                        $i++;
                        continue;
                    } else if ($this->customer_model->check_exist($col3, "mobile") && $this->customer_model->check_exist($col4, "adhar_no") === FALSE) {
                        $data_validate[] = array("Error" => "Mobile: $col4 field already exist", "Line" => $i);
                        $i++;
                        continue;
                    } else if (!$this->customer_model->check_exist($col1, "mem_code")) {            // check if same member code then update expiry of previous member
                        if ($this->customer_model->update_expiry($col1)) {      // update expiry
                            $customer_data = array(
                                "customer_name" => $col[1],
                                "mem_code" => $col[0],
                                "mobile" => $col[2],
                                "adhar_no" => $col[3],
                                "type" => $col[4],
                                "society_id" => $this->session->userdata("id"),
                                "machine_id" => $_POST['machine'],
                                "created_at" => date("Y-m-d"),
                            );
                            $this->customer_model->batch_insert_customer($customer_data);
                        }
                        $i++;
                        continue;
                    } else {
                        $customer_data = array(
                            "customer_name" => $col[1],
                            "mem_code" => $col[0],
                            "mobile" => $col[2],
                            "adhar_no" => $col[3],
                            "type" => $col[4],
                            "society_id" => $this->session->userdata("id"),
                            "machine_id" => $_POST['machine'],
                            "created_at" => date("Y-m-d"),
                        );
                        $this->customer_model->batch_insert_customer($customer_data);
                        $i++;
                        continue;
                    }
                }
            }
//            exit;
            $this->session->set_flashdata("message", $data_validate);
            redirect("customers", "refresh");
        } else {
//            $data['notifications'] = $this->auth_lib->get_machines($this->session->userdata("group"), $this->session->userdata("id"));
            $data['machine'] = $this->machine_model->allocated_soc_machine($this->session->userdata("id"));
            $data['tmp'] = $this->customer_model->get_tmp_data();
            $this->load->view("common/header", $this->data);
            $this->load->view("customers/import", $data);
            $this->load->view("common/footer");
        }
    }
}

/** application/controllers/Customer.php */