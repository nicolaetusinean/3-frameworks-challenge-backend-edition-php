<?php

namespace App\Tests\Db\EventSchema;

use App\Db\EventsSchema\Event;
use App\Db\EventsSchema\EventsModel;
use App\Db\EventsSchema\Registration;
use App\Db\EventsSchema\RegistrationsModel;
use App\Service\EventsService;
use App\Service\RegistrationsService;
use PHPUnit\Framework\TestCase;

class RegistrationsServiceTest extends TestCase
{
    public function testGetOne()
    {
        $this->assertEquals(1, 1);
        $registration = $this->createRegistrationObject();

        $stub = $this->createMock(RegistrationsModel::class);
        $stub->method('getOne')
             ->willReturn($registration);

        $registrationsService = new RegistrationsService();
        $registrationsService->setRegistrationModel($stub);

        $this->assertInstanceOf(Registration::class, $registrationsService->getOne(1));
        $this->assertEquals($registration->getId(), $registrationsService->getOne(1)->getId());
    }

    public function testGetAll()
    {
        $registrations = [$this->createRegistrationObject()];
        $event = $this->createEventObject();

        $stub = $this->createMock(RegistrationsModel::class);
        $stub->method('getAllByEvent')
            ->willReturn($registrations);

        $registrationsService = new RegistrationsService();
        $registrationsService->setRegistrationModel($stub);

        $this->assertInternalType('array', $registrationsService->getAllByEvent($event));
        $this->assertCount(count($registrations), $registrationsService->getAllByEvent($event));
    }

    public function testSave()
    {
        $registration = $this->createRegistrationObject();
        $event = $this->createEventObject();

        $stub = $this->createMock(RegistrationsModel::class);
        $stub->method('save')
            ->willReturn($registration);

        $eventsServiceStub = $this->createMock(EventsService::class);
        $eventsServiceStub->method('getOne')
            ->willReturn($event);
        $eventsServiceStub->method('decrementAvailableSlots')
            ->willReturn($event);

        $registrationsService = new RegistrationsService();
        $registrationsService
            ->setRegistrationModel($stub)
            ->setEventsService($eventsServiceStub);

        $savedRegistration = $registrationsService->save($registration);

        $this->assertInstanceOf(Registration::class, $savedRegistration);
        $this->assertEquals($registration->getId(), $savedRegistration->getId());

        $registrationNoEventData = clone $registration;
        $registrationNoEventData->setEventId(null);

        $this->expectException(\Exception::class);
        $registrationsService->save($registrationNoEventData);
    }

    public function testDelete()
    {
        $registration = $this->createRegistrationObject();
        $event = $this->createEventObject();

        $stub = $this->createMock(RegistrationsModel::class);
        $stub->method('delete')
            ->willReturn($registration);

        $eventsServiceStub = $this->createMock(EventsService::class);
        $eventsServiceStub->method('getOne')
            ->willReturn($event);
        $eventsServiceStub->method('incrementAvailableSlots')
            ->willReturn($event);

        $registrationsService = new RegistrationsService();
        $registrationsService
            ->setRegistrationModel($stub)
            ->setEventsService($eventsServiceStub);

        $deletedRegistration = $registrationsService->delete($registration);

        $this->assertInstanceOf(Registration::class, $deletedRegistration);
        $this->assertEquals($registration->getId(), $deletedRegistration->getId());
    }
    
    private function createRegistrationObject(): Registration
    {
        return new Registration([
            'id' => 1,
            'firstName' => 'Nicolae',
            'lastName' => 'Tusinean',
            'phone' => '+007455555555',
            'email' => 'first.last@email.com',
            'eventId' => 1
        ]);
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
