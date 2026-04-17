# DISCLAIMER
The code provided for this service is a sample code. In the event of any direct or indirect losses due to use of this code, Bank of Maldives is not liable for damages. We advise all merchants to generate a unique code based on this sample code to avoid any possibility of loss that may arise in the future.

# BMLConnectPHP
> PHP API Client and bindings for the [Bank of Maldives Connect API](https://github.com/bankofmaldives/bml-connect)


Using this PHP API Client you can interact with your Bank of Maldives Connect API:
- 💳 __Transactions__

## Installation

Requires **PHP 8.5+**.

Install with Composer:

```bash
composer require zaruto/bml-connect-php
```

## Quick Start

```php
use BMLConnect\Client;

$client = new Client('apikey', 'appid'); // production
// $client = new Client('apikey', 'appid', 'sandbox');
```

## Transactions (V2)

### Create transaction

Uses `POST /public/v2/transactions`.

```php
use BMLConnect\Client;

$client = new Client('apikey', 'appid');

$payload = [
    'redirectUrl' => 'https://merchant.example/orders/123',
    'localId' => 'INV-123',
    'customerReference' => 'Basket 392',
    'order' => [
        'shopId' => '60d34368bb850e00080dfe65',
        'products' => [
            [
                'productId' => '60d344ec71b6b20008b20ced',
                'numberOfItems' => 2,
            ],
        ],
    ],
];

$transaction = $client->transactions->create($payload);
header('Location: '.$transaction['url']);
```

### Get transaction

Uses `GET /public/transactions/{transactionId}`.

```php
$transaction = $client->transactions->get('transaction-id');
```

### Update transaction

Uses `PATCH /public/transactions/{transactionId}`.

```php
$updated = $client->transactions->update('transaction-id', [
    'customerReference' => 'Updated reference',
]);
```

### Share payment link via SMS

Uses `POST /public/transactions/{transactionId}/send-sms`.

```php
$response = $client->transactions->sendSms('transaction-id', '9607770000');
```

### Share payment link via email

Uses `POST /public/transactions/{transactionId}/send-email`.

```php
$single = $client->transactions->sendEmail('transaction-id', 'foo@example.com');
$multiple = $client->transactions->sendEmail('transaction-id', ['foo@example.com', 'bar@example.com']);
```

## Breaking Changes in 2.0 SDK

- `Transactions::create()` now forwards a BML v2 payload directly.
- Legacy transaction signing is removed (no automatic `signature` generation).
- Legacy automatic request metadata injection is removed (`apiVersion`, `appVersion`, `signMethod`).
- `Transactions::list()` is removed.
- New methods: `update()`, `sendSms()`, `sendEmail()`.
- Responses are returned as associative arrays.

## Development

```bash
composer install
composer test
```

## About

Sign up as a merchant at [Bank of Maldives Merchant Portal](https://dashboard.merchants.bankofmaldives.com.mv).
