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
        $this->gateway->setMerchantId('xxxx');
        $this->gateway->setEncKey('xxxx');
        $this->gateway->setMacKey('xxxx');
    }

    public function testPurchase()
    {
        $optionalParameters = [
            'order_details' => [
                'orders' => [
                    'list_item' => [
                        [
                            'order_product_name' => 'test',
                            'order_product_code' => 1,
                            'order_price' => 100,
                            'order_vat' => '', // optional
                            'order_qty' => 1,
                            'order_product_info' => 'desc' // optional
                        ],
                        [
                            'order_product_name' => 'test1',
                            'order_product_code' => 2,
                            'order_price' => 100,
                            'order_vat' => '', // optional
                            'order_qty' => 1,
                            'order_product_info' => 'desc1' // optional
                        ]
                    ]
                ],
                'bill_details' => [
                    'bill_last_name' => 'test1',
                    'bill_first_name' => 'test1',
                    'bill_email' => 'test1@test.com',
                    'bill_phone' => '905555555555',
                    'bill_country_code' => 'TR',
                    'bill_fax' => '', // optional
                    'bill_address' => '', // optional
                    'bill_address2' => '', // optional
                    'bill_zip_code' => 34250, // optional
                    'bill_city' => '', // optional
                    'bill_state' => '' // optional
                ],
                'delivery_details' => [
                    'delivery_last_name' => 'test1', // optional
                    'delivery_first_name' => 'test1', // optional
                    'delivery_email' => 'test1@test.com', // optional
                    'delivery_phone' => '905555555555', // optional
                    'delivery_company' => '', // optional
                    'delivery_country_code' => 'TR', // optional
                    'delivery_address' => '', // optional
                    'delivery_address2' => '', // optional
                    'delivery_zip_code' => 34250, // optional
                    'delivery_city' => '', // optional
                    'delivery_state' => '' // optional
                ]
            ]
        ];

        $this->options = [
            'transactionReference' => '47974',
            'sendSms' => 'Y',
            'sendSmsLanguage' => 'tur',
            'clientIp' => 'xxxx',
            'mode' => 'test',
            'bankIca' => '2110',
            'paymentType' => '3d',
            'amount' => '28140',
            'mdStatus' => '2',
            'token' => '7D3ABE5ABB09D213FEC0CE44A19F1F66A93386C609D512C229BC1C7A9001F1E08FEAE07994C3F927CB1CC03AAD6FC7E854A5AB9952AF75E23D2FD6F53B6A7C5E0D4D62477BDC082AF40B800E8B5AFD40322586E0706D16A4A751D99777D34640',
            'phone' => 'xxx',
            'storeKey' => "xxx",
            'clientId' => 'xxxx',
            'oid' => '47974',
            'authCode' => '',
            'procReturnCode' => '',
            'cavv' => '',
            'eci' => '',
            'md' => '520019:2A4BA901C286392A0A7AB93D5980502D19337C9C409962FE854EEF644720608C:3248:##100100000',
            'rnd' => 'bpEeh/85DfvdZ58NWTDX',
            'hash' => 'z3djbwPUdXXB0UXpw2Amb3Uuago=',
            'hashParams' => 'clientid:oid:mdStatus:cavv:eci:md:rnd:',
            'optionalParameters' => $optionalParameters // optional
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


        /** @var AuthorizeResponse $response */
        $response = $this->gateway->authorize($this->options)->send();
        $this->assertTrue($response->isSuccessful());
    }
}
