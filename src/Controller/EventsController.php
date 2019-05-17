<?php

namespace App\Controller;


use App\Db\EventsSchema\Event;
use App\Service\EventsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventsController
{
    /**
     * @var EventsService
     */
    private $eventsService;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public $result = [
        'success' => true,
        'data'    => null,
        'errors'  => [],
    ];

    public $status = 200;

    /**
     * EventsController constructor.
     * @param EventsService $eventsService
     * @param ValidatorInterface $validator
     */
    public function __construct(EventsService $eventsService, ValidatorInterface $validator)
    {
        $this->setEventsService($eventsService);
        $this->setValidator($validator);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function getOne(int $id)
    {
        try {
            $event = $this->getEventsService()->getOne($id);

            if (!$event) {
                throw new \Exception('Event not found.', 404);
            }

            $this->result['data'] = $event->toArray();
        } catch (\Exception $ex) {
            $this->result['errors'][] = $ex->getMessage();
            $this->status = $ex->getCode() ?
                ($ex->getCode() < 100 || $ex->getCode() > 600 ? 500 : $ex->getCode())  : 500;
        }

        return new JsonResponse($this->result, $this->status);
    }

    /**
     * @return JsonResponse
     */
    public function getAll()
    {
        try {
            $events = $this->getEventsService()->getAll();

            $results = [];

            foreach ($events as $event) {
                $results[] = $event->toArray();
            }

            $this->result['data'] = $results;
        } catch (\Exception $ex) {
            $this->result['errors'][] = $ex->getMessage();
            $this->status = $ex->getCode() ?
                ($ex->getCode() < 100 || $ex->getCode() > 600 ? 500 : $ex->getCode())  : 500;
        }

        return new JsonResponse($this->result, $this->status);
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return JsonResponse
     */
    public function save(Request $request, int $id = null)
    {
        try {
            if ($request->getContentType() !== 'json') {
                throw new \Exception('Content type must be application/json', 400);
            }

            $payload = json_decode($request->getContent(), true);

            $validationErrors = $this->validateEventData($payload);

            if (!empty($validationErrors)) {
                $this->result['errors'] = $validationErrors;
                throw new \Exception('Validation errors.', 400);
            }

            if ($id) {
                if (!isset($payload['id'])) {
                    throw new \Exception('Id is missing from the payload.', 400);
                }
                if ($id !== $payload['id']) {
                    throw new \Exception('Resource ID does not match the payload ID.', 400);
                }

                $existingEvent = $this->getEventsService()->getOne($id);

                if (!$existingEvent) {
                    throw new \Exception('Event not found.', 404);
                }
            }

            $event = new Event($payload);

            $savedEvent = $this->getEventsService()->save($event);
            $this->result['data'] = $savedEvent->toArray();
        } catch (\Exception $ex) {
            $this->result['errors'][] = $ex->getMessage();
            $this->status = $ex->getCode() ?
                ($ex->getCode() < 100 || $ex->getCode() > 600 ? 500 : $ex->getCode())  : 500;
        }

        return new JsonResponse($this->result, $this->status);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        try {
            $event = $this->getEventsService()->getOne($id);

            if (!$event) {
                throw new \Exception('Event not found.', 404);
            }

            $this->getEventsService()->delete($event);

            $this->result['data'] = $event->toArray();
        } catch (\Exception $ex) {
            $this->result['errors'][] = $ex->getMessage();
            $this->status = $ex->getCode() ?
                ($ex->getCode() < 100 || $ex->getCode() > 600 ? 500 : $ex->getCode())  : 500;
        }

        return new JsonResponse($this->result, $this->status);
    }

    /**
     * @param $data
     * @return array
     */
    private function validateEventData($data)
    {
        $constraint = new Collection([
            'fields' => [
                'startDate' => new Date(),
                'endDate'  => new Date(),
                'maxSlots' => new NotBlank(),
            ],
            'allowExtraFields' => true,
        ]);

        $violationList = $this->getValidator()->validate($data, $constraint);

        $errors = [];
        foreach ($violationList as $violation){
            $field = preg_replace('/\[|\]/', "", $violation->getPropertyPath());
            $error = $violation->getMessage();
            $errors[$field] = $error;
        }

        return $errors;
    }

    /**
     * @return EventsService
     */
    public function getEventsService()
    {
        return $this->eventsService;
    }

    /**
     * @param EventsService $eventsService
     * @return EventsController
     */
    public function setEventsService($eventsService)
    {
        $this->eventsService = $eventsService;
        return $this;
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * @param ValidatorInterface $validator
     * @return EventsController
     */
    public function setValidator(ValidatorInterface $validator): EventsController
    {
        $this->validator = $validator;
        return $this;
    }
}
