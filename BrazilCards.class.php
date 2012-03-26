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
 */

abstract class BrazilCards {
  /**
   * Holds all the warnings / errors generated by the classes that are part of
   * this library.
   */
  public $warnings = array();

  /**
   * Name of the card operator. i.e. Cielo or Redecard.
   */
  public $operator;
   
  public $membership = array();
    
  public $order = array();
    
  public $payment = array();
    
  /**
   * If no argument is sent saying otherwise, this property is defaulted to
   * FALSE.
   */
  public $is_test = FALSE;
    
  /**
   * Where the card operator class will set all the necessary data to
   * successfully connect to the remote server.
   */
  public $ws = array();
    
  /**
   * Holds all the arguments handled over by the application being integrated.
   */
  public $arguments = array();
    
  /**
   * Parameters that will be used by the webservice methods.
   */
  public $parameters = array();
    
  /**
   * Holds the last response from the server. Check also $warnings property to
   * find out if there is any error message or an exception.
   */
  public $response;
    
  public function __construct($arguments){
    // Assign the arguments sent through.
    $this->arguments = $arguments;
    
    foreach($arguments as $argument => $value){
      // Assign values to properties.
      $this->$argument = $value;
    }
    // Define card's operator name.
    $this->operator = get_class($this);
    
    $this->setUp();
  }
    
  /**
   * Set Warnings.
   * 
   * @param Array $message
   *   The Key holds the field name and Value holds the Message to be set or
   *   unset.
   * @param Boolean $action
   *   Sets or Unsets the message in the array stack.
   */    
  public function setWarning($message, $action = TRUE){
    if ($action) {
      $this->warnings[$message[0]] = $message[1];
    }
    else {
      unset($this->warnings[$message[0]]);
    }
  } 
}
?>
