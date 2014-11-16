<?php
namespace MediaMine\CoreBundle\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\File\File;
use MediaMine\CoreBundle\Entity\AbstractEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Video Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\Video\VideoRepository")
 * @ORM\Table(name="video_video",indexes={@ORM\Index(name="video_video_name_idx", columns={"name"})})
 * @property int $id
 * @property string $name
 * @property string $originalName
 * @property string $summary
 * @property int $year
 * @property int $episode
 * @property \MediaMine\CoreBundle\Entity\Video\Group $group
 * @property \MediaMine\CoreBundle\Entity\Video\Season $season
 * @property array $images
 * @property array $files
 * @property \MediaMine\CoreBundle\Entity\Common\Country $country
 * @property \MediaMine\CoreBundle\Entity\Video\Rating $rating
 * @property \MediaMine\CoreBundle\Entity\Video\Review $review
 * @property \MediaMine\CoreBundle\Entity\Video\Type $type
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
     * @Gedmo\Slug(fields={"episode", "name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

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
    protected $filePathKey;

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
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\Video\Group", fetch="EAGER", cascade={"detach"})
     * @ORM\JoinColumn(name="group_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $group;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\Video\Season", fetch="EAGER", inversedBy="episodes", cascade={"detach"})
     * @ORM\JoinColumn(name="season_ref", referencedColumnName="id", onDelete="SET NULL", unique=false, nullable=true)
     */
    protected $season;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\CoreBundle\Entity\File\File", fetch="EAGER", cascade={"remove", "persist", "detach"})
     * @ORM\JoinTable(name="video_video_image",
     *      joinColumns={@ORM\JoinColumn(name="video_ref", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_ref", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    protected $images;

    /**
     * @ORM\OneToMany(targetEntity="MediaMine\CoreBundle\Entity\Video\VideoFile", fetch="EAGER", mappedBy="video", cascade={"detach"})
     */
    protected $files;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\Common\Country", fetch="EAGER")
     * @ORM\JoinColumn(name="country_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $country;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\Video\Rating", fetch="EAGER", cascade={"detach", "merge"})
     * @ORM\JoinColumn(name="rating_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $rating;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\Video\Review", fetch="EAGER", cascade={"detach"})
     * @ORM\JoinColumn(name="review_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $review;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $type;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\CoreBundle\Entity\Video\Genre")
     * @ORM\JoinTable(name="video_video_genre",
     *      joinColumns={@ORM\JoinColumn(name="video_ref", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="genre_ref", referencedColumnName="id")}
     *      )
     */
    protected $genres;

    /**
     * @ORM\OneToMany(targetEntity="MediaMine\CoreBundle\Entity\Video\Staff", mappedBy="video", cascade={"detach"})
     */
    protected $staffs;

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

    public function getStaffByRole()
    {
        $result = array();
        if (is_array($this->staffs)) {
            foreach ($this->staffs as $staff) {
                if (!array_key_exists($staff->role, $result)) {
                    $result[$staff->role] = array();
                }
                $result[$staff->role][$staff->person->name] = $staff;
            }
        }
        return $result;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
        $this->genres = new \Doctrine\Common\Collections\ArrayCollection();
        $this->staffs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set slug
     *
     * @param string $slug
     *
     * @return Video
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
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
     * Set group
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Group $group
     *
     * @return Video
     */
    public function setGroup(\MediaMine\CoreBundle\Entity\Video\Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \MediaMine\CoreBundle\Entity\Video\Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set season
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Season $season
     *
     * @return Video
     */
    public function setSeason(\MediaMine\CoreBundle\Entity\Video\Season $season = null)
    {
        $this->season = $season;

        return $this;
    }

    /**
     * Get season
     *
     * @return \MediaMine\CoreBundle\Entity\Video\Season
     */
    public function getSeason()
    {
        return $this->season;
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

    /**
     * Add file
     *
     * @param \MediaMine\CoreBundle\Entity\Video\VideoFile $file
     *
     * @return Video
     */
    public function addFile(\MediaMine\CoreBundle\Entity\Video\VideoFile $file)
    {
        $this->files[] = $file;

        return $this;
    }

    /**
     * Remove file
     *
     * @param \MediaMine\CoreBundle\Entity\Video\VideoFile $file
     */
    public function removeFile(\MediaMine\CoreBundle\Entity\Video\VideoFile $file)
    {
        $this->files->removeElement($file);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set country
     *
     * @param \MediaMine\CoreBundle\Entity\Common\Country $country
     *
     * @return Video
     */
    public function setCountry(\MediaMine\CoreBundle\Entity\Common\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \MediaMine\CoreBundle\Entity\Common\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set rating
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Rating $rating
     *
     * @return Video
     */
    public function setRating(\MediaMine\CoreBundle\Entity\Video\Rating $rating = null)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return \MediaMine\CoreBundle\Entity\Video\Rating
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set review
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Review $review
     *
     * @return Video
     */
    public function setReview(\MediaMine\CoreBundle\Entity\Video\Review $review = null)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review
     *
     * @return \MediaMine\CoreBundle\Entity\Video\Review
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Add genre
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Genre $genre
     *
     * @return Video
     */
    public function addGenre(\MediaMine\CoreBundle\Entity\Video\Genre $genre)
    {
        $this->genres[] = $genre;

        return $this;
    }

    /**
     * Remove genre
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Genre $genre
     */
    public function removeGenre(\MediaMine\CoreBundle\Entity\Video\Genre $genre)
    {
        $this->genres->removeElement($genre);
    }

    /**
     * Get genres
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Add staff
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Staff $staff
     *
     * @return Video
     */
    public function addStaff(\MediaMine\CoreBundle\Entity\Video\Staff $staff)
    {
        $this->staffs[] = $staff;

        return $this;
    }

    /**
     * Remove staff
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Staff $staff
     */
    public function removeStaff(\MediaMine\CoreBundle\Entity\Video\Staff $staff)
    {
        $this->staffs->removeElement($staff);
    }

    /**
     * Get staffs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStaffs()
    {
        return $this->staffs;
    }

    /**
     * Set filePathKey
     *
     * @param string $filePathKey
     *
     * @return Video
     */
    public function setFilePathKey($filePathKey)
    {
        $this->filePathKey = $filePathKey;

        return $this;
    }

    /**
     * Get filePathKey
     *
     * @return string
     */
    public function getFilePathKey()
    {
        return $this->filePathKey;
    }
}
