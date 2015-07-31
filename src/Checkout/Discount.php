<?php

namespace CieloCheckout;

class Discount extends Commons {

  public
	$Type,
	$Value;

  /**
   * List of valid values for $this->Type.
   * Index = Type | Value = Description
   */
  private static $Type_validate = [
	'Amount' => 'Valor de desconto fixo.',
	'Percent' => 'Porcentagem do desconto.',
  ];

  protected function validate() {
	$this->Type_validate();
	$this->Value_validate();
  }

  private function Type_validate() {
	if (!empty($this->Type)) {
	  if (!isset(self::$Type_validate[$this->Type])) {
		throw new \Exception("'Type == {$this->Type}' is invalid.");
	  }
	}
	else {
	  if (!empty($this->Value)) {
		throw new \Exception("'Type' is requided because 'Value' is not empty.");
	  }
	}
  }

  private function Value_validate() {
	if (!empty($this->Value)) {
	  if (!is_int($this->Value) || $this->Value < 0) {
		throw new \Exception("'Value' must be an integer equal or greater than zero.");
	  }
	}
  }

  /**
   * @return Array
   *   A list of valid values for $this->Type.
   *   Index = Type | Value = Description
   */
  public function get_Types() {
	return self::$Type_validate;
  }
}
