<?php

namespace PlacetoPay\MPI\Tests\Functionality;

use PlacetoPay\MPI\Constants\MPI;
use PlacetoPay\MPI\MPIService;
use PlacetoPay\MPI\Tests\BaseTestCase;

class QueryProcessTest extends BaseTestCase
{
    public function create($overwrite = [])
    {
        return new MPIService(array_merge([
            'url' => getenv('MPI_URL'),
            'apiKey' => getenv('MPI_API_KEY'),
            'client' => \PlacetoPay\MPI\Clients\MockClientVersionOne::instance(),
        ], $overwrite));
    }

    public function testItObtainsAQuerySuccessfully()
    {
        $mpi = $this->create();

        $response = $mpi->query(1);

        $this->assertTrue($response->isAuthenticated());
        $this->assertTrue($response->validSignature());
        $this->assertEquals('05', $response->eci());
        $this->assertEquals('AAACCZJiUGVlF4U5AmJQEwAAAAA=', $response->cavv());
        $this->assertEquals('Z8UuHYF8Epz46M8V/MkGJDl2Y5E=', $response->xid());

        $this->assertArrayNotHasKey('extra', $response->toArray());
    }

    public function testItDoesNotAuthenticateWhenResponseIsInvalid()
    {
        $mpi = $this->create();

        $response = $mpi->query(2);

        $this->assertFalse($response->isAuthenticated());
        $this->assertTrue($response->validSignature());
        $this->assertEquals('06', $response->eci());
        $this->assertEquals('CAACAlRGNFVVBEYZGUY0EwAAAAA=', $response->cavv());
        $this->assertEquals('0CI2blBv4uSnIqelFXJX0mV+fMg=', $response->xid());

        $this->assertArrayNotHasKey('extra', $response->toArray());
    }

    public function testItDoesNotFallForInvalidAuthentications()
    {
        $mpi = $this->create();

        $response = $mpi->query(3);

        $this->assertFalse($response->isAuthenticated());
        $this->assertFalse($response->validSignature());
        $this->assertEquals('05', $response->eci());
        $this->assertEquals('AAACA1aTWUhYcxeGg5NZEAAAEAA=', $response->cavv());
        $this->assertEquals('UbRrlDARTXFT8GVALigF4MDyhkk=', $response->xid());

        $this->assertArrayNotHasKey('extra', $response->toArray());
    }

    public function testItGetsAnArrayWithTheInformation()
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

        $this->assertArrayNotHasKey('extra', $response->toArray());
    }

    public function testItObtainsQueryVersionTwoSuccessfully()
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

        $this->assertEquals([
            'transStatusReason' => null,
            'acsTransId' => '37a7b6e0-fd58-4e38-98de-79c70c526a47',
            'threeDSServerTransID' => 'eadd3a60-b870-41d0-977f-921b3dbe6323/MkGJDl2Y5E=',
            'messageVersion' => null,
        ], $response->toArray()['extra']);
    }

    public function testItDoesAuthenticateOnTreeDSServer()
    {
        $mpi = $this->create([
            '3dsVersion' => MPI::VERSION_TWO,
            'client' => \PlacetoPay\MPI\Clients\MockClientVersionTwo::instance(),
        ]);

        $response = $mpi->query(2);
        $this->assertEquals('Y', $response->toArray()['enrolled']);
        $this->assertFalse($response->isAuthenticated());
        $this->assertEquals('U', $response->authenticated());

        $this->assertEquals([
            'transStatusReason' => '22',
            'acsTransId' => '155222d5-3933-475b-a153-db899eee38b2',
            'threeDSServerTransID' => '515ba5ef-100e-4040-8028-df915f9fcdab',
            'messageVersion' => null,
        ], $response->toArray()['extra']);
    }

    public function testItHandlesTheNewQueryResponseOnV2()
    {
        $mpi = $this->create([
            '3dsVersion' => MPI::VERSION_TWO,
            'client' => \PlacetoPay\MPI\Clients\MockClientVersionTwo::instance(),
        ]);

        $response = $mpi->query(7);
        $this->assertEquals('Y', $response->enrolled());
        $this->assertTrue($response->isAuthenticated());
        $this->assertEquals('Y', $response->authenticated());

        $this->assertEquals([
            'transStatusReason' => null,
            'acsTransId' => '173f9515-41b3-4cfc-a9a4-5d8c839faad6',
            'threeDSServerTransID' => '3ed1f41b-30bc-435b-90eb-40028a57d4ea',
            'messageVersion' => '2.2.0',
        ], $response->toArray()['extra']);
    }

    public function testItHandlesANonEnrolledInsteadOfError()
    {
        $mpi = $this->create([
            '3dsVersion' => MPI::VERSION_TWO,
            'client' => \PlacetoPay\MPI\Clients\MockClientVersionTwo::instance(),
        ]);

        $response = $mpi->query(8);
        $this->assertEquals('N', $response->enrolled());
        $this->assertNull($response->authenticated());

        $this->assertEquals([
            'transStatusReason' => null,
            'acsTransId' => null,
            'threeDSServerTransID' => '6063ae4f-a3f2-466e-926c-f568b17ace44',
            'messageVersion' => '2.1.0',
        ], $response->toArray()['extra']);
    }
}
