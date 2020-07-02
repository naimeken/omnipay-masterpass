<?php
/**
 * Masterpass Abstract Request
 */

namespace Omnipay\Masterpass\Messages;

use Exception;
use Omnipay\Common\Exception\InvalidResponseException;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    use BaseSoapService;

    public const BASE = 'MMIUIMasterPass_V2/MerchantServices/';
    /** @var array */
    private $serviceList = [
        'test' => 'https://test.masterpassturkiye.com/',
        'uat' => 'https://uatmmi.masterpassturkiye.com/',
        'prod' => 'https://prod.masterpassturkiye.com/'
    ];

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->getParameter('clientId');
    }

    /**
     * @param string $value
     * @return AbstractRequest
     */
    public function setClientId(string $value): AbstractRequest
    {
        return $this->setParameter('clientId', $value);
    }

    /**
     * @return string
     */
    public function getMode(): string
    {
        $testMode = ($this->getTestMode() === true) ? 'test' : null;
        $mode = $this->getParameter('mode') ?? $testMode ?? 'prod';

        return $this->serviceList[$mode];
    }

    /**
     * @param string $value
     * @return AbstractRequest
     */
    public function setMode(string $value): AbstractRequest
    {
        return $this->setParameter('mode', $value);
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setSendSms($value): AbstractRequest
    {
        return $this->setParameter('sendSms', $value);
    }

    /**
     * @return string
     */
    public function getSendSms(): string
    {
        return $this->getParameter('sendSms') ?: 'Y';
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setSendSmsLanguage($value): AbstractRequest
    {
        return $this->setParameter('sendSmsLanguage', $value);
    }

    /**
     * @return string
     */
    public function getSendSmsLanguage(): string
    {
        return $this->getParameter('sendSmsLanguage') ?: 'tur';
    }

    /**
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     */
    public function getResult(array $data): array
    {
        try {
            $response = $this->makeRequestToService($this->getEndpoint(), $this->getFunction(), $data);
            $method = $this->getFunction() . 'Response';

            return property_exists($response, $method) ? (array)$response->$method : (array)$response;
        } catch (Exception $e) {
            throw new InvalidResponseException($e->getMessage());
        }
    }

    /**
     * @param string $value
     * @return AbstractRequest
     */
    public function setPhone(string $value): AbstractRequest
    {
        return $this->setParameter('phone', $value);
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->getParameter('phone');
    }
}
