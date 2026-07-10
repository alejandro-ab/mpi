<?php

namespace PlacetoPay\MPI\Clients;

use PlacetoPay\MPI\Contracts\MockClientBase;
use PlacetoPay\MPI\Contracts\MPIClient;
use PlacetoPay\MPI\Contracts\MPIException;

class MockClientVersionOne implements MPIClient
{
    use MockClientBase;
    /**
     * Performs a HTTP request and returns the information on array.
     * @param $url
     * @param $method
     * @param $data
     * @param $headers
     * @return array
     * @throws MPIException
     */
    public function execute($url, $method, $data, $headers)
    {
        if (str_contains($url, 'lookup')) {
            return $this->lookup($url, $method, $data, $headers);
        } else {
            $id = explode('/', $url);
            $id = end($id);

            if ($method == 'GET') {
                return $this->query($id);
            } elseif ($method == 'PATCH') {
                return $this->update($id);
            }
            throw new MPIException("Incorrect HTTP Method {$method} ON {$url}");
        }
    }

    /**
     * @param $url
     * @param $method
     * @param $data
     * @param $headers
     * @throws MPIException
     */
    public function lookup($url, $method, $data, $headers): array
    {
        if ($method != 'POST') {
            throw new MPIException("Incorrect HTTP Method {$method} ON {$url}");
        }
        switch ($data['pan']) {
            case '4532840681197602':
                if (isset($data['disable_redirect']) && $data['disable_redirect']) {
                    return [
                        'enrolled' => 'Y',
                        'acs_url' => 'https://pit.3dsecure.net/VbVTestSuiteService/pit1/acsService/paReq?summary=N2Y0YmRjYTUtYzc1Mi00YjQ3LTkwMTQtNGY1ZTkwOGUyMWJh',
                        'pa_req' => 'eJxtUttOwzAM/ZWp722aNqxlcjN1bFwkdhF0EjxmqWGF9ULasu3vSUrHQOIhio9zdGwfB8aHfDf4RFVnZRFZ1HGtARayTLPiNbLWybUdWmMOyVYhTh9Rtgo5zLGuxSsOsjSyVvEDfuAmCBgb+jbzmG8Hrsfs4ZBubOGHwhteyiB9SS0OHZdDX43rYo4H5AS1rJJbUTQchPyY3C04u2AhvQTSQ8hR3U15tRMSm7ISRyDfGShEjnx1H1/NkuUqfgbSJUCWbdGoI6eBC+QEoFU7vm2aqh4Rst/vnbOeI8ucADEEIOduVq2Jai14yFI+n8b7/84ykREQw4BUNMg9l4ZuQN2BG46oP/IZkC4PIjedcKoJuq0eQWWKxH+efqdAO6/0Yo48ZGaYEwI8VGWBmqGd/IkhxVrqGfrrPMDVrTFXNtq02+w6mbWz95u39ZPy6aLNJ/NlHEXG7o5gpDNtmEfpt7YBQIwE6TdJ+p+goz8/5AsrpcPw',
                        'term_url' => $data['redirect_uri'],
                        'md' => '',
                        'transaction_id' => $data['expiration_month'] == '02' ? 2 : 1,
                    ];
                }
                return [
                    'enrolled' => 'Y',
                    'redirect_url' => 'https://dnetix.co/ping/3ds',
                    'transaction_id' => $data['expiration_month'] == '02' ? 2 : 1,
                ];
                break;
            case '4716036206946551':
                if (!isset($data['installments']) || $data['installments'] > 36) {
                    throw new MPIException('Installments are not provided');
                }
                break;
            case '5554575520765108':
                if ($data['redirect_uri'] != 'https://example.com/return') {
                    throw new MPIException('Redirect URL does not match');
                }
                return [
                    'enrolled' => 'N',
                    'eci_flag' => '07',
                ];
            case '6011499026766178':
                if ($headers['Authorization'] != 'Bearer VALID_ONE') {
                    throw new MPIException('Api Key is not VALID_ONE');
                }
                break;
        }
        return [
            'enrolled' => 'N',
            'eci_flag' => '07',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function query($id)
    {
        switch ($id) {
            case 1:
                return [
                    'authentication_status' => 'Y',
                    'validated_signature' => '1',
                    'eci' => '05',
                    'cavv' => 'AAACCZJiUGVlF4U5AmJQEwAAAAA=',
                    'xid' => 'Z8UuHYF8Epz46M8V/MkGJDl2Y5E=',
                ];
                break;
            case 2:
                return [
                    'authentication_status' => 'A',
                    'validated_signature' => '1',
                    'eci' => '06',
                    'cavv' => 'CAACAlRGNFVVBEYZGUY0EwAAAAA=',
                    'xid' => '0CI2blBv4uSnIqelFXJX0mV+fMg=',
                ];
                break;
            case 3:
                return [
                    'authentication_status' => 'Y',
                    'validated_signature' => null,
                    'eci' => '05',
                    'cavv' => 'AAACA1aTWUhYcxeGg5NZEAAAEAA=',
                    'xid' => 'UbRrlDARTXFT8GVALigF4MDyhkk=',
                ];
                break;
        }
    }

    /**
     * @param $id
     */
    public function update($id): array
    {
        return [
            'id' => $id,
            'reference' => 'Test reference',
            'created_at' => '2019-08-28 14:34:23',
            'merchant' => [
                'id' => 1,
                'name' => 'EGM Ingenería sin Fronteras',
                'brand' => 'PlacetoPay',
            ],
            'truncated_pan' => '401200******1112',
            'amount' => '75000.00',
            'amount_formatted' => '$750.00',
            'protocol' => '1.0.2',
            'currency' => [
                'currency' => 'US Dollar',
                'alphabetic_code' => 'USD',
                'numeric_code' => '840',
                'minor_unit' => 2,
            ],
            'payment' => [
                'processor' => 'processorTest',
                'authorization' => 'autorizationCode',
                'provider' => 'Interdin',
                'base24' => 'xid',
                'iso' => null,
            ],
            'verification_response' => [
                'status' => 'Y',
                'text' => 'Card enrolled',
            ],
            'authentication_response' => [
                'status' => 'Y',
                'text' => 'Full Authentication',
            ],
            'eci_response' => [
                'status' => 'success',
                'code' => '05',
                'text' => 'Eci => 05',
            ],
            'validated_signature' => true,
            'franchise' => [
                'brand' => 'visa',
            ],
        ];
    }
}
