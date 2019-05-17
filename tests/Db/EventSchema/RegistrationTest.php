<?php

namespace App\Tests\Db\EventSchema;

use App\Db\EventsSchema\Registration;
use PHPUnit\Framework\TestCase;

class RegistrationTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $event = new Registration();

        $event->setId(1);
        $this->assertEquals(1, $event->getId());

        $event->setFirstName('abc');
        $this->assertEquals('abc', $event->getFirstName());

        $event->setLastName('def');
        $this->assertEquals('def', $event->getLastName());

        $event->setEmail('abc@def.tld');
        $this->assertEquals('abc@def.tld', $event->getEmail());

        $event->setPhone('1234');
        $this->assertEquals('1234', $event->getPhone());

        $event->setEventId(1);
        $this->assertEquals(1, $event->getEventId());
    }

    public function testObjectCreation()
    {
        $event = new Registration([
            'id' => 1,
            'firstName' => 'abc',
            'lastName'  => 'def',
            'email'  => 'abc@def.tld',
            'phone'  => '1234',
            'eventId' => 1,
        ]);

        $this->assertEquals(1, $event->getId());
        $this->assertEquals('abc', $event->getFirstName());
        $this->assertEquals('def', $event->getLastName());
        $this->assertEquals('abc@def.tld', $event->getEmail());
        $this->assertEquals('1234', $event->getPhone());
        $this->assertEquals(1, $event->getEventId());
    }

    public function testToArray()
    {
        $data = [
            'id' => 1,
            'firstName' => 'abc',
            'lastName'  => 'def',
            'email'  => 'abc@def.tld',
            'phone'  => '1234',
            'eventId' => 1,
        ];

        $registration = new Registration($data);

        $this->assertEquals($data, $registration->toArray());
    }
}
