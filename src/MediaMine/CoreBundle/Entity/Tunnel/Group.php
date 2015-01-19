<?php
namespace MediaMine\CoreBundle\Entity\Tunnel;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\File\File;
use MediaMine\CoreBundle\Entity\AbstractEntity;

/**
 * Group Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\Tunnel\GroupRepository")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\Table(name="tunnel_group")
 * @property int $id
 * @property string $name
 * @property string $originalName
 * @property string $summary
 * @property string $type
 * @property array $images
 * @property array $genres
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
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\Video\Group")
     * @ORM\JoinColumn(name="group_ref", referencedColumnName="id", onDelete="SET NULL", unique=false, nullable=true)
     */
    protected $group;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\System\Tunnel")
     * @ORM\JoinColumn(name="tunnel_ref", referencedColumnName="id", onDelete="SET NULL", unique=false, nullable=true)
     */
    protected $tunnel;

    /**
     * @ORM\Column(type="text")
     */
    protected $name;

    /**
     * @ORM\Column(type="text")
     */
    protected $originalName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $summary;

    /**
     * @ORM\Column(type="string", name="type", nullable=true)
     */
    protected $type;

    /**
     * @ORM\Column(type="json_array", nullable=true);
     */
    protected $rating;

    /**
     * @ORM\Column(type="json_array", nullable=true);
     */
    protected $staffs;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\CoreBundle\Entity\File\File", cascade={"remove", "persist", "detach"})
     * @ORM\JoinTable(name="tunnel_group_image",
     *      joinColumns={@ORM\JoinColumn(name="group_ref", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_ref", referencedColumnName="id")}
     *      )
     */
    protected $images;

    /**
     * @ORM\Column(type="json_array", nullable=true);
     */
    protected $genres;

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
     * Set rating
     *
     * @param array $rating
     *
     * @return Group
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
     * Set staffs
     *
     * @param array $staffs
     *
     * @return Group
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
     * Set genres
     *
     * @param array $genres
     *
     * @return Group
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
     * Set raw
     *
     * @param array $raw
     *
     * @return Group
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
     * @return Group
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
     * Set group
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Group $group
     *
     * @return Group
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
     * Set tunnel
     *
     * @param \MediaMine\CoreBundle\Entity\System\Tunnel $tunnel
     *
     * @return Group
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
}
