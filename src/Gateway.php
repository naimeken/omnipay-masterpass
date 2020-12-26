<?php
/**
 * Masterpass Class using API
 */

namespace Omnipay\Masterpass;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Masterpass\Messages\AuthorizeRequest;
use Omnipay\Masterpass\Messages\PayUPurchaseRequest;
use Omnipay\Masterpass\Messages\PurchaseRequest;


/**
 * @method \Omnipay\Common\Message\NotificationInterface acceptNotification(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface capture(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface completePurchase(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface refund(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface fetchTransaction(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface void(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface createCard(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = array())
 */
class Gateway extends AbstractGateway
{
    private const BANK_ICA_PAYU = '1000';

    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     * @return string
     */
    public function getName(): string
    {
        return 'Masterpass';
    }

    /**
     * Default parameters.
     *
     * @return array
     */
    public function getDefaultParameters(): array
    {
        return [
            'merchantId' => '',
            'encKey' => '',
            'macKey' => ''
        ];
    }

    /**
     * @param string $value
     * @return Gateway
     */
    public function setMerchantId(string $value): Gateway
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->getParameter('merchantId');
    }

    /**
     * @param string $value
     * @return Gateway
     */
    public function setEncKey(string $value): Gateway
    {
        return $this->setParameter('encKey', $value);
    }

    /**
     * @return string
     */
    public function getEncKey(): string
    {
        return $this->getParameter('encKey');
    }

    /**
     * @return string
     */
    public function getMacKey(): string
    {
        return $this->getParameter('macKey');
    }

    /**
     * @param string $value
     * @return Gateway
     */
    public function setMacKey(string $value): Gateway
    {
        return $this->setParameter('macKey', $value);
    }

    /**
     * @param array $parameters
     * @return AbstractRequest|RequestInterface
     * @throws \Exception
     */
    public function purchase(array $parameters = []): RequestInterface
    {
        $bankIca = $parameters['bankIca'] ?? null;

        if (empty($bankIca)) {
            throw new \Exception('bankIca not found');
        }

        return ($bankIca === self::BANK_ICA_PAYU) ? $this->payUPurchaseRequest($parameters) : $this->defaultPurchaseRequest($parameters);
    }

    /**
     * @param array $parameters
     * @return AbstractRequest|RequestInterface
     */
    public function authorize(array $parameters = []): RequestInterface
    {
        return $this->createRequest(AuthorizeRequest::class, $parameters);
    }

    /**
     * @param array $parameters
     * @return AbstractRequest|RequestInterface
     */
    public function defaultPurchaseRequest(array $parameters = []): RequestInterface
    {
        return $this->createRequest(PurchaseRequest::class, $parameters);
    }

    /**
     * @param array $parameters
     * @return AbstractRequest|RequestInterface
     */
    public function payUPurchaseRequest(array $parameters = []): RequestInterface
    {
        return $this->createRequest(PayUPurchaseRequest::class, $parameters);
    }
}
