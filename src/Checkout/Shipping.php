<?php

namespace CieloCheckout;

class Shipping extends Commons {

  public
    $Type,
    $SourceZipCode,
    $TargetZipCode,
    $Address,
    $Services;

  /**
   * List of valid values for $this->Type.
   * Index = Type | Value = Description
   */
  private static $Type_validate = [
    'Correios' => 'Entrega via Correios',
    'FixedAmount' => 'Valor Fixo',
    'Free' => 'Entrega gratuita',
    'WithoutShippingPickUp' => 'Cliente faz a coleta',
    'WithoutShipping' => 'Não há entrega',
  ];

  protected $property_requirements = [
    'Type' => [
      'empty' => ['negate' => FALSE],
    ],
  ];

  protected function validate() {
    $this->Type_validate();
    $this->Services_validate();
    $this->TargetZipCode_validate();
  }

  private function Type_validate() {
    if (!isset(self::$Type_validate[$this->Type])) {
      throw new \Exception("'Type == {$this->Type}' is invalid.");
    }
  }

  private function Services_validate() {
    if (!empty($this->Services)) {
      foreach ($this->Services as $delta => $Service) {
        if (!$Service instanceof Services) {
          throw new \Exception("$Service on index $delta of 'Services' is not an instance of Services.");
        }
      }
    }
  }

  private function TargetZipCode_validate() {
    if (!empty($this->Address)) {
      if (empty($this->TargetZipCode) || (strlen($this->TargetZipCode) != 8) ) {
        throw new \Exception("TargetZipCode is required when sending Shipping Address. It must be 8 digits long.");
      }
    }
  }

  protected function set_Address(Address $Address) {
    $this->Address = $Address;
  }

  /**
   * @return Array
   *   A list of valid values for $this->Type.
   *   Index = Type | Value = Description
   */
  public static function get_Types() {
    return self::$Type_validate;
  }
}
