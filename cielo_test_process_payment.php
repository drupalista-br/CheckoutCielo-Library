<?php
    /**
     * We have to check if this script is being called by Cielo as a result of a browser redirection.
     * 
     * To do that we check for $_GET['order'] which is only present when cielo redirects the browser back to the store.
     */
    if(empty($_GET['order'])){
        /** We are still on checkout. Browser hasn't visited cielo as yet **/
        $checkOut = TRUE;
        
        /** Get purchase order details from check out processing **/
        $order = array('number'      => $_POST['po_#'],        //Order Number
                       'totalAmount' => $_POST['order_total'], //purchase order total amount
                       );

        $payment = array('CardFlag'        => $_POST['card_flag'], //Possible values are mastercard, elo or visa (lower case)
                         'Installments'    => $_POST['inst'],      //when set the value must be greater than 1 and 'InstallmentType' must be set to 2 or 3
                                                                   //Default == 1 (one full payment on credit)
                         'Creditor'        => $_POST['creditor'],  //when 'Installments' > 1 then this value gotta be either 2 == merchant is the creditor or 3 == cielo is the creditor
                                                                   //Default == 3
                         'CardType'        => $_POST['card_type'], //Possible values are A == Debit Card, 1 == Credit Card
                                                                   //Default == 1
                         //attribute settings
                         'AuthorizationType' => $_POST['authorization_type'], //Default == 1. Possible values are 0 == authentication only, 1 == authorize only if authenticaded, 2 == authorize either authenticated or not, 3 == skip authentication and go straight to authorization
                         'AutoCapturer'      => $_POST['auto_capture'],       //Default is 'false'. This replaces the method capture() as capturing will be automatically done at authorization phase
                         );
        
        $payment['Authenticate'] = '';
        if(isset($_POST['authenticate'])){
            $payment['Authenticate']  = $_POST['authenticate'];    //default is true. This will be ignored if card details are not collected by the merchant
        }
        
    }else{
        /** This is a redirection from cielo **/
        $checkOut = FALSE;
        
        //Retrieve purchase order from database. For the sake of this demonstration we have saved them into a cookie
        //query for a cookie (which is our table row) that has a name (row id) equal to the po number and then break the fields down into an array
        $queryDataBase = explode(':', $_COOKIE[$_GET['order']]);
        
        foreach($queryDataBase as $fieldName){
            //separete field name from field value
            $field                 = explode('=', $fieldName);
            $fieldValue[$field[0]] = $field[1];
        }
        
        $order = array('number'      => $_GET['order'],
                       'totalAmount' => $fieldValue['totalAmount'],
                       );
        
        $payment = array('CardFlag'        => $fieldValue['CardFlag'],
                         'Installments'    => $fieldValue['Installments'],
                         'Creditor'        => $fieldValue['Creditor'],
                         'CardType'        => $fieldValue['CardType'],
                         
                         'Authenticate'      => $fieldValue['Authenticate'], 
                         'AuthorizationType' => $fieldValue['AuthorizationType'],     
                         'AutoCapturer'      => $fieldValue['AutoCapturer'],
 
                         'tid'             => $fieldValue['tid'],
                         );
        
    }
    
    /**
     * Arguments for instantiating a payment Object
     */
    $arguments = array('operator' => 'Cielo',  //RedeCard is default
                       'is_test'  => TRUE,     //Default is FALSE. When set to TRUE then the library will perform on cielo's webservice sandBox
                       
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
                                          'Creditor'          => $payment['Creditor'],
                                          'CardType'          => $payment['CardType'],

                                          'Authenticate'      => $payment['Authenticate'],
                                          'AuthorizationType' => $payment['AuthorizationType'],
                                          'AutoCapturer'      => $payment['AutoCapturer'],
                                         ),
                       );
    /**
     * If the merchant's store does not collect its customers card details (number, expiration and sec. code) then they will be redirected
     * to cielo's website and asked to provide it there.
     * 
     * IMPORTANT: If card details handling are done at merchant's website then DO NOT save it into the database. It is considered very
     *            risky to keep credit card details stored in a remote web hosting server.
     *            Only collect the data and pass them through. Obviously you need a SSL certificate for data encrypting.
     */
    //check if card details were collected at merchant's store
    if(isset($_POST['card_number'])){
        //Again, do not save them into the database
        $arguments['payment']['CardNumber']     = $_POST['card_number'];     //see list of card numbers that can be used in the sandBox enviroment at the end of this file
        $arguments['payment']['CardExpiration'] = $_POST['card_expiration']; //yyyymm
        $arguments['payment']['CardSecCode']    = $_POST['card_code'];       //3 digits number
    }    



    /** INSTANTIATE A NEW PAYMENT OBJECT **/
    include_once('brazilcards-lib/BrazilCards.class.php');
    $Cielo = new BrazilCards($arguments);

    /**
     * When a redirection to cielo for card handling has to be done then the library sets a default url value for returning
     * back to the store.
     * This default url is the script that instantiates the payment object. In this study case it is
     * http://www.myStoreDomain.com/my_path/cielo_test_process_payment.php
     * 
     * You might want to change that by assigning a new value to request_data['return_url'] property. Like this:
     * 
     * $Cielo->request_data['return_url'] = 'http://www.myStoreDomain.com/my_path/my_return_script.php?order='.$order['number'];
     *
     * or if you want to set a file only for processing the webservice response then you dont need to inform the query string 'order'
     *   ie. $Cielo->request_data['return_url'] = 'http://www.myStoreDomain.com/my_path/my_return_script.php';
     * 
     */

            
    if($checkOut){
        /** Browser is still on Checkout phase **/

        /** REQUEST AUTHORIZATION **/
        $Cielo->authorize();

    }else{
        /** Browser is returning from authentication at cielo's webservice **/
    echo '<pre>';    
    print_r($Cielo);
        //retrieve data from database
        $Cielo->request_data['tid']    = $payment['tid'];
        
         /** DO A FOLLOW UP ON THE TRANSACTION TO FIND OUT IF HAS BEEN AUTHORIZED **/
         //we need to manually set the credentials only when it is a test enviroment because there is two sets of credentials
         //for testing two types of merchant account
        $Cielo->membership['filiacao'] = $fieldValue['filiacao'];
        $Cielo->membership['chave']    = $fieldValue['chave'];
        
        
        $Cielo->followUp();
        
	//Status code descriptions
	$status = array(0 => 'Transaction was created',
			1 => 'In progress',
			2 => 'Authenticated',
			3 => 'Not Authenticated',
			4 => 'Authorized or still to be Captured',
			5 => 'Not Authorized',
			6 => 'Captured',
			8 => 'Not Captured',
			9 => 'Voided (Cancelada)',
			10 => 'Being Authenticated',
		       );
        
        
        
        
        //
        
        
        //capturing
    }

    /** these values should be saved into the database. But for the sake of this demonstration we are putting them into a cookie **/
    $data = 'totalAmount='.        $order['totalAmount'].
            ':CardFlag='.          $payment['CardFlag'].
            ':Installments='.      $payment['Installments'].
            ':Creditor='.          $payment['Creditor'].
            ':CardType='.          $payment['CardType'].
            ':Authenticate='.      $payment['Authenticate'].
            ':AuthorizationType='. $payment['AuthorizationType'].
            ':AutoCapturer='.      $payment['AutoCapturer'].
            //webservice response data
            ':tid='.               $Cielo->response->tid.
            ':status='.            $Cielo->response->status.

            //in a real production enviroment you wont need to save filiacao and chave into the Purchase Order table.
            //we need to do it here because the test enviroment has two sets of credentials whereas a real merchant will have
            //only one set of credential. Later on when doing follow ups we will need to know which set of test credential had been used
            ':filiacao='. $Cielo->membership['filiacao'].
            ':chave='.    $Cielo->membership['chave'];

    //the cookie is going to be our database table row and the po number is gonna be the record id
    setcookie($order['number'], $data);  



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