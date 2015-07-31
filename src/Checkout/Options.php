<?php

namespace CieloCheckout;

class Options extends Commons {

  public
	$AntifraudEnabled;

  protected $property_requirements = [
	'AntifraudEnabled' => [
	  'is_bool' => [],
	],
  ];
}
