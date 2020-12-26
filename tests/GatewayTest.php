<?php

namespace OmnipayTest\Masterpass;

use Omnipay\Common\Message\RequestInterface;
use Omnipay\Masterpass\Gateway;
use Omnipay\Masterpass\Messages\AuthorizeRequest;
use Omnipay\Masterpass\Messages\PurchaseRequest;
use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    public function setUp(): void
    {
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testSupportsPurchase()
    {
        $supportsPurchase = $this->gateway->supportsPurchase();
        $this->assertInternalType('boolean', $supportsPurchase);

        if ($supportsPurchase) {
            $this->assertInstanceOf(RequestInterface::class, $this->gateway->purchase(['bankIca' => '2110']));
        } else {
            $this->assertFalse(method_exists($this->gateway, 'purchase'));
        }
    }

    public function testPurchaseParameters()
    {
        if ($this->gateway->supportsPurchase()) {
            foreach ($this->gateway->getDefaultParameters() as $key => $default) {
                if ($key === 'encKey') {
                    // set property on gateway
                    $getter = 'get' . ucfirst($this->camelCase($key));
                    $setter = 'set' . ucfirst($this->camelCase($key));
                    $value = uniqid();
                    $this->gateway->$setter($value);

                    // request should have matching property, with correct value
                    $request = $this->gateway->purchase(['bankIca' => '2110']);

                    $this->assertSame($value, $request->$getter());
                }
            }
        }
    }

    public function testPurchase(): void
    {
        /** @var PurchaseRequest $request */
        $request = $this->gateway->purchase(['bankIca' => '2110']);

        self::assertInstanceOf(PurchaseRequest::class, $request);
        self::assertSame('2110', $request->getBankIca());
    }

    public function testAuthorize(): void
    {
        /** @var AuthorizeRequest $request */
        $request = $this->gateway->authorize(['merchantId' => '41838239']);

        self::assertInstanceOf(AuthorizeRequest::class, $request);
        self::assertSame('41838239', $request->getMerchantId());
    }
}