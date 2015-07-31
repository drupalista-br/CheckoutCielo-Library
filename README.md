
** Example:

```php
    <?php
    
    use
      CieloCheckout\Order,
      CieloCheckout\Item,
      CieloCheckout\Discount,
      CieloCheckout\Cart,
      CieloCheckout\Address,
      CieloCheckout\Services,
      CieloCheckout\Shipping,
      CieloCheckout\Transaction,
      Cielo\Merchant;
    
    include_once "vendor/autoload.php";
    
    // Instantiate the cart item object and set it to an array of product itemns.
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
    
    // Instantiate the shipping address object.
    $properties = [
      'Street' => 'Endereço de entrega',
      'Number' => '123',
      'Complement' => '',
      'District' => 'Bairro da entrega',
      'City' => 'Cidade da entrega',
      'State' => 'SP',
    ];
    $Address = new Address($properties);
    
    // Instantiate the shipping services object.
    $properties = [
      'Name' => 'Serviço de frete',
      'Price' => 123,
      'DeadLine' => 15,
    ];
    
    $Services = [
      new Services($properties),
    ];
    
    // Instantiate the shipping object.
    $properties = [
      'Type' => 'Correios',
      'SourceZipCode' => '14400000',
      'TargetZipCode' => '11000000',
      'Address' => $Address,
      'Services' => $Services,
    ];
    $Shipping = new Shipping($properties);
    
    // Instantiate the order object.
    $properties = [
      'OrderNumber' => '1234',
      'SoftDescriptor' => 'Test',
      // Instantiate the cart object.
      'Cart' => new Cart(['Discount' => $Discount, 'Items' => $Items]),
      'Shipping' => $Shipping,
    ];
    $Order = new Order($properties);
    
    //print_r(json_encode($Order));
    
    // Instantiate the merchant object.
    $Merchant = new Merchant('merchant id numer here', 'merchant key here');
    
    // Instantiate the transaction object.
    $Transaction = new Transaction($Merchant, $Order);
    $Transaction->request();
    print_r($Transaction->response);
