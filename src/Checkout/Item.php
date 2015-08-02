<?php

namespace CieloCheckout;

class Item extends Commons {

  public
		$Name,
		$Description,
		$UnitPrice,
		$Quantity,
		$Type,
		$Sku,
		$Weight;

  /**
   * List of valid values for $this->Type.
   * Index = Type | Value = Description
   */
  private static $Type_validate = [
		'Asset' => 'Material Físico',
		'Digital' => 'Produtos Digitais',
		'Service' => 'Serviços',
		'Payment' => 'Outros pagamentos',
  ];

  protected $property_requirements = [
		'Name' => [
			'empty' => ['negate' => FALSE],
		],
		'UnitPrice' => [
			'is_int' => [],
		],
		'Quantity' => [
			'is_int' => [],
		],
  ];

  protected function validate() {
		$this->Type_validate();
		$this->Weight_validate();
  }

  private function Type_validate() {
		if (!isset(self::$Type_validate[$this->Type])) {
			throw new \Exception("'Type == {$this->Type}' is invalid.");
		}
  }

  private function Weight_validate() {
		if (!empty($this->Weight) && !is_int($this->Weight)) {
			throw new \Exception("'Weight == {$this->Weight}' is invalid. It must be an integer.");
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
