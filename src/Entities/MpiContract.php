<?php

namespace PlacetoPay\MPI\Entities;

use PlacetoPay\MPI\Contracts\LookupResponse;
use PlacetoPay\MPI\Contracts\QueryResponse;
use PlacetoPay\MPI\Contracts\Request;

interface MpiContract
{
    /**
     * Instantiate the search method according to the version of 3ds.
     * @param  array  $data.
     * @return Request.
     */
    public function lookup(array $data): Request;

    /**
     * Return a lookup response handler according to the 3ds version.
     */
    public function lookupResponse(array $data): LookupResponse;

    public function lookupEndpoint(): string;

    /**
     * @param $id
     */
    public function queryEndpoint($id): string;

    /**
     * Return a query response handler according to the 3ds version.
     * @param $id
     */
    public function queryResponse(array $data, $id): QueryResponse;
}
