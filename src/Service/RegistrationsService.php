<?php
namespace App\Service;

use App\Db\EventsSchema\Event;
use App\Db\EventsSchema\Registration;
use App\Db\EventsSchema\RegistrationsModel;
use PommProject\Foundation\Pomm;

class RegistrationsService
{
    /**
     * @var RegistrationsModel
     */
    private $registrationModel;

    /**
     * @var EventsService
     */
    private $eventsService;

    /**
     * @var Pomm
     */
    protected $pomm;

    public function __construct(Pomm $pomm = null, EventsService $eventsService = null)
    {
        $this->setPomm($pomm);
        $this->setEventsService($eventsService);
    }

    /**
     * @param integer $id
     * @return Registration
     */
    public function getOne($id): ?Registration
    {
        return $this->getRegistrationsModel()->getOne($id);
    }

    /**
     * @param Event $event
     * @return Registration[]
     */
    public function getAllByEvent(Event $event): array
    {
        return $this->getRegistrationsModel()->getAllByEvent($event);
    }

    /**
     *
     * @param Registration $registration
     * @param Event|null $event
     * @return Registration
     * @throws \Exception
     */
    public function save(Registration $registration, Event $event = null): Registration
    {
        $eventId = $registration->getEventId();

        if ($event instanceof Event) {
            $eventId = $event->getId();
        }

        if (!$eventId) {
            throw new \Exception(
                'Either set event ID to the Registration object or pass an Event object.',
                500
            );
        }

        // make sure that the event exists
        $event = $this->getEventsService()->getOne($eventId);

        if (!$event) {
            throw new \Exception('Event not found.', 404);
        }

        $registration->setEventId($eventId);

        $this->getEventsService()->decrementAvailableSlots($event);

        return $this->getRegistrationsModel()->save($registration);
    }

    /**
     * @param Registration $registration
     * @return Registration
     * @throws \Exception
     */
    public function delete(Registration $registration): Registration
    {
        $event = $this->getEventsService()->getOne($registration->getEventId());
        $this->getEventsService()->incrementAvailableSlots($event);

        return $this->getRegistrationsModel()->delete($registration);
    }

    /**
     * @param RegistrationsModel $registrationModel
     * @return RegistrationsService
     */
    public function setRegistrationModel($registrationModel)
    {
        $this->registrationModel = $registrationModel;
        return $this;
    }

    /**
     * @return RegistrationsModel
     * @throws \Exception
     */
    public function getRegistrationsModel()
    {
        if (!$this->registrationModel) {
            if (!$this->getPomm()) {
                throw new \Exception('Pomm service was not injected.');
            }

            /** @var \PommProject\ModelManager\Session $model */
            $dbSession = $this->getPomm()['db'];
            $this->registrationModel = $dbSession->getModel(RegistrationsModel::class);
        }

        return $this->registrationModel;
    }

    /**
     * @return Pomm
     */
    public function getPomm()
    {
        return $this->pomm;
    }

    /**
     * @param Pomm $pomm
     * @return RegistrationsService
     */
    public function setPomm($pomm)
    {
        $this->pomm = $pomm;
        return $this;
    }

    /**
     * @return EventsService
     * @throws \Exception
     */
    public function getEventsService()
    {
        if (!$this->eventsService) {
            throw new \Exception('Events Service was not injected.', 500);
        }

        return $this->eventsService;
    }

    /**
     * @param EventsService $eventsService
     * @return RegistrationsService
     */
    public function setEventsService($eventsService)
    {
        $this->eventsService = $eventsService;
        return $this;
    }
}
