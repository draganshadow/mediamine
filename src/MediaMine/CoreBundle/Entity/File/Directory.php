<?php
namespace MediaMine\CoreBundle\Entity\File;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\AbstractEntity;

/**
 * Directory Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\File\DirectoryRepository")
 * @ORM\Table(name="file_directory",indexes={@ORM\Index(name="file_directory_parent_directory_idx", columns={"parent_directory_ref"})})
 * @property int $id
 * @property string $name
 * @property string $path
 * @property Directory $parentDirectory
 * @property \DateTime $modificationDate
 * @property string $status
 */
class Directory extends AbstractEntity
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
    protected $path;

    /**
     * @ORM\ManyToOne(targetEntity="Directory")
     * @ORM\JoinColumn(name="parent_directory_ref", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parentDirectory;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $modificationDate;

    /**
     * @ORM\Column(type="string")
     */
    protected $status;

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
     * @return Directory
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
     * Set path
     *
     * @param string $path
     *
     * @return Directory
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set modificationDate
     *
     * @param \DateTime $modificationDate
     *
     * @return Directory
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
     * Set status
     *
     * @param string $status
     *
     * @return Directory
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Directory
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
     * @return Directory
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
     * Set parentDirectory
     *
     * @param \MediaMine\CoreBundle\Entity\File\Directory $parentDirectory
     *
     * @return Directory
     */
    public function setParentDirectory(\MediaMine\CoreBundle\Entity\File\Directory $parentDirectory = null)
    {
        $this->parentDirectory = $parentDirectory;

        return $this;
    }

    /**
     * Get parentDirectory
     *
     * @return \MediaMine\CoreBundle\Entity\File\Directory
     */
    public function getParentDirectory()
    {
        return $this->parentDirectory;
    }
}
