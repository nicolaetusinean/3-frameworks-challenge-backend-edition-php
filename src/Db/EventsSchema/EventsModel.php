<?php

namespace App\Db\EventsSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use App\Db\EventsSchema\AutoStructure\Events as EventsStructure;
use App\Db\EventsSchema\Events;

/**
 * EventsModel
 *
 * Model class for table events.
 *
 * @see Model
 */
class EventsModel extends Model
{
    use WriteQueries;

    /**
     * __construct()
     *
     * Model constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->structure = new EventsStructure;
        $this->flexible_entity_class = '\App\Db\EventsSchema\Event';
    }

    public function getOne(int $id): ?Event {
        return clone $this->findByPK(['id' => $id]);
    }

    /**
     * Return all events.
     *
     * Convert PommProject\ModelManager\Model\CollectionIterator instance to a normal array
     * in order to avoid high coupling.
     *
     * @return Event[]
     */
    public function getAll(): array {
        $results = $this->findAll();

        $events = [];
        foreach ($results as $result) {
            $events[] = $result;
        }

        return $events;
    }

    /**
     * @param Event $event
     * @return Event
     */
    public function save(Event $event): Event {
        if (!$event->getId()) {
            $this->insertOne($event);
        } else {
            $fields = array_keys($event->extract());
            $this->updateOne($event, $fields);
        }

        return $event;
    }

    /**
     * @param Event $event
     * @return Event
     */
    public function delete(Event $event): Event {
        $this->deleteOne($event);
        return $event;
    }
}
