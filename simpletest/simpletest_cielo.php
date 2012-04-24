<?php
require_once('../../simpletest/autorun.php');
require_once('../../Cielo.class.php');

class TestOfCielo extends UnitTestCase {
  function testObjectCreation() {

  }

  function get_arguments() {
    
    // TODO: Incomplete.
    
    $arguments = array(
      // Default is FALSE. When set to TRUE then the library will perform on
      // cielo's webservice sandBox.
      'is_test' => TRUE,
      // These are the credentials for the production enviroment. They are ignored
      // when 'is_test' == TRUE.
      'membership' => array(
        // Merchant's membership number at the card operator.
        'filiacao' => 1006993068,
        // Token.
        'chave'    => 'blahblahblah',
      ),
      // Purchase order details.
      'order' => array(
        'pedido' => rand(1000000, 9999999),
        'TotalAmount' => '100.00',
      ),

      // Payment details.
      'payment' => array(
        'CardFlag' => $payment['CardFlag'],
        'Installments' => $payment['Installments'],
        'Creditor' => $payment['Creditor'],
        'CardType' => $payment['CardType'],
        'Authenticate' => $payment['Authenticate'],
        'AuthorizationType' => $payment['AuthorizationType'],
        'AutoCapturer'    => $payment['AutoCapturer'],
      ),
    );

    if(isset($_POST['card_number'])) {
      // See list of card numbers that can be used in the sandBox enviroment at
      // the end of this file.
      $arguments['payment']['CardNumber'] = $_POST['card_number'];
      $arguments['payment']['ExpirationYear']  = $_POST['expiration_year'];
      $arguments['payment']['ExpirationMonth'] = $_POST['expiration_month'];
      $arguments['payment']['CVC'] = $_POST['CVC'];
    } 
    
  }
    

}