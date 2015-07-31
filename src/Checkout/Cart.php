<?php

namespace CieloCheckout;

class Cart extends Commons {

  public
	$Discount,
	$Items;

  protected $property_requirements = [
	'Items' => [
	  'is_array' => [],
	],
  ];

  protected function validate() {
	$this->Items_validate();
  }

  private function Items_validate() {
	foreach ($this->Items as $delta => $Item) {
	  if (!$Item instanceof Item) {
		throw new \Exception("Item on index $delta of 'Items' is not an instance of Items.");
	  }
	}
  }

  protected function set_Discount(Discount $Discount) {
	$this->Discount = $Discount;
  }
}
