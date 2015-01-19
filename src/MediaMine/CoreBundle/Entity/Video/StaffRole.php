<?php
namespace MediaMine\CoreBundle\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\AbstractEntity;

/**
 * StaffRole Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\Video\StaffRoleRepository")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\Table(name="video_staff_role")
 * @property int $id
 * @property string $name
 * @property string $summary
 */
class StaffRole extends AbstractEntity
{
    const DIRECTOR = 'director';
    const WRITER = 'writer';
    const ACTOR = 'actor';
    const GUEST = 'guest';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="text", unique=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $summary;

    public static function getValues()
    {
        return array(self::DIRECTOR, self::ACTOR, self::WRITER, self::GUEST);
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
     * @return StaffRole
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
     * @return StaffRole
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
}
