<?php

namespace OmnipayTest\Masterpass\Messages;

use Omnipay\Masterpass\Messages\PayUPurchaseRequest;

class PayUPurchase3dRequestTest extends MasterpassTestCase
{
    /**
     * @var PayUPurchaseRequest
     */
    private $request;

    public function setUp(): void
    {
        $this->request = new PayUPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize($this->getPayUPurchaseParams());
    }


    public function testSendSuccess(): void
    {
        $this->setMockHttpResponse('PayUPurchase3dSuccess.txt');
        $response = $this->request->send();

        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isRedirect());
    }

    public function testSendError(): void
    {
        $this->setMockHttpResponse('PayUPurchase3dFailure.txt');
        $this->request->setHash('xxxx');
        $response = $this->request->send();

        self::assertFalse($response->isSuccessful());
        self::assertFalse($response->isRedirect());
        self::assertSame('HASH_MISTMATCH', $response->getMessage());
    }
}