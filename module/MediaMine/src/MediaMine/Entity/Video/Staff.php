<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Group Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\Video\StaffRepository")
 * @ORM\Table(name="video_staff")
 * @property int $id
 * @property string $name
 * @property string $summary
 */
class Staff implements ArraySerializableInterface
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\Video")
     * @ORM\JoinColumn(name="video_ref", referencedColumnName="id", onDelete="CASCADE", unique=false)
     */
    protected $video;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Common\Person")
     * @ORM\JoinColumn(name="person_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $person;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\Character")
     * @ORM\JoinColumn(name="character_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $character;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\StaffRole")
     * @ORM\JoinColumn(name="staff_role_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $role;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\Entity\File\File")
     * @ORM\JoinTable(name="video_staff_image",
     *      joinColumns={@ORM\JoinColumn(name="staff_ref", referencedColumnName="id")},
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
