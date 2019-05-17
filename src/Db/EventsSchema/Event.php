<?php

namespace App\Db\EventsSchema;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * Events
 *
 * Flexible entity for relation
 * events.events
 *
 * @see FlexibleEntity
 */
class Event extends FlexibleEntity
{
    public function __construct(array $values = null)
    {
        $data = [
            'start_date' => isset($values['startDate']) ? new \DateTime($values['startDate']) : null,
            'end_date' => isset($values['endDate']) ? new \DateTime($values['endDate']) : null,
            'max_slots' => isset($values['maxSlots']) ? $values['maxSlots'] : null,
            'available_slots' => isset($values['availableSlots']) ? $values['availableSlots'] : null,
        ];

        if (isset($values['id']) && !is_null($values['id'])) {
            $data['id'] = $values['id'];
        }

        parent::__construct($data);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        if (!$this->has('id')) {
            return null;
        }

        return parent::getId();
    }

    /**
     * @param integer $id
     * @return Event
     */
    public function setId($id)
    {
        parent::setId($id);
        return $this;
    }

    /**
     * @return string
     */
    public function getStartDate()
    {
        return parent::getStartDate();
    }

    /**
     * @param string $startDate
     * @return Event
     */
    public function setStartDate($startDate)
    {
        parent::setStartDate($startDate);
        return $this;
    }

    /**
     * @return string
     */
    public function getEndDate()
    {
        return parent::getEndDate();
    }

    /**
     * @param string $endDate
     * @return Event
     */
    public function setEndDate($endDate)
    {
        parent::setEndDate($endDate);
        return $this;
    }

    /**
     * @return integer
     */
    public function getMaxSlots()
    {
        return parent::getMaxSlots();
    }

    /**
     * @param integer $maxSlots
     * @return Event
     */
    public function setMaxSlots($maxSlots)
    {
        parent::setMaxSlots($maxSlots);
        return $this;
    }

    /**
     * @return integer
     */
    public function getAvailableSlots()
    {
        return parent::getAvailableSlots();
    }

    /**
     * @param integer $availableSlots
     * @return Event
     */
    public function setAvailableSlots($availableSlots)
    {
        parent::setAvailableSlots($availableSlots);
        return $this;
    }

    /**
     * Fail silently if by incrementing the number of available slots, the value exceeds max slots.
     * (i.e.: if registrations were added manually to the database)
     *
     * @return $this
     */
    public function incrementAvailableSlots()
    {
        $availableSlots = $this->getAvailableSlots() + 1;

        if ($availableSlots <= $this->getMaxSlots()) {
            $this->setAvailableSlots($availableSlots);
        }

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function decrementAvailableSlots()
    {
        $availableSlots = $this->getAvailableSlots() - 1;

        if ($availableSlots < 0) {
            throw new \Exception('The event is fully booked.');
        }

        $this->setAvailableSlots($availableSlots);
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
          'id' => $this->getId(),
          'startDate' => $this->getStartDate()->format('Y-m-d'),
          'endDate'   => $this->getEndDate()->format('Y-m-d'),
          'maxSlots'  => $this->getMaxSlots(),
          'availableSlots' => $this->getAvailableSlots(),
        ];
    }
}
