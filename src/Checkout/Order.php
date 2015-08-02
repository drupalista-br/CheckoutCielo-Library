<?php

namespace CieloCheckout;

class Order extends Commons {

  public
    /**
     * Merchant's order number.
     */
    $OrderNumber,

    /**
     * Text shown on the buyer's bill right after merchant's name.
     */
    $SoftDescriptor,
    $Cart,
    $Shipping,
    $Payment,
    $Customer,
    $Options;

  protected $property_requirements = [
    'Cart' => [
      'empty' => ['negate' => FALSE],
    ],
    'Shipping' => [
      'empty' => ['negate' => FALSE],
    ],
  ];

  protected function validate() {
    $this->SoftDescriptor_validate();
  }

  private function SoftDescriptor_validate() {
    $size_limit = 13;
    if (!empty($this->SoftDescriptor) && strlen($this->SoftDescriptor) > $size_limit) {
      throw new \Exception("'SoftDescriptor' must contain less than $size_limit characters.");
    }
  }

  protected function set_Cart(Cart $Cart) {
    $this->Cart = $Cart;
  }

  protected function set_Payment(Payment $Payment) {
    $this->Payment = $Payment;
  }

  protected function set_Customer(Customer $Customer) {
    $this->Customer = $Customer;
  }

  protected function set_Options(Options $Options) {
    $this->Options = $Options;
  }
}
