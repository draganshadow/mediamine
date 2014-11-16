<?php
namespace MediaMine\CoreBundle\Entity\Tunnel;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\File\File;
use MediaMine\CoreBundle\Entity\AbstractEntity;

/**
 * Person Entity
 * @package MediaMine\CoreBundle\Entity\Tunnel
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\Tunnel\PersonRepository")
 * @ORM\Table(name="tunnel_person")
 * @ORM\Table(name="tunnel_person",uniqueConstraints={@ORM\UniqueConstraint(name="unique_tunnel_person_ref", columns={"tunnel_ref", "person_ref"})})
 * @property int $id
 * @property string $tunnel
 * @property \MediaMine\CoreBundle\Entity\Common\Person $person
 * @property string $name
 * @property string $firstName
 * @property string $lastName
 * @property string $country
 * @property \DateTime $birthDate
 * @property \DateTime $deathDate
 * @property string $summary
 * @property array  $images
 * @property string $raw
 */
class Person extends AbstractEntity
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
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\Common\Person")
     * @ORM\JoinColumn(name="person_ref", referencedColumnName="id", onDelete="SET NULL", unique=false, nullable=true)
     */
    protected $person;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $country;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $birthDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deathDate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $summary;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\CoreBundle\Entity\File\File")
     * @ORM\JoinTable(name="tunnel_person_image",
     *      joinColumns={@ORM\JoinColumn(name="person_ref", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_ref", referencedColumnName="id")}
     *      )
     */
    protected $images;

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
     * @return Person
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Person
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Person
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Person
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
     * Set birthDate
     *
     * @param \DateTime $birthDate
     *
     * @return Person
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set deathDate
     *
     * @param \DateTime $deathDate
     *
     * @return Person
     */
    public function setDeathDate($deathDate)
    {
        $this->deathDate = $deathDate;

        return $this;
    }

    /**
     * Get deathDate
     *
     * @return \DateTime
     */
    public function getDeathDate()
    {
        return $this->deathDate;
    }

    /**
     * Set summary
     *
     * @param string $summary
     *
     * @return Person
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
     * Set raw
     *
     * @param array $raw
     *
     * @return Person
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
     * @return Person
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
     * @return Person
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
     * @return Person
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
     * @return Person
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
     * Set person
     *
     * @param \MediaMine\CoreBundle\Entity\Common\Person $person
     *
     * @return Person
     */
    public function setPerson(\MediaMine\CoreBundle\Entity\Common\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \MediaMine\CoreBundle\Entity\Common\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Add image
     *
     * @param \MediaMine\CoreBundle\Entity\File\File $image
     *
     * @return Person
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
