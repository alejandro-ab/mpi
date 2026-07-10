<?php

namespace PlacetoPay\MPI\Clients;

use PlacetoPay\MPI\Constants\MPI;
use PlacetoPay\MPI\Contracts\MockClientBase;
use PlacetoPay\MPI\Contracts\MPIClient;
use PlacetoPay\MPI\Contracts\MPIException;

class MockClientVersionTwo implements MPIClient
{
    use MockClientBase;

    protected ?string $url = null;
    protected ?string $method = null;
    protected ?array $data = null;

    /**
     * {@inheritdoc}
     * @throws MPIException
     */
    public function execute($url, $method, $data, $headers)
    {
        $this->url = $url;
        $this->method = $method;
        $this->data = $data;

        if (str_contains($url, MPI::LOOKUP_ENDPOINTS[MPI::VERSION_TWO])) {
            return $this->lookup($url, $method, $data);
        } else {
            $id = explode('/', $url);
            $id = end($id);

            if ($method == 'PATCH') {
                return $this->update($id);
            } else {
                return $this->query($url, $method);
            }
        }
    }

    /**
     * @param $url
     * @param $method
     * @param $data
     * @throws MPIException
     */
    public function lookup($url, $method, $data): array
    {
        if ($method != 'POST') {
            throw new MPIException("Incorrect HTTP Method {$method} ON {$url}");
        }

        switch ($data['acctNumber']) {
            case '4532840681197602':
                return [
                    'sessionToken' => rand(60, 60),
                    'redirectURL' => 'https://dnetix.co/ping/3ds',
                    'transactionID' => substr($data['cardExpiryDate'], -2),
                ];
                break;
            case '5554575520765108':
                return [
                    'error_number' => 1004,
                    'error_description' => 'There is no subscription associated',
                ];
            case '4716036206946551':
                return [
                        'error_number' => 1011,
                        'error_description' =>  'Invalid arguments to initiate the authentication request',
                        'errors' => [
                            'acctNumber'=> [
                                "The card number doesn't pass validation",
                            ],
                        ],
                ];
                break;
            case '6011499026766178':
                return [
                    'error_number' => 1011,
                    'error_description' =>  'Invalid arguments to initiate the authentication request',
                    'errors' => [
                        'acctNumber'=> [
                            'The card number is invalid',
                        ],
                    ],
                ];
                break;
            default:
                return [
                    'sessionToken' => rand(60, 60),
                    'redirectURL' => 'https://dnetix.co/ping/3ds',
                    'transactionID' => substr($data['cardExpiryDate'], -2),
                ];
        }
    }

    /**
     * @param $url
     * @param $method
     * @return array|mixed
     * @throws MPIException
     */
    public function query($url, $method)
    {
        $id = explode('/', $url);
        $id = end($id);

        if ($method == 'GET') {
            switch ($id) {
                case 1:
                    return [
                        'transStatus' => 'Y',
                        'eci' => '05',
                        'acsTransID' => '37a7b6e0-fd58-4e38-98de-79c70c526a47',
                        'dsTransID' => 'de018c08-bd14-426a-9d52-46500a17091e=',
                        'threeDSServerTransID' => 'eadd3a60-b870-41d0-977f-921b3dbe6323/MkGJDl2Y5E=',
                        'authenticationValue' => 'AAABBZEEBgAAAAAAAAQGAAAAAAA=',
                    ];
                    break;
                case 2:
                    return [
                        'transStatus' => 'U',
                        'transStatusReason' => '22',
                        'eci' => '07',
                        'acsTransID' => '155222d5-3933-475b-a153-db899eee38b2',
                        'dsTransID' => '51e8bbbb-5316-4a6b-a301-726a46a02dc5',
                        'threeDSServerTransID' => '515ba5ef-100e-4040-8028-df915f9fcdab',
                    ];
                case 3:
                    return [
                        'transStatus' => 'N',
                        'transStatusReason' => '01',
                        'eci' => '07',
                        'acsTransID' => '52afc3e7-84fd-4420-bdc4-236901fbf09f',
                        'dsTransID' => 'e11cb28d-470b-4a7b-96ca-86ad813fa16d',
                        'threeDSServerTransID' => 'd27082af-6c3f-4664-95a2-319b07f1ba8b',
                    ];
                case 4:
                    return [
                        'transStatus' => 'R',
                        'transStatusReason' => '11',
                        'eci' => '07',
                        'acsTransID' => 'fb068cda-a161-4c8c-86e9-78433f3b69c0',
                        'dsTransID' => '38148c14-9cc6-4e91-a78a-005d7b6f8d51',
                        'threeDSServerTransID' => '728ac7b6-136b-4a5f-a32a-04bab37b5796',
                    ];
                case 5:
                    return [
                        'transStatus' => 'I',
                        'eci' => '07',
                        'acsTransID' => '5c5814d5-8cdd-44b2-bd1c-3f50b7e8e699',
                        'dsTransID' => 'dee10bd1-7139-46c4-a5ec-40948af1c591',
                        'threeDSServerTransID' => 'd98a3261-7da3-4b1a-98cc-e14a07edfdc6',
                        'authenticationValue' => 'AAABBZEEBgAAAAAAAAQGAAAAAAA=',
                    ];
                case 6:
                    return [
                        'transStatus' => 'A',
                        'eci' => '06',
                        'acsTransID' => '42e4727a-77ac-44a3-8b8c-c58b48e48cc8',
                        'dsTransID' => 'a59c5a5d-f041-4c23-ad22-eca4ee9060b6',
                        'threeDSServerTransID' => 'b5723933-f822-43af-b82f-b48b4983da81',
                        'authenticationValue' => 'AAABBZEEBgAAAAAAAAQGAAAAAAA=',
                    ];
                case 7:
                    return [
                        'enrolled' => null,
                        'transStatus' => 'Y',
                        'transStatusReason' => null,
                        'eci' => '05',
                        'acsTransID' => '173f9515-41b3-4cfc-a9a4-5d8c839faad6',
                        'dsTransID' => '5fd81313-e21c-4018-b049-5c2e2a9a527d',
                        'threeDSServerTransID' => '3ed1f41b-30bc-435b-90eb-40028a57d4ea',
                        'sdkTransID' => null,
                        'authenticationValue' => 'AAEBBViWAgAABJPghAMTdUeHIJE=',
                        'messageVersion' => '2.2.0',
                    ];
                case 8:
                    return [
                        'enrolled' => 'N',
                        'transStatus' => null,
                        'transStatusReason' => null,
                        'eci' => '00',
                        'acsTransID' => null,
                        'dsTransID' => null,
                        'threeDSServerTransID' => '6063ae4f-a3f2-466e-926c-f568b17ace44',
                        'sdkTransID' => null,
                        'authenticationValue' => null,
                        'messageVersion' => '2.1.0',
                    ];
            }
        }

        throw new MPIException("Incorrect HTTP Method {$method} ON {$url}");
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

    public function lastUrl(): ?string
    {
        return $this->url;
    }

    public function lastMethod(): ?string
    {
        return $this->method;
    }

    public function lastData(): ?array
    {
        return $this->data;
    }
}
