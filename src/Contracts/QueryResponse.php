<?php

namespace PlacetoPay\MPI\Contracts;

use PlacetoPay\MPI\Constants\MPI;
use PlacetoPay\MPI\Messages\MPIBaseMessage;

abstract class QueryResponse extends MPIBaseMessage
{
    abstract public function id();

    abstract public function isAuthenticated(): bool;

    abstract public function enrolled(): ?string;

    abstract public function authenticated(): ?string;

    abstract public function eci(): string;

    abstract public function cavv(): ?string;

    abstract public function xid(): ?string;

    abstract public function version(): string;

    abstract public function extra(): array;

    abstract public function validSignature(): bool;

    public function toArray(): array
    {
        $data = [
            'id' => $this->id(),
            'enrolled' => $this->enrolled(),
            'authenticated' => $this->authenticated(),
            'validSignature' => $this->validSignature(),
            'eci' => $this->eci(),
            'cavv' => $this->cavv(),
            'xid' => $this->xid(),
            'version' => $this->version(),
        ];

        if ($this->version() === MPI::VERSION_TWO) {
            $data['extra'] = $this->extra();
        }

        return $data;
    }

    public static function readVersion(string $version): string
    {
        if (str_starts_with($version, '1')) {
            return MPI::VERSION_ONE;
        }

        return MPI::VERSION_TWO;
    }
}
