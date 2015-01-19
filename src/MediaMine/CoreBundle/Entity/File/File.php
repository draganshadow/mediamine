<?php
namespace MediaMine\CoreBundle\Entity\File;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\AbstractEntity;

use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * File Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\File\FileRepository")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\Table(name="file_file",indexes={@ORM\Index(name="file_file_extension_idx", columns={"extension"}), @ORM\Index(name="file_file_directory_idx", columns={"directory_ref"})})
 * @property int $id
 * @property string $name
 * @property string $pathKey
 * @property \MediaMine\CoreBundle\Repository\File\Directory $directory
 * @property string $status
 * @property string $extension
 * @property int $size
 * @property DateTime $modificationDate
 */
class File extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="text")
     */
    protected $pathKey;

    /**
     * @ManyToOne(targetEntity="Directory", fetch="EAGER")
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

    /**
     * @var \DateTime $createdAt
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime $updatedAt
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function getFullName()
    {
        return $this->name . '.' . $this->extension;
    }

    public function getPath()
    {
        return $this->directory->path . '/' . $this->name . '.' . $this->extension;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set pathKey
     *
     * @param string $pathKey
     *
     * @return File
     */
    public function setPathKey($pathKey)
    {
        $this->pathKey = $pathKey;

        return $this;
    }

    /**
     * Get pathKey
     *
     * @return string
     */
    public function getPathKey()
    {
        return $this->pathKey;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return File
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set extension
     *
     * @param string $extension
     *
     * @return File
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return File
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set modificationDate
     *
     * @param \DateTime $modificationDate
     *
     * @return File
     */
    public function setModificationDate($modificationDate)
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }

    /**
     * Get modificationDate
     *
     * @return \DateTime
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return File
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return File
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set directory
     *
     * @param \MediaMine\CoreBundle\Entity\File\Directory $directory
     *
     * @return File
     */
    public function setDirectory(\MediaMine\CoreBundle\Entity\File\Directory $directory = null)
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * Get directory
     *
     * @return \MediaMine\CoreBundle\Entity\File\Directory
     */
    public function getDirectory()
    {
        return $this->directory;
    }
}
