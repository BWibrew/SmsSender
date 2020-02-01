<?php

namespace App\Service;

use App\Entity\SmsMessage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;

class TwilioService
{
    /**
     * Country code used for E.164 phone number formatting.
     */
    protected const COUNTRY_CODE = 'GB';

    /**
     * Symfony parameter identifier for 'from' number.
     */
    protected const TWILIO_FROM_NUMBER = 'app.twilio_number';

    /**
     * @var Client
     */
    protected $twilio;

    /**
     * @var ParameterBagInterface
     */
    protected $parameters;

    public function __construct(Client $twilio, ParameterBagInterface $parameters)
    {
        $this->twilio = $twilio;
        $this->parameters = $parameters;
    }

    /**
     * Submit the SMS message to the Twilio API.
     *
     * @param $smsMessage
     * @return MessageInstance
     * @throws TwilioException
     */
    public function createSmsMessage(SmsMessage $smsMessage): MessageInstance
    {
        return $this->twilio->messages->create(
            $this->formatPhoneNumber($smsMessage->getRecipient()),
            [
                'from' => $this->parameters->get(self::TWILIO_FROM_NUMBER),
                'body' => $smsMessage->getBody()
            ]
        );
    }

    /**
     * Format the recipient phone number to E.164 standard.
     * @param string $unformatedNumber
     * @return string
     * @throws TwilioException
     */
    public function formatPhoneNumber(string $unformatedNumber): string
    {
        $response = $this->twilio->lookups->v1->phoneNumbers->getContext($unformatedNumber)->fetch([
            'countryCode' => self::COUNTRY_CODE
        ]);

        return $response->phoneNumber;
    }
}
