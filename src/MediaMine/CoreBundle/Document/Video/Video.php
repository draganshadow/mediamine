<?php
namespace MediaMine\CoreBundle\Document\Video;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document()
 */
class Video {

    /**
     * @MongoDB\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @MongoDB\String()
     */
    protected $videoRef;

    /**
     * @MongoDB\EmbedMany(targetDocument="\MediaMine\CoreBundle\Document\Tunnel\TunnelVideo", strategy="set")
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
     * Set videoRef
     *
     * @param string $videoRef
     * @return self
     */
    public function setVideoRef($videoRef)
    {
        $this->videoRef = $videoRef;
        return $this;
    }

    /**
     * Get videoRef
     *
     * @return string $videoRef
     */
    public function getVideoRef()
    {
        return $this->videoRef;
    }
}
