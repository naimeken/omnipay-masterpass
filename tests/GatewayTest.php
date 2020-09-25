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

    public function testPurchase()
    {
        $this->options = [
            'transactionReference' => '47974',
            'sendSms' => 'Y',
            'sendSmsLanguage' => 'tur',
            'clientIp' => 'xxxx',
            'mode' => 'test',
            'macro_merchant_id' => 'xxx',
            'bankIca' => '2110',
            'paymentType' => '3d',
            'amount' => '28140',
            'mdStatus' => '2',
            'token' => '7D3ABE5ABB09D213FEC0CE44A19F1F66A93386C609D512C229BC1C7A9001F1E08FEAE07994C3F927CB1CC03AAD6FC7E854A5AB9952AF75E23D2FD6F53B6A7C5E0D4D62477BDC082AF40B800E8B5AFD40322586E0706D16A4A751D99777D34640',
            'phone' => 'xxx',
            'storeKey' => "xxx",
            'clientId' => $this->gateway->getClientId(),
            'oid' => '47974',
            'authCode' => '',
            'procReturnCode' => '',
            'cavv' => '',
            'eci' => '',
            'md' => '520019:2A4BA901C286392A0A7AB93D5980502D19337C9C409962FE854EEF644720608C:3248:##100100000',
            'rnd' => 'bpEeh/85DfvdZ58NWTDX',
            'hash' => 'z3djbwPUdXXB0UXpw2Amb3Uuago=',
            'hashParams' => 'clientid:oid:mdStatus:cavv:eci:md:rnd:'
        ];

        /** @var PurchaseResponse $response */
        $response = $this->gateway->purchase($this->options)->send();
        $this->assertTrue($response->isSuccessful());
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
}
