<?php
namespace MediaMine\CoreBundle\Entity\Video;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\AbstractEntity;

/**
 * Rating Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\Video\RatingRepository")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\Table(name="video_rating")
 * @property int $id
 * @property int $localRate
 * @property int $localNbVote
 * @property int $webRate
 * @property int $webNbVote
 */
class Rating extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $localRate;

    /**
     * @ORM\Column(type="integer")
     */
    protected $localNbVote;

    /**
     * @ORM\Column(type="integer")
     */
    protected $webRate;

    /**
     * @ORM\Column(type="integer")
     */
    protected $webNbVote;

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
     * Set localRate
     *
     * @param integer $localRate
     *
     * @return Rating
     */
    public function setLocalRate($localRate)
    {
        $this->localRate = $localRate;

        return $this;
    }

    /**
     * Get localRate
     *
     * @return integer
     */
    public function getLocalRate()
    {
        return $this->localRate;
    }

    /**
     * Set localNbVote
     *
     * @param integer $localNbVote
     *
     * @return Rating
     */
    public function setLocalNbVote($localNbVote)
    {
        $this->localNbVote = $localNbVote;

        return $this;
    }

    /**
     * Get localNbVote
     *
     * @return integer
     */
    public function getLocalNbVote()
    {
        return $this->localNbVote;
    }

    /**
     * Set webRate
     *
     * @param integer $webRate
     *
     * @return Rating
     */
    public function setWebRate($webRate)
    {
        $this->webRate = $webRate;

        return $this;
    }

    /**
     * Get webRate
     *
     * @return integer
     */
    public function getWebRate()
    {
        return $this->webRate;
    }

    /**
     * Set webNbVote
     *
     * @param integer $webNbVote
     *
     * @return Rating
     */
    public function setWebNbVote($webNbVote)
    {
        $this->webNbVote = $webNbVote;

        return $this;
    }

    /**
     * Get webNbVote
     *
     * @return integer
     */
    public function getWebNbVote()
    {
        return $this->webNbVote;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Rating
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
     * @return Rating
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
}
