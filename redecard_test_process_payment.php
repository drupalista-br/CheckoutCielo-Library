<?php
    include_once('brazilcards-lib/BrazilCards.class.php');
   
    /**
     * Arguments for instantiating a payment Object
     */
    $arguments = array('is_test'  => TRUE,     //Default is FALSE. When set to TRUE then the library will perform on cielo's webservice sandBox
                       
                        /** These are the credentials for the production enviroment. They are ignored when 'is_test' == TRUE **/
                       'membership' => array('filiacao'     => '156984429',     //Merchant's membership number at the operator
                                             'userName'     => 'blahblahblah',
                                             'password'     => 'blahblahblah',
                                            ),
                       
                        //purchase order details
                       'order'      => array('pedido'      => rand(1000000, 9999999),       
                                             'TotalAmount' => '0.01',  //no thousand separator
                                             ),
                       //payment details
                       'payment' => array('CardNumber'        => '4012001037141112',
                                          'ExpirationYear'    => '2015',
                                          'ExpirationMonth'   => '08',
                                          'CVC'               => 973,
                                          'CardHolder'        => 'Francisco Luz',    //Card Holder Name
                                          
                                          'Installments'      => 1,
                                          //'Creditor'        => 2, //when 'Installments' > 1 then this value gotta be either 2 == merchant is the creditor or 3 == redecard is the creditor
                                                                    //Default == 3

                                          //'Authenticate'   => TRUE,  //Default == FALSE, When TRUE you will have to send the
                                                                       //AVS (Address Verification System) data below
                                                                       
                                          /* AVS available only for mastercard and diners issued in Brazil */
                                          'AVS_CPF'          => '',  //Brazilian Tax Id Number
                                          'AVS_StreetName'   => '',
                                          'AVS_StreetNumber' => '',
                                          'AVS_Complement'   => '',
                                          'AVS_ZipCode1'     => '',  //first 5 digits of the zip code
                                          'AVS_ZipCode2'     => '',  //the last 3 digits of the zip code
                                       

                                          //'AuthorizationType' => 73,    //set it to 73 for pre authorization
                                          //'AutoCapturer'      => FALSE, //Default == TRUE
                                         ),
                       );
    
    

    //instantiating a new object
    $obj = new RedeCard($arguments);
    
    echo '<pre>';
    $obj->authorize();
    
    print_r($obj);
    
/*
            [CODRET] => 0
            [MSGRET] => Autorizado+com+sucesso
            [NUMPEDIDO] => 7356846
            [DATA] => 20110602
            [NUMAUTOR] => 5693
            [NUMCV] => 6422
            [NUMAUTENT] => 7937
            [NUMSQN] => 1407
            [ORIGEM_BIN] => BRA
            [RESPAVS] => A
            [MSGAVS] => CPF+coincide,+CEP+e+endereÃ§o+nÃ£o+coincidem.
*/
?>