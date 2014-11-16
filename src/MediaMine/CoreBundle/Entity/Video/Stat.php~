<?php
namespace MediaMine\CoreBundle\Entity\Video;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\AbstractEntity;

/**
 * Stat Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\Video\StatRepository")
 * @ORM\Table(name="video_stat")
 * @property int $id
 * @property int $nbView
 * @property string $lastView
 */
class Stat extends AbstractEntity
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
    protected $nbView;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $lastView;

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
     * Set nbView
     *
     * @param integer $nbView
     *
     * @return Stat
     */
    public function setNbView($nbView)
    {
        $this->nbView = $nbView;

        return $this;
    }

    /**
     * Get nbView
     *
     * @return integer
     */
    public function getNbView()
    {
        return $this->nbView;
    }

    /**
     * Set lastView
     *
     * @param \DateTime $lastView
     *
     * @return Stat
     */
    public function setLastView($lastView)
    {
        $this->lastView = $lastView;

        return $this;
    }

    /**
     * Get lastView
     *
     * @return \DateTime
     */
    public function getLastView()
    {
        return $this->lastView;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Stat
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
     * @return Stat
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
