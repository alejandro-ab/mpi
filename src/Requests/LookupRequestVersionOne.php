<?php

namespace PlacetoPay\MPI\Requests;

use PlacetoPay\MPI\Contracts\Request;

class LookupRequestVersionOne implements Request
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $pan;

    /**
     * @var string
     */
    private $expiration_year;

    /**
     * @var string
     */
    private $expiration_month;

    /**
     * @var string
     */
    private $amount;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $redirect_uri;

    /**
     * @var bool
     */
    private $disable_redirect;

    /**
     * @var string
     */
    private $installments;

    public function __construct(array $data)
    {
        $this->locale = $data['locale'] ?? 'en';
        $this->pan = $data['card']['number'];
        $this->expiration_year = $data['card']['expirationYear'];
        $this->expiration_month = $data['card']['expirationMonth'];
        $this->amount = $data['amount'] ?? 1;
        $this->reference = $data['reference'] ?? null;
        $this->currency = $data['currency'] ?? 'USD';
        $this->redirect_uri = $data['redirectUrl'];
        $this->disable_redirect = $data['disableRedirect'] ?? false;
        $this->installments = $data['card']['installments'] ?? null;
    }

    public function toArray(): array
    {
        $request = [
            'locale' => $this->locale,
            'pan' => $this->pan,
            'expiration_year' => $this->expiration_year,
            'expiration_month' => $this->expiration_month,
            'amount' => $this->amount,
            'reference' => $this->reference,
            'currency' => $this->currency,
            'redirect_uri' => $this->redirect_uri,
            'disable_redirect' => $this->disable_redirect,
        ];

        if (isset($this->installments)) {
            $request['installments'] = $this->installments;
        }

        return $request;
    }
}
