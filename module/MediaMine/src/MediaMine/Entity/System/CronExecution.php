<?php
namespace MediaMine\Entity\System;

use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Task Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\System\CronExecution")
 * @ORM\Table(name="system_cron_execution")
 * @property int $id
 * @property string $key
 * @property string $reference
 */
class CronExecution implements ArraySerializableInterface
{
    protected $inputFilter;

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
     * @ORM\Column(type="string")
     */
    protected $status;

    /**
     * @ORM\Column(type="text")
     */
    protected $errorMsg;

    /**
     * @ORM\Column(type="text")
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
     * @ORM\Column(type="datetime")
     */
    protected $executeTime;

    /**
     * @ORM\Column(type="datetime")
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
    }
}
