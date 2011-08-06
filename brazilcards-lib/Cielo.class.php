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
include_once(dirname(__FILE__).'/BrazilCards.class.php'); 
include_once(dirname(__FILE__).'/cielo/cielo_xml_xsd.class.php');

class Cielo extends BrazilCards{
    
    //holds the xml object
    private $envelope;
    
    public function setUp(){
        
        /** Webservice settings **/
        //define defaut values for both test and live services
        $test = array('url'             => 'https://qasecommerce.cielo.com.br/servicos/ecommwsec.do',
                      //these are the membership number and token for when merchant is going to collect card detaisl from its customers
                      'merchant'        => '1006993069',
                      'merchant_chave'  => '25fbb99741c739dd84d7b06ec78c9bac718838630f30b112d033ce2e621b34f3',
                      
                      //these are the membership number and token for when customers are going to provide card detaisl at cielo's website
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
        $this->ws['curl_pubKey'] = 'VeriSignClass3PublicPrimaryCertificationAuthority-G5.crt';
        
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
                                              );
        
        /** url for returning from cielo after authentication has taken place **/
        $http = 'http://';
        $domainName = $_SERVER["SERVER_NAME"];
        
        if($_SERVER["SERVER_PORT"] == 443){
            //add a 's' between 'p' and ':'
            $http = substr_replace($http, 's', 4, 0);
        }elseif($_SERVER["SERVER_PORT"] <> 80){
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
     
    public function authorize(){

        /** Create a transaction at cielo's webservice */
        if($this->parameters['CardHandling']){
            //merchant is collecting card details and sending them through
            
            if($this->parameters['Authenticate']){
                //request a new transaction
                self::httprequest($this->envelope->requisicao_transacao());

                //redirect browser to cielo for authenticating the card holder
                header('Location: '.$this->response['url-autenticacao']);
                
                //once the browser is redirected back from cielo, the application will have to perform a follow up on this
                //transaction to find out if it has been authorized or not by calling $myObject->followUp().
            }else{
                //card holder wont be authenticated
                
                //request a new transaction
                self::httprequest($this->envelope->requisicao_tid());
                
                //request authorization
                $this->envelope->request_data['tid'] = $this->response['tid'];
                self::httprequest($this->envelope->requisicao_autorizacao_portador());
            }
        }else{
            //customers will be asked to provide their card details at cielo's website
            
            //request a new transaction
            self::httprequest($this->envelope->requisicao_transacao());

            //redirect browser to cielo for collecting card details and doing authentication
            header( 'Location: '.$this->response['url-autenticacao']);
        }
    }
    
    public function followUp(){
        //check if there is a tid available
        if(empty($this->envelope->request_data['tid'])){
            $this->setWarning(array('follow_up', 'Could not do the follow up because request_data[\'tid\'] property is not set.'));    
        }else{
            self::httprequest($this->envelope->requisicao_consulta());            
        }
    }

    public function capture(){
        self::capturePreAuthorize();
    }
    
    public function capturePreAuthorize($amount = ''){
        if(empty($amount)){
            //capture its total
            $this->envelope->request_data['captureAmount'] = $this->order['TotalAmount'];
        }elseif($amount > $this->order['TotalAmount']){
            //even when this check does not fail, the webservice still might deny it if remaining balance from previous
            //partial captures is less than the amount of this capturing attempt.
            
            //throw a warning. 
            $this->setWarning(array('capturePreAuthorize', 'Amount to be captured can\'t be greater than the amount previously authorized.'));
            break;
        }else{
            //partial capture or $amount represents 100% of the authorized amount
            $this->envelope->request_data['captureAmount'] = $amount;
        }
        
        //check if there is a tid available
        if(empty($this->envelope->request_data['tid'])){
            $this->setWarning(array('capturePreAuthorize', 'Could not do the capturing because request_data[\'tid\'] property is not set.'));    
        }else{
            self::httprequest($this->envelope->requisicao_captura());
        }
    }
    
    public function voidTransaction(){        
        //check if there is a tid available
        if(empty($this->envelope->request_data['tid'])){
            $this->setWarning(array('capturePreAuthorize', 'Could not do the voiding because request_data[\'tid\'] property is not set.'));    
        }else{
            self::httprequest($this->envelope->requisicao_cancelamento());
        }
    }

    
    /**
     * Helper function
     */
    private function setPaymentAttributes(){
        
        //save payment attributes on parameters property
        $this->parameters = $paymentAttributes = $this->arguments['payment'];
        
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

        foreach($checkList as $attribute => $settings){
            //if parameter was set but is not one of the expected values then we override it with its default value
            if(isset($paymentAttributes[$attribute]) && isset($settings['#expected']) && !in_array($paymentAttributes[$attribute], $settings['#expected'])){
                $this->parameters[$attribute]  = $settings['#default'];
            }

            //if paramenter was not set and there is a default value for it, then we set its default
            if(!isset($paymentAttributes[$attribute]) && isset($settings['#default'])){
                $this->parameters[$attribute]  = $settings['#default'];
            }
        }
        
        //make sure the boolean value `true' wont be represented by 1
        if($this->parameters['AutoCapturer'] == 1){
            $this->parameters['AutoCapturer'] = 'true';    
        }

        /** define InstallmentType **/
        //one single payment
        $this->parameters['InstallmentType'] = $this->parameters['CardType']; //either A (Debit Card) or 1 (Credit Card)
        //payment on installment term
        if($this->parameters['Installments'] > 1){
            //define who has the guts to be the creditor
            $this->parameters['InstallmentType'] = $this->parameters['Creditor'];  //2 (merchant) or 3 (cielo)
        }
        
        /** check if card details are being collected by the merchant **/
        $this->parameters['CardHandling'] = FALSE; //default
        if(!empty($paymentAttributes['CardNumber'])){
            $this->parameters['CardHandling'] = true;
            
            //TODO: validate card details
            
            //set default indicator
            $this->envelope->request_data['indicador'] = 1;
     
            if(empty($paymentAttributes['CVC'])){
                $this->envelope->request_data['indicador'] = 0;
            }elseif($paymentAttributes['CardFlag'] == 'mastercard'){
                $this->envelope->request_data['indicador'] = 1;
            }
        }
        
        if($this->is_test){
            /** this is a test environment so we need to define filiacao and chave values **/
            
            //default: merchant collects card details from its customers 
            $this->membership['filiacao'] = $this->ws['merchant'];
            $this->membership['chave']    = $this->ws['merchant_chave'];

            if(!$this->parameters['CardHandling']){
                //customers are asked to provide their card details at cielo's website
                $this->membership['filiacao'] = $this->ws['cielo'];
                $this->membership['chave']    = $this->ws['cielo_chave'];
            }
        }else{
            $this->envelope->validateServer($this);
        }
    }

    /**
     * Set transaction Id
     */
    public function setTid($tid){
        $this->envelope->request_data['tid'] = $tid;
    }
    
    /**
     * Set Currency
     */
    public function setCurrency($currency){
        $this->envelope->request_data['currency_code'] = $currency;
    }

    /**
     * Set Returning URL
     */
    public function setReturnUrl($url){
        $this->envelope->request_data['return_url'] = $url;
    }

    
    /**
     * Helper function
     *
     * Script portion extracted from Cielo's code sample with minor modifications
     *
     * It sends xml requests to cielo's webservice
     */    
    private function httprequest($xsd){
        $xsd = 'mensagem='.$xsd;
        
        $sessao_curl = curl_init();
        curl_setopt($sessao_curl, CURLOPT_URL, $this->ws['url']);
        
        curl_setopt($sessao_curl, CURLOPT_FAILONERROR, true);

        //  CURLOPT_SSL_VERIFYPEER
        //  verifica a validade do certificado
        curl_setopt($sessao_curl, CURLOPT_SSL_VERIFYPEER, $this->ws['curl_use_ssl']);
        //  CURLOPPT_SSL_VERIFYHOST
        //  verifica se a identidade do servidor bate com aquela informada no certificado
        curl_setopt($sessao_curl, CURLOPT_SSL_VERIFYHOST, 2);

        //  CURLOPT_SSL_CAINFO
        //  informa a localização do certificado para verificação com o peer
        curl_setopt($sessao_curl, CURLOPT_CAINFO, dirname(__FILE__).'/cielo/'.$this->ws['curl_pubKey']);
        curl_setopt($sessao_curl, CURLOPT_SSLVERSION, 3);

        //  CURLOPT_CONNECTTIMEOUT
        //  o tempo em segundos de espera para obter uma conexão
        curl_setopt($sessao_curl, CURLOPT_CONNECTTIMEOUT, 10);

        //  CURLOPT_TIMEOUT
        //  o tempo máximo em segundos de espera para a execução da requisição (curl_exec)
        curl_setopt($sessao_curl, CURLOPT_TIMEOUT, 40);

        //  CURLOPT_RETURNTRANSFER
        //  TRUE para curl_exec retornar uma string de resultado em caso de sucesso, ao
        //  invés de imprimir o resultado na tela. Retorna FALSE se há problemas na requisição
        curl_setopt($sessao_curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($sessao_curl, CURLOPT_POST, true);
        curl_setopt($sessao_curl, CURLOPT_POSTFIELDS, $xsd);

        $resultado = curl_exec($sessao_curl);
        
        curl_close($sessao_curl);

        if ($resultado){
            $this->response = simplexml_load_string($resultado);
            
            //covert the simplexml objects into arrays
            $this->response = (array) $this->response;
            
            foreach($this->response as $key => $value){
              if(is_object($value)){
                $this->response[$key] = (array) $value;
              }
            }
            
        }else{
            $this->setWarning(array('curl_error', '<pre>'.curl_error($sessao_curl).'</pre>'));
        }
    }
}
?>
