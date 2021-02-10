<?php
/**
 * Masterpass Abstract Request
 */

namespace Omnipay\Masterpass\Messages;


abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    use BaseSoapService;

    public const BASE = 'MMIUIMasterPass_V2/MerchantServices/';
    public const SECURE_3D = 'SECURE_3D';

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
        $testMode = $this->serviceList['test'];

        if ($this->getTestMode() === true) {
            return $testMode;
        }

        return $this->serviceList[$this->getParameter('paymentEnv')] ?? $testMode;
    }

    /**
     * @param string $value
     * @return AbstractRequest
     */
    public function setPaymentEnv(string $value): AbstractRequest
    {
        return $this->setParameter('paymentEnv', $value);
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
     */
    public function getResult(array $data): array
    {
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

    /**
     * @param string $value
     * @return AuthorizeRequest
     */
    public function setEncKey(string $value): AbstractRequest
    {
        return $this->setParameter('encKey', $value);
    }

    /**
     * @return string
     */
    public function getEncKey(): ?string
    {
        return $this->getParameter('encKey');
    }

    /**
     * @param string $value
     * @return AuthorizeRequest
     */
    public function setMacKey(string $value): AbstractRequest
    {
        return $this->setParameter('macKey', $value);
    }

    /**
     * @return string
     */
    public function getMacKey(): ?string
    {
        return $this->getParameter('macKey');
    }

    /**
     * @param string $value
     * @return AbstractRequest
     */
    public function setMacroMerchantId(string $value): AbstractRequest
    {
        return $this->setParameter('macroMerchantId', $value);
    }

    /**
     * @return mixed
     */
    public function getMacroMerchantId()
    {
        return $this->getParameter('macroMerchantId');
    }

    /**
     * @param string $value
     * @return AbstractRequest
     */
    public function setBankIca(string $value): AbstractRequest
    {
        return $this->setParameter('bankIca', $value);
    }

    /**
     * @return string|null
     */
    public function getBankIca(): ?string
    {
        return $this->getParameter('bankIca');
    }

    /**
     * @param string $value
     * @return AbstractRequest
     */
    public function setPaymentType(string $value): AbstractRequest
    {
        return $this->setParameter('paymentType', $value);
    }

    /**
     * @return string
     */
    public function getPaymentType(): string
    {
        return $this->getPaymentTypes()[$this->getParameter('paymentType')];
    }

    /**
     * @return array|null
     */
    public function getOptionalParameters(): ?array
    {
        return $this->getParameter('optionalParameters') ?? [];
    }

    /**
     * @param array $value
     * @return AbstractRequest
     */
    public function setOptionalParameters(array $value): AbstractRequest
    {
        return $this->setParameter('optionalParameters', $value);
    }

    /**
     * @param string $value
     * @return AbstractRequest
     */
    public function setStoreKey(string $value): AbstractRequest
    {
        return $this->setParameter('storeKey', $value);
    }

    /**
     * @return string
     */
    public function getStoreKey(): ?string
    {
        return $this->getParameter('storeKey');
    }

    /**
     * @return string
     */
    public function getHash(): ?string
    {
        return $this->getParameter('hash');
    }

    /**
     * @param string $value
     * @return AbstractRequest
     */
    public function setHash(string $value): AbstractRequest
    {
        return $this->setParameter('hash', $value);
    }

    public function getOrderNo(): ?string
    {
        return $this->getParameter('orderNo');
    }

    public function setOrderNo(string $value): AbstractRequest
    {
        return $this->setParameter('orderNo', $value);
    }

    /**
     * @return array
     */
    protected function requestParams(): array
    {
        $headerParams = [
            'client_id' => $this->getMerchantId(),
            'request_datetime' => gmdate("Y-m-d\TH:i:s") . date('P'),
            'request_reference_no' => $this->getTransactionReference(),
            'send_sms' => $this->getSendSms(),
            'send_sms_language' => $this->getSendSmsLanguage(),
            'ip_address' => $this->getClientIp()
        ];

        $bodyParams = [
            'amount' => $this->getAmountInteger(),
            'order_no' => $this->getOrderNo() ?? $this->getTransactionReference(),
            'payment_type' => $this->getPaymentType(),
            'bank_ica' => $this->getBankIca(),
            'token' => $this->getToken(),
            'msisdn' => $this->getPhone(),
            'order_details' => null,
            'bill_details' => null,
            'delivery_details' => null
        ];

        $bodyParams = array_filter(array_merge($bodyParams, $this->getOptionalParameters()));

        if ($this->getMacroMerchantId()) {
            $bodyParams['macro_merchant_id'] = $this->getMacroMerchantId();
        }

        return [
            'CommitPurchaseRequest' => [
                'transaction_header' => $headerParams,
                'transaction_body' => $bodyParams
            ]
        ];
    }

    /**
     * @return array
     */
    private function getPaymentTypes(): array
    {
        return [
            '3d' => 'SECURE_3D',
            'direct' => 'DIRECT_PAYMENT'
        ];
    }
}
