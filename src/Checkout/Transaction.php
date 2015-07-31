<?php

namespace CieloCheckout;
use Cielo\Merchant;

class Transaction {

  const
	STATUS_CODE_CREATED = 0,
	STATUS_CODE_IN_PROGRESS = 1,
	STATUS_CODE_AUTHENTICATED = 2,
	STATUS_CODE_NOT_AUTHENTICATED = 3,
	STATUS_CODE_AUTHORIZED = 4,
	STATUS_CODE_NOT_AUTHORIZED = 5,
	STATUS_CODE_CAPTURED = 6,
	STATUS_CODE_CANCELED = 9,

	ENDPOINT = 'https://cieloecommerce.cielo.com.br/api/public/v1/orders';

  public $response;

  private
	$Merchant,
	$Order;

  public function __construct(Merchant $Merchant, Order $Order) {
	$this->Merchant = $Merchant;
	$this->Order = $Order;
  }

  /**
   * Sends the order object over to Cielo and listen for a response.
   */
  public function request() {
	$merchant_key = $this->Merchant->getAffiliationKey();
	$curl = curl_init();

	curl_setopt($curl, CURLOPT_URL, self::ENDPOINT);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_POST, TRUE);
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->Order));
	curl_setopt($curl, CURLOPT_HTTPHEADER, [
	  "MerchantId: $merchant_key",
	  'Content-Type: application/json'
	]);

	$response = curl_exec($curl);
	curl_close($curl);

	$this->response = json_decode($response);
  }

  /**
   * @return Array
   *  List of all transaction statuses.
   *  Index = Status Code | Value = Status Name.
   */
  public function get_response_statuses() {
	return [
	  self::STATUS_CODE_CREATED => 'Transação Criada',
	  self::STATUS_CODE_IN_PROGRESS => 'Transação em Andamento',
	  self::STATUS_CODE_AUTHENTICATED => 'Transação Autenticada',
	  self::STATUS_CODE_NOT_AUTHENTICATED => 'Transação não Autenticada',
	  self::STATUS_CODE_AUTHORIZED => 'Transação Autorizada',
	  self::STATUS_CODE_NOT_AUTHORIZED => 'Transação não Autorizada',
	  self::STATUS_CODE_CAPTURED => 'Transação Capturada',
	  self::STATUS_CODE_CANCELED => 'Transação Cancelada',
	];
  }
}
