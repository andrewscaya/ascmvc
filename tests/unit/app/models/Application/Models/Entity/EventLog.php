<?php

namespace Application\Models\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity("Application\Log\Entity\EventLog")
 * @ORM\Entity(repositoryClass="Application\Models\Repository\EventLogRepository")
 * @ORM\Table("eventlog")
 */
class EventLog
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=150, name="name")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=5000, name="parameters")
     */
    protected $parameters;

    /**
     * @ORM\Column(type="datetime", columnDefinition="TIMESTAMP", name="created")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", columnDefinition="TIMESTAMP", name="occurred")
     */
    protected $occurred;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param mixed $parameters
     */
    public function setParameters($parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created): void
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getOccurred()
    {
        return $this->occurred;
    }

    /**
     * @param mixed $occurred
     */
    public function setOccurred($occurred): void
    {
        $this->occurred = $occurred;
    }
}
