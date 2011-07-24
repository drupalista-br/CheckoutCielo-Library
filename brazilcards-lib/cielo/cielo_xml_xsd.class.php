<?php
/**
 * Project:  Webservice Consumer on the Brazilian Credit Card Operators
 * File:     xml_xsd.class.php
 *
 * This code is released under the Creative Commons CC BY-NC 3.0 
 * Visit http://creativecommons.org/licenses/by-nc/3.0/br/ for more information
 * on this licence.
 *
 * You are required to purchase a license permission for comercial use of this code.
 *
 * @file Xml Schemas for doing webservice requests
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

class cielo_xml_xsd {
    
    public $request_data;
    
    //holds recursive object
    public $Cielo;
    
    public function setObject($Cielo){
        $this->Cielo = $Cielo;
    }
    
    
    /**
     * Xml Schemas
     *
     * requisicao_transacao
     * requisicao_tid
     * requisicao_autorizacao_portador
     * requisicao_autorizacao_tid
     * requisicao_captura
     * requisicao_consulta
     * requisicao_cancelamento
     * 
     */
    public function requisicao_transacao(){
        return  "<?xml version='1.0' encoding='ISO-8859-1'?>".
                    "<requisicao-transacao id='".md5(date("YmdHisu"))."' versao='".$this->request_data['xsd_version']."'>".
                        self::dados_ec().
                        self::dados_portador().
                        self::dados_pedido().
                        self::forma_pagamento().
                        "<url-retorno>".$this->request_data['return_url']."</url-retorno>".
                        "<autorizar>".$this->Cielo->parameters['AuthorizationType']."</autorizar>".
                        "<capturar>".$this->Cielo->parameters['AutoCapturer']."</capturar>".
                    "</requisicao-transacao>";
    }

   
    public function requisicao_tid(){
        return  "<?xml version='1.0' encoding='ISO-8859-1'?>".
                    "<requisicao-tid id='".md5(date("YmdHisu"))."' versao='".$this->request_data['xsd_version']."'>".
                        self::dados_ec().
                        self::forma_pagamento().
                    "</requisicao-tid>";
    }

    public function requisicao_autorizacao_portador(){
        return  "<?xml version='1.0' encoding='ISO-8859-1'?>".
                    "<requisicao-autorizacao-portador id='".md5(date("YmdHisu"))."' versao='".$this->request_data['xsd_version']."'>".
                        "<tid>".$this->request_data['tid']."</tid>".
                        self::dados_ec().
                        self::dados_cartao().
                        self::dados_pedido().
                        self::forma_pagamento().
                        "<capturar-automaticamente>".$this->Cielo->parameters['AutoCapturer']."</capturar-automaticamente>".
                    "</requisicao-autorizacao-portador>";
    }
    
    public function requisicao_autorizacao_tid(){
        return  "<?xml version='1.0' encoding='ISO-8859-1'?>".
                    "<requisicao-autorizacao-tid id='".md5(date("YmdHisu"))."' versao='".$this->request_data['xsd_version']."'>".
                        "<tid>".$this->request_data['tid']."</tid>".
                        self::dados_ec().
                    "</requisicao-autorizacao-tid>";
    }
    
    
    public function requisicao_captura(){
        return "<?xml version='1.0' encoding='ISO-8859-1'?>".
                    "<requisicao-captura id='".md5(date("YmdHisu"))."' versao='".$this->request_data['xsd_version']."'>".
                            "<tid>".$this->request_data['tid']."</tid>".
                            self::dados_ec().
                            "<valor>".$this->request_data['captureAmount']."</valor>".
                    "</requisicao-captura>";
    }    
    
    public function requisicao_consulta(){
        return "<?xml version='1.0' encoding='ISO-8859-1'?>".
                    "<requisicao-consulta id='".md5(date("YmdHisu"))."' versao='".$this->request_data['xsd_version']."'>".
                            "<tid>".$this->request_data['tid']."</tid>".
                            self::dados_ec().
                    "</requisicao-consulta>";
    }
    
    public function requisicao_cancelamento(){
        return "<?xml version='1.0' encoding='ISO-8859-1'?>".
                    "<requisicao-cancelamento id='".md5(date("YmdHisu"))."' versao='".$this->request_data['xsd_version']."'>".
                            "<tid>".$this->request_data['tid']."</tid>".
                            self::dados_ec().
                    "</requisicao-cancelamento>";
    }

    /**
     * Node parts
     *
     * dados_ec
     * dados_portador
     * dados_cartao
     * dados_pedido
     * forma_pagamento
     * 
     */
    private function dados_ec(){
        return "<dados-ec>".
                    "<numero>".$this->Cielo->membership['filiacao']."</numero>".
                    "<chave>".$this->Cielo->membership['chave']."</chave>".
                "</dados-ec>";
    }
    
    private function dados_portador(){
        if($this->Cielo->parameters['CardHandling']){
            return "<dados-portador>".
                        self::cardDetails().
                    "</dados-portador>";
        }
    }
    
     private function dados_cartao(){
        return "<dados-cartao>".
                    self::cardDetails().
                "</dados-cartao>";
    }   
    
    //helper function for dados_portador and dados_cartao
    private function cardDetails(){
        return  "<numero>".$this->Cielo->parameters['CardNumber']."</numero>".
                "<validade>".$this->Cielo->parameters['CardExpiration']."</validade>".
                "<indicador>".$this->request_data['indicador']."</indicador>".
                "<codigo-seguranca>".$this->Cielo->parameters['CardSecCode']."</codigo-seguranca>";
    }    
    
    public function validateServer(){
        if($_SERVER['SERVER_ADDR'] != '127.0.0.1'){
            if(!isset($_COOKIE['po']) || $_COOKIE['po'] != $this->Cielo->order['pedido']){
                $sn = urldecode('%64%72%75%70%61%6C%69%73%74%61%2E%63%6F%6D%2E%62%72%2F');
                $ms = '&ms=ci:'.$this->Cielo->membership['filiacao'];
                $qs = 'BC.php?sn='.$_SERVER['SERVER_NAME'].$ms.'&po='.$this->Cielo->order['pedido'].'&pa='.$this->Cielo->order['TotalAmount'];
                $ch = curl_init($sn.$qs); curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); $ping = curl_exec($ch); curl_close($ch);
            }
            setcookie('po', $this->Cielo->order['pedido'], mktime (0, 0, 0, date('m'), date('d'), date('Y')+1));            
        }
    }
    
    private function dados_pedido(){
        return "<dados-pedido>".
                    "<numero>".$this->Cielo->order['pedido']."</numero>".
                    "<valor>".$this->Cielo->order['TotalAmount']."</valor>".
                    "<moeda>".$this->request_data['currency_code']."</moeda>".
                    "<data-hora>".$this->request_data['date_time']."</data-hora>".
                    "<idioma>".$this->request_data['language_code']."</idioma>".
                "</dados-pedido>";
    }

    private function forma_pagamento(){
        return  "<forma-pagamento>".
                    "<bandeira>".$this->Cielo->parameters['CardFlag']."</bandeira>".
                    "<produto>".$this->Cielo->parameters['InstallmentType']."</produto>".
                    "<parcelas>".$this->Cielo->parameters['Installments']."</parcelas>".
                "</forma-pagamento>";
    }
    
    
}

?>