<?php

namespace PlacetoPay\MPI\Messages;

use PlacetoPay\MPI\Constants\MPI;
use PlacetoPay\MPI\Contracts\QueryResponse;

class QueryResponseVersionTwo extends QueryResponse
{
    private $id;
    private $version;
    private $transStatus;
    private $eci;
    private $acsTransID;
    private $dsTransID;
    private $threeDSServerTransID;
    private $authenticationValue;
    private $transStatusReason;
    private $enrolled;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->version = $data['version'] ?? MPI::VERSION_TWO;
        $this->transStatus = $data['transStatus'];
        $this->eci = $data['eci'];
        $this->acsTransID = $data['acsTransID'] ?? null;
        $this->dsTransID = $data['dsTransID' ?? null];
        $this->threeDSServerTransID = $data['threeDSServerTransID'] ?? null;
        $this->authenticationValue = $data['authenticationValue'] ?? null;
        $this->transStatusReason = $data['transStatusReason'] ?? null;
        $this->enrolled = $data['enrolled'] ?? null;
    }

    public function id()
    {
        return $this->id;
    }

    /**
     * Returns true if the authentication process has been successfully completed.
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return $this->authenticated() == 'Y' && $this->cavv();
    }

    public function enrolled(): ?string
    {
        return $this->enrolled;
    }

    /**
     * Returns the indicator for the authentication from the ACS
     *  “Y” - Successful Authentication
     *  “A” - Successful Attempt
     *  “N” - Failed Authentication
     *  “U” - Unable to Authenticate
     *  ”C” - Challenge Required
     *  ”D” - Challenge Required
     *  ”R” - Failed Authenticate
     *  ”I” - Challenge Required only informative.
     * @return string
     */
    public function authenticated(): ?string
    {
        return $this->transStatus;
    }

    /**
     * Represents the Electronic Commerce Indicator
     *  For VISA
     *      05 - Issuer Liability
     *      06 - Issuer/Merchant Liability
     *      07 - Merchant Liability.
     * @return string
     */
    public function eci(): string
    {
        return $this->eci;
    }

    /**
     * Return true if the signature for the ACS response has been validated.
     * @return bool
     */
    public function cavv(): ?string
    {
        return $this->authenticationValue;
    }

    public function acsTransID()
    {
        return $this->acsTransID;
    }

    public function xid(): ?string
    {
        return $this->dsTransID;
    }

    public function reasonCode()
    {
        return $this->transStatusReason;
    }

    public function threeDSServerTransID()
    {
        return $this->threeDSServerTransID;
    }

    public function version(): string
    {
        return $this->version;
    }

    public function validSignature(): bool
    {
        return true;
    }

    public function extra(): array
    {
        return [
            'transStatusReason' => $this->reasonCode(),
            'acsTransId' => $this->acsTransID(),
            'threeDSServerTransID' => $this->threeDSServerTransID(),
        ];
    }

    public static function loadFromResult($result, $id = null)
    {
        parent::loadFromResult($result);

        $data = [
            'id' => $id,
            'enrolled' => $result['enrolled'] ?? 'Y',
            'transStatus' => $result['transStatus'],
            'eci' => $result['eci'],
            'acsTransID' => $result['acsTransID'] ?? null,
            'dsTransID' => $result['dsTransID'] ?? null,
            'threeDSServerTransID' => $result['threeDSServerTransID'] ?? null,
            'authenticationValue' => $result['authenticationValue'] ?? null,
            'transStatusReason' => $result['transStatusReason'] ?? null,
            'version' => self::readVersion($result['messageVersion'] ?? '2.1.0'),
        ];

        return new self($data);
    }
}
