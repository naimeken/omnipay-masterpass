<?php

namespace Examples;


class Helper
{

    /**
     * @return array
     */
    public function getAuthorizeParams(): array
    {
        $params = [
            'transactionReference' => '324324',
            'sendSms' => 'Y',
            'sendSmsLanguage' => 'tur',
            'userId' => 'abc123',
            'mode' => 'test',
            'phone' => '905555555555',
            'timezone' => '03',
            'merchantType' => '00',
            'validationType' => '00',
            'validatedPhone' => '01'
        ];

        return $this->provideMergedParams($params);
    }

    /**
     * @return array
     */
    public function getPurchaseParams(): array
    {
        $params = $this->getDefaultPurchaseParams();

        return $this->provideMergedParams($params);
    }

    /**
     * @return array
     */
    public function getPurchase3dParams(): array
    {
        $params = [
            'transactionReference' => '48019',
            'sendSms' => 'Y',
            'sendSmsLanguage' => 'tur',
            'clientIp' => '11.11.11.11',
            'mode' => 'test',
            'bankIca' => '2110',
            'paymentType' => '3d',
            'amount' => '28140',
            'mdStatus' => '1',
            'token' => '8A9AC15C5CE52C159D2A845C6B7D090BADB133A08FF674EBCCB8851583A5B045A5414A627319839985DA30BCE848805BAA4B858B031451982B48297B56E74D9C31440E43FEB4A734D57F09B83E30F43183A1CD3323319E7DC9F1701AB00884D871421C4',
            'phone' => 'xxx',
            'storeKey' => "123456",
            'clientId' => '100100000',
            'oid' => '48019',
            'authCode' => '',
            'procReturnCode' => '',
            'cavv' => '',
            'eci' => '',
            'md' => '435508:801AB3C3E0DA3E528D8D9C1EDF341FE62F0235603E3C164FD5820771893CE0A6:3589:##100100000',
            'rnd' => '1OGg16nQI0AwvFF0dq76',
            'hash' => 'O7ViTF0KaK503/pu4iqbfTuP3U8=',
            'hashParams' => 'clientid:oid:mdStatus:cavv:eci:md:rnd:',
            'hashParamsVal' => '100100000480190435508:801AB3C3E0DA3E528D8D9C1EDF341FE62F0235603E3C164FD5820771893CE0A6:3589:##1001000001OGg16nQI0AwvFF0dq76',
            'responseCode' => '4058'
        ];

        return $this->provideMergedParams($params);
    }

    /**
     * @return array
     */
    public function getPayUPurchaseParams(): array
    {
        $params = $this->getPayUPurchase3dParams();
        $params['paymentType'] = 'direct';

        return $params;
    }

    /**
     * @return array
     */
    public function getPayUPurchase3dParams(): array
    {
        $params = [
            'transactionReference' => '47974',
            'sendSms' => 'Y',
            'sendSmsLanguage' => 'tur',
            'clientIp' => 'xxxx',
            'mode' => 'test',
            'bankIca' => '1000',
            'paymentType' => '3d',
            'amount' => '28140',
            'mdStatus' => '1',
            'token' => 'xxx',
            'phone' => 'xxx',
            'storeKey' => "u+f0?H8]O9_|p4]+H=T3",
            'clientId' => 'xxxx',
            'oid' => '47974',
            'authCode' => '',
            'procReturnCode' => '',
            'cavv' => '',
            'eci' => '',
            'md' => 'xxxx',
            'rnd' => 'bpEeh/85DfvdZ58NWTDX',
            'hash' => '4ee5e4ccf669b26ad333f21a81a21dae',
            'hashParams' => 'clientid:oid:mdStatus:cavv:eci:md:rnd:',
            'hashParamsVal' => 'xxxx',
            'optionalParameters' => $this->getOptionalParameters(), // optional
            'responseCode' => '0000'
        ];

        return $this->provideMergedParams($params);
    }

    /**
     * @return array
     */
    private function getOptionalParameters(): array
    {
        return [
            'order_details' => [
                'orders' => [
                    'list_item' => [
                        [
                            'order_product_name' => 'test',
                            'order_product_code' => 1,
                            'order_price' => 100,
                            'order_vat' => '', // optional
                            'order_qty' => 1,
                            'order_product_info' => 'desc' // optional
                        ],
                        [
                            'order_product_name' => 'test1',
                            'order_product_code' => 2,
                            'order_price' => 100,
                            'order_vat' => '', // optional
                            'order_qty' => 1,
                            'order_product_info' => 'desc1' // optional
                        ]
                    ]
                ],
                'bill_details' => [
                    'bill_last_name' => 'test1',
                    'bill_first_name' => 'test1',
                    'bill_email' => 'test1@test.com',
                    'bill_phone' => '905555555555',
                    'bill_country_code' => 'TR',
                    'bill_fax' => '', // optional
                    'bill_address' => '', // optional
                    'bill_address2' => '', // optional
                    'bill_zip_code' => 34250, // optional
                    'bill_city' => '', // optional
                    'bill_state' => '' // optional
                ],
                'delivery_details' => [
                    'delivery_last_name' => 'test1', // optional
                    'delivery_first_name' => 'test1', // optional
                    'delivery_email' => 'test1@test.com', // optional
                    'delivery_phone' => '905555555555', // optional
                    'delivery_company' => '', // optional
                    'delivery_country_code' => 'TR', // optional
                    'delivery_address' => '', // optional
                    'delivery_address2' => '', // optional
                    'delivery_zip_code' => 34250, // optional
                    'delivery_city' => '', // optional
                    'delivery_state' => '' // optional
                ]
            ]
        ];

    }

    /**
     * @return array
     */
    private function getDefaultOptions(): array
    {
        return [
            'testMode' => true,
            'merchantId' => '34703818',
            'macKey' => '1111',
            'encKey' => '1111'
        ];
    }

    /**
     * @param array $params
     * @return array
     */
    private function provideMergedParams(array $params): array
    {
        $params = array_merge($params, $this->getDefaultOptions());
        return $params;
    }

    /**
     * @return array
     */
    protected function getDefaultPurchaseParams(): array
    {
        return [
            'transactionReference' => '48019',
            'sendSms' => 'Y',
            'sendSmsLanguage' => 'tur',
            'clientIp' => '88.119.14.245',
            'mode' => 'test',
            'bankIca' => '2110',
            'paymentType' => 'direct',
            'amount' => '28140',
            'mdStatus' => '1',
            'token' => '8A9AC15C5CE52C159D2A845C6B7D090BADB133A08FF674EBCCB8851583A5B045A5414A627319839985DA30BCE848805BAA4B858B031451982B48297B56E74D9C31440E43FEB4A734D57F09B83E30F43183A1CD3323319E7DC9F1701AB00884D871421C46',
            'phone' => 'xxx',
            'storeKey' => "123456",
            'clientId' => '100100000',
            'oid' => '48019',
            'authCode' => '',
            'procReturnCode' => '',
            'cavv' => '',
            'eci' => '',
            'md' => '435508:801AB3C3E0DA3E528D8D9C1EDF341FE62F0235603E3C164FD5820771893CE0A6:3589:##100100000',
            'rnd' => '1OGg16nQI0AwvFF0dq76',
            'hash' => '42dFfzdHA31tEpoXW7e7nb7lBs0g=',
            'hashParams' => 'clientid:oid:mdStatus:cavv:eci:md:rnd:',
            'hashParamsVal' => '100100000480190435508:801AB3C3E0DA3E528D8D9C1EDF341FE62F0235603E3C164FD5820771893CE0A6:3589:##1001000001OGg16nQI0AwvFF0dq76',
            'responseCode' => '4058'
        ];
    }
}

