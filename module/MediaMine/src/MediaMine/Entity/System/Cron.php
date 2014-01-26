<?php
namespace MediaMine\Entity\System;

use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Task Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\System\CronRepository")
 * @ORM\Table(name="system_cron")
 * @property int $id
 * @property string $key
 * @property string $reference
 */
class Cron implements ArraySerializableInterface
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text", unique=true)
     */
    protected $key;

    /**
     * @ORM\Column(type="string");
     */
    protected $frequency;

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
        $this->frequency = (isset($data['frequency'])) ? $data['frequency'] : null;
        $this->service = (isset($data['service'])) ? $data['service'] : null;
        $this->callback = (isset($data['callback'])) ? $data['callback'] : null;
        $this->arguments = (isset($data['arguments'])) ? $data['arguments'] : null;
    }
}
