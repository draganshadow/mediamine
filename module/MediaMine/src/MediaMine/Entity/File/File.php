<?php
namespace MediaMine\Entity\File;

use Doctrine\ORM\Mapping as ORM;
use Netsyos\Common\Entity\AbstractEntity;

use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * File Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\File\FileRepository")
 * @ORM\Table(name="file_file")
 * @property int $id
 * @property string $name
 * @property Directory $directory
 * @property date $modificationDate
 * @property Extension $extension
 * @property string $size
 * @property string $status
 */
class File extends AbstractEntity
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
    protected $pathKey;

    /**
     * @ManyToOne(targetEntity="Directory")
     * @JoinColumn(name="directory_ref", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $directory;

    /**
     * @ORM\Column(type="string")
     */
    protected $status;

    /**
     * @ORM\Column(type="string")
     */
    protected $extension;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $size;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $modificationDate;

    public function getFullName()
    {
        return $this->name . '.' . $this->extension;
    }

    public function getPath()
    {
        return $this->directory->path . '/' . $this->name . '.' . $this->extension;
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function exchangeArray(array $data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
    }
}
