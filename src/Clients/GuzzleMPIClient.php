<?php

namespace PlacetoPay\MPI\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use PlacetoPay\MPI\Contracts\MPIClient;
use PlacetoPay\MPI\Contracts\MPIException;
use PlacetoPay\MPI\Exceptions\ServiceException;

class GuzzleMPIClient implements MPIClient
{
    /**
     * Performs a HTTP request and returns the information on array.
     * @param $url
     * @param $method
     * @param $data
     * @param $headers
     * @return array
     * @throws \Exception
     */
    public function execute($url, $method, $data, $headers)
    {
        $client = new Client();
        try {
            if (in_array($method, ['POST', 'PATCH'])) {
                $response = $client->request($method, $url, [
                    'headers' => $headers,
                    'json' => $data,
                ]);
            } elseif ($method == 'GET') {
                $response = $client->get($url, [
                    'headers' => $headers,
                    'json' => $data,
                ]);
            } elseif ($method == 'PUT') {
                $response = $client->put($url, [
                    'headers' => $headers,
                    'json' => $data,
                ]);
            } else {
                throw new \Exception('No valid method for this request');
            }

            $body = $response->getBody()->getContents();
            return json_decode($body, true);
        } catch (BadResponseException $e) {
            throw new MPIException($e->getResponse()->getBody()->getContents());
        } catch (\Exception $exception) {
            throw new ServiceException($exception->getMessage());
        }
    }
}
