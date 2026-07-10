<?php

namespace PlacetoPay\MPI\Messages;

use PlacetoPay\MPI\Constants\MPI;
use PlacetoPay\MPI\Contracts\LookupResponse;

class LookupResponseVersionOne extends LookupResponse
{
    protected $enrolled;

    /**
     * @var null|string
     */
    protected $redirectUrl;

    /**
     * @var null|string
     */
    protected $identifier;
    protected $eci;
    /**
     * @var array
     */
    protected $formData;

    public function __construct($data)
    {
        $this->enrolled = $data['enrolled'];
        $this->redirectUrl = $data['redirectUrl'];
        $this->identifier = $data['identifier'];
        $this->eci = $data['eci'];
        if (isset($data['formData'])) {
            $this->formData = $data['formData'];
        }
    }

    /**
     * Return true if the user can be authenticated through the MPI.
     */
    public function canAuthenticate(): bool
    {
        if ($this->redirectUrl) {
            return true;
        }
        return false;
    }

    /**
     * Returns the URL to send the user to finish the authentication process.
     */
    public function processUrl(): ?string
    {
        return $this->redirectUrl;
    }

    /**
     * Returns the request identifier used to query for the status of the authentication later on.
     */
    public function identifier(): ?string
    {
        return $this->identifier;
    }

    public function enrolled()
    {
        return $this->enrolled;
    }

    public function eci()
    {
        return $this->eci;
    }

    public function formData()
    {
        return $this->formData;
    }

    public function toArray()
    {
        return [
            'version' => $this->version(),
            'enrolled' => $this->enrolled(),
            'redirectUrl' => $this->processUrl(),
            'identifier' => $this->identifier(),
            'eci' => $this->eci(),
            'formData' => $this->formData(),
        ];
    }

    public static function loadFromResult($result)
    {
        parent::loadFromResult($result);

        $data = [
            'enrolled' => $result['enrolled'],
            'redirectUrl' => $result['redirect_url'] ?? null,
            'identifier' => $result['transaction_id'] ?? null,
            'eci' => $result['eci_flag'] ?? null,
            'formData' => null,
        ];

        if (isset($result['acs_url'])) {
            $data['formData'] = [
                'acsUrl' => $result['acs_url'],
                'paReq' => $result['pa_req'],
                'termUrl' => $result['term_url'],
                'md' => $result['md'] ?? null,
            ];
        }

        return new self($data);
    }

    public function version(): string
    {
        return MPI::VERSION_ONE;
    }

    public function extra(): array
    {
        return [
            'enrolled' => $this->enrolled(),
            'eci' => $this->eci(),
            'formData' => $this->formData(),
        ];
    }
}
