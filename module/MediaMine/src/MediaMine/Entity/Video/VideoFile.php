<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Group Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\Video\VideoFileRepository")
 * @ORM\Table(name="video_video_file")
 * @property MediaMine\Entity\Video\Video $video
 * @property MediaMine\Entity\File\File $file
 */
class VideoFile implements ArraySerializableInterface
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
     * @ORM\OneToOne(targetEntity="MediaMine\Entity\File\File")
     * @ORM\JoinColumn(name="file_ref", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $file;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\Picture")
     * @ORM\JoinColumn(name="picture_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $picture;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\Quality")
     * @ORM\JoinColumn(name="quality_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $quality;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\Stat")
     * @ORM\JoinColumn(name="stat_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $stat;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\Entity\Common\Country")
     * @ORM\JoinTable(name="video_file_language",
     *      joinColumns={@ORM\JoinColumn(name="video_file_ref", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="country_ref", referencedColumnName="id")}
     *      )
     */
    protected $languages;

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
    }
}
