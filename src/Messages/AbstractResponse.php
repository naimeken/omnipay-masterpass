<?php
/**
 * Masterpass  AbstractResponse
 */

namespace Omnipay\Masterpass\Messages;

class AbstractResponse extends \Omnipay\Common\Message\AbstractResponse
{
    /** @var array */
    protected $serviceRequestParams;

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->data['Detail']->response_desc ?? null;
    }

    public function getCode(): ?string
    {
        return $this->data['Detail']->response_code ?? null;
    }

    /**
     * @return boolean
     */
    public function isSuccessful(): bool
    {
        if (!empty($this->data['Detail'])) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getServiceRequestParams(): array
    {
        return $this->serviceRequestParams;
    }

    /**
     * @param array $serviceRequestParams
     */
    public function setServiceRequestParams(array $serviceRequestParams): void
    {
        $this->serviceRequestParams = $serviceRequestParams;
    }

}
