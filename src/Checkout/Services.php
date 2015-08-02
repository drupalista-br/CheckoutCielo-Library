<?php

namespace CieloCheckout;

class Services extends Commons {

  public
    $Name,
    $Price,
    $DeadLine;

  protected $property_requirements = [
    'Name' => [
      'empty' => ['negate' => FALSE],
    ],
    'Price' => [
      'is_int' => [],
    ],
  ];
}
