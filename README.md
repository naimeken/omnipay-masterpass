# omnipay-masterpass
<p>
<a href="https://github.com/alegraio/omnipay-masterpass/actions"><img src="https://github.com/alegraio/omnipay-masterpass/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/alegra/omnipay-masterpass"><img src="https://img.shields.io/packagist/dt/alegra/omnipay-masterpass" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/alegra/omnipay-masterpass"><img src="https://img.shields.io/packagist/v/alegra/omnipay-masterpass" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/alegra/omnipay-masterpass"><img src="https://img.shields.io/packagist/l/alegra/omnipay-masterpass" alt="License"></a>
</p>
MasterPass Domestic Integration ( Türkiye ) gateway for Omnipay V3 payment processing library

<a href="https://github.com/thephpleague/omnipay">Omnipay</a> is a framework agnostic, multi-gateway payment
processing library for PHP 7.3+. This package implements MasterPass Online Payment Gateway support for Omnipay.

* You have to contact the MasterPass for the document.
* You need to macKey,encKey,merchantId. You can take them from MasterPass.

## Requirement

* PHP >= 7.3.x,
* [Omnipay V.3](https://github.com/thephpleague/omnipay) repository,
* PHPUnit to run tests

## Autoload

You have to install omnipay V.3

```bash
composer require league/omnipay:^3
```

Then you have to install omnipay-payu package:

```bash
composer require alegra/omnipay-masterpass
```

> `payment-masterpass` follows the PSR-4 convention names for its classes, which means you can easily integrate `payment-masterpass` classes loading in your own autoloader.

## Basic Usage

- You can use /examples folder to execute examples. This folder is exists here only to show you examples, it is not for production usage.
- First in /examples folder:

```bash
composer install
```


**Authorize Example**

- You can check authorize.php file in /examples folder.
- This method get token. You have to use token in all requests

```php
<?php

$loader = require __DIR__ . '/vendor/autoload.php';
$loader->addPsr4('Examples\\', __DIR__);

use Omnipay\Masterpass\Gateway;
use Examples\Helper;

$gateway = new Gateway();
$helper = new Helper();

try {
    $params = $helper->getAuthorizeParams();
    $response = $gateway->authorize($params)->send();

    $result = [
        'status' => $response->isSuccessful() ?: 0,
        'token' => $response->getToken(),
        'message' => $response->getMessage(),
        'requestParams' => $response->getServiceRequestParams(),
        'response' => $response->getData()
    ];

    print("<pre>" . print_r($result, true) . "</pre>");
} catch (Exception $e) {
    throw new \RuntimeException($e->getMessage());
}
```

**Purchase Example**

- You can check purchase.php file in /examples folder.

```php
<?php

$loader = require __DIR__ . '/vendor/autoload.php';
$loader->addPsr4('Examples\\', __DIR__);

use Omnipay\Masterpass\Gateway;
use Examples\Helper;

$gateway = new Gateway();
$helper = new Helper();

try {
    $params = $helper->getPurchaseParams();
    $response = $gateway->purchase($params)->send();

    $result = [
        'status' => $response->isSuccessful() ?: 0,
        'redirect' => $response->isRedirect() ?: 0,
        'message' => $response->getMessage(),
        'requestParams' => $response->getServiceRequestParams(),
        'response' => $response->getData()
    ];

    print("<pre>" . print_r($result, true) . "</pre>");
} catch (Exception $e) {
    throw new \RuntimeException($e->getMessage());
}
```

**Purchase 3d Example**

- You can check purchase3d.php file in /examples folder.

```php
<?php

$loader = require __DIR__ . '/vendor/autoload.php';
$loader->addPsr4('Examples\\', __DIR__);

use Omnipay\Masterpass\Gateway;
use Examples\Helper;

$gateway = new Gateway();
$helper = new Helper();

try {
    $params = $helper->getPurchase3dParams();
    $response = $gateway->purchase($params)->send();

    $result = [
        'status' => $response->isSuccessful() ?: 0,
        'redirect' => $response->isRedirect() ?: 0,
        'message' => $response->getMessage(),
        'requestParams' => $response->getServiceRequestParams(),
        'response' => $response->getData()
    ];

    print("<pre>" . print_r($result, true) . "</pre>");
} catch (Exception $e) {
    throw new \RuntimeException($e->getMessage());
}
```

**payU Purchase Example**

- You can check payUPurchase.php file in /examples folder.

```php
<?php

$loader = require __DIR__ . '/vendor/autoload.php';
$loader->addPsr4('Examples\\', __DIR__);

use Omnipay\Masterpass\Gateway;
use Examples\Helper;

$gateway = new Gateway();
$helper = new Helper();

try {
    $params = $helper->getPayUPurchaseParams();
    $response = $gateway->purchase($params)->send();

    $result = [
        'status' => $response->isSuccessful() ?: 0,
        'redirect' => $response->isRedirect() ?: 0,
        'message' => $response->getMessage(),
        'requestParams' => $response->getServiceRequestParams(),
        'response' => $response->getData()
    ];

    print("<pre>" . print_r($result, true) . "</pre>");
} catch (Exception $e) {
    throw new \RuntimeException($e->getMessage());
}
```

**payU Purchase 3d Example**

- You can check payUPurchase3d.php file in /examples folder.
- This method does hash check.

```php
<?php

$loader = require __DIR__ . '/vendor/autoload.php';
$loader->addPsr4('Examples\\', __DIR__);

use Omnipay\Masterpass\Gateway;
use Examples\Helper;

$gateway = new Gateway();
$helper = new Helper();

try {
    $params = $helper->getPayUPurchase3dParams();
    $response = $gateway->purchase($params)->send();

    $result = [
        'status' => $response->isSuccessful() ?: 0,
        'redirect' => $response->isRedirect() ?: 0,
        'message' => $response->getMessage(),
        'requestParams' => $response->getServiceRequestParams(),
        'response' => $response->getData()
    ];

    print("<pre>" . print_r($result, true) . "</pre>");
} catch (Exception $e) {
    throw new \RuntimeException($e->getMessage());
}
```

requestParams:

> System send request to payU api. It shows request information.
>

## Licensing

[GNU General Public Licence v3.0](LICENSE)

    For the full copyright and license information, please view the LICENSE
    file that was distributed with this source code.