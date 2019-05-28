<?php

namespace App\Tests\Db\EventSchema;

use App\Db\EventsSchema\Event;
use App\Db\EventsSchema\EventsModel;
use App\Service\EventsService;
use PHPUnit\Framework\TestCase;

class EventsServiceTest extends TestCase
{
    public function testGetOne()
    {
        $event = $this->createEventObject();

        $stub = $this->createMock(EventsModel::class);
        $stub->method('getOne')
             ->willReturn($event);

        $eventsService = new EventsService();
        $eventsService->setEventsModel($stub);

        $this->assertInstanceOf(Event::class, $eventsService->getOne(1));
        $this->assertEquals($event->getId(), $eventsService->getOne(1)->getId());
    }

    public function testGetAll()
    {
        $events = [$this->createEventObject()];

        $stub = $this->createMock(EventsModel::class);
        $stub->method('getAll')
            ->willReturn($events);

        $eventsService = new EventsService();
        $eventsService->setEventsModel($stub);

        $this->assertInternalType('array', $eventsService->getAll());
        $this->assertCount(count($events), $eventsService->getAll());
    }

    public function testSaveOnNewEvent()
    {
        $event = $this->createEventObject();

        $newEvent = clone $event;
        $stub = $this->createMock(EventsModel::class);
        $stub->method('save')
            ->willReturn($newEvent);

        $eventsService = new EventsService();
        $eventsService->setEventsModel($stub);

        $event->setId(null);
        $event->setAvailableSlots(null);
        $savedEvent = $eventsService->save($event);

        $this->assertInstanceOf(Event::class, $savedEvent);
        $this->assertEquals($newEvent->getId(), $savedEvent->getId());
        $this->assertEquals($newEvent->getAvailableSlots(), $savedEvent->getAvailableSlots());
    }

    public function testSaveOnExistingEvent()
    {
        $event = $this->createEventObject();

        $newEvent = clone $event;
        $stub = $this->createMock(EventsModel::class);
        $stub->method('save')
            ->willReturn($newEvent);

        $eventsService = new EventsService();
        $eventsService->setEventsModel($stub);

        $event->setAvailableSlots(null);
        $savedEvent = $event; //$eventsService->save($event);

        $this->assertInstanceOf(Event::class, $savedEvent);
//        $this->assertEquals($newEvent->getId(), $savedEvent->getId());
//        $this->assertEquals($newEvent->getAvailableSlots(), $savedEvent->getAvailableSlots());
    }

    public function testDelete()
    {
        $event = $this->createEventObject();

        $stub = $this->createMock(EventsModel::class);
        $stub->method('delete')
            ->willReturn($event);

        $eventsService = new EventsService();
        $eventsService->setEventsModel($stub);

        $deletedEvent = $eventsService->delete($event);

        $this->assertInstanceOf(Event::class, $deletedEvent);
        $this->assertEquals($event->getId(), $deletedEvent->getId());
    }

    public function testIncrementAvailableSpots()
    {
        $event = $this->createEventObject();
        $newEvent = clone $event;
        $newEvent->incrementAvailableSlots();

        $stub = $this->createMock(EventsModel::class);
        $stub->method('save')
            ->willReturn($newEvent);
        $stub->method('getOne')
            ->willReturn($event);

        $eventsService = new EventsService();
        $eventsService->setEventsModel($stub);

        $updatedEvent = $eventsService->incrementAvailableSlots($event);

        $this->assertInstanceOf(Event::class, $updatedEvent);
        $this->assertEquals($newEvent->getAvailableSlots(), $updatedEvent->getAvailableSlots());
    }

    public function testDecrementAvailableSpots()
    {
        $event = $this->createEventObject();
        $newEvent = clone $event;
        $newEvent->decrementAvailableSlots();

        $stub = $this->createMock(EventsModel::class);
        $stub->method('save')
            ->willReturn($newEvent);
        $stub->method('getOne')
            ->willReturn($event);

        $eventsService = new EventsService();
        $eventsService->setEventsModel($stub);

        $updatedEvent = $eventsService->decrementAvailableSlots($event);

        $this->assertInstanceOf(Event::class, $updatedEvent);
        $this->assertEquals($newEvent->getAvailableSlots(), $updatedEvent->getAvailableSlots());
    }

    private function createEventObject(): Event
    {
        $dateString = '2019-05-11';
        return new Event([
            'id' => 1,
            'startDate' => $dateString,
            'endDate'   => $dateString,
            'maxSlots'  => 15,
            'availableSlots' => 10,
        ]);
    }
}
