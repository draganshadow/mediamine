<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use MediaMine\Entity\File\File;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Group Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Entity\Video\GroupRepository")
 * @ORM\Table(name="video_group")
 * @property int $id
 * @property string $name
 * @property string $summary
 * @property MediaMine\Entity\File\File $image
 */
class Group implements ArraySerializableInterface
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
     * @ORM\Column(type="text")
     */
    protected $summary;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\GroupType")
     * @ORM\JoinColumn(name="group_type_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $type;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\Entity\File\File")
     * @ORM\JoinTable(name="video_group_image",
     *      joinColumns={@ORM\JoinColumn(name="group_ref", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_ref", referencedColumnName="id")}
     *      )
     */
    protected $images;

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
     * Add categories
     *
     * @param File $image
     */
    public function addImage(File $image)
    {
        $this->images[] = $image;
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
