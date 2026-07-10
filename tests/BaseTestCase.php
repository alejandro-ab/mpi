<?php

namespace PlacetoPay\MPI\Tests;

use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    public function serialize($data): string
    {
        return base64_encode(serialize($data));
    }

    public function unserialize($coded): mixed
    {
        return unserialize(base64_decode($coded));
    }
}
