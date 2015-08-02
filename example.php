<?php

use
  CieloCheckout\Order,
  CieloCheckout\Item,
  CieloCheckout\Discount,
  CieloCheckout\Cart,
  CieloCheckout\Address,
  CieloCheckout\Services,
  CieloCheckout\Shipping,
  CieloCheckout\Payment,
  CieloCheckout\Customer,
  CieloCheckout\Options,
  CieloCheckout\Transaction,
  Cielo\Merchant;

include_once "vendor/autoload.php";

// Instantiate cart item object and set it to an array of product itemns.
$properties = [
  'Name' => 'Nome do produto',
  'Description' => 'Descrição do produto',
  'UnitPrice' => 100,
  'Quantity' => 2,
  'Type' => 'Asset',
  'Sku' => 'Sku do item no carrinho',
  'Weight' => 200,
];
$Items = [
  new Item($properties),
];

// Instantiate cart discount object.
$properties = [
  'Type' => 'Percent',
  'Value' => 10,
];
$Discount = new Discount($properties);

// Instantiate shipping address' object.
$properties = [
  'Street' => 'Rua Em Algum Lugar',
  'Number' => '123',
  'Complement' => '',
  'District' => 'Setor F',
  'City' => 'Alta Floresta',
  'State' => 'MT',
];
$Address = new Address($properties);

// Instantiate shipping services' object.
$properties = [
  'Name' => 'Serviço de frete',
  'Price' => 123,
  'DeadLine' => 15,
];

$Services = [
  new Services($properties),
];

// Instantiate shipping's object.
$properties = [
  'Type' => 'Free',
  'SourceZipCode' => '78580000',
  'TargetZipCode' => '78580000',
  'Address' => $Address,
  'Services' => $Services,
];
$Shipping = new Shipping($properties);

// Instantiate payment's object.
$properties = [
  'BoletoDiscount' => 0,
  'DebitDiscount' => 10,
];
$Payment = new Payment($properties);

// Instantiate customer's object.
$properties = [
  'Identity' => '83255885515',
  'FullName' => 'Fulano Comprador da Silva',
  'Email' => 'fulano@email.com',
  'Phone' => '11999999999',
];
$Customer = new Customer($properties);

// Instantiate options' object.
$properties = [
  'AntifraudEnabled' => FALSE,
];
$Options = new Options($properties);

// Instantiate order's object.
$properties = [
  'OrderNumber' => '1234',
  'SoftDescriptor' => 'Test',
  // Instantiate cart's object.
  'Cart' => new Cart(['Discount' => $Discount, 'Items' => $Items]),
  'Shipping' => $Shipping,
  'Payment' => $Payment,
  'Customer' => $Customer,
  'Options' => $Options,
];
$Order = new Order($properties);

// Instantiate merchant's object.
$Merchant = new Merchant($id, $key);

// Instantiate transaction's object.
$Transaction = new Transaction($Merchant, $Order);
$Transaction->request_new_transaction();

print_r($Transaction->response);

// This will throw an exception when running from terminal cli.
$Transaction->redirect_to_cielo();
