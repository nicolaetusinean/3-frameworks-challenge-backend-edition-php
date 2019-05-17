<?php

namespace App\Tests\Db\EventSchema;

use App\Db\EventsSchema\Event;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $event = new Event();

        $date = new \DateTime('2019-05-11');

        $event->setId(1);
        $this->assertEquals(1, $event->getId());

        $event->setStartDate($date);
        $this->assertEquals($date, $event->getStartDate());

        $event->setEndDate($date);
        $this->assertEquals($date, $event->getEndDate());

        $event->setMaxSlots(15);
        $this->assertEquals(15, $event->getMaxSlots());

        $event->setAvailableSlots(15);
        $this->assertEquals(15, $event->getAvailableSlots());
    }

    public function testObjectCreation()
    {
        $dateString = '2019-05-11';
        $dateObject = new \DateTime($dateString);

        $event = new Event([
            'id' => 1,
            'startDate' => $dateString,
            'endDate'   => $dateString,
            'maxSlots'  => 15,
            'availableSlots' => 15,
        ]);

        $this->assertEquals(1, $event->getId());

        $this->assertEquals($dateObject, $event->getStartDate());

        $this->assertEquals($dateObject, $event->getEndDate());

        $this->assertEquals(15, $event->getMaxSlots());

        $this->assertEquals(15, $event->getAvailableSlots());
    }

    public function testIncrementAvailableSlots()
    {
        $event = new Event();

        $event->setMaxSlots(10);
        $event->setAvailableSlots(2);
        $event->incrementAvailableSlots();
        $this->assertEquals(3, $event->getAvailableSlots());

        $event->setMaxSlots(10);
        $event->setAvailableSlots(10);
        $event->incrementAvailableSlots();
        $this->assertEquals(10, $event->getAvailableSlots());
    }

    public function testDecrementAvailableSlots()
    {
        $event = new Event();

        $event->setAvailableSlots(10);
        $event->decrementAvailableSlots();
        $this->assertEquals(9, $event->getAvailableSlots());

        $event->setAvailableSlots(0);
        $this->expectException(\Exception::class);
        $event->decrementAvailableSlots();
    }

    public function testToArray()
    {
        $date = '2019-05-11';

        $data = [
            'id' => 1,
            'startDate' => $date,
            'endDate'   => $date,
            'maxSlots'  => 15,
            'availableSlots' => 15,
        ];

        $event = new Event($data);

        $this->assertEquals($data, $event->toArray());
    }
}
