<?php
/**
 * Project:  Webservice Consumer on Brazilian Credit Card Operators
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
 *  --------------------------------H I R I N G   M E ---------------------------------------------------
 *  
 * - I am available for freelancing jobs and casual employment.
 *   (I speak both english and portuguese fluently, portuguese being my native tongue)
 * - My services include, but not limited to, training sessions on Drupal for business enterprises,
 *   individuals and professionals in the IT / Webdevelopment industry;
 *    # Drupal consulting and module development;
 *    # Deploying and implementing solutions like:
 *        1. Open Public (http://openpublicapp.com), ideal for local goverment websites and
 *           goverment agencies in general.
 *        2. Open Publish (http://openpublishapp.com), this is a perfect fit for media websites such as
 *           Tv Networks, News and Magazines.
 *
 *   Contact me via
 *   E-mail: contato at drupalista dot com dot br
 *   Phone: +55 66 3521 9132 (Business hours, my time zone is Cuiaba, Brazil GMT -4)
 *   Mobile: +55 66 9245 5809
 *
 *   Francisco Luz
 *   July 2011
 */
include_once('Commons/Commons.class.php');

class BrazilCards extends Commons {
    //Defines which operators child class will handle transactions
    //Defaut == 'RedeCard'
    public $operator = 'RedeCard';
    
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
        foreach($arguments as $argument => $value){
            //assign the arguments sent through
            $this->arguments[$argument] = $value;
            
            //assign values to properties
            $this->$argument = $value;
        }
        
        //include operator's class
        $operator = $this->operator;
        include_once($operator.'.class.php');
        $operator::setUp();

    }
    
    public function authorize($parameters = NULL){
        $operator = $this->operator;       
        $operator::authorize($parameters);
    }
 
    public function capture(){
        
        
    } 
    
}
?>
