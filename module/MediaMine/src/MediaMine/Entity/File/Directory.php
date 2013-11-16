<?php
namespace MediaMine\Entity\File;

use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Directory Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Entity\File\DirectoryRepository")
 * @ORM\Table(name="file_directory")
 * @property int $id
 * @property string $path
 * @property string $name
 * @property Directory $parentDirectory
 * @property date $date_modified
 * @property string $status
 */
class Directory implements ArraySerializableInterface
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
    protected $path;

    /**
     * @ORM\ManyToOne(targetEntity="Directory")
     * @ORM\JoinColumn(name="parent_directory_ref", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parentDirectory;

    /**
     * @ORM\Column(name="date_modified", type="datetime")
     */
    protected $dateModified;

    /**
     * @ORM\Column(type="string")
     */
    protected $status;

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
        $this->path = (isset($data['path'])) ? $data['path'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
    }
}
