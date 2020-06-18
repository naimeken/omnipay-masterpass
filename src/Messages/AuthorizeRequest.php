<?php
/**
 * Masterpass Authorize Request
 */

namespace Omnipay\Masterpass\Messages;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\ResponseInterface;

class AuthorizeRequest extends AbstractRequest
{
    /**
     * @return array
     */
    public function getData(): array
    {
        $headerParams = array(
            'client_id' => $this->getClientId(),
            'request_datetime' => date('Y-m-d\TH:i:s'),
            'request_reference_no' => $this->getTransactionReference(),
            'send_sms' => $this->getSendSms(),
            'send_sms_language' => $this->getSendSmsLanguage()
        );

        return [
            "GenerateKeyRequest" => [
                'transaction_header' => $headerParams
            ]
        ];
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->getMode() . "MMIUIMasterPass_V2/MerchantServices/MPGGenerateKeyService.asmx?wsdl";
    }

    /**
     * @return string
     */
    public function getFunction(): string
    {
        return 'GenerateKey';
    }

    /**
     * @param mixed $data
     * @return ResponseInterface|AbstractResponse
     * @throws InvalidResponseException
     */
    public function sendData($data): ResponseInterface
    {
        try {
            $tokenNeedingInfos = [
                'user_id' => $this->getUserId(),
                'reference_number' => $this->getTransactionReference(),
                'client_id' => $this->getClientId(),
                'phone' => $this->getPhone()
            ];

            $response = ($this->getEncKey() && $this->getMacKey()) ? [
                "mac_key" => $this->getMacKey(),
                "encryption_key" => $this->getEncKey(),
            ] : $this->getResult($data);

            $response = array_merge($response, $tokenNeedingInfos);

            return new AuthorizeResponse($this, array_unique($response));
        } catch (\Exception $e) {
            throw new InvalidResponseException(
                'Error communicating with payment gateway: ' . $e->getMessage(),
                $e->getCode()
            );
        }
    }

    /**
     * @param string $value
     * @return AuthorizeRequest
     */
    public function setEncKey(string $value): AuthorizeRequest
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
     * @param string $value
     * @return AuthorizeRequest
     */
    public function setMacKey(string $value): AuthorizeRequest
    {
        return $this->setParameter('macKey', $value);
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
     * @return AuthorizeRequest
     */
    public function setUserId(string $value): AuthorizeRequest
    {
        return $this->setParameter('userId', $value);
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->getParameter('userId');
    }

    /**
     * @param string $value
     * @return AuthorizeRequest
     */
    public function setPhone(string $value): AuthorizeRequest
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

