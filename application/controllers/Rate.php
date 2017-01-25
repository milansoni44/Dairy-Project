<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of Rate
 *
 * @author Milan Soni
 */
class Rate extends MY_Controller{
    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->library("auth_lib");
        $this->load->library("session");
        $this->load->library("form_validation");
        $this->load->model("rate_model");
        $this->load->model("setting_model");
        $this->load->database();
        if(!$this->auth_lib->is_logged_in()){
            redirect("auth/login","refresh");
        }
    }
    
    function index(){
        if($this->session->userdata("group") == "admin"){
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        if($this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "society"){
//            if($this->rate_model->read_notification()){
//                $this->session->set_userdata("machine_notify",($this->session->userdata("machine_notify")-1));
//            }
            $data['bf_rate'] = $this->rate_model->get_bufallo_rate();
            $this->load->view("common/header", $this->data);
            $this->load->view("rate/index", $data);
            $this->load->view("common/footer");
        }
    }
    
    function import_bfat(){
        if($this->session->userdata("group") == "admin"){
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        if(isset($_POST['submit'])){
            $res_low = $this->setting_model->get_config("BUFFALO","FAT_LOW_LIMIT")->config_value;
            $res_high = $this->setting_model->get_config("BUFFALO","FAT_HIGH_LIMIT")->config_value;
    //        print_r($_FILES);exit;
            $ext = pathinfo($_FILES['import_bfat']['name'], PATHINFO_EXTENSION);
            if($ext != "csv"){
                $this->session->set_flashdata("message", "Only CSV file is accepted");
                redirect("rate/import_bfat", "refresh");
            }
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
            $this->load->view("common/header", $this->data);
            $this->load->view("rate/bfat");
            $this->load->view("common/footer");
        }
    }
    
    function export_bfat(){
        if($this->session->userdata("group") == "admin"){
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        $data = $this->rate_model->get_bfat();
        if(!empty($data)){
            $fp = fopen('php://output', 'w');
            if ($fp && $data) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="BFAT.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');
                fputcsv($fp, array("BUFFAT"));
                foreach($data as $rr){
                    fputcsv($fp, array($rr['Rate']));
                }
                die;
            }
        }else{
            $this->session->set_flashdata("message", "There is not data for bufalo fat");
            redirect("rate","refresh");
        }
    }
    
    function cfat(){
        if($this->session->userdata("group") == "admin"){
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        
        if($this->session->userdata("group") == "dairy" || $this->session->userdata("group") == "society"){
            $data['c_rate'] = $this->rate_model->get_cow_fatrate();
            $this->load->view("common/header", $this->data);
            $this->load->view("rate/cfat_index", $data);
            $this->load->view("common/footer");
        }
    }
    
    function import_cfat(){
        if($this->session->userdata("group") == "admin"){
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        if(isset($_POST['submit'])){
            $res_low = $this->setting_model->get_config("COW","FAT_LOW_LIMIT")->config_value;
            $res_high = $this->setting_model->get_config("COW","FAT_HIGH_LIMIT")->config_value;
    //        print_r($_FILES);exit;
            $ext = pathinfo($_FILES['import_cfat']['name'], PATHINFO_EXTENSION);
            if($ext != "csv"){
                $this->session->set_flashdata("message", "Only CSV file is accepted");
                redirect("rate/import_cfat", "refresh");
            }
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
            $this->load->view("common/header", $this->data);
            $this->load->view("rate/cfat");
            $this->load->view("common/footer");
        }
    }
    
    function export_cfat(){
        if($this->session->userdata("group") == "admin"){
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        $data = $this->rate_model->get_cfat();
        if(!empty($data)){
            $fp = fopen('php://output', 'w');
            if ($fp && $data) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="CFAT.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');
                fputcsv($fp, array("COWFAT"));
                foreach($data as $rr){
                    fputcsv($fp, array($rr['Rate']));
                }
                die;
            }
        }else{
            $this->session->set_flashdata("message", "There is no data for cow fat");
            redirect("rate/cfat", "refresh");
        }
    }
    
    function import_bfat_snf(){
        if($this->session->userdata("group") == "admin"){
            $this->session->set_flashdata("message", "Access Denied");
            redirect("/", "refresh");
        }
        
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $res_low = $this->setting_model->get_config("BUFFALO","SNF_LOW_LIMIT")->config_value;
            $res_high = $this->setting_model->get_config("BUFFALO","SNF_HIGH_LIMIT")->config_value;
            
            $csvFile = $_FILES['import_bfat']['tmp_name'];
            if (($getfile = fopen($csvFile, "r")) !== FALSE) {
            //    $data = fgetcsv($getfile, 1000, ",");
                $fat1 = $res_low;
                $fat2 = $res_high;
                $fat = $res_low;
            //    $fat = $row_high[3];
                $this->setting_model->delete_bf_data("buffalo_fat_snf", $this->session->userdata("id"));
                $f_start = $fat1 * 10;
                $f_end = $fat2 * 10;
                $row = 1;
                while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                    $data1[] = $data;
                }
//                echo "<pre>";
//                print_r($data1);exit;
                for($i = $f_start; $i <= $f_end; $i++){
                    $snf = $res_low;
                    for($j = $f_start; $j < count($data1[$i]); $j++){
                        $a[] = array(
                            "Fat"=>$fat1,
                            "Snf"=>$snf,
                            "Rate"=>  (number_format((float)$data1[$i][$j],3,'.','')/100),
                            "dairy_id"=>$this->session->userdata("id")
                        );
                        $snf = $snf + 0.1;
                    }
                    $fat1 = $fat1 + 0.1;
                }
                fclose($getfile);
                try{
                    $this->setting_model->insert_bfat_snf_data($a);
                    $this->session->set_flashdata("success", "Buffalo Fat SNF uploaded successfully.");
                    redirect("rate/bfat_snf", "refresh");
                } catch (Exception $ex) {
                    
                }
            }
        }else{
            $this->load->view("common/header", $this->data);
            $this->load->view("rate/bfat_snf");
            $this->load->view("common/footer");
        }
    }
    
    function bfat_snf(){
        $fat_arr = array();
        $array = array();
        if($this->session->userdata("group") == "dairy"){
            $id = $this->session->userdata("id");
        }else if($this->session->userdata("group") == "society"){
            $sid = $this->session->userdata("id");
            $query = $this->db->query("SELECT dairy_id FROM users WHERE id = '$sid'");
            $id = $query->row()->dairy_id;
        }
        $q = $this->db->query("SELECT DISTINCT(Fat) FROM `buffalo_fat_snf` WHERE dairy_id = '$id'");
        $fat = $q->result_array();
        if(!empty($fat)){
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
        }else{
            $data['fat'] = $fat_arr;
            $data['vals'] = $array;
        }
        $this->load->view("common/header", $this->data);
        $this->load->view("rate/bfat_snf_index",$data);
        $this->load->view("common/footer");
    }
    
    public function export_bsnf(){
        if($this->session->userdata("group") == "dairy"){
            $id = $this->session->userdata("id");
        }else if($this->session->userdata("group") == "society"){
            $id = $this->session->userdata("id");
        }
        $q = $this->db->query("SELECT DISTINCT(Fat) FROM `buffalo_fat_snf` WHERE dairy_id = '$id'");
        $fat = $q->result_array();
        if(!empty($fat)){
            $fat_arr = array("SNFTAB");
    //        echo "<pre>";
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
    //        echo "<pre>";
    //        print_r($array);exit;
            $fp = fopen('php://output', 'w');
            if ($fp && $array) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="SNF.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');
                fputcsv($fp, $fat_arr);
                foreach($array as $rr){
                    fputcsv($fp, $rr);
                }
                die;
            }
        }else{
            $this->session->set_flashdata("message", "There is no data in snf");
            redirect("rate/bfat_snf", "refresh");
        }
    }
    
    function cfat_snf(){
        $fat_arr = array();
        $array = array();
        if($this->session->userdata("group") == "dairy"){
            $id = $this->session->userdata("id");
        }else if($this->session->userdata("group") == "society"){
            $sid = $this->session->userdata("id");
            $query = $this->db->query("SELECT dairy_id FROM users WHERE id = '$sid'");
            $id = $query->row()->dairy_id;
        }
        $q = $this->db->query("SELECT DISTINCT(Fat) FROM `cow_fat_snf` WHERE dairy_id = '$id'");
        $fat = $q->result_array();
        if(!empty($fat)){
            $fat_arr = array("SNFTAB");

            foreach($fat as $row_f){
                array_push($fat_arr, $row_f['Fat']);
                $q_s = $this->db->query("SELECT Snf FROM `cow_fat_snf` WHERE dairy_id = '$id' AND Fat = ".$row_f['Fat']);
                $snf = $q_s->result_array();
                $i = 0;
                foreach($snf as $row_s){
                    $q_r = $this->db->query("SELECT Rate FROM `cow_fat_snf` WHERE dairy_id = '$id' AND Snf = ".$row_s['Snf']);
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
        }else{
            $data['fat'] = $fat_arr;
            $data['vals'] = $array;
        }
        $this->load->view("common/header", $this->data);
        $this->load->view("rate/cfat_snf_index", $data);
        $this->load->view("common/footer");
    }
    
    public function export_csnf(){
        if($this->session->userdata("group") == "dairy"){
            $id = $this->session->userdata("id");
        }else if($this->session->userdata("group") == "society"){
            $id = $this->session->userdata("id");
        }
        $q = $this->db->query("SELECT DISTINCT(Fat) FROM `cow_fat_snf` WHERE dairy_id = '$id'");
        $fat = $q->result_array();
        if(!empty($fat)){
            $fat_arr = array("SNFTAB");
    //        echo "<pre>";
            foreach($fat as $row_f){
                array_push($fat_arr, $row_f['Fat']);
                $q_s = $this->db->query("SELECT Snf FROM `cow_fat_snf` WHERE dairy_id = '$id' AND Fat = ".$row_f['Fat']);
                $snf = $q_s->result_array();
                $i = 0;
                foreach($snf as $row_s){
                    $q_r = $this->db->query("SELECT Rate FROM `cow_fat_snf` WHERE dairy_id = '$id' AND Snf = ".$row_s['Snf']);
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
            $fp = fopen('php://output', 'w');
            if ($fp && $array) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="SNF.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');
                fputcsv($fp, $fat_arr);
                foreach($array as $rr){
                    fputcsv($fp, $rr);
                }
                die;
            }
        }else{
            $this->session->set_flashdata("message", "There is no data in snf");
            redirect("rate/cfat_snf", "refresh");
        }
    }
    
    function cfat_clr(){
        $fat_arr = array();
        $array = array();
        if($this->session->userdata("group") == "dairy"){
            $id = $this->session->userdata("id");
        }else if($this->session->userdata("group") == "society"){
            $sid = $this->session->userdata("id");
            $query = $this->db->query("SELECT dairy_id FROM users WHERE id = '$sid'");
            $id = $query->row()->dairy_id;
        }
        $q = $this->db->query("SELECT DISTINCT(Fat) FROM `cow_fat_clr` WHERE dairy_id = '$id'");
        $fat = $q->result_array();
        if(!empty($fat)){
            $fat_arr = array("CLRTAB");

            foreach($fat as $row_f){
                array_push($fat_arr, $row_f['Fat']);
                $q_s = $this->db->query("SELECT Clr FROM `cow_fat_clr` WHERE dairy_id = '$id' AND Fat = ".$row_f['Fat']);
                $snf = $q_s->result_array();
                $i = 0;
                foreach($snf as $row_s){
                    $q_r = $this->db->query("SELECT Rate FROM `cow_fat_clr` WHERE dairy_id = '$id' AND Clr = ".$row_s['Clr']);
                    $rate = $q_r->result_array();
                    foreach($rate as $row_r){
                        $rr = array(
                            "Clr"=>$row_s['Clr'],
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
                if($rate['Clr'] != $Snf){
                    if($item != 0){
                        $key++;
                    }
                    $output[$key]['Clr'] =  $rate['Clr'];
                    $keyRate = 0;
                }
    //            $output[$key]['Rate'][$keyRate] = $rate['Rate'];
                $output[$key][$keyRate] = $rate['Rate'];
                $Snf = $rate['Clr'];
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
        }else{
            $data['fat'] = $fat_arr;
            $data['vals'] = $array;
        }
        $this->load->view("common/header", $this->data);
        $this->load->view("rate/cfat_clr_index", $data);
        $this->load->view("common/footer");
    }
    
    function export_cfatclr(){
        if($this->session->userdata("group") == "dairy"){
            $id = $this->session->userdata("id");
        }else if($this->session->userdata("group") == "society"){
            $id = $this->session->userdata("id");
        }
        $q = $this->db->query("SELECT DISTINCT(Fat) FROM `cow_fat_clr` WHERE dairy_id = '$id'");
        $fat = $q->result_array();
        if(!empty($fat)){
            $fat_arr = array("CLRTAB");
    //        echo "<pre>";
            foreach($fat as $row_f){
                array_push($fat_arr, $row_f['Fat']);
                $q_s = $this->db->query("SELECT Clr FROM `cow_fat_clr` WHERE dairy_id = '$id' AND Fat = ".$row_f['Fat']);
                $snf = $q_s->result_array();
                $i = 0;
                foreach($snf as $row_s){
                    $q_r = $this->db->query("SELECT Rate FROM `cow_fat_clr` WHERE dairy_id = '$id' AND Clr = ".$row_s['Clr']);
                    $rate = $q_r->result_array();
                    foreach($rate as $row_r){
                        $rr = array(
                            "Clr"=>$row_s['Clr'],
                            "Rate"=>$row_r['Rate']
                        );
                        $a[$i] = $rr;
                        $i++;
                    }
                }
            }

            $Clr = "";
            $output = array();
            $key = 0;
            foreach($a as $item=>$rate){
                if($rate['Clr'] != $Clr){
                    if($item != 0){
                        $key++;
                    }
                    $output[$key]['Clr'] =  $rate['Clr'];
                    $keyRate = 0;
                }
                $output[$key][$keyRate] = $rate['Rate'];
                $Clr = $rate['Clr'];
                $keyRate++;
            }
            // print the output
    //        echo "<pre>";
    //        print_r($output);
    //        echo "</pre>";exit;
            foreach($output as $result){
                $array[] = array_values($result);
            }
    //        echo "<pre>";
    //        print_r($array);exit;
            $fp = fopen('php://output', 'w');
            if ($fp && $array) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="CLR.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');
                fputcsv($fp, $fat_arr);
                foreach($array as $rr){
                    fputcsv($fp, $rr);
                }
                die;
            }
        }else{
            $this->session->set_flashdata("message", "There is no data in clr");
            redirect("rate/cfat_clr", "refresh");
        }
    }
    
    function bfat_clr(){
        $fat_arr = array();
        $array = array();
        if($this->session->userdata("group") == "dairy"){
            $id = $this->session->userdata("id");
        }else if($this->session->userdata("group") == "society"){
            $sid = $this->session->userdata("id");
            $query = $this->db->query("SELECT dairy_id FROM users WHERE id = '$sid'");
            $id = $query->row()->dairy_id;
        }
        $q = $this->db->query("SELECT DISTINCT(Fat) FROM `buffalo_fat_clr` WHERE dairy_id = '$id'");
        $fat = $q->result_array();
        if(!empty($fat)){
            $fat_arr = array("CLRTAB");

            foreach($fat as $row_f){
                array_push($fat_arr, $row_f['Fat']);
                $q_s = $this->db->query("SELECT Clr FROM `buffalo_fat_clr` WHERE dairy_id = '$id' AND Fat = ".$row_f['Fat']);
                $snf = $q_s->result_array();
                $i = 0;
                foreach($snf as $row_s){
                    $q_r = $this->db->query("SELECT Rate FROM `buffalo_fat_clr` WHERE dairy_id = '$id' AND Clr = ".$row_s['Clr']);
                    $rate = $q_r->result_array();
                    foreach($rate as $row_r){
                        $rr = array(
                            "Clr"=>$row_s['Clr'],
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
                if($rate['Clr'] != $Snf){
                    if($item != 0){
                        $key++;
                    }
                    $output[$key]['Clr'] =  $rate['Clr'];
                    $keyRate = 0;
                }
    //            $output[$key]['Rate'][$keyRate] = $rate['Rate'];
                $output[$key][$keyRate] = $rate['Rate'];
                $Snf = $rate['Clr'];
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
        }else{
            $data['fat'] = $fat_arr;
            $data['vals'] = $array;
        }
        $this->load->view("common/header", $this->data);
        $this->load->view("rate/bfat_clr_index", $data);
        $this->load->view("common/footer");
    }
    
    function import_bfat_clr(){
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $res_low = $this->setting_model->get_config("BUFFALO","CLR_LOW_LIMIT")->config_value;
            $res_high = $this->setting_model->get_config("BUFFALO","CLR_HIGH_LIMIT")->config_value;
            
            $csvFile = $_FILES['import_bfat']['tmp_name'];
            
            if (($getfile = fopen($csvFile, "r")) !== FALSE) {
            $fat_low = $res_low;
            $fat_high = $res_high;

            $f_start = $res_low * 10;
            $f_end = $res_high * 10;
//            $this->db2->truncate("Buffalo_Fat_Clr");
            $this->setting_model->delete_bf_data("buffalo_fat_clr", $this->session->userdata("id"));
            while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
                $data1[] = $data;
            }
            for($i = $f_start; $i < $f_end; $i++){
                    $clr = $res_low;
                    for($j = $f_start; $j < count($data1[$i]); $j++){
            //            echo "Row $i AND Column $j Value=".$data1[$i][$j];
            //            echo "<br>";
                        $arr[] = array(
                            "Fat"=>$fat_low,
                            "Clr"=>$clr,
                            "Rate"=>  (number_format((float)$data1[$i][$j],3,'.','')/100),
                            "dairy_id"=>$this->session->userdata("id")
                        );
                        $clr = $clr + 0.1;
                    }
                    $fat_low = $fat_low + 0.1;
                }
                fclose($getfile);
                try{
                    $this->setting_model->insert_bfat_clr_data($arr);
                    $this->session->set_flashdata("success", "Buffalo Fat Clr uploaded successfully.");
                    redirect("rate/bfat_clr", "refresh");
                } catch (Exception $ex) {
                    return json_encode(array("success"=>FALSE));
                    exit;
                }
            }
        }else{
            $this->load->view("common/header");
            $this->load->view("rate/bfat_clr_import");
            $this->load->view("common/footer");
        }
    }
    
    function export_bfatclr(){
        if($this->session->userdata("group") == "dairy"){
            $id = $this->session->userdata("id");
        }else if($this->session->userdata("group") == "society"){
            $id = $this->session->userdata("id");
        }
        $q = $this->db->query("SELECT DISTINCT(Fat) FROM `buffalo_fat_clr` WHERE dairy_id = '$id'");
        $fat = $q->result_array();
        if(!empty($fat)){
            $fat_arr = array("CLRTAB");
    //        echo "<pre>";
            foreach($fat as $row_f){
                array_push($fat_arr, $row_f['Fat']);
                $q_s = $this->db->query("SELECT Clr FROM `buffalo_fat_clr` WHERE dairy_id = '$id' AND Fat = ".$row_f['Fat']);
                $snf = $q_s->result_array();
                $i = 0;
                foreach($snf as $row_s){
                    $q_r = $this->db->query("SELECT Rate FROM `buffalo_fat_clr` WHERE dairy_id = '$id' AND Clr = ".$row_s['Clr']);
                    $rate = $q_r->result_array();
                    foreach($rate as $row_r){
                        $rr = array(
                            "Clr"=>$row_s['Clr'],
                            "Rate"=>$row_r['Rate']
                        );
                        $a[$i] = $rr;
                        $i++;
                    }
                }
            }

            $Clr = "";
            $output = array();
            $key = 0;
            foreach($a as $item=>$rate){
                if($rate['Clr'] != $Clr){
                    if($item != 0){
                        $key++;
                    }
                    $output[$key]['Clr'] =  $rate['Clr'];
                    $keyRate = 0;
                }
                $output[$key][$keyRate] = $rate['Rate'];
                $Clr = $rate['Clr'];
                $keyRate++;
            }
            // print the output
    //        echo "<pre>";
    //        print_r($output);
    //        echo "</pre>";exit;
            foreach($output as $result){
                $array[] = array_values($result);
            }
    //        echo "<pre>";
    //        print_r($array);exit;
            $fp = fopen('php://output', 'w');
            if ($fp && $array) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="CLR.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');
                fputcsv($fp, $fat_arr);
                foreach($array as $rr){
                    fputcsv($fp, $rr);
                }
                die;
            }
        }else{
            $this->session->set_flashdata("message", "There is no data in clr");
            redirect("rate/bfat_clr", "refresh");
        }
    }
}

/** application/controllers/Rate.php */