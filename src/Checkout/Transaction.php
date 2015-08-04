<?php

namespace CieloCheckout;

use
  Cielo\Merchant,
  Httpful\Request;

class Transaction {

  const
    /**
     * For all payment methods.
     */
    STATUS_CODE_PENDING = 1,
    /**
     * Credit Cards only.
     */
    STATUS_CODE_AUTHORIZED = 7,
    /**
     * For all payment methods.
     */
    STATUS_CODE_PAID = 2,
    /**
     * Credit Cards only.
     */
    STATUS_CODE_REFUSED = 3,
    /**
     * For all payment methods.
     */
    STATUS_CODE_FAILED = 6,
    /**
     * Credit Cards primarily.
     */
    STATUS_CODE_CANCELED = 5,

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
   *
   * @param Bool $validate_response
   *   Whether or not the response from Cielo should be validated.
   */
  public function request_new_transaction($validate_response = TRUE) {
    $merchant_key = $this->Merchant->getAffiliationKey();

    $response = Request::post(self::ENDPOINT)
      ->withoutStrictSsl()
      ->sendsJson()
      ->expectsJson()
      ->body(json_encode($this->Order))
      ->addHeader('MerchantId', $merchant_key)
      ->send();

    $this->response = $response->body;
    if ($validate_response) {
      $this->response_validate();
    }
  }

  /**
   * Redirects customers to Cielo for completing their checkout payment.
   */
  public function redirect_to_cielo() {
    if (php_sapi_name() === 'cli') {
      throw new \Exception("Can not redirect to Cielo. You gotta run this script from a web browser.");
    }
    else {
      header("Location: {$this->response->settings->checkoutUrl}");
    }
  }

  /**
   * Checks if a new transaction response contains the valid data necessary
   * for redirecting the customer to Cielo.
   */
  public function response_validate() {
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
  public static function get_response_statuses() {
    return [
      self::STATUS_CODE_PENDING => 'Pendente',
      self::STATUS_CODE_AUTHORIZED => 'Autorizado',
      self::STATUS_CODE_PAID => 'Pago',
      self::STATUS_CODE_REFUSED => 'Negado',
      self::STATUS_CODE_FAILED => 'Não Finalizado',
      self::STATUS_CODE_CANCELED => 'Cancelado',
    ];
  }
}
