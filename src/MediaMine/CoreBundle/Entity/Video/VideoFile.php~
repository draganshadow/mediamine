<?php
namespace MediaMine\CoreBundle\Entity\Video;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\AbstractEntity;

/**
 * VideoFile Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\Video\VideoFileRepository")
 * @ORM\Table(name="video_video_file",indexes={@ORM\Index(name="video_video_file_video_idx", columns={"video_ref"})})
 * @property int $id
 * @property \MediaMine\CoreBundle\Entity\Video\Video $video
 * @property \MediaMine\CoreBundle\Entity\File\File $file
 * @property \MediaMine\CoreBundle\Entity\Video\Picture $picture
 * @property \MediaMine\CoreBundle\Entity\Video\Quality $quality
 * @property \MediaMine\CoreBundle\Entity\Video\Stat $stat
 * @property array $languages
 */
class VideoFile extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\Video\Video", fetch="EAGER", inversedBy="files", cascade={"detach"})
     * @ORM\JoinColumn(name="video_ref", referencedColumnName="id", onDelete="CASCADE", unique=false)
     */
    protected $video;

    /**
     * @ORM\OneToOne(targetEntity="MediaMine\CoreBundle\Entity\File\File", fetch="EAGER", cascade={"detach"})
     * @ORM\JoinColumn(name="file_ref", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $file;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\Video\Picture", fetch="EAGER", cascade={"detach"})
     * @ORM\JoinColumn(name="picture_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $picture;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\Video\Quality", fetch="EAGER")
     * @ORM\JoinColumn(name="quality_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $quality;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\Video\Stat", fetch="EAGER")
     * @ORM\JoinColumn(name="stat_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $stat;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\CoreBundle\Entity\Common\Country", fetch="EAGER")
     * @ORM\JoinTable(name="video_file_language",
     *      joinColumns={@ORM\JoinColumn(name="video_file_ref", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="country_ref", referencedColumnName="id")}
     *      )
     */
    protected $languages;

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
     * Constructor
     */
    public function __construct()
    {
        $this->languages = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return VideoFile
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
     * @return VideoFile
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
     * Set video
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Video $video
     *
     * @return VideoFile
     */
    public function setVideo(\MediaMine\CoreBundle\Entity\Video\Video $video = null)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video
     *
     * @return \MediaMine\CoreBundle\Entity\Video\Video
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set file
     *
     * @param \MediaMine\CoreBundle\Entity\File\File $file
     *
     * @return VideoFile
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

    /**
     * Set picture
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Picture $picture
     *
     * @return VideoFile
     */
    public function setPicture(\MediaMine\CoreBundle\Entity\Video\Picture $picture = null)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return \MediaMine\CoreBundle\Entity\Video\Picture
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set quality
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Quality $quality
     *
     * @return VideoFile
     */
    public function setQuality(\MediaMine\CoreBundle\Entity\Video\Quality $quality = null)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * Get quality
     *
     * @return \MediaMine\CoreBundle\Entity\Video\Quality
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Set stat
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Stat $stat
     *
     * @return VideoFile
     */
    public function setStat(\MediaMine\CoreBundle\Entity\Video\Stat $stat = null)
    {
        $this->stat = $stat;

        return $this;
    }

    /**
     * Get stat
     *
     * @return \MediaMine\CoreBundle\Entity\Video\Stat
     */
    public function getStat()
    {
        return $this->stat;
    }

    /**
     * Add language
     *
     * @param \MediaMine\CoreBundle\Entity\Common\Country $language
     *
     * @return VideoFile
     */
    public function addLanguage(\MediaMine\CoreBundle\Entity\Common\Country $language)
    {
        $this->languages[] = $language;

        return $this;
    }

    /**
     * Remove language
     *
     * @param \MediaMine\CoreBundle\Entity\Common\Country $language
     */
    public function removeLanguage(\MediaMine\CoreBundle\Entity\Common\Country $language)
    {
        $this->languages->removeElement($language);
    }

    /**
     * Get languages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLanguages()
    {
        return $this->languages;
    }
}
