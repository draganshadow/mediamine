<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Group Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Entity\Video\CharacterRepository")
 * @ORM\Table(name="video_character")
 * @property int $id
 * @property string $name
 * @property string $summary
 */
class Character implements ArraySerializableInterface
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
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $summary;


    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\Video")
     * @ORM\JoinColumn(name="video_ref", referencedColumnName="id", onDelete="CASCADE", unique=false)
     */
    protected $video;

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
