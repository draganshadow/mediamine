<?php
namespace MediaMine\CoreBundle\Entity;

abstract class AbstractEntity
{
    /**
     * @return mixed
     */
    abstract public function getId();

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
    public function getArrayCopy($maxDepth = 1)
    {
        $array = [];
        foreach ($this as $f => $v) {
            if (is_scalar($v)) {
                $array[$f] = $v;
            }
            elseif (($maxDepth > 0) && $v instanceof AbstractEntity) {
                $array[$f] = $v->getArrayCopy($maxDepth - 1);
            } elseif (is_array($v)) {
                foreach ($v as $sf => $sv) {
                    if (($maxDepth > 0) && $sv instanceof AbstractEntity) {
                        $v[$sf] = $sv->getArrayCopy($maxDepth - 1);
                    }
                }
            }
        }
        return $array;
    }
}