<?php
namespace MediaMine\CoreBundle\Entity\Video;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\AbstractEntity;

/**
 * Staff Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\Video\StaffRepository")
 * @ORM\Table(name="video_staff",indexes={@ORM\Index(name="video_staff_video_idx", columns={"video_ref"})})
 * @property int $id
 * @property \MediaMine\CoreBundle\Entity\Video\Video $video
 * @property \MediaMine\CoreBundle\Entity\Common\Person $person
 * @property \MediaMine\CoreBundle\Entity\Video\Character $character
 * @property string $role
 * @property array $images
 */
class Staff extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\Video\Video", fetch="EAGER", inversedBy="staffs")
     * @ORM\JoinColumn(name="video_ref", referencedColumnName="id", onDelete="CASCADE", unique=false)
     */
    protected $video;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\Common\Person", fetch="EAGER")
     * @ORM\JoinColumn(name="person_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $person;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\Video\Character", fetch="EAGER")
     * @ORM\JoinColumn(name="character_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $character;


    /**
     * @ORM\Column(type="string")
     */
    protected $role;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\CoreBundle\Entity\File\File", fetch="EAGER")
     * @ORM\JoinTable(name="video_staff_image",
     *      joinColumns={@ORM\JoinColumn(name="staff_ref", referencedColumnName="id")},
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
     * Set role
     *
     * @param string $role
     *
     * @return Staff
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Staff
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
     * @return Staff
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
     * @return Staff
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
     * Set person
     *
     * @param \MediaMine\CoreBundle\Entity\Common\Person $person
     *
     * @return Staff
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
     * Set character
     *
     * @param \MediaMine\CoreBundle\Entity\Video\Character $character
     *
     * @return Staff
     */
    public function setCharacter(\MediaMine\CoreBundle\Entity\Video\Character $character = null)
    {
        $this->character = $character;

        return $this;
    }

    /**
     * Get character
     *
     * @return \MediaMine\CoreBundle\Entity\Video\Character
     */
    public function getCharacter()
    {
        return $this->character;
    }

    /**
     * Add image
     *
     * @param \MediaMine\CoreBundle\Entity\File\File $image
     *
     * @return Staff
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
