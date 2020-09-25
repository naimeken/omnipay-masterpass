<?php
/**
 * Masterpass Purchase Request
 */

namespace Omnipay\Masterpass\Messages;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\ResponseInterface;
use Exception;
use RuntimeException;

class PurchaseRequest extends AbstractRequest
{
    public const ENDPOINT = self::BASE . 'MPGCommitPurchaseService.asmx?wsdl';
    private const SECURE_3D = 'SECURE_3D';

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

            $headerParams = [
                'client_id' => $this->getClientId(),
                'request_datetime' => gmdate("Y-m-d\TH:i:s") . date('P'),
                'request_reference_no' => $this->getTransactionReference(),
                'send_sms' => $this->getSendSms(),
                'send_sms_language' => $this->getSendSmsLanguage(),
                'ip_address' => $this->getClientIp()
            ];

            $bodyParams = [
                'RewardLists' => null,
                'ChequeLists' => null,
                'MoneyCard' => null,
                'amount' => $this->getAmount(),
                'order_no' => $this->getTransactionReference(),
                'payment_type' => $this->getPaymentType(),
                'bank_ica' => $this->getBankIca(),
                'token' => $this->getToken(),
                'msisdn' => $this->getPhone(),
                'asseco_order_details' => null,
                'order_details' => null,
                'bill_detail' => null,
                'delivery_details' => null,
                'buyer_details' => null,
                'anti_fraud_details' => null,
                'other_details' => null,
                'custom_fields' => null,
                'campaign_id' => null
            ];

            if ($this->getMacroMerchantId()) {
                $bodyParams['macro_merchant_id'] = $this->getMacroMerchantId();
            }

            return [
                'CommitPurchaseRequest' => [
                    'transaction_header' => $headerParams,
                    'transaction_body' => $bodyParams
                ]
            ];
        } catch (InvalidRequestException $e) {
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

            return new PurchaseResponse($this, $response);
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
    public function setMacroMerchantId(string $value): PurchaseRequest
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
     * @return PurchaseRequest
     */
    public function setBankIca(string $value): PurchaseRequest
    {
        return $this->setParameter('bankIca', $value);
    }

    /**
     * @return string
     */
    public function getBankIca(): string
    {
        return $this->getParameter('bankIca');
    }

    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setPaymentType(string $value): PurchaseRequest
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
    public function getMdStatus(): string
    {
        return $this->getParameter('mdStatus');
    }

    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setStoreKey(string $value): PurchaseRequest
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
    public function getHashParams(): string
    {
        return $this->getParameter('hashParams');
    }

    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setInstallmentCount(string $value): PurchaseRequest
    {
        return $this->setParameter('installmentCount', $value);
    }

    /**
     * @return string
     */
    public function getOid(): string
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
    public function getMd(): string
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
    public function getRnd(): string
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
     * @return string
     */
    public function getHash(): string
    {
        return $this->getParameter('hash');
    }

    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setHash(string $value): PurchaseRequest
    {
        return $this->setParameter('hash', $value);
    }

    /**
     * @return mixed
     */
    public function getInstallmentCount()
    {
        return $this->getParameter('installmentCount');
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

    /**
     * @return bool
     * @throws Exception
     */
    private function checkMdStatus(): bool
    {
        if (empty($this->getBankIca())) {
            throw new RuntimeException('Not found bank value');
        }

        $successStatusCodes = [1, 2, 3, 4];

        if (!(isset($successStatusCodes[$this->getMdStatus()])) && !in_array($this->getBankIca(),
                $this->getBankIcaList(), true)) {
            throw new RuntimeException('3DSecure verification error');
        }

        return true;
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
}
