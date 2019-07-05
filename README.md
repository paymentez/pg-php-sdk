# Paymentez PHP SDK

### Instalation

Install via composer

`composer require paymentez/sdk`

## Usage

```php
<?php

require 'vendor/autoload.php';

use Paymentez\Paymentez;

// First setup your credentials provided by paymentez
$applicationCode = "SOME_APP_CODE";
$aplicationKey = "SOME_APP_KEY";

Paymentez::init($applicationCode, $aplicationKey);
```

Once time are set your credentials, you can use available resources.

Resources availables:

- **Card** 
 * Available methods: `getList`, `add (only for PCI merchats)`
- **Cash**
 * Available methods: `generateOrder`

### Card

Resource of card

#### List

```php
<?php

use Paymentez\Paymentez;
use Paymentez\Exceptions\PaymentezErrorException;

Paymentez::init($applicationCode, $aplicationKey);

$card = Paymentez::card();

// Success response
$userId = "1";
$listOfUserCards = $card->getList($userId);

$totalSizeOfCardList = $listOfUserCards->result_size;
$listCards = $listOfUserCards->cards;

// Get all data of response
$response = $listOfUserCards->getData();

// Catch fail response
try {
	$listOfUserCards = $card->getList("someUID");
} catch (PaymentezErrorException $error) {
	// Details of exception
	echo $error->getMessage();
	// You can see the logs for complete information
}
```

### Cash

#### TODO: Add this docs

### Run tests

`composer run test`