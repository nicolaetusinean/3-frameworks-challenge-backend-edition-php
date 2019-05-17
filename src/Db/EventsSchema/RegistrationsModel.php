<?php

namespace App\Db\EventsSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use App\Db\EventsSchema\AutoStructure\Registrations as RegistrationsStructure;

/**
 * RegistrationsModel
 *
 * Model class for table registrations.
 *
 * @see Model
 */
class RegistrationsModel extends Model
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
        $this->structure = new RegistrationsStructure;
        $this->flexible_entity_class = '\App\Db\EventsSchema\Registration';
    }

    /**
     * @param int $id
     * @return Registration
     */
    public function getOne(int $id): ?Registration
    {
        return $this->findByPK(['id' => $id]);
    }

    /**
     * Return all events.
     *
     * Convert PommProject\ModelManager\Model\CollectionIterator instance to a normal array
     * in order to avoid high coupling.
     *
     * @return Registration[]
     */
    public function getAllByEvent(Event $event)
    {
        $results = $this->findWhere('event_id = $*', [$event->getId()]);

        $registrations = [];
        foreach ($results as $registration) {
            $registrations[] = $registration;
        }

        return $registrations;
    }

    /**
     * @param \App\Db\EventsSchema\Registration $registration
     * @return \App\Db\EventsSchema\Registration
     */
    public function save(Registration $registration): Registration
    {
        $this->insertOne($registration);
        return $registration;
    }

    /**
     * @param \App\Db\EventsSchema\Registration $registration
     * @return \App\Db\EventsSchema\Registration
     */
    public function delete(Registration $registration): Registration
    {
        $this->deleteOne($registration);
        return $registration;
    }
}
