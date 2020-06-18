<?php

namespace Omnipay\Masterpass\Messages;

use Exception;
use SoapClient;
use SoapFault;

trait BaseSoapService
{
    /**
     * @param string $endpoint
     * @param string $function
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public function makeRequestToService(string $endpoint, string $function, array $data = [])
    {
        try {
            $client = new SoapClient($endpoint, $this->getClientOptions());

            $response = $client->$function($data);
        } catch (SoapFault $exception) {
            $response = $exception->detail;
        }
        return $response;
    }

    /**
     * @param array $options
     * @return array
     */
    private function getClientOptions(array $options = []): array
    {
        $defaults = array(
            'trace' => 1,
            'exceptions' => true
        );

        return array_merge($defaults, $options);
    }
}
