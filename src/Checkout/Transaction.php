<?php

namespace CieloCheckout;

use
  Cielo\Merchant,
  Httpful\Request;

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
  public function request_new_transaction() {
    $merchant_key = $this->Merchant->getAffiliationKey();

    $response = Request::post(self::ENDPOINT)
      ->withoutStrictSsl()
      ->sendsJson()
      ->expectsJson()
      ->body(json_encode($this->Order))
      ->addHeader('MerchantId', $merchant_key)
      ->send();

    $this->response = $response->body;
  }

  /**
   * Redirects the customers to Cielo for completing their checkout payment.
   */
  public function redirect_to_cielo() {
    if (php_sapi_name() === 'cli') {
      throw new \Exception("Can not redirect to Cielo. You gotta run this script from a web browser.");
    }
    else {
      $this->response_validate();
      header("Location: {$this->response->settings->checkoutUrl}");
    }
  }

  /**
   * Checks if a new transaction response contains the valid data necessary
   * for redirecting the customer to Cielo.
   */
  private function response_validate() {
    if (isset($this->response->settings)) {
      $settings = $this->response->settings;
      // Check if merchant profile is valid.
      if (isset($settings->profile) && $settings->profile != 'CheckoutCielo') {
      throw new \Exception("Merchant profile at Cielo is invalid.");
      }
  
      if (!isset($settings->checkoutUrl) || empty($settings->checkoutUrl)) {
      throw new \Exception("Cielo's response hasn't a redirect URL in it.");
      }
    }
    else {
      if (isset($this->response->message)) {
      // Cielo has thrown an error.
      throw new \Exception("{$this->response->message} Check response property for more details.");
      }
      else {
      // Something went wrong but we don't know what.
      throw new \Exception("Something went wrong requesting a new transaction. Check response property for more details.");
      }
    }
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
