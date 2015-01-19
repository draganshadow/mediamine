<?php
namespace MediaMine\CoreBundle\Entity\Tunnel;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\File\File;
use MediaMine\CoreBundle\Entity\AbstractEntity;

/**
 * Video Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\Tunnel\VideoRepository")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\Table(name="tunnel_video",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="unique_tunnel_video_ref", columns={"tunnel_ref", "video_ref"})},
 *      indexes={@ORM\Index(name="tunnel_video_name_idx", columns={"name"}),@ORM\Index(name="tunnel_video_video_idx", columns={"video_ref"})}
 * )
 * @property int $id
 * @property string $tunnel
 * @property \MediaMine\CoreBundle\Entity\Video\Video $video
 * @property string $name
 * @property string $originalName
 * @property string $summary
 * @property int $year
 * @property int $episode
 * @property string $group
 * @property string $season
 * @property array $images
 * @property array $files
 * @property string $country
 * @property array $rating
 * @property array $review
 * @property string $type
 * @property array $genres
 * @property array $staffs
 *
 */
class Video extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\System\Tunnel")
     * @ORM\JoinColumn(name="tunnel_ref", referencedColumnName="id", onDelete="SET NULL", unique=false, nullable=true)
     */
    protected $tunnel;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\Video\Video")
     * @ORM\JoinColumn(name="video_ref", referencedColumnName="id", onDelete="SET NULL", unique=false, nullable=true)
     */
    protected $video;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $originalName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $summary;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $year;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $episode;

    /**
     * @ORM\Column(type="string", name="vgroup", nullable=true)
     */
    protected $group;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $season;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\CoreBundle\Entity\File\File", cascade={"remove", "persist", "detach"})
     * @ORM\JoinTable(name="tunnel_video_image",
     *      joinColumns={@ORM\JoinColumn(name="video_ref", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_ref", referencedColumnName="id")}
     *      )
     */
    protected $images;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $country;

    /**
     * @ORM\Column(type="json_array", nullable=true);
     */
    protected $rating;

    /**
     * @ORM\Column(type="json_array", nullable=true);
     */
    protected $review;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $type;

    /**
     * @ORM\Column(type="json_array", nullable=true);
     */
    protected $genres;

    /**
     * @ORM\Column(type="json_array", nullable=true);
     */
    protected $staffs;

    /**
     * @ORM\Column(type="json_array", nullable=true);
     */
    protected $raw;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $dataLanguage;

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
     * Constructor
     */
    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Video
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
     * Set originalName
     *
     * @param string $originalName
     *
     * @return Video
     */
    public function setOriginalName($originalName)
    {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * Get originalName
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * Set summary
     *
     * @param string $summary
     *
     * @return Video
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set year
     *
     * @param integer $year
     *
     * @return Video
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set episode
     *
     * @param integer $episode
     *
     * @return Video
     */
    public function setEpisode($episode)
    {
        $this->episode = $episode;

        return $this;
    }

    /**
     * Get episode
     *
     * @return integer
     */
    public function getEpisode()
    {
        return $this->episode;
    }

    /**
     * Set group
     *
     * @param string $group
     *
     * @return Video
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set season
     *
     * @param integer $season
     *
     * @return Video
     */
    public function setSeason($season)
    {
        $this->season = $season;

        return $this;
    }

    /**
     * Get season
     *
     * @return integer
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Video
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set rating
     *
     * @param array $rating
     *
     * @return Video
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return array
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set review
     *
     * @param array $review
     *
     * @return Video
     */
    public function setReview($review)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review
     *
     * @return array
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Video
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set genres
     *
     * @param array $genres
     *
     * @return Video
     */
    public function setGenres($genres)
    {
        $this->genres = $genres;

        return $this;
    }

    /**
     * Get genres
     *
     * @return array
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Set staffs
     *
     * @param array $staffs
     *
     * @return Video
     */
    public function setStaffs($staffs)
    {
        $this->staffs = $staffs;

        return $this;
    }

    /**
     * Get staffs
     *
     * @return array
     */
    public function getStaffs()
    {
        return $this->staffs;
    }

    /**
     * Set raw
     *
     * @param array $raw
     *
     * @return Video
     */
    public function setRaw($raw)
    {
        $this->raw = $raw;

        return $this;
    }

    /**
     * Get raw
     *
     * @return array
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * Set dataLanguage
     *
     * @param string $dataLanguage
     *
     * @return Video
     */
    public function setDataLanguage($dataLanguage)
    {
        $this->dataLanguage = $dataLanguage;

        return $this;
    }

    /**
     * Get dataLanguage
     *
     * @return string
     */
    public function getDataLanguage()
    {
        return $this->dataLanguage;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Video
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
     * @return Video
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
     * Set tunnel
     *
     * @param \MediaMine\CoreBundle\Entity\System\Tunnel $tunnel
     *
     * @return Video
     */
    public function setTunnel(\MediaMine\CoreBundle\Entity\System\Tunnel $tunnel = null)
    {
        $this->tunnel = $tunnel;

        return $this;
    }

    /**
     * Get tunnel
     *
     * @return \MediaMine\CoreBundle\Entity\System\Tunnel
     */
    public function getTunnel()
    {
        return $this->tunnel;
    }

    /**
     * Set video
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Video $video
     *
     * @return Video
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
     * Add image
     *
     * @param \MediaMine\CoreBundle\Entity\File\File $image
     *
     * @return Video
     */
    public function addImage(\MediaMine\CoreBundle\Entity\File\File $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \MediaMine\CoreBundle\Entity\File\File $image
     */
    public function removeImage(\MediaMine\CoreBundle\Entity\File\File $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }
}
