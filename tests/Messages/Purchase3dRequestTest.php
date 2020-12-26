<?php

namespace OmnipayTest\Masterpass\Messages;

use Omnipay\Masterpass\Messages\PurchaseRequest;
use Omnipay\Masterpass\Messages\PurchaseResponse;

class Purchase3dRequestTest extends MasterpassTestCase
{
    /**
     * @var PurchaseRequest
     */
    private $request;

    public function setUp(): void
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize($this->getPurchase3dParams());
    }

    /**
     * @throws \Exception
     */
    public function testTransactionReference(): void
    {
        self::assertArrayNotHasKey('transactionReference', $this->request->getData());

        $this->request->setTransactionReference('47974');

        self::assertSame('47974', $this->request->getTransactionReference());
    }

    public function testSendSuccess(): void
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseSuccess.txt');
        $request = $this->getMockRequest();
        $content = $this->transform($httpResponse->getBody()->getContents());
        $response = new PurchaseResponse($request, $content);

        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isRedirect());
    }

    public function testSendError(): void
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseFailure.txt');
        $request = $this->getMockRequest();
        $content = json_decode($httpResponse->getBody()->getContents(), true);
        $response = new PurchaseResponse($request, $content);

        self::assertFalse($response->isSuccessful());
        self::assertFalse($response->isRedirect());
    }

    /**
     * @param $data
     * @return array
     */
    private function transform($data): array
    {
        return (is_string($data)) ? json_decode(json_encode((array)simplexml_load_string($data),
            JSON_THROW_ON_ERROR), 1, 512,
            JSON_THROW_ON_ERROR) : $data;
    }
}