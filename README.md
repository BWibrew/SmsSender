# SMS Sender

This Symfony 4 app uses the Twilio service to send SMS messages.

## Installation for local development
Install Composer packages by running:
```
composer install
```

Set the required environment variables for Twilio:
```
TWILIO_SID="SomeSid"
TWILIO_AUTH_TOKEN="SomeAuthToken"
TWILIO_NUMBER="SomeNumber"
TWILIO_CALLBACK_URL="SomeUrl"
```

**Note:** For the Twilio callback to work on a local machine, you will need to set up an ngrok tunnel.

Build and run Docker Compose:
```
docker-compose up -d
```

Run RabbitMQ:
```
docker-compose exec app bin/console rabbitmq:consumer send_sms
```

## Testing
To run the test suite:
```
docker-compose exec app bin/console doctrine:fixtures:load
docker-compose exec app bin/phpunit
```
