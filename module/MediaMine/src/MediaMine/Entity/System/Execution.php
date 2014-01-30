<?php
namespace MediaMine\Entity\System;

use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Task Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\System\ExecutionRepository")
 * @ORM\Table(name="system_execution")
 * @property int $id
 * @property string $key
 * @property string $status
 * @property \DateTime $createTime
 * @property \DateTime $scheduleTime
 */
class Execution implements ArraySerializableInterface
{
    const STATUS_PLANNED    = 'planned';
    const STATUS_SKIPPED    = 'skipped';
    const STATUS_RUNNING    = 'running';
    const STATUS_DONE       = 'done';
    const STATUS_ERROR      = 'error';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     */
    protected $key;

    /**
     * @ORM\Column(type="string");
     */
    protected $service;

    /**
     * @ORM\Column(type="string");
     */
    protected $callback;

    /**
     * @ORM\Column(type="json_array");
     */
    protected $arguments;

    /**
     * @ORM\Column(type="string")
     */
    protected $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $errorMsg;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $stackTrace;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createTime;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $scheduleTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $executeTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $finishTime;

    /**
     * Magic getter to expose protected properties.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * Magic setter to save protected properties.
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function exchangeArray(array $data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->key = (isset($data['key'])) ? $data['key'] : null;
        $this->service = (isset($data['service'])) ? $data['service'] : null;
        $this->callback = (isset($data['callback'])) ? $data['callback'] : null;
        $this->arguments = (isset($data['arguments'])) ? $data['arguments'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->createTime = (isset($data['createTime'])) ? $data['createTime'] : null;
        $this->scheduleTime = (isset($data['scheduleTime'])) ? $data['scheduleTime'] : null;
        $this->executeTime = (isset($data['executeTime'])) ? $data['executeTime'] : null;
        $this->finishTime = (isset($data['finishTime'])) ? $data['finishTime'] : null;
    }
}
