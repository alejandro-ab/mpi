<?php

namespace PlacetoPay\MPI\Tests\Functionality;

use PlacetoPay\MPI\Clients\MockClientVersionOne;
use PlacetoPay\MPI\Messages\UpdateTransactionRequest;
use PlacetoPay\MPI\MPIService;
use PlacetoPay\MPI\Tests\BaseTestCase;

class UpdateTransactionTest extends BaseTestCase
{
    public function create($overwrite = []): MPIService
    {
        return new MPIService(array_merge([
            'url' => 'https://3ds-mpi.placetopay.com',
            'apiKey' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImMzZDBjNmE0OGZhZjYyZjI0MGY0NjE2MDgxZGVkNGU2MzQ3MGZkN2ZlYjFiN2Y3MjgxMzMwZjgyMGVjYmZmZjEzNzMwYjc3MTdjMDYzNWY5In0.eyJhdWQiOiIxIiwianRpIjoiYzNkMGM2YTQ4ZmFmNjJmMjQwZjQ2MTYwODFkZWQ0ZTYzNDcwZmQ3ZmViMWI3ZjcyODEzMzBmODIwZWNiZmZmMTM3MzBiNzcxN2MwNjM1ZjkiLCJpYXQiOjE1NjE3MzA2MDAsIm5iZiI6MTU2MTczMDYwMCwiZXhwIjoxNTkzMzUzMDAwLCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.N78xpOeEFJInxIAVeuA0UU5xsEXBjPS0GpPacbHFWtF-VIIlejZVLz-kZjCk0rgb0M7tx525Kn0xs8EnCotkQWbBBJxAnruYH0GicRPF5ANvbXjpzVUmvOwbRKY1gDQA2Z7-cqh1OdoOxnMCOnp2fUhdn4riX4LXciY24ump5gU',
            'client' => MockClientVersionOne::instance(),
        ], $overwrite));
    }

    public function testItConstructTheEntityCorrectly(): void
    {
        $result = $this->create()->update(165150, new UpdateTransactionRequest([
            'processor' => 'INTERDIN',
            'authorization' => '000000',
            'iso' => '05',
        ]));

        $this->assertEquals(75000, $result->amount());
    }
}
