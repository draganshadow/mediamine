<?php
namespace MediaMine\CoreBundle\Entity;

abstract class AbstractEntity
{
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
     * @param $array
     */
    public function exchangeArray($array)
    {
        foreach($array as $key => $value)
        {
            if (property_exists($this,$key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @param $array
     */
    public function exchangeArrayComplete($array)
    {
        foreach($array as $key => $value)
        {
            if (property_exists($this,$key) && empty($this->{$key})) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @param $array
     */
    public function exchangeArrayNoEmpty($array)
    {
        foreach($array as $key => $value)
        {
            if (property_exists($this,$key) && !empty($value)) {
                $this->{$key} = $value;
            }
        }
    }
}