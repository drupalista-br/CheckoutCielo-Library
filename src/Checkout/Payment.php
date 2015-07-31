<?php

namespace CieloCheckout;

class Payment extends Commons {

  public
	$BoletoDiscount,
	$DebitDiscount;

  protected function validate() {
	$this->BoletoDiscount_validate();
	$this->DebitDiscount_validate();
  }

  private function BoletoDiscount_validate() {
	if (!empty($this->BoletoDiscount)) {
	  if (!is_int($this->BoletoDiscount) || $this->BoletoDiscount > 100 || $this->BoletoDiscount < 0) {
		throw new \Exception("'BoletoDiscount' must be an integer between 0 and 100.");
	  }
	}
  }

  private function DebitDiscount_validate() {
	if (!empty($this->DebitDiscount)) {
	  if (!is_int($this->DebitDiscount) || $this->DebitDiscount > 100 || $this->DebitDiscount < 0) {
		throw new \Exception("'DebitDiscount' must be an integer between 0 and 100.");
	  }
	}
  }
}
