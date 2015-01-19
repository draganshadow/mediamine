<?php
namespace MediaMine\CoreBundle\Entity\System;

use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\AbstractEntity;

/**
 * Tunnel Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\System\TunnelRepository")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\Table(name="system_tunnel")
 * @property int $id
 * @property string $key
 * @property string $reference
 */
class Tunnel extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\CoreBundle\Entity\System\Module", fetch="EAGER")
     * @ORM\JoinColumn(name="module_ref", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $module;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $key;


    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $service;

    /**
     * @ORM\Column(type="boolean");
     */
    protected $enabled;

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
     * @return Tunnel
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
     * Set service
     *
     * @param string $service
     *
     * @return Tunnel
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Tunnel
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
     * Set module
     *
     * @param \MediaMine\CoreBundle\Entity\System\Module $module
     *
     * @return Tunnel
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
