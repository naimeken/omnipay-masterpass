<?php
/**
 * Masterpass PayU Purchase Request
 */

namespace Omnipay\Masterpass\Messages;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\ResponseInterface;
use Exception;
use RuntimeException;

class PayUPurchaseRequest extends AbstractRequest
{
    public const ENDPOINT = self::BASE . 'MPGCommitPurchaseService.asmx?wsdl';

    /**
     * @return array|mixed
     * @throws Exception
     */
    public function getData()
    {
        try {
            return $this->getPaymentType() === self::SECURE_3D ? $this->getParameters() : $this->requestParams();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
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
        return 'CommitPurchase';
    }

    /**
     * @param mixed $data
     * @return ResponseInterface|AbstractResponse
     * @throws InvalidResponseException
     */
    public function sendData($data): ResponseInterface
    {
        try {
            if ($this->getPaymentType() === self::SECURE_3D) {
                $response = new PayUPurchaseResponse($this, $data);
            } else {
                $result = $this->getResult($data);
                $response = new PurchaseResponse($this, $result);
            }

            $requestParams = $this->getRequestParams();
            $response->setServiceRequestParams($requestParams);

            return $response;
        } catch (Exception $e) {
            throw new InvalidResponseException(
                'Error communicating with payment gateway: ' . $e->getMessage(),
                $e->getCode()
            );
        }
    }

    /**
     * @param string $value
     * @return PayUPurchaseRequest
     */
    public function setInstallmentCount(string $value): PayUPurchaseRequest
    {
        return $this->setParameter('installmentCount', $value);
    }

    /**
     * @return mixed
     */
    public function getInstallmentCount()
    {
        return $this->getParameter('installmentCount');
    }

    /**
     * @return string|null
     */
    public function getHashParamsVal(): ?string
    {
        return $this->getParameter('hashParamsVal');
    }

    /**
     * @param string $value
     * @return PayUPurchaseRequest
     */
    public function setHashParamsVal(string $value): PayUPurchaseRequest
    {
        return $this->setParameter('hashParamsVal', $value);
    }

    /**
     * @param string $value
     * @return PayUPurchaseRequest
     */
    public function setResponseCode(string $value): PayUPurchaseRequest
    {
        return $this->setParameter('responseCode', $value);
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getRequestParams(): array
    {
        return [
            'url' => $this->getPaymentType() === self::SECURE_3D ? '' : $this->getEndPoint(),
            'data' => $this->getPaymentType() === self::SECURE_3D ? [] : $this->getData(),
            'method' => $this->getPaymentType() === self::SECURE_3D ? '' : $this->getFunction()
        ];
    }
}
