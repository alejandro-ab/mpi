<?php

namespace PlacetoPay\MPI\Messages;

use PlacetoPay\MPI\Constants\MPI;
use PlacetoPay\MPI\Contracts\QueryResponse;

class QueryResponseVersionOne extends QueryResponse
{
    protected $id;
    protected $authenticated;
    protected $validSignature;
    protected $eci;
    protected $cavv;
    protected $xid;

    public function __construct($data)
    {
        $this->id = $data['id'] ?? null;
        $this->authenticated = $data['authenticated'];
        $this->validSignature = $data['validSignature'];
        $this->eci = $data['eci'];
        $this->cavv = $data['cavv'];
        $this->xid = $data['xid'];
    }

    public function id()
    {
        return $this->id;
    }

    /**
     * Returns true if the authentication process has been successfully completed.
     */
    public function isAuthenticated(): bool
    {
        return $this->authenticated() == 'Y' && $this->validSignature();
    }

    public function enrolled(): ?string
    {
        return 'Y';
    }

    /**
     * Returns the indicator for the authentication from the ACS
     *  "Y" - Successful Authentication
     *  "A" - Successful Attempt
     *  "N" - Failed Authentication
     *  "U" - Unable to Authenticate.
     */
    public function authenticated(): ?string
    {
        return $this->authenticated;
    }

    /**
     * Return true if the signature for the ACS response has been validated.
     */
    public function validSignature(): bool
    {
        return $this->validSignature;
    }

    /**
     * Represents the Electronic Commerce Indicator
     *  For VISA
     *      05 - Issuer Liability
     *      06 - Issuer/Merchant Liability
     *      07 - Merchant Liability.
     */
    public function eci(): string
    {
        return $this->eci;
    }

    /**
     * Cardholder Authentication Verification Value (CAVV).
     */
    public function cavv(): ?string
    {
        return $this->cavv;
    }

    /**
     * Identifier of the resulting transaction for the authentication process.
     */
    public function xid(): ?string
    {
        return $this->xid;
    }

    /**
     * @param $result
     * @param $id
     * @return QueryResponseVersionOne
     * @throws \PlacetoPay\MPI\Exceptions\ErrorResultMPI
     */
    public static function loadFromResult($result, $id = null)
    {
        parent::loadFromResult($result);

        $data = [
            'id' => $id,
            'authenticated' => $result['authentication_status'],
            'validSignature' => (bool)$result['validated_signature'],
            'eci' => $result['eci'],
            'cavv' => $result['cavv'] ?? null,
            'xid' => $result['xid'] ?? null,
        ];

        return new self($data);
    }

    public function version(): string
    {
        return MPI::VERSION_ONE;
    }

    public function extra(): array
    {
        return [
            'validSignature' => $this->validSignature(),
        ];
    }
}
