<?php
namespace MediaMine\Entity\System;

use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Parameter Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\System\Parameter")
 * @ORM\Table(name="system_option",uniqueConstraints={@ORM\UniqueConstraint(name="unique_group_key", columns={"group", "key"})})
 * @property int $id
 * @property string $key
 * @property string $reference
 */
class Option implements ArraySerializableInterface
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $group;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $key;

    /**
     * @ORM\Column(type="text");
     */
    protected $value;

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