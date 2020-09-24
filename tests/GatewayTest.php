<?php
/**
 * Masterpass unit test
 */

namespace Omnipay\Tests;

use Omnipay\Masterpass\Gateway;
use Omnipay\Masterpass\Messages\AuthorizeResponse;
use Omnipay\Masterpass\Messages\PurchaseResponse;

class GatewayTest extends GatewayTestCase
{
    /** @var Gateway */
    public $gateway;

    /** @var array */
    public $options;

    public function setUp()
    {
        /** @var Gateway gateway */
        $this->gateway = new Gateway(null, $this->getHttpRequest());
        $this->gateway->setClientId('xxxx');
        $this->gateway->setEncKey('xxxx');
        $this->gateway->setMacKey('xxxx');
    }

    public function testAuthorize()
    {
        $this->options = [
            'transactionReference' => '324324',
            'sendSms' => 'Y',
            'sendSmsLanguage' => 'tur',
            'userId' => 'abc123',
            'mode' => 'test',
            'macKey' => 'xxxx',
            'encKey' => 'xxx',
            'phone' => '905555555555',
            'timezone' => '03',
            'merchantType' => '00',
            'validationType' => '00',
            'validatedPhone' => '01'
        ];


        $response = $this->gateway->authorize($this->options)->send();
        $this->assertTrue($response->isSuccessful());
    }

    public function testPurchase()
    {
        $this->options = [
            'transactionReference' => '54564564',
            'sendSms' => 'Y',
            'sendSmsLanguage' => 'tur',
            'clientIp' => 'xxxx',
            'mode' => 'test',
            'macro_merchant_id' => 'xxxxx',
            'bankIca' => '2030',
            'paymentType' => '3d',
            'amount' => '4599',
            'mdStatus' => '1',
            'token' => 'xxxx',
            'phone' => 'xxxx',
            'merchantStoreKey' => "xxxx",
            'installmentCount' => 1,
            'clientId' => $this->gateway->getClientId(),
            'oid' => '',
            'authCode' => '',
            'procReturnCode' => '',
            'cavv' => '',
            'eci' => '',
            'md' => '',
            'rnd' => '',
            'hash' => 'vPIt/G+3a3NmsfFnW68jMqDmp84=',
            'hashParams' => 'clientid:oid:mdStatus:cavv:eci:md:rnd:'
        ];

        /** @var PurchaseResponse $response */
        $response = $this->gateway->purchase($this->options)->send();
        $this->assertTrue($response->isSuccessful());
    }
}
