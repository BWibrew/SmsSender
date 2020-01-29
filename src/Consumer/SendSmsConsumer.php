<?php

namespace App\Consumer;

use App\Entity\SmsMessage;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;

class SendSmsConsumer implements ConsumerInterface
{
    protected const COUNTRY_CODE = 'GB';

    /**
     * @var Client
     */
    protected $twilio;

    /**
     * @var ParameterBagInterface
     */
    protected $params;

    public function __construct(Client $twilio, ParameterBagInterface $params)
    {
        $this->twilio = $twilio;
        $this->params = $params;
    }

    public function execute(AMQPMessage $msg)
    {
        /** @var SmsMessage $smsMessage */
        $smsMessage = unserialize($msg->getBody(), ['allowed_classes' => [SmsMessage::class]]);

        try {
            $response = $this->submitSmsMessage($smsMessage);
        } catch (TwilioException $exception) {
            //
        }
    }

    /**
     * Submit the SMS message to the Twilio API.
     *
     * @param $smsMessage
     * @return MessageInstance
     * @throws TwilioException
     */
    protected function submitSmsMessage(SmsMessage $smsMessage): MessageInstance
    {
        return $this->twilio->messages->create(
            $this->formatPhoneNumber($smsMessage->getRecipient()),
            [
                'from' => $this->params->get('app.twilio_number'),
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
    protected function formatPhoneNumber(string $unformatedNumber): string
    {
        $response = $this->twilio->lookups->v1->phoneNumbers($unformatedNumber)->fetch([
            'countryCode' => self::COUNTRY_CODE
        ]);

        return $response->phoneNumber;
    }
}
