<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * VideoFileMeta Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\Video\VideoFileMetaRepository")
 * @ORM\Table(name="video_video_file_meta")
 * @property int $id
 * @property string $name
 */
class VideoFileMeta implements ArraySerializableInterface
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="MediaMine\Entity\File\File")
     * @ORM\JoinColumn(name="file_ref", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $file;

    /**
     * @ORM\Column(type="text")
     */
    protected $width;

    /**
     * @ORM\Column(type="text")
     */
    protected $height;

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
