<?php
/**
 * Masterpass Purchase Request
 */

namespace Omnipay\Masterpass\Messages;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\ResponseInterface;
use Exception;

class PurchaseRequest extends AbstractRequest
{
    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function getData()
    {
        try {
            if ($this->getPaymentType() == '3d') {
                $this->checkMdStatus($this->getBankIca(), $this->getMdStatus());
                $this->hashControl($this->getBankIca());
            }

            $headerParams = [
                'client_id' => $this->getClientId(),
                'request_datetime' => gmdate("Y-m-d\TH:i:s") . date("P"),
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
                'macro_merchant_id' => $this->getMacroMerchantId(),
                'payment_type' => $this->getPaymentType(),
                'installment_count' => null,
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

            return [
                "CommitPurchaseRequest" => [
                    'transaction_header' => $headerParams,
                    'transaction_body' => $bodyParams
                ]
            ];
        } catch (InvalidRequestException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->getMode() . "MMIUIMasterPass_V2/MerchantServices/MPGCommitPurchaseService.asmx?wsdl";
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
        } catch (\Exception $e) {
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
        return $this->setParameter('macro_merchant_id', $value);
    }

    /**
     * @return string
     */
    public function getMacroMerchantId(): string
    {
        return $this->getParameter('macro_merchant_id');
    }

    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setBankIca(string $value): PurchaseRequest
    {
        return $this->setParameter('bank_ica', $value);
    }

    /**
     * @return string
     */
    public function getBankIca(): string
    {
        return $this->getParameter('bank_ica');
    }

    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setPaymentType(string $value): PurchaseRequest
    {
        return $this->setParameter('payment_type', $value);
    }

    /**
     * @return string
     */
    public function getPaymentType(): string
    {
        return $this->getPaymentTypes()[$this->getParameter('payment_type')];
    }

    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setMdStatus(string $value): PurchaseRequest
    {
        return $this->setParameter('md_status', $value);
    }

    /**
     * @return string
     */
    public function getMdStatus(): string
    {
        return $this->getParameter('md_status');
    }

    /**
     * @param string $value
     * @return PurchaseRequest
     */
    public function setMerchantStoreKey(string $value): PurchaseRequest
    {
        return $this->setParameter('merchantStoreKey', $value);
    }

    /**
     * @return string
     */
    public function getMerchantStoreKey(): string
    {
        return $this->getParameter('merchantStoreKey');
    }

    /**
     * @param array $value
     * @return PurchaseRequest
     */
    public function setHashResponse(array $value): PurchaseRequest
    {
        return $this->setParameter('hashResponse', $value);
    }

    /**
     * @return array
     */
    public function getHashResponse(): array
    {
        return $this->getParameter('hashResponse');
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
     * @param string $bankIca
     * @param string $mdStatus
     * @return bool
     * @throws Exception
     */
    private function checkMdStatus(string $bankIca, string $mdStatus): bool
    {
        if (empty($bankIca)) {
            throw new Exception('Not found bank value');
        }

        $successStatusCodes = [1, 2, 3, 4];

        if (!in_array($bankIca, $this->getBankIcaList()) && !(isset($successStatusCodes[$mdStatus]))) {
            throw new Exception('3DSecure verification error');
        }

        return true;
    }

    /**
     * @param string $bankIca
     * @return bool
     * @throws Exception
     */
    private function hashControl(string $bankIca): bool
    {
        if (empty($this->getHashResponse()['hashParams'])) {
            throw new Exception ('Hash params error');
        }

        if (in_array($bankIca, $this->getBankIcaList())) {
            $calculatedHashParams = '';
            $params = explode(':', $this->getHashResponse()['hashParams']);
            foreach ($params as $param) {
                $calculatedHashParams .= $this->getHashResponse()[$param] ?? '';
            }

            $calculatedHashParams .= $this->getMerchantStoreKey();
            $hashCalculated = base64_encode(sha1($calculatedHashParams, true));
            if ($hashCalculated != $this->getHashResponse()['hash']) {
                throw new Exception ('Not equal calculated hash and hash');
            }

            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    private function getBankIcaList(): array
    {
        return ['2030', '2110', '3771', '1684', '9165', '3039', '7656'];
    }
}
