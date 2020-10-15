<?php
/**
 * Masterpass Abstract Request
 */

namespace Omnipay\Masterpass\Messages;


abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    use BaseSoapService;

    public const BASE = 'MMIUIMasterPass_V2/MerchantServices/';

    protected const BANK_ICA_PAYU = '1000';

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
     * @throws \JsonException
     */
    public function getResult(array $data): array
    {
        if (isset($data['bank_ica']) && $data['bank_ica'] === self::BANK_ICA_PAYU) {
            return $this->getParameters();
        }

        $response = $this->makeRequestToService($this->getEndpoint(), $this->getFunction(), $data);

        if ($response->Detail) {
            return (array)$response;
        }

        $method = $this->getFunction() . 'Response';
        $stdClass = property_exists($response, $method) ? (array)$response->$method : (array)$response;

        return json_decode(json_encode($stdClass, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
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

    /**
     * @param string $value
     * @return AbstractRequest
     */
    public function setMerchantId(string $value): AbstractRequest
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * @return mixed
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }
}
