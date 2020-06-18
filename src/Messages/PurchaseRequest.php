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
            $this->checkMdStatus($this->getBankIca(), $this->getMdStatus());
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
                'msisdn' => null,
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
            throw new Exception('Bank Tanımı Gönderilmemiş');
        }

        $successStatusCodes = [];
        switch ($bankIca) {
            case '2030': //garantibank
            case '2110': //akbank
            case '3771': //işbank
            case '1684': //finansbank
            case '9165': //TEB
            case '3039': //Halkbank
            case '7656': //HSBC
                $successStatusCodes = [1, 2, 3, 4];
                break;
            default:
                break;
        }

        if (empty($successStatusCodes)) {
            throw new Exception('Undefined 3DSecure verification for bank');
        }

        if (!(isset($successStatusCodes[$mdStatus]))) {
            throw new Exception('3DSecure verification error');
        }

        return true;
    }
}
