<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MY_Form_validation
 *
 * @author Intel
 */
class MY_Form_validation extends CI_Form_validation{
    //put your code here
    function __construct($rules = array()) {
        parent::__construct($rules);
    }
    
    function error_array() {
        if(count($this->_error_array) === 0){
            return FALSE;
        }else{
            return $this->_error_array;
        }
    }
}
