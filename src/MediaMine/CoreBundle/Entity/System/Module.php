<?php
namespace MediaMine\CoreBundle\Entity\System;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\AbstractEntity;

/**
 * Module Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\System\ModuleRepository")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\Table(name="system_module")
 * @property int $id
 * @property string $key
 * @property string $reference
 */
class Module extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $key;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $namespace;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="boolean");
     */
    protected $lock;

    /**
     * @ORM\Column(type="string");
     */
    protected $version;

    /**
     * @ORM\Column(type="boolean");
     */
    protected $installed;

    /**
     * @ORM\Column(type="boolean");
     */
    protected $enabled;

    /**
     * For future can contain json array representing a custom admin form
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $admin;

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
     * Set key
     *
     * @param string $key
     *
     * @return Module
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
     * Set namespace
     *
     * @param string $namespace
     *
     * @return Module
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Get namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Module
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
     * Set lock
     *
     * @param boolean $lock
     *
     * @return Module
     */
    public function setLock($lock)
    {
        $this->lock = $lock;

        return $this;
    }

    /**
     * Get lock
     *
     * @return boolean
     */
    public function getLock()
    {
        return $this->lock;
    }

    /**
     * Set version
     *
     * @param string $version
     *
     * @return Module
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set installed
     *
     * @param boolean $installed
     *
     * @return Module
     */
    public function setInstalled($installed)
    {
        $this->installed = $installed;

        return $this;
    }

    /**
     * Get installed
     *
     * @return boolean
     */
    public function getInstalled()
    {
        return $this->installed;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Module
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set admin
     *
     * @param array $admin
     *
     * @return Module
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return array
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Module
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
     * @return Module
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
