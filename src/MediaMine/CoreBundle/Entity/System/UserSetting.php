<?php
namespace MediaMine\CoreBundle\Entity\System;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\AbstractEntity;

/**
 * Setting Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\System\SettingRepository")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\Table(name="system_user_setting",uniqueConstraints={@ORM\UniqueConstraint(name="user_setting_unique_group_key", columns={"groupKey", "key"})})
 * @property int $id
 * @property string $key
 * @property string $reference
 */
class UserSetting extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\System\Module")
     * @ORM\JoinColumn(name="module_ref", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $module;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_ref", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @ORM\Column(type="string")
     */
    protected $groupKey;

    /**
     * @ORM\Column(type="string")
     */
    protected $key;

    /**
     * @ORM\Column(type="json_array");
     */
    protected $value;

    /**
     * @ORM\Column(type="integer")
     */
    protected $size = 1;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $editable = true;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set groupKey
     *
     * @param string $groupKey
     *
     * @return Setting
     */
    public function setGroupKey($groupKey)
    {
        $this->groupKey = $groupKey;

        return $this;
    }

    /**
     * Get groupKey
     *
     * @return string
     */
    public function getGroupKey()
    {
        return $this->groupKey;
    }

    /**
     * Set key
     *
     * @param string $key
     *
     * @return Setting
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set value
     *
     * @param array $value
     *
     * @return Setting
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return Setting
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set editable
     *
     * @param boolean $editable
     *
     * @return Setting
     */
    public function setEditable($editable)
    {
        $this->editable = $editable;

        return $this;
    }

    /**
     * Get editable
     *
     * @return boolean
     */
    public function getEditable()
    {
        return $this->editable;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Setting
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
     * @return Setting
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
     * Set module
     *
     * @param \MediaMine\CoreBundle\Entity\System\Module $module
     *
     * @return Setting
     */
    public function setModule(\MediaMine\CoreBundle\Entity\System\Module $module = null)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get module
     *
     * @return \MediaMine\CoreBundle\Entity\System\Module
     */
    public function getModule()
    {
        return $this->module;
    }
}
