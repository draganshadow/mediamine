<?php
namespace MediaMine\CoreBundle\Entity\Video;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\File\File;
use MediaMine\CoreBundle\Entity\AbstractEntity;

/**
 * Group Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\Video\GroupRepository")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\Table(name="video_group",indexes={@ORM\Index(name="video_group_name_idx", columns={"name"})})
 * @property int $id
 * @property string $name
 * @property string $originalName
 * @property string $summary
 * @property \MediaMine\CoreBundle\Entity\Video\GroupType $type
 * @property array $images
 * @property \MediaMine\CoreBundle\Entity\Video\Genre $genre
 */
class Group extends AbstractEntity
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
     * @Gedmo\Slug(fields={"name"}, updatable=false, unique=false)
     * @ORM\Column(length=128)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $originalName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $summary;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $type;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\CoreBundle\Entity\File\File", fetch="EAGER")
     * @ORM\JoinTable(name="video_group_image",
     *      joinColumns={@ORM\JoinColumn(name="group_ref", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_ref", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    protected $images;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\CoreBundle\Entity\Video\Genre", fetch="EAGER")
     * @ORM\JoinTable(name="video_group_genre",
     *      joinColumns={@ORM\JoinColumn(name="group_ref", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="genre_ref", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    protected $genres;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\File\Directory", fetch="EAGER")
     * @ORM\JoinColumn(name="directory_ref", referencedColumnName="id", onDelete="CASCADE", unique=false)
     */
    protected $directory;

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
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
        $this->genres = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Group
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
     * Set slug
     *
     * @param string $slug
     *
     * @return Group
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
     * Set originalName
     *
     * @param string $originalName
     *
     * @return Group
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
     * @return Group
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
     * Set type
     *
     * @param string $type
     *
     * @return Group
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
     * @return Group
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
     * @return Group
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
     * Add image
     *
     * @param \MediaMine\CoreBundle\Entity\File\File $image
     *
     * @return Group
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
     * Add genre
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Genre $genre
     *
     * @return Video
     */
    public function addGenreUnique($genre)
    {
        $found = false;
        foreach ($this->genres as $g) {
            if ($g->id == $genre->id) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $this->genres[] = $genre;
        }
        return $this;
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
     * Set directory
     *
     * @param \MediaMine\CoreBundle\Entity\File\Directory $directory
     *
     * @return Group
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
