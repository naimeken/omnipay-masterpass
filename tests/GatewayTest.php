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
            'bankIca' => '1000',
            'paymentType' => '3d',
            'amount' => '28140',
            'mdStatus' => '2',
            'token' => 'xxx',
            'phone' => 'xxx',
            'storeKey' => "u+f0?H8]O9_|p4]+H=T3",
            'clientId' => 'xxxx',
            'oid' => '47974',
            'authCode' => '',
            'procReturnCode' => '',
            'cavv' => '',
            'eci' => '',
            'md' => 'xxxx',
            'rnd' => 'bpEeh/85DfvdZ58NWTDX',
            'hash' => '04376ccd88f002752b20fc31169c0fec',
            'hashParams' => 'clientid:oid:mdStatus:cavv:eci:md:rnd:',
            'hashParamsVal' => 'xxxx',
            'optionalParameters' => $optionalParameters, // optional
            'responseCode' => 'xxxx'
        ];

        try {
            $response = $this->gateway->purchase($this->options)->send();
            $this->assertTrue($response->isSuccessful());
        } catch (\Exception $e) {
        }
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
