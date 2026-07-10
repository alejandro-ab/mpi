<?php

namespace PlacetoPay\MPI\Tests\Functionality;

use PlacetoPay\MPI\MPIService;
use PlacetoPay\MPI\Tests\BaseTestCase;

class ProcessTest extends BaseTestCase
{
    public function create($overwrite = []): MPIService
    {
        return new MPIService(array_merge([
            'url' => 'https://dev.placetopay.ec/3ds-mpi/',
            'apiKey' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6Ijk4ZTFmMTVmZTk1ZDg4NTE4NjEyYTFhMjBlNDBmODA2ODA1MDllOWQ3Y2I1Yzg5ZTc3ZmRhY2U3OTRhMjZhZjk5ZjBjNjI5ZTc4MjZkZTVhIn0.eyJhdWQiOiIxIiwianRpIjoiOThlMWYxNWZlOTVkODg1MTg2MTJhMWEyMGU0MGY4MDY4MDUwOWU5ZDdjYjVjODllNzdmZGFjZTc5NGEyNmFmOTlmMGM2MjllNzgyNmRlNWEiLCJpYXQiOjE1MjU4MDcwMDYsIm5iZiI6MTUyNTgwNzAwNiwiZXhwIjoxNTU3MzQzMDA2LCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.e4tvpy2tCXOsoz3fDhD8QI4guFZOmLq0TIv4491F1vi8WGGtxAT-68E5pJXHzKwocpF8mBDSz2Rlhmuypktv9nNEVTFPp18A7hReZlsjMqumOiUxCDF4YCejcfQOrY2NVxIvB8yrnQBaYXFD8au2EXfjAeSsOnrPjqZoTsK3XXY',
            'client' => \PlacetoPay\MPI\Clients\MockClientVersionOne::instance(),
        ], $overwrite));
    }

    public function testItObtainsAQuerySuccessfully(): void
    {
        $mpi = $this->create();

        $response = $mpi->lookUp([
            'card' => [
                'number' => '4012001037141112',
                'expirationYear' => '20',
                'expirationMonth' => '12',
            ],
            'amount' => 1200,
            'currency' => 'COP',
            'redirectUrl' => 'https://dnetix.co/ping/3ds',
            'userAgent' => 'Testing',
        ]);

        $this->assertEquals('N', $response->enrolled());
    }
}
