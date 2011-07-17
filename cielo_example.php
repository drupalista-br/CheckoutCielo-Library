<?php
    include_once('brazilcards-lib/BrazilCards.class.php');
    /**
     * We have to check if this script is being called by Cielo as a result of a browser redirection.
     * 
     * To do that we check for $_GET['order'] which is only present when cielo redirects the browser back to the store.
     *
     * By default the returning url is the script that instantiates the object. In this study case it will be example.php
     * You might want to change that by assigning a new value to request_data['return_url'] property.
     * 
     * ie. $myObject->request_data['return_url'] = 'path/my_return_script.php?order='.$order['number']; or
     *     $myObject->request_data['return_url'] = 'path/my_return_script.php';
     *
     * Do the above right after you instantiate the object and before calling $myObject->authorize();
     * 
     */
    if(empty($_GET['order'])){
        /** we are still on checkout **/
        $checkOut = TRUE;
        
        //these are the purchase order details obtained from the checkout process
        $order = array('number'      => 1234,    //Order Number
                       'totalAmount' => '10.10', //purchase order total amount
                       );
        
        //these are the purchase order PAYMENT details obtained from the checkout process
        $payment = array('CardFlag'        => 'mastercard', //Possible values are mastercard, elo or visa (lower case)
                         'Installments'    => 3,            //when set the value must be greater than 1 and 'InstallmentType' must be set to 2 or 3
                                                            //Default == 1
                       
                         'InstallmentType' => 2,            //Possible values are A == Debit, 1 == one full payment on credit, when 'Installments' > 1 then the value must be 2 == merchant is the creditor or 3 == cielo is the creditor
                                                            //Defaults are:  == 3 if 'Installments' > 1,  == 1 if 'Installments' == 1 and 'InstallmentType' != 1 or != A
                          /**
                           * If you dont collect the card details then your customers will be asked to provide them at cielo's website.
                           * This is the recommended practice as the card details won't transit from your website to cielo's website/webservice.
                           * 
                           * Although if you wish to have your customers interacting only with your website, then you should collect the card number, expiration date
                           * and secure code by setting these tree attributes as shown below.
                           * I should not have to say that you must have a SSL certificate but I will say anyway, you must have a SSL certificate :).
                           * 
                           * IMPORTANT: DO NOT save card details into your database. It is considered very risky to keep credit card
                           *            details stored in a remote web hosting server. Seriously dude this is f***ing no kidding.
                           *            Only collect the data and pass them through
                           */
                          'CardNumber'        => '4012001037141112', //see list of card numbers that can be used in the sandBox enviroment at the end of this file
                          'CardExpiration'    => '201506',           //yyyymm
                          'CardSecCode'       => '123',              //3 digits number
                          
                          //attribute settings
                          'Authenticate'      => false, //default is true. This will be ignored if card details are not collected / set
                          'AuthorizationType' => 1,     //Default == 1. Possible values are 0 == authentication only, 1 == authorize only if authenticaded, 2 == authorize either authenticated or not, 3 == skip authentication and go straight to authorization
                          //'AutoCapturer'      => true,  //Default is 'false', if set to 'true' then your application won't need to explicity call capture() as capturing will be done at authorization phase
                         );
        
    }else{
        /** this is a redirection from cielo after authentication **/
        $checkOut = FALSE;
        
        //you have to retrieve the purchase order and payment details from database. For the sake of this demonstration we have put them into a cookie
        $order = array('number'      => $_GET['order'],
                       'totalAmount' => $_COOKIE['totalAmount'], //no decimal separator. ie. 1010 represents 10.10 (ten real and ten cents)
                       );
        
        $payment = array('CardFlag'        => $_COOKIE['CardFlag'],
                         'Installments'    => $_COOKIE['Installments'],
                         'InstallmentType' => $_COOKIE['InstallmentType'],
 
                         'tid'             => $_COOKIE['tid'],
                         );
        
    }
    
    /**
     * Arguments for instantiating a payment Object
     */
    $arguments = array('operator' => 'Cielo',  //RedeCard is default
                       'is_test'  => TRUE,     //Default is FALSE. When set to TRUE then the library will perform on cielo's sandBox webservice
                       
                        /** These are the credentials for the production enviroment. They are ignored when 'is_test' == TRUE **/
                       'membership' => array('filiacao'     => 1006993068,     //Merchant's membership number at the operator
                                             'chave'        => 'blahblahblah', //token
                                            ),
                       
                        //purchase order details
                       'order'      => array('pedido'      => $order['number'],       
                                             'TotalAmount' => $order['totalAmount'],  
                                             ),
                       //payment details
                       'payment' => array('CardFlag'          => $payment['CardFlag'],
                                          'Installments'      => $payment['Installments'],
                                          'InstallmentType'   => $payment['InstallmentType'],

                                         ),
                       );

    /* check if optional attributes have been set */
    if(isset($payment['Authenticate'])){
        $arguments['payment']['Authenticate']  = $payment['Authenticate'];
    }
    if(isset($payment['AuthorizationType'])){
        $arguments['payment']['AuthorizationType']  = $payment['AuthorizationType'];
    }
    if(isset($payment['AutoCapturer'])){
        $arguments['payment']['AutoCapturer']  = $payment['AutoCapturer'];
    }


    if($checkOut){
        /** Browser is still on Checkout phase **/
        
        if(!empty($payment['CardNumber'])){
            //card details collected at the checkout. Again, do not save them into the database
            $arguments['payment']['CardNumber']     = $payment['CardNumber'];
            $arguments['payment']['CardExpiration'] = $payment['CardExpiration'];
            $arguments['payment']['CardSecCode']    = $payment['CardSecCode'];
        }
        
        //these values should be saved into the database. But for the sake of this demonstration we are putting them into a cookie
        setcookie('totalAmount',     $order['totalAmount']);
        
        //payment details
        setcookie('CardFlag',        $payment['CardFlag']);
        setcookie('Installments',    $payment['Installments']);
        setcookie('InstallmentType', $payment['InstallmentType']);

        //instantiation
        $Cielo = new BrazilCards($arguments);

        //request authorization
        $Cielo->authorize();

        /**
         * These are the possible scenarios from here on
         *
         * 1st scenario: You are collecting your customer's card details and have NOT set Authenticate to FALSE (default is TRUE).
         *               
         *               If this is the case then you are done with authorization and the script on 2nd or 3rd scenarios will never run because
         *               authorize() will redirect the browser to cielo for authentication which will then be automatic redirect back.
         */
       
        /* 2nd scenario: You are collection your customer's card details and have set Authenticate to FALSE.
         *
         *               If this is the case then you either can save the respon:
         *               1. 
            setcookie('tid',    $Cielo->response->tid);
            setcookie('pan',    $Cielo->response->pan);
            setcookie('status', $Cielo->response->status); //see list of codes values down at the end of this file
            
            header( 'Location: '.$Cielo->response->{'url-autenticacao'});
        
        //3rd scenario: You don't redirect the browser for authentication but rather save the authorize() response and then*/

    }else{
        /** Browser is returning from authentication at cielo's webservice **/
        
        //retrieve data from database
        $Cielo->request_data['tid']    = $_COOKIE['tid'];
        
        //do a follow up on the transaction to see if it has been authorized
        
        
        //capturing
    }

    echo '<pre>';    
    print_r($Cielo);
 
    


  /*Cartão com autenticação: 4012 0010 3714 1112 (visa)
Cartão sem autenticação: 4551 8700 0000 0183 (visa), 5453 0100 0006 6167
(mastercard), 6362 9700 0045 7013 (elo)
Data de validade: qualquer posterior ao corrente
Código de segurança: qualquer
Valor do pedido: para simular transação autorizada, use qualquer valor em que os dois
últimos dígitos sejam zeros. Do contrário, toda autorização será negada.

Status Nome
0 Criada
1 Em andamento
2 Autenticada
3 Não autenticada
4 Autorizada ou pendente de captura
5 Não autorizada
6 Capturada
8 Não capturada
9 Cancelada
10 Em Autenticação

*/


?>