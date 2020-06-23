<?php
/**
 * Masterpass unut test
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
    }


    public function testAuthorize()
    {
        $this->options = [
            'transactionReference' => '12345',
            'sendSms' => 'Y',
            'sendSmsLanguage' => 'tur',
            'userId' => '1',
            'mode' => 'test',
            'macKey' => 'xxxx',
            'encKey' => 'xxxx',
            'phone' => 'xxxx'
        ];

        /** @var AuthorizeResponse $response */
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
            'bank_ica' => 'xxxx',
            'payment_type' => '3d',
            'amount' => '4599',
            'mdStatus' => '1',
            'token' => 'xxxx',
            'phone' => 'xxxx',
            'merchantStoreKey' => "xxxx",
            'hashResponse' => [
                'clientid' => $this->gateway->getClientId(),
                'oid' => '',
                'authcode' => '',
                'procreturncode' => '',
                'response' => '',
                'mdstatus' => '',
                'cavv' => '',
                'eci' => '',
                'md' => '',
                'rnd' => ''
            ]
        ];

        /** @var PurchaseResponse $response */
        $response = $this->gateway->purchase($this->options)->send();

        $this->assertTrue($response->isSuccessful());
    }
}
