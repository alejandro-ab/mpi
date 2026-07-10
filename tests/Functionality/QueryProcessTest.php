<?php

namespace PlacetoPay\MPI\Tests\Functionality;

use PlacetoPay\MPI\Constants\MPI;
use PlacetoPay\MPI\MPIService;
use PlacetoPay\MPI\Tests\BaseTestCase;

class QueryProcessTest extends BaseTestCase
{
    public function create($overwrite = []): MPIService
    {
        return new MPIService(array_merge([
            'url' => getenv('MPI_URL'),
            'apiKey' => getenv('MPI_API_KEY'),
            'client' => \PlacetoPay\MPI\Clients\MockClientVersionOne::instance(),
        ], $overwrite));
    }

    public function testItObtainsAQuerySuccessfully(): void
    {
        $mpi = $this->create();

        $response = $mpi->query(1);

        $this->assertTrue($response->isAuthenticated());
        $this->assertTrue($response->validSignature());
        $this->assertEquals('05', $response->eci());
        $this->assertEquals('AAACCZJiUGVlF4U5AmJQEwAAAAA=', $response->cavv());
        $this->assertEquals('Z8UuHYF8Epz46M8V/MkGJDl2Y5E=', $response->xid());
    }

    public function testItDoesNotAuthenticateWhenResponseIsInvalid(): void
    {
        $mpi = $this->create();

        $response = $mpi->query(2);

        $this->assertFalse($response->isAuthenticated());
        $this->assertTrue($response->validSignature());
        $this->assertEquals('06', $response->eci());
        $this->assertEquals('CAACAlRGNFVVBEYZGUY0EwAAAAA=', $response->cavv());
        $this->assertEquals('0CI2blBv4uSnIqelFXJX0mV+fMg=', $response->xid());
    }

    public function testItDoesNotFallForInvalidAuthentications(): void
    {
        $mpi = $this->create();

        $response = $mpi->query(3);

        $this->assertFalse($response->isAuthenticated());
        $this->assertFalse($response->validSignature());
        $this->assertEquals('05', $response->eci());
        $this->assertEquals('AAACA1aTWUhYcxeGg5NZEAAAEAA=', $response->cavv());
        $this->assertEquals('UbRrlDARTXFT8GVALigF4MDyhkk=', $response->xid());
    }

    public function testItGetsAnArrayWithTheInformation(): void
    {
        $mpi = $this->create();

        $response = $mpi->query(1);

        $this->assertEquals([
            'validSignature' => true,
            'eci' => '05',
            'cavv' => 'AAACCZJiUGVlF4U5AmJQEwAAAAA=',
            'xid' => 'Z8UuHYF8Epz46M8V/MkGJDl2Y5E=',
            'enrolled' => 'Y',
            'authenticated' => 'Y',
            'version' => MPI::VERSION_ONE,
            'id' => 1,
        ], $response->toArray());
    }

    public function testItObtainsQueryVersionTwoSuccessfully(): void
    {
        $mpi = $this->create([
            '3dsVersion' => MPI::VERSION_TWO,
            'client' => \PlacetoPay\MPI\Clients\MockClientVersionTwo::instance(),
        ]);

        $response = $mpi->query(1);

        $this->assertArrayHasKey('authenticated', $response->toArray());
        $this->assertArrayHasKey('eci', $response->toArray());
        $this->assertArrayHasKey('xid', $response->toArray());
        $this->assertArrayHasKey('cavv', $response->toArray());
        $this->assertArrayHasKey('extra', $response->toArray());

        $this->assertTrue($response->isAuthenticated());
        $this->assertEquals('AAABBZEEBgAAAAAAAAQGAAAAAAA=', $response->cavv());
        $this->assertEquals('05', $response->eci());
    }

    public function testItDoesAuthenticateOnTreeDSServer(): void
    {
        $mpi = $this->create([
            '3dsVersion' => MPI::VERSION_TWO,
            'client' => \PlacetoPay\MPI\Clients\MockClientVersionTwo::instance(),
        ]);

        $response = $mpi->query(2);
        $this->assertEquals('Y', $response->toArray()['enrolled']);
        $this->assertFalse($response->isAuthenticated());
        $this->assertEquals('U', $response->authenticated());
    }

    public function testItHandlesTheNewQueryResponseOnV2(): void
    {
        $mpi = $this->create([
            '3dsVersion' => MPI::VERSION_TWO,
            'client' => \PlacetoPay\MPI\Clients\MockClientVersionTwo::instance(),
        ]);

        $response = $mpi->query(7);
        $this->assertEquals('Y', $response->enrolled());
        $this->assertTrue($response->isAuthenticated());
        $this->assertEquals('Y', $response->authenticated());
    }

    public function testItHandlesANonEnrolledInsteadOfError(): void
    {
        $mpi = $this->create([
            '3dsVersion' => MPI::VERSION_TWO,
            'client' => \PlacetoPay\MPI\Clients\MockClientVersionTwo::instance(),
        ]);

        $response = $mpi->query(8);
        $this->assertEquals('N', $response->enrolled());
        $this->assertNull($response->authenticated());
    }
}
