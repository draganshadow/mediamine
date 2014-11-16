<?php
namespace MediaMine\CoreBundle\Entity\Tunnel;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\File\File;
use MediaMine\CoreBundle\Entity\AbstractEntity;

/**
 * Season Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\Tunnel\SeasonRepository")
 * @ORM\Table(name="tunnel_season")
 * @property int $id
 * @property int $number
 * @property string $name
 * @property string $summary
 * @property string $group
 * @property array $images
 * @property array $episodes
 */
class Season extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\Video\Season")
     * @ORM\JoinColumn(name="season_ref", referencedColumnName="id", onDelete="SET NULL", unique=false, nullable=true)
     */
    protected $season;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\System\Tunnel")
     * @ORM\JoinColumn(name="tunnel_ref", referencedColumnName="id", onDelete="SET NULL", unique=false, nullable=true)
     */
    protected $tunnel;

    /**
     * @ORM\Column(type="integer")
     */
    protected $number;

    /**
     * @ORM\Column(type="text")
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $summary;

    /**
     * @ORM\Column(type="string", name="sgroup", nullable=true)
     */
    protected $group;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\CoreBundle\Entity\File\File", cascade={"remove", "persist", "detach"})
     * @ORM\JoinTable(name="tunnel_season_image",
     *      joinColumns={@ORM\JoinColumn(name="season_ref", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_ref", referencedColumnName="id")}
     *      )
     */
    protected $images;

    /**
     * @ORM\OneToMany(targetEntity="MediaMine\CoreBundle\Entity\Video\Video", mappedBy="season")
     */
    protected $episodes;

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
        $this->episodes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set number
     *
     * @param integer $number
     *
     * @return Season
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Season
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
     * Set summary
     *
     * @param string $summary
     *
     * @return Season
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
     * Set group
     *
     * @param string $group
     *
     * @return Season
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
     * Set raw
     *
     * @param array $raw
     *
     * @return Season
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
     * @return Season
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
     * @return Season
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
     * @return Season
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
     * Set season
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Season $season
     *
     * @return Season
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
     * Set tunnel
     *
     * @param \MediaMine\CoreBundle\Entity\System\Tunnel $tunnel
     *
     * @return Season
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
     * @return Season
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
     * Add episode
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Video $episode
     *
     * @return Season
     */
    public function addEpisode(\MediaMine\CoreBundle\Entity\Video\Video $episode)
    {
        $this->episodes[] = $episode;

        return $this;
    }

    /**
     * Remove episode
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Video $episode
     */
    public function removeEpisode(\MediaMine\CoreBundle\Entity\Video\Video $episode)
    {
        $this->episodes->removeElement($episode);
    }

    /**
     * Get episodes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEpisodes()
    {
        return $this->episodes;
    }
}
