<?php
/**
 * Masterpass Authorize Request
 */

namespace Omnipay\Masterpass\Messages;

use Exception;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\ResponseInterface;

class AuthorizeRequest extends AbstractRequest
{

    public const ENDPOINT = self::BASE . 'MPGGenerateKeyService.asmx?wsdl';

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
            'GenerateKeyRequest' => [
                'transaction_header' => $headerParams
            ]
        ];
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->getMode() . self::ENDPOINT;
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
            $tokenNeedingInfo = [
                'user_id' => $this->getUserId(),
                'reference_number' => $this->getTransactionReference(),
                'client_id' => $this->getClientId(),
                'phone' => $this->getPhone(),
                'timezone' => $this->getTimezone(),
                'validationType' => $this->getValidationType(),
                'merchantType' => $this->getMerchantType(),
                'validatedMsisdn' => $this->getValidatedMsisdn(),
            ];

            $response = ($this->getEncKey() && $this->getMacKey()) ? [
                'mac_key' => $this->getMacKey(),
                'encryption_key' => $this->getEncKey(),
            ] : $this->getResult($data);

            $response = array_merge($response, $tokenNeedingInfo);
            $response['request'] = $data;

            return new AuthorizeResponse($this, $response);
        } catch (Exception $e) {
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
    public function getEncKey(): ?string
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
    public function getMacKey(): ?string
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
    public function setTimezone(string $value): AuthorizeRequest
    {
        return $this->setParameter('timezone', $value);
    }

    /**
     * @return string
     */
    public function getTimezone(): ?string
    {
        return $this->getParameter('timezone') ?? $this->calculateTimeZone();
    }

    /**
     * @param string $value
     * @return AuthorizeRequest
     */
    public function setMerchantType(string $value): AuthorizeRequest
    {
        return $this->setParameter('merchantType', $value);
    }

    /**
     * @return string
     */
    public function getMerchantType(): ?string
    {
        return $this->getParameter('merchantType') ?? '00';
    }

    /**
     * @param string $value
     * @return AuthorizeRequest
     */
    public function setValidationType(string $value): AuthorizeRequest
    {
        return $this->setParameter('validationType', $value);
    }

    /**
     * @return string
     */
    public function getValidationType(): ?string
    {
        return $this->getParameter('validationType') ?? '00';
    }

    /**
     * @param string $value
     * @return AuthorizeRequest
     */
    public function setValidatedMsisdn(string $value): AuthorizeRequest
    {
        return $this->setParameter('validatedMsisdn', $value);
    }

    /**
     * @return string
     */
    public function getValidatedMsisdn(): ?string
    {
        return $this->getParameter('validatedMsisdn') ?? '00';
    }

    /**
     * @return string
     */
    private function calculateTimeZone(): string
    {
        $p = date('P');
        $x = explode(':', $p);
        $dif = $x[0];
        $f = $dif[0];
        $s = substr($dif, 1);

        if ($f === '-') {
            $rTime = '8';
        } else {
            $rTime = '0';
        }

        $rTime .= dechex($s);

        return $rTime;
    }
}

