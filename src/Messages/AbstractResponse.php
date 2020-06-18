<?php
/**
 * Masterpass  AbstractResponse
 */

namespace Omnipay\Masterpass\Messages;

class AbstractResponse extends \Omnipay\Common\Message\AbstractResponse
{

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        if (!empty($this->data['Detail'])) {
            return $this->data['Detail']->response_code . " : " . $this->data['Detail']->response_desc;
        }

        return null;
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
}
