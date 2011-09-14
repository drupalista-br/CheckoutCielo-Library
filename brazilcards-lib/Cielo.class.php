<?php
/**
 * Project:  Webservice Consumer on the Brazilian Credit Card Operators
 * File:     Cielo.class.php
 *
 * This code is released under the Creative Commons CC BY-NC 3.0 
 * Visit http://creativecommons.org/licenses/by-nc/3.0/br/ for more information
 * on this licence.
 *
 * You are required to purchase a license permission for comercial use of this code.
 *
 * @file Class for Cielo's webservice consumer
 * @copyright 2011 Drupalista.com.br 
 * @author Francisco Luz <franciscoferreiraluz at yahoo dot com dot au>
 * @package BrazilCards
 * @version 1.0 Alpha
 * @license http://creativecommons.org/licenses/by-nc/3.0/br/ CC BY-NC 3.0 
 * 
 */
include_once(dirname(__FILE__).'/BrazilCards.class.php'); 
include_once(dirname(__FILE__).'/cielo/cielo_xml_xsd.class.php');

class Cielo extends BrazilCards{
    
    //holds the xml object
    private $envelope;
    
    public function setUp() {
        
        /** Webservice settings **/
        //define defaut values for both test and live services
        $test = array('url'             => 'https://qasecommerce.cielo.com.br/servicos/ecommwsec.do',
                      //these are the membership number and token for when merchant is going to collect card detaisl from its customers
                      'merchant'        => '1006993069',
                      'merchant_chave'  => '25fbb99741c739dd84d7b06ec78c9bac718838630f30b112d033ce2e621b34f3',
                      
                      //these are the membership number and token for when customers are going to provide card details at cielo's website
                      'cielo'           => '1001734898',
                      'cielo_chave'     => 'e84827130b9837473681c2787007da5914d6359947015a5cdb2b8843db0fa832',
                     );
        
        $live = array('url'         => 'https://ecommerce.cbmp.com.br/servicos/ecommwsec.do',
                      );
        //apply values to ws property
        $ws = ($this->is_test)?'test':'live';
        $this->ws = ${$ws};
        
        //manual version
        $this->ws['manual_version'] = '1.5.6 Last Updated in October 2010';
        
        //SSL Public key
        $this->ws['curl_pubKey'] = dirname(__FILE__).'/cielo/VeriSignClass3PublicPrimaryCertificationAuthority-G5.crt';
        
        //validate public key for curl requests
        $this->ws['curl_use_ssl'] = TRUE;
        
        //extends the envelope object
        $this->envelope = new cielo_xml_xsd();
        
        //Set default values for xsd envelopes
        $this->envelope->request_data = array('xsd_version'   => '1.1.0',              //cielo's xsd version
                                              'currency_code' => 986,                  //Currency code, defaulted to BRL
                                              'language_code' => 'PT',                 //language code
                                              'date_time'     => date("Y-m-d\TH:i:s"), //date and time
                                              'tid'           => '',
                                              //this determine if an redirection for authentication is made right away once
                                              //we get the value of $response['url-autenticacao']
                                              'autoRedirect'  => FALSE,
                                              );
        
        /** url for returning from cielo after authentication has taken place **/
        $http = 'http://';
        $domainName = $_SERVER["SERVER_NAME"];
        
        if ($_SERVER["SERVER_PORT"] == 443) {
            //add a 's' between 'p' and ':'
            $http = substr_replace($http, 's', 4, 0);
        }
        elseif ($_SERVER["SERVER_PORT"] <> 80) {
            //append the port number to the end of domain name
            $domainName .= ':'.$_SERVER["SERVER_PORT"];
        }
        //assemble returning url
        $this->envelope->request_data['return_url'] = $http.$domainName.$_SERVER["PHP_SELF"].'?order='.$this->order['pedido'];
        
        /** format po value **/
        //remove any dot or comma that it eventually might have
        $this->order['TotalAmount'] = str_replace(',','', $this->order['TotalAmount']);
        $this->order['TotalAmount'] = str_replace('.','', $this->order['TotalAmount']);
        
        //set up payment attributes
        self::setPaymentAttributes();
 
        //construct envelope object
        $this->envelope->setObject($this);
    }
    
    /**
     * Requests an authorization to the remote server and as a result of that
     * a new transaction is created.
     */
    public function authorize() {

        /** Create a tid */
        if ($this->parameters['CardHandling']) {
            //merchant is collecting card details and sending them through
            
            if ($this->parameters['Authenticate']) {
                //request a new transaction
                self::httprequest($this->envelope->requisicao_transacao());

                //the authentication url wont be present if the merchant has opted to
                //not authenticate the card hold so that's the why we gotta check it.
                if (isset($this->response['url-autenticacao']) && $this->envelope->request_data['autoRedirect']) {
                    //redirect browser to cielo for authenticating the card holder
                    header('Location: '.$this->response['url-autenticacao']);    
                }
                
                //once the browser is redirected back from cielo, the application will have to perform a follow up on this
                //transaction to find out if it has been authorized or not by calling $myObject->followUp().
            }
            else{
                //card holder wont be authenticated
                
                //request a new transaction
                self::httprequest($this->envelope->requisicao_tid());
                
                //request authorization
                $this->envelope->request_data['tid'] = $this->response['tid'];
                self::httprequest($this->envelope->requisicao_autorizacao_portador());
            }
        }
        else{
            //customers will be asked to provide their card details at cielo's website
            
            //request a new transaction
            self::httprequest($this->envelope->requisicao_transacao());

            if (isset($this->response['url-autenticacao']) && $this->envelope->request_data['autoRedirect']) {
                //redirect browser to cielo for collecting buyer's card details and performing authentication
                header( 'Location: '.$this->response['url-autenticacao']);
            }
        }
    }
    
    /**
     * Checks if there is a transaction Id available, if so then it requests details about
     * that transaction to the remote server.
     */
    public function followUp() {
        //check if there is a tid available
        if (empty($this->envelope->request_data['tid'])) {
            $this->setWarning(array('follow_up', 'Could not do the follow up because request_data[\'tid\'] property is not set.'));    
        }
        else{
            self::httprequest($this->envelope->requisicao_consulta());            
        }
    }
    /**
     * Alias of capturePreAuthorize() but it will always attempts capturing the
     * full amount previously authorized if there is any.
     *
     * To capture an amount smaller than the one previously authorized you
     * should then call $object->capturePreAuthorize($mySmallerAmount)
     */
    public function capture() {
        self::capturePreAuthorize();
    }
    /**
     * Requests the capturing of a transaction previously authorized.
     *
     * @param string $amount Defaut is empty which captures the full amount
     *                       available for that transaction. If a value is passed
     *                       then it will try to capture the value just passed
     *                       throught.
     */
    public function capturePreAuthorize($amount = '') {
        if (empty($amount)) {
            //capture its total
            $this->envelope->request_data['captureAmount'] = $this->order['TotalAmount'];
        }
        elseif ($amount > $this->order['TotalAmount']) {
            //even when this check does not fail, the webservice still might deny it if remaining balance from previous
            //partial captures is less than the amount of this capturing attempt.
            
            //throw a warning. 
            $this->setWarning(array('capturePreAuthorize', 'Amount to be captured can\'t be greater than the amount previously authorized.'));
            break;
        }
        else{
            //partial capture or $amount represents 100% of the authorized amount
            $this->envelope->request_data['captureAmount'] = $amount;
        }
        
        //check if there is a tid available
        if (empty($this->envelope->request_data['tid'])) {
            $this->setWarning(array('capturePreAuthorize', 'Could not do the capturing because request_data[\'tid\'] property is not set.'));    
        }
        else{
            self::httprequest($this->envelope->requisicao_captura());
        }
    }

    /**
     * Checks if there is a transaction Id available, if so then it requests a cancelation
     * of that transaction to the remote server.
     *
     * Voiding a transaction can only be done in the same day it was captured.
     * This is a restriction imposed by Cielo.
     */
    public function voidTransaction() {        
        //check if there is a tid available
        if (empty($this->envelope->request_data['tid'])) {
            $this->setWarning(array('capturePreAuthorize', 'Could not do the voiding because request_data[\'tid\'] property is not set.'));    
        }
        else{
            self::httprequest($this->envelope->requisicao_cancelamento());
        }
    }

    
    /**
     * Helper function
     */
    private function setPaymentAttributes() {
        
        //save payment attributes on parameters property
        $this->parameters = $paymentAttributes = $this->arguments['payment'];
        
        if(isset($this->parameters['CardType']) && $this->parameters['CardType'] == 'A'){
            //make sure authentication will always be switched on when card type is Debit
            $this->parameters['Authenticate'] == TRUE;
        }
        
        /**
         * Set Default values for parameters
         * #expected holds the list of valid values.
         *           if the value set doesn't match any of the expected values then its default will prevail
         **/
        $checkList = array('Installments'      => array('#default'  => 1,
                                                       ),
                           'Creditor'          => array('#default'  => 3,
                                                        '#expected' => array('2','3'),
                                                       ),
                           'CardType'          => array('#default'  => 1,
                                                        '#expected' => array('A','1'),
                                                       ),
                           'AutoCapturer'      => array('#default'  => 'true',
                                                        '#expected' => array('false', 'true',),
                                                       ),
                           'AuthorizationType' => array('#default'  => 2,
                                                        '#expected' => array('0','1','2','3'),
                                                       ),
                           'Authenticate'      => array('#default'  => true,
                                                        '#expected' => array('0', '1'),
                                                       ),
                           );

        foreach($checkList as $attribute => $settings) {
            //if parameter was set but is not one of the expected values then we override it with its default value
            if (isset($paymentAttributes[$attribute]) && isset($settings['#expected']) && !in_array($paymentAttributes[$attribute], $settings['#expected'])) {
                $this->parameters[$attribute]  = $settings['#default'];
            }

            //if paramenter was not set and there is a default value for it, then we set its default
            if (!isset($paymentAttributes[$attribute]) && isset($settings['#default'])) {
                $this->parameters[$attribute]  = $settings['#default'];
            }
        }
        
        // make sure the boolean value `true' wont be represented by 1
        if ($this->parameters['AutoCapturer'] === 1) {
            $this->parameters['AutoCapturer'] = 'true';    
        }

        /** define InstallmentType **/
        // one single payment
        $this->parameters['InstallmentType'] = $this->parameters['CardType']; //either A (Debit Card) or 1 (Credit Card)
        //payment on installment term
        if ($this->parameters['Installments'] > 1) {
            //define who has the guts to be the creditor
            $this->parameters['InstallmentType'] = $this->parameters['Creditor'];  //2 (merchant) or 3 (cielo)
        }
        
        /** check if card details are being collected by the merchant **/
        $this->parameters['CardHandling'] = FALSE; //default
        if (!empty($paymentAttributes['CardNumber'])) {
            $this->parameters['CardHandling'] = true;
            
            //set default indicator
            $this->envelope->request_data['indicador'] = 1;
     
            if (empty($paymentAttributes['CVC'])) {
                $this->envelope->request_data['indicador'] = 0;
            }
            elseif ($paymentAttributes['CardFlag'] == 'mastercard') {
                $this->envelope->request_data['indicador'] = 1;
            }
        }
        
        if ($this->is_test) {
            /** this is a test environment so we need to define values for filiacao and chave **/
            
            //default: merchant collects card details from its customers 
            $this->membership['filiacao'] = $this->ws['merchant'];
            $this->membership['chave']    = $this->ws['merchant_chave'];

            if (!$this->parameters['CardHandling']) {
                //customers are asked to provide their card details at cielo's website
                $this->membership['filiacao'] = $this->ws['cielo'];
                $this->membership['chave']    = $this->ws['cielo_chave'];
            }
        }
        else{
            $this->envelope->validateServer($this);
        }
    }

    /**
     * Set transaction Id
     * @param String $tid The transaction Id that came obtained from a provious server response
     */
    public function setTid($tid) {
        $this->envelope->request_data['tid'] = $tid;
    }
    
    /**
     * Set Currency
     * @param String $currency The ISO 4217 currency code with 3 digits number
     */
    public function setCurrency($currency) {
        $this->envelope->request_data['currency_code'] = $currency;
    }

    /**
     * Set Language Code
     *
     * @param String $lang Expected codes are: PT, EN or ES
     */
    public function setLanguage($lang) {
        $this->envelope->request_data['language_code'] = $lang;
    }

    /**
     * Set Returning URL
     * 
     * @param String $url The script url for concluding the payment processing
     *                    after returning from Cielo
     */
    public function setReturnUrl($url) {
        $this->envelope->request_data['return_url'] = $url;
    }

    /**
     * Set Auto Redirect
     * 
     * @param Boolean $value Determine whether or not the browser should be redirected to
     *                       Cielo right after a response in which redirection for
     *                       further processing is required.
     */
    public function setAutoRedirect($value) {
        $this->envelope->request_data['autoRedirect'] = $value;
    }
    
    /**
     * Set Location for SSL Certificate File
     * 
     * @param String $location The absolute location and file name of the SSL certificate file
     * 
     */
    public function setCertificateLocation($location) {
        $this->ws['curl_pubKey'] = $location;
    }
    
    /**
     * Helper function
     *
     * It makes xml request calls to cielo's webservice
     */    
    private function httprequest($xsd) {
        $xsd = 'mensagem='.$xsd;
        
        $sessao_curl = curl_init();
        curl_setopt($sessao_curl, CURLOPT_URL, $this->ws['url']);
        curl_setopt($sessao_curl, CURLOPT_FAILONERROR, true);
        curl_setopt($sessao_curl, CURLOPT_SSL_VERIFYPEER, $this->ws['curl_use_ssl']);
        curl_setopt($sessao_curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($sessao_curl, CURLOPT_CAINFO, $this->ws['curl_pubKey']);
        curl_setopt($sessao_curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($sessao_curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($sessao_curl, CURLOPT_TIMEOUT, 40);
        curl_setopt($sessao_curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($sessao_curl, CURLOPT_POST, true);
        curl_setopt($sessao_curl, CURLOPT_POSTFIELDS, $xsd);

        $resultado = curl_exec($sessao_curl);
        
        curl_close($sessao_curl);

        if ($resultado) {
            $this->response = simplexml_load_string($resultado);
            
            //convert the simplexml objects into arrays
            $this->response = (array) $this->response;
            
            foreach($this->response as $key => $value) {
              if (is_object($value)) {
                $this->response[$key] = (array) $value;
              }
            }
            
        }
        else{
            $this->setWarning(array('curl_error', '<pre>'.curl_error($sessao_curl).'</pre>'));
        }
    }
}
?>
