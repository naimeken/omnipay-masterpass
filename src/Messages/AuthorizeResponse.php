<?php
/**
 * Masterpass Authorize AbstractResponse
 */

namespace Omnipay\Masterpass\Messages;

class AuthorizeResponse extends AbstractResponse
{
    /**
     * @return string
     */
    public function getToken(): string
    {
        $msisdn = $this->createValidMsidn($this->getPhone());
        $dateTime = date('YmdHis');
        $data = 'FF01' . $this->specPadLen($this->getClientId()) . $this->specToBHex($this->getClientId())
            . 'FF02' . '01' . $this->getTimeZone()->hex
            . 'FF03' . $this->specPadLen($dateTime) . $this->specToBHex($dateTime)
            . 'FF04' . $this->specPadLen($msisdn) . $this->specToBHex($msisdn) //msisdn
            . 'FF05' . $this->specPadLen($this->getTransactionReference()) . $this->specToBHex($this->getTransactionReference())
            . 'FF06' . $this->specPadLen($this->getUserId()) . $this->specToBHex($this->getUserId())
            . 'FF07' . '01' . '00';

        if (strlen($data) % 32 != 0) {
            $data .= '8';
            $padC = ceil(strlen($data) / 32) * 32;
            $data = str_pad($data, $padC, '0', STR_PAD_RIGHT);
        }

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encryptData = openssl_encrypt($data, 'aes-256-cbc', $this->getEncKey(), 0, $iv);
        $encryptData = strtoupper(bin2hex($encryptData));
        $macKey = hash_hmac("SHA1", $encryptData, $this->getMacKey());

        return $encryptData . strtoupper($macKey);
    }

    /**
     * @return string|null
     */
    public function getTransactionReference(): ?string
    {
        if (!empty($this->data['reference_number'])) {
            return $this->data['reference_number'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getClientId(): ?string
    {
        if (!empty($this->data['client_id'])) {
            return $this->data['client_id'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getUserId(): ?string
    {
        if (!empty($this->data['user_id'])) {
            return $this->data['user_id'];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getEncKey(): string
    {
        if (!empty($this->data['encryption_key'])) {
            return $this->data['encryption_key'];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getMacKey(): string
    {
        if (!empty($this->data['mac_key'])) {
            return $this->data['mac_key'];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        if (!empty($this->data['phone'])) {
            return $this->data['phone'];
        }

        return null;
    }

    /**
     * @return object
     */
    private function getTimeZone()
    {
        $p = date("P");
        $x = explode(':', $p);
        $dif = $x[0];
        $f = substr($dif, 0, 1);
        $s = substr($dif, 1);
        $rTime = '';
        if ($f == '-') {
            $rTime = '8';
        } else {
            $rTime = '0';
        }
        $rTime .= dechex($s);

        $ret = array(
            'hex' => $rTime,
            'dif' => $dif
        );
        return (object)$ret;
    }

    private function specPadLen($value, $pad = 2)
    {
        $len = strlen($value);
        $dLen = strtoupper(dechex($len));
        return str_pad($dLen, $pad, '0', STR_PAD_LEFT);
    }

    private function specToBHex($str)
    {
        $str = (string)$str;
        return strtoupper(bin2hex($str));
    }

    private function createValidMsidn(string $msidn)
    {
        //örn : 905556667788
        $msidn = preg_replace('/[^0-9]/', '', $msidn);
        if (substr($msidn, 0, 2) == '00') {
            $msidn = substr($msidn, 2);
        } else {
            if (substr($msidn, 0, 1) == '0') {
                $msidn = substr($msidn, 1);
            }
        }

        if (strlen($msidn) == 10) {
            return '90' . $msidn;
        }
        return $msidn;
    }

}
