<?php

namespace App\Service;

use App\Entity\SmsMessage;
use Doctrine\ORM\EntityManagerInterface;
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

    protected const ERROR_STATUS = 'error';

    /**
     * @var Client
     */
    protected $twilio;

    /**
     * @var ParameterBagInterface
     */
    protected $parameters;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(
        Client $twilio,
        ParameterBagInterface $parameters,
        EntityManagerInterface $entityManager
    ) {
        $this->twilio = $twilio;
        $this->parameters = $parameters;
        $this->entityManager = $entityManager;
    }

    /**
     * Submit the SMS message to the Twilio API.
     *
     * @param $smsMessage
     * @return MessageInstance|null
     */
    public function createSmsMessage(SmsMessage $smsMessage): ?MessageInstance
    {
        $smsMessage = $this->entityManager->getRepository(SmsMessage::class)->find($smsMessage->getId());

        try {
            $response = $this->twilio->messages->create(
                $this->formatPhoneNumber($smsMessage->getRecipient()),
                [
                    'from' => $this->parameters->get(self::TWILIO_FROM_NUMBER),
                    'body' => $smsMessage->getBody()
                ]
            );

            $smsMessage->setStatus($response->status);
            $this->entityManager->flush();

            return $response;
        } catch (TwilioException $exception) {
            $smsMessage->setStatus(self::ERROR_STATUS);
            $smsMessage->setErrorMessage($exception->getMessage());
            $this->entityManager->flush();
        }

        return null;
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
