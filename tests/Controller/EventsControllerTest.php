<?php

namespace App\Tests\Db\EventSchema;

use App\Db\EventsSchema\Event;
use App\Db\EventsSchema\EventsModel;
use App\Service\EventsService;
use App\Controller\EventsController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class EventsControllerTest extends TestCase
{
    public function testGetOne()
    {
        $event = $this->createEventObject();
        $validator = Validation::createValidatorBuilder()->getValidator();

        $stub = $this->createMock(EventsService::class);
        $stub->method('getOne')
            ->willReturn($event);

        $eventsController = new EventsController($stub, $validator);
        $result = $eventsController->getOne(1);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertInternalType('string', $result->getContent());
    }

    public function testGetOne404()
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        $stub = $this->createMock(EventsService::class);
        $stub->method('getOne')
            ->willReturn(null);

        $eventsController = new EventsController($stub, $validator);
        $result = $eventsController->getOne(1);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(404, $result->getStatusCode());
        $this->assertInternalType('string', $result->getContent());
    }

    public function testGetAll()
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        $stub = $this->createMock(EventsService::class);
        $stub->method('getAll')
            ->willReturn([]);

        $eventsController = new EventsController($stub, $validator);
        $result = $eventsController->getAll();

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertInternalType('string', $result->getContent());
    }

    public function testSave()
    {
        $event = $this->createEventObject();
        $validator = Validation::createValidatorBuilder()->getValidator();

        $stub = $this->createMock(EventsService::class);
        $stub->method('save')
            ->willReturn($event);

        // empty request
        $eventsController = new EventsController($stub, $validator);
        $request = new Request();
        $result = $eventsController->save($request);
        $this->assertEquals(400, $result->getStatusCode());

        // request claiming that it has a json payload
        $eventsController = new EventsController($stub, $validator);
        $request->headers->set('Content-Type', 'application/json');
        $result = $eventsController->save($request);
        $this->assertEquals(400, $result->getStatusCode());

        // request invalid data
        $eventsController = new EventsController($stub, $validator);
        $invalidContent = json_encode([
            'start_date' => '2012-02'
        ]);
        $request = new Request([], [], [], [], [], [], $invalidContent);
        $request->headers->set('Content-Type', 'application/json');
        $result = $eventsController->save($request);
        $this->assertEquals(400, $result->getStatusCode());

        // valid request
        $eventsController = new EventsController($stub, $validator);
        $invalidContent = json_encode([
            'startDate' => '2012-02-01',
            'endDate'   => '2012-03-01',
            'maxSlots'  => 10
        ]);
        $request = new Request([], [], [], [], [], [], $invalidContent);
        $request->headers->set('Content-Type', 'application/json');
        $result = $eventsController->save($request);
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testDelete()
    {
        $event = $this->createEventObject();
        $validator = Validation::createValidatorBuilder()->getValidator();

        $stub = $this->createMock(EventsService::class);
        $stub->method('getOne')
            ->willReturn($event);
        $stub->method('delete')
            ->willReturn($event);

        $eventsController = new EventsController($stub, $validator);
        $result = $eventsController->delete(1);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertInternalType('string', $result->getContent());
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
