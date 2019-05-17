<?php

namespace App\Controller;


use App\Db\EventsSchema\Registration;
use App\Service\EventsService;
use App\Service\RegistrationsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationsController
{
    /**
     * @var RegistrationsService
     */
    private $registrationsService;

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
     * RegistrationsController constructor.
     * @param RegistrationsService $registrationsService
     * @param EventsService $eventsService
     * @param ValidatorInterface $validator
     */
    public function __construct(
        RegistrationsService $registrationsService,
        EventsService $eventsService,
        ValidatorInterface $validator
    ) {
        $this->setRegistrationsService($registrationsService);
        $this->setEventsService($eventsService);
        $this->setValidator($validator);
    }

    /**
     * @param $eventId
     * @return JsonResponse
     */
    public function getAllByEvent($eventId) {
        try {
            $event = $this->getEventsService()->getOne($eventId);

            if (!$event) {
                throw new \Exception('Event not found.', 404);
            }

            $registrations = $this->getRegistrationsService()->getAllByEvent($event);

            $results = [];

            foreach ($registrations as $registration) {
                $results[] = $registration->toArray();
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
     * @param $eventId
     * @return JsonResponse
     */
    public function save(Request $request, $eventId) {
        try {
            if ($request->getContentType() !== 'json') {
                throw new \Exception('Content type must be application/json', 400);
            }

            $payload = json_decode($request->getContent(), true);

            $validationErrors = $this->validateRegistrationData($payload);

            if (!empty($validationErrors)) {
                $this->result['errors'] = $validationErrors;
                throw new \Exception('Validation errors.', 400);
            }

            $event = $this->getEventsService()->getOne($eventId);

            if (!$event) {
                throw new \Exception('Event not found.', 404);
            }

            $registration = new Registration($payload);

            $savedRegistration = $this->getRegistrationsService()->save($registration, $event);
            $this->result['data'] = $savedRegistration->toArray();
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
    public function delete($id) {
        try {
            $registration = $this->getRegistrationsService()->getOne($id);

            if (!$registration) {
                throw new \Exception('Registration not found.', 404);
            }

            $this->getRegistrationsService()->delete($registration);

            $this->result['data'] = $registration->toArray();
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
    private function validateRegistrationData($data) {
        $constraint = new Collection([
            'firstName'  => new NotBlank(),
            'lastName'  => new NotBlank(),
            'email' => new Email(),
            'phone' => new NotBlank(),
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
     * @return RegistrationsService
     */
    public function getRegistrationsService()
    {
        return $this->registrationsService;
    }

    /**
     * @param RegistrationsService $registrationsService
     * @return RegistrationsController
     */
    public function setRegistrationsService($registrationsService): RegistrationsController
    {
        $this->registrationsService = $registrationsService;
        return $this;
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
     * @return RegistrationsController
     */
    public function setEventsService($eventsService): RegistrationsController
    {
        $this->eventsService = $eventsService;
        return $this;
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param ValidatorInterface $validator
     * @return RegistrationsController
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;
        return $this;
    }
}
