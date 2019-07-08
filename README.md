# Paymentez PHP SDK

### Installation

Install via composer (not hosted in packagist yet)

`composer require paymentez/sdk`

## Usage

```php
<?php

require 'vendor/autoload.php';

use Paymentez\Paymentez;

// First setup your credentials provided by paymentez
$applicationCode = "SOME_APP_CODE";
$applicationKey = "SOME_APP_KEY";

Paymentez::init($applicationCode, $applicationKey);
```

Once time are set your credentials, you can use available resources.

Resources availables:

- **Card** 
 * Available methods: `getList`, `delete`
- **Charge**
 * Available methods: `create`, `authorize`, `capture`, `verify`, `refund`
- **Cash**
 * Available methods: `generateOrder`

### Card

See full documentation of these features [here](https://paymentez.github.io/api-doc/?shell#payment-methods-cards).

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

### Charges

See full documentation of these features [here](https://paymentez.github.io/api-doc/?shell#payment-methods-cards).

#### Create new charge

See full documentation about this [here](https://paymentez.github.io/api-doc/?shell#payment-methods-cards-debit-with-token)

```php
<?php

use Paymentez\Paymentez;
use Paymentez\Exceptions\PaymentezErrorException;

// Card token
$cardToken = "myAwesomeTokenCard";

$charge = Paymentez::charge();

$userDetails = [
    'id' => "1", // Field required
    'email' => "cbenavides@paymentez.com" // Field required
];

$orderDetails = [
    'amount' => 100.00, // Field required
    'description' => "XXXXXX", // Field required
    'dev_reference' => "XXXXXX", // Field required
    'vat' => 0.00 // Field required 
];

try {
    $created = $charge->create($cardToken, $orderDetails, $userDetails);
} catch (PaymentezErrorException $error) {
    // See the console output for complete information
    // Access to HTTP code from paymentez service
    $code = $error->getCode();
    $message = $error->getMessage();
}

// Get transaction status
$status = $created->transaction->status;
// Get transaction ID
$transactionId = $created->transaction->id;
// Get authorization code
$authCode = $created->transaction->authorization_code;
```

#### Authorize charge

See the full documentation [here](https://paymentez.github.io/api-doc/?shell#payment-methods-cards-authorize)

```php
<?php

use Paymentez\Paymentez;
use Paymentez\Exceptions\PaymentezErrorException;

// Card token
$cardToken = "myAwesomeTokenCard";

$charge = Paymentez::charge();

$userDetails = [
    'id' => "1", // Field required
    'email' => "cbenavides@paymentez.com" // Field required
];

$orderDetails = [
    'amount' => 100.00, // Field required
    'description' => "XXXXXX", // Field required
    'dev_reference' => "XXXXXX", // Field required
    'vat' => 0.00 // Field required 
];

try {
    $authorization = $charge->authorize($cardToken, $orderDetails, $userDetails);
} catch (PaymentezErrorException $error) {
    // See the console output for complete information
    // Access to HTTP code from paymentez service
    $code = $error->getCode();
    $message = $error->getMessage();
}

// Get transaction status
$status = $authorization->transaction->status;
// Get transaction ID
$transactionId = $authorization->transaction->id;
// Get authorization code
$authCode = $authorization->transaction->authorization_code;
```

#### Capture

See the full documentation [here](https://paymentez.github.io/api-doc/?shell#payment-methods-cards-capture)

Need make a [authorization process](#authorize-charge)

````php
<?php

use Paymentez\Paymentez;
use Paymentez\Exceptions\PaymentezErrorException;

$charge = Paymentez::charge();

$authorization = $charge->authorize($cardToken, $orderDetails, $userDetails);
$transactionId = $authorization->transaction->id;

try {
    $capture = $charge->capture($transactionId);
} catch (PaymentezErrorException $error) {
    // See the console output for complete information
    // Access to HTTP code from paymentez service
    $code = $error->getCode();
    $message = $error->getMessage();
}

// Get transaction status
$status = $capture->transaction->status;

// Make a capture with different amount
$newAmountForCapture = 1000.46;
$capture = $charge->capture($transactionId, $newAmountForCapture);
````

#### Refund

See the full documentation [here](https://paymentez.github.io/api-doc/?shell#payment-methods-cards-refund)

Need make a [create process](#authorize-charge)

````php
<?php

use Paymentez\Paymentez;
use Paymentez\Exceptions\PaymentezErrorException;

$charge = Paymentez::charge();

$created = $charge->create($cardToken, $orderDetails, $userDetails);
$transactionId = $created->transaction->id;

try {
    $refund = $charge->refund($transactionId);
} catch (PaymentezErrorException $error) {
    // See the console output for complete information
    // Access to HTTP code from paymentez service
    $code = $error->getCode();
    $message = $error->getMessage();
}

// Get status of refund
$status = $refund->status;
$detail = $refund->detail;

// Make a partial refund
$partialAmountToRefund = 10;
$refund = $charge->refund($transactionId, $partialAmountToRefund);
````

### Cash

#### Generate order

See the all available options in [here](https://paymentez.github.io/api-doc/?shell#payment-methods-cash-generate-a-reference)

```php
<?php

use Paymentez\Paymentez;
use Paymentez\Exceptions\PaymentezErrorException;

$cash = Paymentez::cash();

$carrierDetails = [
    'id' => 'oxxo', // Field required
    'extra_params' => [ // Depends of carrier, for oxxo is required
        'user' => [ // For oxxo is required
            'name' => "Juan",
            'last_name' => "Perez"
        ]
    ]
];

$userDetails = [
   'id' => "1", // Field required
   'email' => "randm@mail.com" // Field required
];

$orderDetails = [
    'dev_reference' => "XXXXXXX", // Field required 
    'amount' => 100, // Field required
    'expiration_days' => 1, // Field required
    'recurrent' => false, // Field required
    'description' => "XXXXXX" // Field required
];

try {
    $order = $cash->generateOrder($carrierDetails, 
    $userDetails, 
    $orderDetails);
} catch (PaymentezErrorException $error) {
    // See the console output for complete information
    // Access to HTTP code from paymentez service
    $code = $error->getCode();
    $message = $error->getMessage();
}

// Get reference code
$referenceCode = $order->transaction->reference;
// Get expiration date
$expirationData = $order->transaction->expiration_date;
// Get order status
$status = $order->transaction->status;
```

### Run unit tests

`composer run test`