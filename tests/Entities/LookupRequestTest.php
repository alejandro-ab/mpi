<?php

namespace PlacetoPay\MPI\Tests\Entities;

use PlacetoPay\MPI\Requests\LookupRequestVersionOne;
use PlacetoPay\MPI\Requests\LookupRequestVersionTwo;
use PlacetoPay\MPI\Tests\BaseTestCase;

class LookupRequestTest extends BaseTestCase
{
    public function testItHandlesARequestOnVersionOne(): void
    {
        $data = [
            'payer' => [
                'name' => 'Diego',
                'surname' => 'Calle',
            ],
            'card' => [
                'number' => '4111111111111111',
                'expirationYear' => '2021',
                'expirationMonth' => '08',
            ],
            'amount' => null,
            'reference' => 'TEST_REFERENCE',
            'currency' => null,
            'redirectUrl' => 'https://dnetix.co/ping',
            'disableRedirect' => false,
            'userAgent' => 'Mozilla Firefox all the other things here 3.583',
        ];

        $request = new LookupRequestVersionOne($data);

        $this->assertEquals([
            'locale' => 'en',
            'pan' => '4111111111111111',
            'expiration_year' => '2021',
            'expiration_month' => '08',
            'amount' => 1,
            'reference' => 'TEST_REFERENCE',
            'currency' => 'USD',
            'redirect_uri' => 'https://dnetix.co/ping',
            'disable_redirect' => false,
        ], $request->toArray());
    }

    public function testItParsesSuccessfullyARequest(): void
    {
        $data = [
            'payer' => [
                'name' => 'Diego',
                'surname' => 'Calle',
            ],
            'card' => [
                'number' => '4111111111111111',
                'expirationYear' => '2021',
                'expirationMonth' => '08',
            ],
            'amount' => 100,
            'reference' => 'TEST_REFERENCE',
            'currency' => 'USD',
            'redirectUrl' => 'https://dnetix.co/ping',
            'disableRedirect' => false,
            'userAgent' => 'Mozilla Firefox all the other things here 3.583',
        ];

        $request = new LookupRequestVersionTwo($data);

        $this->assertEquals([
            'acctNumber' => '4111111111111111',
            'cardExpiryDate' => '2108',
            'purchaseAmount' => 100,
            'purchaseCurrency' => 'USD',
            'redirectURI' => 'https://dnetix.co/ping',
            'threeDSAuthenticationInd' => '01',
            'reference' => 'TEST_REFERENCE',
            'cardholderName' => 'Diego',
        ], $request->toArray());
    }

    public function testItHandlesASubscriptionRequest(): void
    {
        $data = [
            'payer' => [
                'name' => 'Diego',
                'surname' => 'Calle',
            ],
            'card' => [
                'number' => '4111111111111111',
                'expirationYear' => '2021',
                'expirationMonth' => '08',
            ],
            'amount' => null,
            'reference' => 'TEST_REFERENCE',
            'currency' => null,
            'redirectUrl' => 'https://dnetix.co/ping',
            'disableRedirect' => false,
            'userAgent' => 'Mozilla Firefox all the other things here 3.583',
        ];

        $request = new LookupRequestVersionTwo($data);

        $this->assertEquals([
            'acctNumber' => '4111111111111111',
            'cardExpiryDate' => '2108',
            'redirectURI' => 'https://dnetix.co/ping',
            'threeDSAuthenticationInd' => '04',
            'reference' => 'TEST_REFERENCE',
            'cardholderName' => 'Diego',
            'messageCategory' => '02',
        ], $request->toArray());
    }
}
