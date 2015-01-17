<?php
namespace MediaMine\CoreBundle\Entity\Common;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\File\File;
use MediaMine\CoreBundle\Entity\AbstractEntity;

/**
 * Person Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\Common\PersonRepository")
 * @ORM\Table(name="common_person",indexes={@ORM\Index(name="common_person_name_idx", columns={"name"})})
 * @property int $id
 * @property string $name
 * @property string $firstName
 * @property string $lastName
 * @property \MediaMine\CoreBundle\Entity\Common\Country $country
 * @property \DateTime $birthDate
 * @property \DateTime $deathDate
 * @property string $summary
 * @property array $images
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
     * @Gedmo\Slug(fields={"name"}, updatable=false, unique=false)
     * @ORM\Column(length=128)
     */
    private $slug;

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
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\Common\Country", fetch="EAGER")
     * @ORM\JoinColumn(name="country_ref", referencedColumnName="id", onDelete="SET NULL", unique=false, nullable=true)
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
     * @ORM\JoinTable(name="common_person_image",
     *      joinColumns={@ORM\JoinColumn(name="person_ref", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_ref", referencedColumnName="id")}
     *      )
     */
    protected $images;

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
     * Set slug
     *
     * @param string $slug
     *
     * @return Person
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
     * Set country
     *
     * @param \MediaMine\CoreBundle\Entity\Common\Country $country
     *
     * @return Person
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
