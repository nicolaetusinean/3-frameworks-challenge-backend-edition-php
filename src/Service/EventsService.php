<?php
namespace App\Service;

use App\Db\EventsSchema\Event;
use App\Db\EventsSchema\EventsModel;
use PommProject\Foundation\Pomm;

class EventsService
{
    /**
     * @var EventsModel
     */
    private $eventsModel;

    /**
     * EventsService constructor.
     * @param Pomm $pomm
     */
    public function __construct(Pomm $pomm = null)
    {
        $this->setPomm($pomm);
    }

    /**
     * @param $id
     * @return Event|null
     * @throws \Exception
     */
    public function getOne($id): ?Event {
        return $this->getEventsModel()->getOne($id);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getAll(): array
    {
        return $this->getEventsModel()->getAll();
    }

    /**
     * @param Event $event
     * @throws \Exception
     * @return Event
     */
    public function save(Event $event): Event
    {
        if (!$event->getId()) {
            $event->setAvailableSlots($event->getMaxSlots());
        } else {
            $oldEvent = $this->getOne($event->getId());
            $newAvailableSlots = $oldEvent->getAvailableSlots();

            if ($event->getMaxSlots() > $oldEvent->getMaxSlots()) {
                $newAvailableSlots += $event->getMaxSlots() - $oldEvent->getMaxSlots();
                $event->setAvailableSlots($newAvailableSlots);
            } else if ($event->getMaxSlots() < $oldEvent->getMaxSlots()) {
                $newAvailableSlots -= ($oldEvent->getMaxSlots() - $event->getMaxSlots());

                $registrationsNo = $oldEvent->getMaxSlots() - $oldEvent->getAvailableSlots();
                if ($event->getMaxSlots() < $registrationsNo) {
                    throw new \Exception(
                        'Registrations exceeds the new value for available slots.',
                        409
                    );
                }
                $event->setAvailableSlots($newAvailableSlots);
            }
        }

        return $this->getEventsModel()->save($event);
    }

    /**
     * @param Event $event
     * @return Event
     * @throws \Exception
     */
    public function delete(Event $event): Event
    {
        return $this->getEventsModel()->delete($event);
    }

    /**
     * @param Event $event
     * @return Event
     * @throws \Exception
     */
    public function incrementAvailableSlots(Event $event): Event
    {
        $event->incrementAvailableSlots();
        $this->save($event);
        return $event;
    }

    /**
     * @param Event $event
     * @return Event
     * @throws \Exception
     */
    public function decrementAvailableSlots(Event $event): Event
    {
        $event->decrementAvailableSlots();
        $this->save($event);
        return $event;
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
     * @return EventsService
     */
    public function setPomm($pomm): EventsService
    {
        $this->pomm = $pomm;
        return $this;
    }

    /**
     * @param $eventsModel
     */
    public function setEventsModel($eventsModel)
    {
        $this->eventsModel = $eventsModel;
    }

    /**
     * @return EventsModel|\PommProject\ModelManager\Model\Model
     * @throws \Exception
     */
    public function getEventsModel()
    {
        if (!$this->eventsModel) {
            if (!$this->getPomm()) {
                throw new \Exception('Pomm service was not injected.');
            }

            /** @var \PommProject\ModelManager\Session $dbSession */
            $dbSession = $this->getPomm()['db'];
            $this->eventsModel = $dbSession->getModel(EventsModel::class);
        }

        return $this->eventsModel;
    }
}
