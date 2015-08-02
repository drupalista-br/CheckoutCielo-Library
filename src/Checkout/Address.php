<?php

namespace CieloCheckout;

class Address extends Commons {

  public
    $Street,
    $Number,
    $Complement,
    $District,
    $City,
    $State;

  protected $property_requirements = [
    'Street' => [
      'empty' => ['negate' => FALSE],
    ],
    'Number' => [
      'empty' => ['negate' => FALSE],
    ],
    'District' => [
      'empty' => ['negate' => FALSE],
    ],
    'City' => [
      'empty' => ['negate' => FALSE],
    ],
    'State' => [
      'empty' => ['negate' => FALSE],
    ],
  ];

}
