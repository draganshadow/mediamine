<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Rating Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Entity\Video\RatingRepository")
 * @ORM\Table(name="video_rating")
 * @property int $id
 * @property int $localRate
 */
class Rating implements ArraySerializableInterface
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $localRate;

    /**
     * @ORM\Column(type="integer")
     */
    protected $localNbVote;

    /**
     * @ORM\Column(type="integer")
     */
    protected $webRate;

    /**
     * @ORM\Column(type="integer")
     */
    protected $webNbVote;

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
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->summary = (isset($data['summary'])) ? $data['summary'] : null;
    }
}
