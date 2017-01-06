<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Rate
 *
 * @author Milan Soni
 */
class Rate extends CI_Controller{
    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->library("auth_lib");
        $this->load->library("session");
        $this->load->library("form_validation");
        $this->load->model("rate_model");
        $this->load->model("setting_model");
        $this->load->database();
    }
    
    function index(){
        if($this->session->userdata("group") == "dairy"){
            if($this->rate_model->read_notification()){
                $this->session->set_userdata("machine_notify",($this->session->userdata("machine_notify")-1));
            }
            $data['bf_rate'] = $this->rate_model->get_bufallo_rate();
            $this->load->view("common/header");
            $this->load->view("rate/index", $data);
            $this->load->view("common/footer");
        }else if($this->session->userdata("group") == "admin"){
            $data['bf_rate'] = $this->rate_model->get_bufallo_rate();
            $this->load->view("common/header");
            $this->load->view("rate/index", $data);
            $this->load->view("common/footer");
        }
    }
    
    function import_bfat(){
        if(isset($_POST['submit'])){
            $res_low = $this->setting_model->get_config("BUFFALO","FAT_LOW_LIMIT")->config_value;
            $res_high = $this->setting_model->get_config("BUFFALO","FAT_HIGH_LIMIT")->config_value;
    //        print_r($_FILES);exit;
            $csv_file = $_FILES['import_bfat']['tmp_name'];
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
                $data = fgetcsv($getfile, 1000, ",");
                $fat1 = $res_low;
                $i = 0;
                $this->setting_model->delete_bf_data("buffalo_fat",$this->session->userdata("id"));
                while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                    array_push($data, $fat1);
                    $result = $data;
            //        print_r($result);
                    if($i > $res_low && $i <= $res_high){
                        $buffalo_fat_data[] = array(
                            "Fat"=>$i,
                            "Rate"=>$result[0],
                            "dairy_id"=>$this->session->userdata("id")
                        );
    //                    Models::insert($cow_fat_data,"buffalo_fat");

                    }
                    $i += 0.1;
                    $fat1 += 0.1;
                }
                try{
                    $this->rate_model->insert_bfat_data($buffalo_fat_data);
                    $this->session->set_flashdata("success","Buffalo rate");
                    redirect("rate","refresh");
                } catch (Exception $ex) {
                    $this->session->set_flashdata("message","Please try again");
                    redirect("rate","refresh");
                }
            }else{
                $this->session->set_flashdata("message","Please try again");
                redirect("rate","refresh");
            }
        }else{
            $this->load->view("common/header");
            $this->load->view("rate/bfat");
            $this->load->view("common/footer");
        }
    }
    
    function cfat(){
        $data['c_rate'] = $this->rate_model->get_cow_fatrate();
        $this->load->view("common/header");
        $this->load->view("rate/cfat_index", $data);
        $this->load->view("common/footer");
    }
    
    function import_cfat(){
        if(isset($_POST['submit'])){
            $res_low = $this->setting_model->get_config("COW","FAT_LOW_LIMIT")->config_value;
            $res_high = $this->setting_model->get_config("COW","FAT_HIGH_LIMIT")->config_value;
    //        print_r($_FILES);exit;
            $csv_file = $_FILES['import_cfat']['tmp_name'];
            if (($getfile = fopen($csv_file, "r")) !== FALSE) {
                $data = fgetcsv($getfile, 1000, ",");
                $fat1 = $res_low;
                $i = 0;
    //            Models::truncate("buffalo_fat");
                $this->setting_model->delete_c_data("cow_fat",$this->session->userdata("id"));
                while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                    array_push($data, $fat1);
                    $result = $data;
            //        print_r($result);
                    if($i > $res_low && $i <= $res_high){
                        $cow_fat_data[] = array(
                            "Fat"=>$i,
                            "Rate"=>$result[0],
                            "dairy_id"=>$this->session->userdata("id"),
                        );
    //                    Models::insert($cow_fat_data,"buffalo_fat");

                    }
                    $i += 0.1;
                    $fat1 += 0.1;
                }
                try{
                    $this->rate_model->insert_cfat_data($cow_fat_data);
                    $this->session->set_flashdata("success","CFAT Updated successfully.");
                    redirect("rate/cfat", "refresh");
                } catch (Exception $ex) {
                    $this->session->set_flashdata("message","CFAT failed to update.");
                    redirect("rate/cfat", "refresh");
                }
            }else{
                $this->session->set_flashdata("message","CFAT failed to Update.");
                redirect("rate/cfat", "refresh");
            }
        }else{
            $this->load->view("common/header");
            $this->load->view("rate/cfat");
            $this->load->view("common/footer");
        }
    }
    
    function bfat_snf(){
        $id = $this->session->userdata("id");
        $q = $this->db->query("SELECT DISTINCT(Fat) FROM `buffalo_fat_snf` WHERE dairy_id = '$id'");
        $fat = $q->result_array();
        $fat_arr = array("SNFTAB");

        foreach($fat as $row_f){
            array_push($fat_arr, $row_f['Fat']);
            $q_s = $this->db->query("SELECT Snf FROM `buffalo_fat_snf` WHERE dairy_id = '$id' AND Fat = ".$row_f['Fat']);
            $snf = $q_s->result_array();
            $i = 0;
            foreach($snf as $row_s){
                $q_r = $this->db->query("SELECT Rate FROM `buffalo_fat_snf` WHERE dairy_id = '$id' AND Snf = ".$row_s['Snf']);
                $rate = $q_r->result_array();
                foreach($rate as $row_r){
                    $rr = array(
                        "Snf"=>$row_s['Snf'],
                        "Rate"=>$row_r['Rate']
                    );
                    $a[$i] = $rr;
                    $i++;
                }
            }
        }
        $Snf = "";
        $output = array();
        $key = 0;
        foreach($a as $item=>$rate){
            if($rate['Snf'] != $Snf){
                if($item != 0){
                    $key++;
                }
                $output[$key]['Snf'] =  $rate['Snf'];
                $keyRate = 0;
            }
//            $output[$key]['Rate'][$keyRate] = $rate['Rate'];
            $output[$key][$keyRate] = $rate['Rate'];
            $Snf = $rate['Snf'];
            $keyRate++;
        }
        // print the output
//        echo "<pre>";
//        print_r($output);
//        echo "</pre>";exit;
        foreach($output as $result){
            $array[] = array_values($result);
        }
        
        $data['fat'] = $fat_arr;
        $data['vals'] = $array;
        $this->load->view("common/header");
        $this->load->view("rate/bfat_snf_index",$data);
        $this->load->view("common/footer");
    }
    
    function cfat_snf(){
        $this->load->view("common/header");
        $this->load->view("common/footer");
    }
}
