<?php
namespace MediaMine\CoreBundle\Document\Tunnel;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @MongoDB\Document()
 */
class TunnelVideo {

    /**
     * @MongoDB\Id(strategy="UUID")
     */
    protected $id;
    
    /**
     * @MongoDB\String()
     */
    protected $tunnel;

    /**
     * @MongoDB\Hash();
     */
    protected $data;

    /**
     * @Gedmo\Timestampable(on="create")
     * @MongoDB\Date()
     */
    private $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @MongoDB\Date()
     */
    private $updated;

    /**
     * Set tunnel
     *
     * @param string $tunnel
     * @return self
     */
    public function setTunnel($tunnel)
    {
        $this->tunnel = $tunnel;
        return $this;
    }

    /**
     * Get tunnel
     *
     * @return string $tunnel
     */
    public function getTunnel()
    {
        return $this->tunnel;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return self
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get data
     *
     * @return hash $data
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * Set created
     *
     * @param date $created
     * @return self
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get created
     *
     * @return date $created
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param date $updated
     * @return self
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * Get updated
     *
     * @return date $updated
     */
    public function getUpdated()
    {
        return $this->updated;
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
}
