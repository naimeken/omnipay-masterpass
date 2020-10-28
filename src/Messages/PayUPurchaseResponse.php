<?php
/**
 * Masterpass PayU Purchase Response
 */

namespace Omnipay\Masterpass\Messages;

use Omnipay\Common\Message\RequestInterface;

class PayUPurchaseResponse extends AbstractResponse
{
    private const SUCCESS_RESPONSE_CODE = '0000';

    /** @var string|null */
    private $message = null;

    /** @var bool */
    private $isSuccess = false;

    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);
        $this->prepareData();
    }

    /**
     * @return boolean
     */
    public function isSuccessful(): bool
    {
        return $this->isSuccess;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    private function prepareData(): void
    {
        if ($this->hashControllable()) {
            $this->hashControl();
        }
    }

    /**
     * @return bool
     */
    private function hashControllable(): bool
    {
        if (!isset($this->data['responseCode']) || (isset($this->data['responseCode']) && $this->data['responseCode'] !== self::SUCCESS_RESPONSE_CODE)) {
            $this->message = 'Payu response code error';
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function hashControl(): bool
    {
        if (!isset($this->data['hashParamsVal']) || !isset($this->data['storeKey']) || !isset($this->data['hash'])) {
            $this->message = 'HashParamsVal, storeKey or hash params not found';

            return false;
        }

        $calculatedHashParams = $this->data['hashParamsVal'];
        $hashCalculated = hash_hmac("md5", $calculatedHashParams, $this->data['storeKey']);

        if ($hashCalculated !== $this->data['hash']) {
            $this->message = 'Expected hash not equal to calculated hash';

            return false;
        }

        $this->isSuccess = true;

        return true;
    }
}
