<?php
namespace MediaMine\CoreBundle\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\AbstractEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * VideoFileMeta Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\Video\VideoFileMetaRepository")
 * @ORM\Table(name="video_video_file_meta")
 * @property int $id
 * @property \MediaMine\CoreBundle\Entity\File\File $file
 * @property int $width
 * @property int $height
 */
class VideoFileMeta extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="MediaMine\CoreBundle\Entity\File\File", fetch="EAGER")
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
     * @var \DateTime $createdAt
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \Datetime $updatedAt
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
     * Set width
     *
     * @param string $width
     *
     * @return VideoFileMeta
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param string $height
     *
     * @return VideoFileMeta
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return string
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return VideoFileMeta
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
     * @return VideoFileMeta
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
     * Set file
     *
     * @param \MediaMine\CoreBundle\Entity\File\File $file
     *
     * @return VideoFileMeta
     */
    public function setFile(\MediaMine\CoreBundle\Entity\File\File $file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return \MediaMine\CoreBundle\Entity\File\File
     */
    public function getFile()
    {
        return $this->file;
    }
}
