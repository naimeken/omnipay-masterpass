<?php
/**
 * Masterpass Authorize AbstractResponse
 */

namespace Omnipay\Masterpass\Messages;

use Exception;
use RuntimeException;

class AuthorizeResponse extends AbstractResponse
{
    /**
     * @return string
     */
    public function getToken(): string
    {
        $phone = $this->createValidPHone();
        $dateTime = date('YmdHis');
        $data = 'FF01' . $this->specPadLen($this->getClientId()) . $this->specToBHex($this->getClientId())
            . 'FF02' . '01' . $this->getTimeZone()
            . 'FF03' . $this->specPadLen($dateTime) . $this->specToBHex($dateTime)
            . 'FF04' . $this->specPadLen($phone) . $this->specToBHex($phone)
            . 'FF05' . $this->specPadLen($this->getTransactionReference()) . $this->specToBHex($this->getTransactionReference())
            . 'FF06' . $this->specPadLen($this->getUserId()) . $this->specToBHex($this->getUserId())
            . 'FF07' . '01' . '00';

        if (strlen($data) % 32 !== 0) {
            $data .= '8';
            $padC = ceil(strlen($data) / 32) * 32;
            $data = str_pad($data, $padC, '0', STR_PAD_RIGHT);
        }
        return $this->prepareToken($data);
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
     * @return string
     */
    private function getTimeZone(): string
    {
        $p = date('P');
        $x = explode(':', $p);
        $dif = $x[0];
        $f = substr($dif, 0, 1);
        $s = substr($dif, 1);

        if ($f === '-') {
            $rTime = '8';
        } else {
            $rTime = '0';
        }

        $rTime .= dechex($s);

        return $rTime;
    }

    /**
     * @param string $value
     * @param int $pad
     * @return string
     */
    private function specPadLen(string $value, int $pad = 2): string
    {
        $len = strlen($value);
        $dLen = strtoupper(dechex($len));
        return str_pad($dLen, $pad, '0', STR_PAD_LEFT);
    }

    /**
     * @param string $str
     * @return string
     */
    private function specToBHex(string $str): string
    {
        return strtoupper(bin2hex($str));
    }

    /**
     * @return string
     */
    private function createValidPHone(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->getPhone());

        if (strpos($phone, '00') === 0) {
            $phone = substr($phone, 2);
        } elseif (strpos($phone, '0') === 0) {
            $phone = substr($phone, 1);
        }

        if (strlen($phone) === 10) {
            return '90' . $phone;
        }

        return $phone;
    }

    /**
     * @param string $data
     * @return string
     */
    private function prepareToken(string $data): string
    {
        // $iv = '00000000000000000000000000000000';
        // $iv = pack('H*', $iv);
        try {
            $iv = random_bytes(32);
        } catch (Exception $exception) {
            throw new RuntimeException($exception);
        }
        $encKey = $this->getEncKey();
        $encKey = pack('H*', $encKey);
        $packData = pack('H*', $data);
        $encryptData = openssl_encrypt($packData, 'aes-128-cbc', $encKey, OPENSSL_RAW_DATA, $iv);
        $encryptData2 = strtoupper($encryptData);
        $macKey = hash_hmac('SHA1', $encryptData2, $this->getMacKey());

        return $encryptData2 . strtoupper($macKey);
    }
}
