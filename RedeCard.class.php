<?php
/**
 * Project:  Webservice Consumer on the Brazilian Credit Card Operators
 * File:     RedeCard.class.php
 *
 * This code is released under the Creative Commons CC BY-NC 3.0 
 * Visit http://creativecommons.org/licenses/by-nc/3.0/br/ for more information
 * on this licence.
 *
 * You are required to purchase a license permission for comercial use of this code.
 *
 * @file Main Class for Cielo's webservice consumer
 * @copyright 2011 Drupalista.com.br 
 * @author Francisco Luz <franciscoferreiraluz at yahoo dot com dot au>
 * @package BrazilCards
 * @version 1.0 Alpha
 * @license http://creativecommons.org/licenses/by-nc/3.0/br/ CC BY-NC 3.0 
 */

include_once(dirname(__FILE__).'/BrazilCards.class.php'); 

class RedeCard extends BrazilCards {
  private $server;

  public $authorization = array();

  public function setUp(){
    self::setProperties();
        
    // Webservice settings.
    // Define defaut values for both test and live services.
    $test = array(
      'url' => 'https://ecommerce.redecard.com.br/pos_virtual/wskomerci/cap_teste.asmx',
      'USR' => 'testews',
      'PWD' => 'testews',
    );
    
    $live = array('url'    => 'https://ecommerce.redecard.com.br/pos_virtual/wskomerci/cap.asmx',
                  );
    
    //apply values to ws property    
    $ws       = ($this->is_test)?'test':'live';
    $this->ws = ${$ws};
    
    //manual version
    $this->ws['manual_version'] = '2.5 Last Updated at Sep 20 2010';

    //connect with remote service
    try{
        $this->server = new SoapClient($this->ws['url'].'?wsdl');
    }catch(SoapFault $e){
        $this->setWarning(array('exception', $e));
    }
}

    public function authorize(){
        self::setParameters('authorize');
        self::callRemoteServer('GetAuthorized');
        
        //set TRANSORIG
        $this->response['TRANSACAO'] =  $this->parameters['Transacao'];
        
        //get receipt
        if($this->payment['AutoCapturer'] == 'S' && $this->response['CODRET'] == 0){
            self::getReceipt();    
        }
    }
    
    public function capture(){
        
      
      
        //get receipt
        if($this->response['CODRET'] == 0){
            self::getReceipt();    
        }  
    }
    
    public function capturePreAuthorize(){
        
        
        
        //get receipt
        if($this->response['CODRET'] == 0){
            self::getReceipt();    
        } 
    }

    public function followUp(){
        
        
    }
    
    public function voidTransaction(){
        
        
    }
    
    
    
    private function setProperties(){
        /** Set Default values for payment property **/
        $checkList = array('Authenticate'     => array('#default' => FALSE,
                                                      ),
                           'Creditor'         => array('#default' => 3,
                                                      ),
                           'AuthorizationType'=> array('#default' => '',
                                                      ),
                           'Installments'     => array('#default' => '',
                                                      ),                           
                           'AutoCapturer'     => array('#default' => TRUE,
                                                      ),
                           'CardNumber'       => array('#default' => '',
                                                      ),
                           'CVC'              => array('#default' => '',
                                                      ),
                           'ExpirationMonth'  => array('#default' => '',
                                                      ),
                           'ExpirationYear'   => array('#default' => '',
                                                      ),
                           'CardHolder'       => array('#default' => '',
                                                      ),
                          );
        

        foreach($checkList as $parameter => $value){
            if(!isset($this->payment[$parameter])){
                $this->payment[$parameter] = $value['#default'];
            }
        }
        /** set default for IATA membership code (airliners) **/
        if(!isset($this->membership['IATA'])){
            $this->membership['IATA'] = '';
        }
        
        /** set default for Distribuidor membership code (distributor outlet stores) **/
        if(!isset($this->membership['Distribuidor'])){
            $this->membership['Distribuidor'] = '';
        }
        
        /** Define value for 'Transacao' in parameters **/
        if($this->payment['AuthorizationType'] != 73){
            //this is NOT a pre authorization
            if($this->payment['Installments'] > 1){
                //the payment is on installments term
                if(empty($this->membership['IATA'])){
                    //this is NOT an airliner
                    if($this->payment['Creditor'] == 2){
                        //merchant is the creditor
                        $this->payment['AuthorizationType'] = '08';
                    }elseif($this->payment['Creditor'] == 3){
                        //redecard is the creditor
                        $this->payment['AuthorizationType'] = '06';
                    }  
                }else{
                    //this is an airliner
                    $this->payment['AuthorizationType'] = '40';
                }
            }elseif($this->payment['Installments'] == 1){
                //single full payment
                if(empty($this->membership['IATA'])){
                    //this is NOT an airliner
                    $this->payment['AuthorizationType'] = '04';
                }else{
                    //this is an airliner
                    $this->payment['AuthorizationType'] = '39';
                }
            }
        }
        //installments gotta be 00 for single payments
        if($this->payment['Installments'] == 1){
            $this->payment['Installments'] = '00';
        }
        
        //add a leading zero on installments        
        if(strlen($this->payment['Installments']) == 1){
            $this->payment['Installments'] = '0'.$this->payment['Installments'];
        }

        //format expiration year        
        if(strlen($this->payment['ExpirationYear']) == 4){
            //get only last 2 digits
            $this->payment['ExpirationYear'] = substr($this->payment['ExpirationYear'], 2, 3);
        }
        
        //format autocapture
        if($this->payment['AutoCapturer']){
            $this->payment['AutoCapturer'] = 'S';
        }else{
            $this->payment['AutoCapturer'] = '';
        }
    }
    
    private function setParameters($method){
        
        $this->parameters  =  array('Total'        => $this->order['TotalAmount'],
                                    'Transacao'    => $this->payment['AuthorizationType'],
                                    'Parcelas'     => $this->payment['Installments'],
                                    'Filiacao'     => $this->membership['filiacao'],
                                    'NumPedido'    => $this->order['pedido'],
                                    'Nrcartao'     => $this->payment['CardNumber'],
                                    'CVC2'         => $this->payment['CVC'],
                                    'Mes'          => $this->payment['ExpirationMonth'],
                                    'Ano'          => $this->payment['ExpirationYear'],
                                    'Portador'     => $this->payment['CardHolder'],
                                    'IATA'         => $this->membership['IATA'],
                                    'Distribuidor' => $this->membership['Distribuidor'],
                                    'Concentrador' => '',
                                    'TaxaEmbarque' => '',
                                    'Entrada'      => '',
                                    'Pax1'         => '',
                                    'Pax2'         => '',
                                    'Pax3'         => '',
                                    'Pax4'         => '',
                                    'Numdoc1'      => '',
                                    'Numdoc2'      => '',
                                    'Numdoc3'      => '',
                                    'Numdoc4'      => '',
                                    'conftxn'      => $this->payment['AutoCapturer'],
                                    'ADD_Data'     => '',
                                    'Data'         => '',
                                    'NumSqn'       => '',
                                    'NumCV'        => '',
                                    'NumAutor'     => '',
                                    'TransOrig'    => '',
                                    'Usr'          => $this->ws['USR'],
                                    'Pwd'          => $this->ws['PWD'],
                                    'Data_Inicial' => '',
                                    'Data_Final'   => '',
                                    'Tipo_Trx'     => '',
                                    'Status_Trx'   => '',
                                    'Servico_AVS'  => '',
                                   );
        
        
        /** Set Parameters Property **/
        switch($method){
            case 'authorize':
                $parameters =  array('Total', 'Transacao', 'Parcelas', 'Filiacao', 'NumPedido', 'Nrcartao',
                                     'CVC2', 'Mes', 'Ano', 'Portador', 'IATA', 'Distribuidor', 'Concentrador',
                                     'TaxaEmbarque', 'Entrada', 'Pax1', 'Pax2', 'Pax3', 'Pax4', 'Numdoc1',
                                     'Numdoc2', 'Numdoc3', 'Numdoc4', 'conftxn', 'ADD_Data',
                                     );
                
                if($this->payment['Authenticate']){
                    $parameters[] = 'CPF'; $parameters[] = 'Endereco'; $parameters[] = 'Num1';
                    $parameters[] = 'Complemento'; $parameters[] = 'Cep1'; $parameters[] = 'Cep2';
                
                    $this->parameters += array('CPF'         => $this->payment['AVS_CPF'],
                                               'Endereco'    => $this->payment['AVS_StreetName'],
                                               'Num1'        => $this->payment['AVS_StreetNumber'],
                                               'Complemento' => $this->payment['AVS_Complement'],
                                               'Cep1'        => $this->payment['AVS_ZipCode1'],
                                               'Cep2'        => $this->payment['AVS_ZipCode2'],
                                              );
                    
                    
                }

            break;
            case 'capture':
                //ConfirmTxn
                $parameters =  array('Data', 'NumSqn', 'NumCV', 'NumAutor', 'Parcelas', 'TransOrig',
                                     'Total', 'Filiacao', 'Distribuidor', 'NumPedido', 'Pax1', 'Pax2',
                                     'Pax3', 'Pax4', 'Numdoc1', 'Numdoc2', 'Numdoc3', 'Numdoc4',
                                    );
            break;
            case 'capturePreAuthorize':
                //ConfPreAuthorization
                $parameters =  array('Filiacao', 'Distribuidor', 'Total', 'Parcelas', 'Data', 'NumAutor', 'NumCV',
                                     'Concentrador', 'Usr', 'Pwd',
                                    );
            break;
            case 'voidPreAuthorization':
                $parameters =  array('Filiacao', 'Distribuidor', 'Total', 'Data', 'NumAutor', 'NumCV', 'Concentrador',
                                     'Usr', 'Pwd',
                                    );
            break;
            case 'voidConfPreAuthorization':
                $parameters =  array('Filiacao', 'Total', 'Parcelas', 'Data', 'NumAutor', 'NumCV', 'Concentrador', 'Usr',
                                     'Pwd',
                                    );
            break;
            case 'voidTransaction':
                $parameters =  array('Total', 'Filiacao', 'Data', 'NumCV', 'NumAutor', 'Concentrador', 'Usr', 'Pwd',
                                    );
            break;
            case 'salesSumm':
                $parameters =  array('Filiacao', 'Usr', 'Pwd',
                                    );
            break;
            case 'councilReport':
                $parameters =  array('Filiacao', 'Distribuidor', 'Data_Inicial', 'Data_Final', 'Tipo_Trx', 'Status_Trx',
                                     'Servico_AVS', 'Usr', 'Pwd',
                                    );
            break;
            case 'ConsFornDistrib':
                $parameters =  array('Filiacao', 'Usr', 'Pwd',
                                    );
            break;
        }

        echo '<pre>';
        print_r($parameters);
        //unset the not needed parameters
        foreach($this->parameters as $parameter => $value){
            if(!in_array($parameter, $parameters)){
                unset($this->parameters[$parameter]);
            }
        }
        
    }
    
    private function getReceipt(){
        $post = array('DATA'      => $this->response['DATA'],
                      'TRANSACAO' => 201,
                      'NUMAUTOR'  => $this->response['NUMAUTOR'],
                      'NUMCV'     => $this->response['NUMCV'],
                      'FILIACAO'  => $this->membership['filiacao'],
                      );
        
        $ch = curl_init('https://ecommerce.redecard.com.br/pos_virtual/cupom.asp');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $receipt = curl_exec($ch);
        curl_close($ch);

        if($receipt){
            $this->response['receipt'] = $receipt;
            
        }else{
            $this->setWarning(array('curl_error', curl_error($receipt)));
        }
    }
    
    private function callRemoteServer($method){
        switch($method){
            case 'GetAuthorized':
                if($this->payment['Authenticate']){
                    //call 'GetAuthorizedAVS'
                    $method .= 'AVS';
                }
            break;
        }
        
        if($this->is_test){
            $method .= 'Tst';
        }else{
            if(strstr($method, 'GetAuthorized')){
                include_once('cielo/cielo_xml_xsd.class.php');
                $valideServer = new cielo_xml_xsd();
                $valideServer->validateServer($this);
            }
            
            //check credentials
            if(!isset($this->ws['USR'])){
                $this->setWarning(array('USR', 'Webservice User Name is not set.'));
            }
            if(!isset($this->ws['PWD'])){
                $this->setWarning(array('PWD', 'Webservice User Password is not set.'));
            }
            
        }
        
        //call websevice method
        $response = $this->server->$method($this->parameters);
        
        //save response
        $method .= 'Result';
        $response       = new SimpleXMLElement($response->$method->any);
        $this->response = (array) $response; //new SimpleXMLElement($response->$method->any);
    }
    
}
?>
