# SolunoBC API wrapper

## Installation
```bash
$ composer require solunobc/solunobc
```

## Usage
```php
$options = [
    'username' => 'user@example.org',
    'password' => 's3cret',
];
$soluno = new \SolunoBC\SolunoBC($options);
$soluno->setHttpClient($httpClient); // $client is a \Http\Client\HttpClient
$message = new \SolunoBC\Message('test', '+4611111111', ['+4622222222']);
$soluno->sendSms($message);
```
