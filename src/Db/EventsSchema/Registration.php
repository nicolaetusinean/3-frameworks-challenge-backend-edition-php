<?php

namespace App\Db\EventsSchema;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * Registration
 *
 * Flexible entity for relation
 * events.registrations
 *
 * @see FlexibleEntity
 */
class Registration extends FlexibleEntity
{
    /**
     * Registration constructor.
     * @param array|null $values
     */
    public function __construct(array $values = null)
    {
        $data = [
            'first_name' => isset($values['firstName']) ? $values['firstName'] : null,
            'last_name' => isset($values['lastName']) ? $values['lastName'] : null,
            'email' => isset($values['email']) ? $values['email'] : null,
            'phone' => isset($values['phone']) ? $values['phone'] : null,
            'event_id' => isset($values['eventId']) ? $values['eventId'] : null,
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
     * @return Registration
     */
    public function setId($id)
    {
        parent::setId($id);
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return parent::getFirstName();
    }

    /**
     * @param string $firstName
     * @return Registration
     */
    public function setFirstName(string $firstName)
    {
        parent::setFirstName($firstName);
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return parent::getLastName();
    }

    /**
     * @param string $firstName
     * @return Registration
     */
    public function setLastName(string $lastName)
    {
        parent::setLastName($lastName);
        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return parent::getPhone();
    }

    /**
     * @param string $phone
     * @return Registration
     */
    public function setPhone(string $phone)
    {
        parent::setPhone($phone);
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return parent::getEmail();
    }

    /**
     * @param string $email
     * @return Registration
     */
    public function setEmail(string $email)
    {
        parent::setEmail($email);
        return $this;
    }

    /**
     * @return integer
     */
    public function getEventId()
    {
        return parent::getEventId();
    }

    /**
     * @param string $email
     * @return Registration
     */
    public function setEventId($eventId)
    {
        parent::setEventId($eventId);
        return $this;
    }

    /**
     * @return array
     */
    public function toArray() {
        return [
            'id' => $this->getId(),
            'firstName' => $this->getFirstName(),
            'lastName'  => $this->getLastName(),
            'phone'     => $this->getPhone(),
            'email'     => $this->getEmail(),
            'eventId'     => $this->getEventId(),
        ];
    }
}
