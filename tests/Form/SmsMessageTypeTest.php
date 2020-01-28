<?php

namespace App\Tests\Form;

use App\Entity\SmsMessage;
use App\Form\SmsMessageType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class SmsMessageTypeTest extends TypeTestCase
{
    private $objectManager;

    protected function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManager::class);

        parent::setUp();
    }

    protected function getExtensions(): array
    {
        $type = new SmsMessageType($this->objectManager);

        return [
            new PreloadedExtension([$type], []),
        ];
    }

    public function testCreateNew(): void
    {
        $objectToCompare = new SmsMessage();
        $form = $this->factory->create(SmsMessageType::class, $objectToCompare);

        $object = new SmsMessage();
        $object->setRecipient('123456');
        $object->setBody('foobar');

        $formData = [
            'recipient' => '123456',
            'body' => 'foobar',
        ];

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object->getRecipient(), $objectToCompare->getRecipient());
        $this->assertEquals($object->getBody(), $objectToCompare->getBody());
        $this->assertEquals($object->getStatus(), $objectToCompare->getStatus());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
