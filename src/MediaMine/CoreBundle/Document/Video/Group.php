<?php
namespace MediaMine\CoreBundle\Document\Video;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document()
 */
class Group {

    /**
     * @MongoDB\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @MongoDB\String()
     */
    protected $groupRef;

    /**
     * @MongoDB\EmbedMany(targetDocument="\MediaMine\CoreBundle\Document\Tunnel\TunnelGroup", strategy="set")
     */
    protected $tunnels;

    public function __construct()
    {
        $this->tunnels = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return custom_id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add tunnel
     *
     * @param $tunnel
     */
    public function addTunnel($tunnel)
    {
        $this->tunnels[] = $tunnel;
    }

    /**
     * Remove tunnel
     *
     * @param $tunnel
     */
    public function removeTunnel($tunnel)
    {
        $this->tunnels->removeElement($tunnel);
    }

    /**
     * Get tunnels
     *
     * @return Doctrine\Common\Collections\Collection $tunnels
     */
    public function getTunnels()
    {
        return $this->tunnels;
    }

    /**
     * Set groupRef
     *
     * @param string $groupRef
     * @return self
     */
    public function setGroupRef($groupRef)
    {
        $this->groupRef = $groupRef;
        return $this;
    }

    /**
     * Get groupRef
     *
     * @return string $groupRef
     */
    public function getGroupRef()
    {
        return $this->groupRef;
    }
}
