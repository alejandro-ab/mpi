<?php

namespace PlacetoPay\MPI\Requests;

use PlacetoPay\MPI\Constants\MPI;
use PlacetoPay\MPI\Contracts\MPIException;
use PlacetoPay\MPI\Contracts\Request;

class LookupRequestVersionTwo implements Request
{
    /**
     * @var string
     */
    private $accNumber;

    /**
     * @var string
     */
    private $cardExpiryDate;

    /**
     * @var string
     */
    private $purchaseAmount;

    /**
     * @var string
     */
    private $purchaseCurrency;

    /**
     * @var string
     */
    private $redirectURI;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $threeDSAuthenticationInd;

    /**
     * @var string
     */
    private $messageCategory;

    /**
     * @var string|null
     */
    private $purchaseInstallData;

    /**
     * @var string|null
     */
    private $recurringFrequency;

    /**
     * @var string|null
     */
    private $recurringExpiry;

    /**
     * Load other information useful to risk management.
     * @var array
     */
    private $additional = [];

    public function __construct($data)
    {
        if (!empty($data['currency'])) {
            $this->purchaseAmount = $data['amount'];
            $this->purchaseCurrency = $data['currency'];
            $this->threeDSAuthenticationInd = $data['threeDSAuthenticationInd'] ?? MPI::PAYMENT_TRANSACTION_CODE;
        } else {
            $this->messageCategory = '02';
            $this->threeDSAuthenticationInd = $data['threeDSAuthenticationInd'] ?? MPI::ADD_NEW_CARD_CODE;
        }

        $this->accNumber = $data['card']['number'];
        $this->cardExpiryDate = $this->expirationYearShort($data['card']['expirationYear']) . $data['card']['expirationMonth'];
        $this->redirectURI = $data['redirectUrl'];
        $this->reference = $data['reference'] ?? null;

        $this->threeDSAuthValidation($data);

        $this->purchaseInstallData = $data['purchaseInstallData'] ?? null;
        $this->recurringFrequency = $data['recurringFrequency'] ?? null;
        $this->recurringExpiry = $data['recurringExpiry'] ?? null;

        $this->loadAdditional($data);
    }

    /**
     * Returns the expiration year always with YY format.
     * @return string
     */
    public function expirationYearShort(string $expirationYear)
    {
        if (!$expirationYear) {
            return null;
        }
        if (mb_strlen($expirationYear) == 4) {
            $expirationYear = substr($expirationYear, 2, 2);
        }
        return $expirationYear;
    }

    public function toArray(): array
    {
        return array_filter(array_merge([
            'acctNumber' => $this->accNumber,
            'cardExpiryDate' => $this->cardExpiryDate,
            'purchaseAmount' => $this->purchaseAmount,
            'purchaseCurrency' => $this->purchaseCurrency,
            'redirectURI' => $this->redirectURI,
            'threeDSAuthenticationInd' => $this->threeDSAuthenticationInd,
            'reference' => $this->reference,
            'messageCategory' => $this->messageCategory(),

            'purchaseInstallData' => $this->purchaseInstallData,
            'recurringFrequency' => $this->recurringFrequency,
            'recurringExpiry' => $this->recurringExpiry,
        ], $this->additional));
    }

    public function endpoint(): string
    {
        return MPI::LOOKUP_ENDPOINTS[MPI::VERSION_TWO];
    }

    public function messageCategory()
    {
        return $this->messageCategory;
    }

    /**
     * @param $data
     * @throws MPIException
     */
    protected function threeDSAuthValidation($data)
    {
        if (in_array($this->threeDSAuthenticationInd, MPI::THREEDS_AUTH_INDICATOR)) {
            if (!isset($data['recurringFrequency'])) {
                throw new MPIException("The recurring frequency field is required when three d s authentication ind is {$this->threeDSAuthenticationInd}.");
            }

            if (!isset($data['recurringExpiry'])) {
                throw new MPIException("The recurring expiry field is required when three d s authentication ind is {$this->threeDSAuthenticationInd}.");
            }

            if ($this->threeDSAuthenticationInd == '03' && !isset($data['recurringFrequency'])) {
                throw new MPIException("The purchase instal data field is required when three d s authentication ind is {$this->threeDSAuthenticationInd}.");
            }
        }
    }

    private function loadAdditional(array $data)
    {
        if ($payer = $data['payer'] ?? []) {
            $this->additional = array_merge($this->additional, [
                'email' => $payer['email'] ?? null,
                'mobilePhone' => $data['mobile'] ?? null,
                'cardholderName' => $payer['name'] ?? null,
            ]);

            if ($address = $payer['address'] ?? []) {
                $this->additional = array_merge($this->additional, [
                    'billAddrCity' => $address['city'] ?? null,
                    'billAddrCountry' => $address['country'] ?? null,
                    'billAddrLine1' => $address['street'] ?? null,
                    'billAddrPostCode' => $address['postalCode'] ?? null,
                    'billAddrState' => $address['state'] ?? null,
                ]);
            }
        }

        $shipping = $data['buyer'] ?? [];
        if ($data['shipping'] ?? []) {
            $shipping = $data['shipping'];
        }

        if ($shipping) {
            if ($address = $shipping['address'] ?? []) {
                $this->additional = array_merge($this->additional, [
                    'shipAddrCity' => $address['city'] ?? null,
                    'shipAddrCountry' => $address['country'] ?? null,
                    'shipAddrLine1' => $address['street'] ?? null,
                    'shipAddrPostCode' => $address['postalCode'] ?? null,
                    'shipAddrState' => $address['state'] ?? null,
                ]);
            }
        }
    }
}
