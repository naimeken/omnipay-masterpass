<?php
/**
 * Masterpass Purchase Request
 */

namespace Omnipay\Masterpass\Messages;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\ResponseInterface;
use Exception;
use RuntimeException;

class PurchaseRequest extends AbstractRequest
{
    public const ENDPOINT = self::BASE . 'MPGCommitPurchaseService.asmx?wsdl';

    /**
     * @return array|mixed
     * @throws Exception
     */
    public function getData()
    {
        try {
            if ($this->getPaymentType() === self::SECURE_3D) {
                $this->checkMdStatus();
                $this->hashControl();
            }

            return $this->requestParams();
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
            $response = $this->getResult($data);
            $purchaseResponse = new PurchaseResponse($this, $response);
            $requestParams = $this->getRequestParams();
            $purchaseResponse->setServiceRequestParams($requestParams);

            return $purchaseResponse;
        } catch (Exception $e) {
            throw new InvalidResponseException(
                'Error communicating with payment gateway: ' . $e->getMessage(),
                $e->getCode()
            );
        }
    }


    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setMdStatus(string $value): PurchaseRequest
    {
        return $this->setParameter('mdStatus', $value);
    }

    /**
     * @return string
     */
    public function getMdStatus(): ?string
    {
        return $this->getParameter('mdStatus');
    }

    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setHashParams(string $value): PurchaseRequest
    {
        return $this->setParameter('hashParams', $value);
    }

    /**
     * @return string
     */
    public function getHashParams(): ?string
    {
        return $this->getParameter('hashParams');
    }

    /**
     * @return string
     */
    public function getOid(): ?string
    {
        return $this->getParameter('oid');
    }

    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setOid(string $value): PurchaseRequest
    {
        return $this->setParameter('oid', $value);
    }

    /**
     * @return string
     */
    public function getAuthCode(): ?string
    {
        return $this->getParameter('authCode');
    }

    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setAuthCode(string $value): PurchaseRequest
    {
        return $this->setParameter('authCode', $value);
    }

    /**
     * @return string
     */
    public function getProcReturnCode(): ?string
    {
        return $this->getParameter('procReturnCode');
    }

    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setProcReturnCode(string $value): PurchaseRequest
    {
        return $this->setParameter('procReturnCode', $value);
    }

    /**
     * @return string
     */
    public function getCavv(): ?string
    {
        return $this->getParameter('cavv');
    }

    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setCavv(string $value): PurchaseRequest
    {
        return $this->setParameter('cavv', $value);
    }

    /**
     * @return string
     */
    public function getEci(): ?string
    {
        return $this->getParameter('eci');
    }

    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setEci(string $value): PurchaseRequest
    {
        return $this->setParameter('eci', $value);
    }

    /**
     * @return string
     */
    public function getMd(): ?string
    {
        return $this->getParameter('md');
    }

    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setMd(string $value): PurchaseRequest
    {
        return $this->setParameter('md', $value);
    }

    /**
     * @return string
     */
    public function getRnd(): ?string
    {
        return $this->getParameter('rnd');
    }

    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setRnd(string $value): PurchaseRequest
    {
        return $this->setParameter('rnd', $value);
    }

    /**
     * @return mixed
     */
    public function getInstallmentCount()
    {
        return $this->getParameter('installmentCount');
    }

    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setInstallmentCount(string $value): PurchaseRequest
    {
        return $this->setParameter('installmentCount', $value);
    }

    private function checkMdStatus(): void
    {
        $successStatusCodes = [1, 2, 3, 4];

        if (!in_array((int)$this->getMdStatus(), $successStatusCodes, true) || !in_array($this->getBankIca(),
                $this->getBankIcaList(), true)) {
            throw new RuntimeException('3DSecure verification error for mdStatus or wrong bankIca value');
        }
    }

    /**
     * @throws Exception
     */
    private function hashControl(): void
    {
        if (!$this->getHashParams()) {
            throw new RuntimeException ('Hash params error');
        }

        if (in_array($this->getBankIca(), $this->getBankIcaList(), true)) {
            $calculatedHashParams = '';
            $params = explode(':', $this->getHashParams());
            foreach ($params as $param) {
                $calculatedHashParams .= $this->getHashParameters()[$param] ?? '';
            }

            $calculatedHashParams .= $this->getStoreKey();
            $hashCalculated = base64_encode(sha1($calculatedHashParams, true));

            if ($hashCalculated !== $this->getHash()) {
                throw new RuntimeException ('Expected hash not equal to calculated hash');
            }
        }
    }

    /**
     * @return array
     */
    private function getBankIcaList(): array
    {
        return ['2030', '2110', '3771', '1684', '9165', '3039', '7656'];
    }

    /**
     * @return array
     */
    private function getHashParameters(): array
    {
        return [
            'clientid' => $this->getClientId(),
            'oid' => $this->getOid(),
            'authCode' => $this->getAuthCode(),
            'procReturnCode' => $this->getProcReturnCode(),
            'cavv' => $this->getCavv(),
            'eci' => $this->getEci(),
            'md' => $this->getMd(),
            'rnd' => $this->getRnd(),
            'mdStatus' => $this->getMdStatus()
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getRequestParams(): array
    {
        return [
            'url' => $this->getEndPoint(),
            'data' => $this->getData(),
            'method' => $this->getFunction()
        ];
    }
}
