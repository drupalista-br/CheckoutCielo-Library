<?php
/**
 * Project:  Webservice Consumer on the Brazilian Credit Card Operators
 * File:     BrazilCards.class.php
 *
 * This code is released under the Creative Commons CC BY-NC 3.0 
 * Visit http://creativecommons.org/licenses/by-nc/3.0/br/ for more information
 * on this licence.
 *
 * You are required to purchase a license permission for comercial use of this code.
 *
 * @file Main Class for Object Instantiation
 * @copyright 2011 Drupalista.com.br 
 * @author Francisco Luz <franciscoferreiraluz at yahoo dot com dot au>
 * @package BrazilCards
 * @version 1.0 Alpha
 * @license http://creativecommons.org/licenses/by-nc/3.0/br/ CC BY-NC 3.0 
 * 
 */

abstract class BrazilCards {
    //warnings
    public $warnings = array();
    
    //Card's operator name
    public $operator;
    
    public $membership = array();
    
    public $order = array();
    
    public $payment = array();
    
    //if no argument is sent saying otherwise, this property is defaulted to FALSE
    public $is_test = FALSE;
    
    //here, the operator class will set all the necessary data to successfully connect to the remote server
    public $ws = array();
    
    //all the arguments handled over by the application
    public $arguments = array();
    
    //parameters that will be used by the webservice methods
    public $parameters = array();
    
    //holds the last response from the server. Check also $warnings property to find out if there is any error message or an exception
    public $response;
    
    public function __construct($arguments){
        //assign the arguments sent through
        $this->arguments = $arguments;
        
        foreach($arguments as $argument => $value){
            //assign values to properties
            $this->$argument = $value;
        }
        //define card's operator name
        $this->operator = get_class($this);
        
        $this->setUp();
    }
    
    /**
     * Set Warnings
     * 
     * @param $message == (array) Key holds the field name and Value holds the Message to be set or unset
     * @param $action  == 1 sets, 0 unsets
     * 
     */    
    public function setWarning($message, $action = 1){
        if($action){
            $this->warnings[$message[0]] = $message[1];
        }else{
            unset($this->warnings[$message[0]]);
        }
    } 

}
?>
