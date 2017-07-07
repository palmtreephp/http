# Palmtree Http

Http component for Palmtree PHP

## Requirements
* PHP >= 5.6

## Installation

Use composer to add the package to your dependencies:
```bash
composer require palmtree/csv
```

## Usage

##### RemoteUser
```php
<?php
$user = new Palmtree\Http\RemoteUser();

$user->setTrustedIpHeaders(['HTTP_X_FORWARDED_FOR']); // optional for load balancers etc.

$user->getIpAddress();
$user->getUserAgent();

```
