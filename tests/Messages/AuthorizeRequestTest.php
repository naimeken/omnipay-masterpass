<?php

namespace OmnipayTest\Masterpass\Messages;

use Omnipay\Masterpass\Messages\AuthorizeRequest;

class AuthorizeRequestTest extends MasterpassTestCase
{
    /**
     * @var AuthorizeRequest
     */
    private $request;

    public function setUp(): void
    {
        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize($this->getAuthorizeParams());
    }

    public function testEndpoint(): void
    {
        self::assertSame('https://test.masterpassturkiye.com/MMIUIMasterPass_V2/MerchantServices/MPGGenerateKeyService.asmx?wsdl',
            $this->request->getEndpoint());
    }

    public function testMerchantId(): void
    {
        self::assertArrayNotHasKey('merchantId', $this->request->getData());

        $this->request->setMerchantId('181683681');

        self::assertSame('181683681', $this->request->getMerchantId());
    }

    public function testSendSuccess(): void
    {
        $this->setMockHttpResponse('AuthorizeSuccess.txt');
        $response = $this->request->send();
        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isRedirect());
        self::assertSame('https://test.masterpassturkiye.com/MMIUIMasterPass_V2/MerchantServices/MPGGenerateKeyService.asmx?wsdl',
            $this->request->getEndpoint());
        self::assertSame('34703818', $response->getClientId());
        self::assertSame('324324', $response->getTransactionReference());
    }
}