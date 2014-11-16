<?php
namespace MediaMine\CoreBundle\Entity\System;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use MediaMine\CoreBundle\Entity\AbstractEntity;

/**
 * Job Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\CoreBundle\Repository\System\JobRepository")
 * @ORM\Table(name="system_job")
 */
class Job extends AbstractEntity
{
    const STATUS_NEW = 'new';
    const STATUS_RUNNING = 'running';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DONE = 'done';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $groupKey;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $key;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $type;

    /**
     * @ORM\Column(type="string")
     */
    protected $status = self::STATUS_NEW;

    /**
     * @ORM\Column(type="string")
     */
    protected $service;

    /**
     * @ORM\Column(type="string")
     */
    protected $method;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $params;

    /**
     * @ORM\Column(type="integer");
     */
    protected $nbTasks;

    /**
     * @ORM\Column(type="integer");
     */
    protected $nbTasksDone;

    /**
     * @ORM\ManyToOne(targetEntity="Job")
     * @ORM\JoinColumn(name="parentjob_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parentJob;

    /**
     * @ORM\ManyToOne(targetEntity="Job")
     * @ORM\JoinColumn(name="previousjob_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $previousJob;

    /**
     * @ORM\ManyToOne(targetEntity="Job")
     * @ORM\JoinColumn(name="nextjob_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $nextJob;

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
     * @return Job
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
     * @return Job
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
     * Set type
     *
     * @param string $type
     *
     * @return Job
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Job
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set service
     *
     * @param string $service
     *
     * @return Job
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
     * Set method
     *
     * @param string $method
     *
     * @return Job
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set params
     *
     * @param array $params
     *
     * @return Job
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set nbTasks
     *
     * @param integer $nbTasks
     *
     * @return Job
     */
    public function setNbTasks($nbTasks)
    {
        $this->nbTasks = $nbTasks;

        return $this;
    }

    /**
     * Get nbTasks
     *
     * @return integer
     */
    public function getNbTasks()
    {
        return $this->nbTasks;
    }

    /**
     * Set nbTasksDone
     *
     * @param integer $nbTasksDone
     *
     * @return Job
     */
    public function setNbTasksDone($nbTasksDone)
    {
        $this->nbTasksDone = $nbTasksDone;

        return $this;
    }

    /**
     * Get nbTasksDone
     *
     * @return integer
     */
    public function getNbTasksDone()
    {
        return $this->nbTasksDone;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Job
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
     * @return Job
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
     * Set parentJob
     *
     * @param \MediaMine\CoreBundle\Entity\System\Job $parentJob
     *
     * @return Job
     */
    public function setParentJob(\MediaMine\CoreBundle\Entity\System\Job $parentJob = null)
    {
        $this->parentJob = $parentJob;

        return $this;
    }

    /**
     * Get parentJob
     *
     * @return \MediaMine\CoreBundle\Entity\System\Job
     */
    public function getParentJob()
    {
        return $this->parentJob;
    }

    /**
     * Set previousJob
     *
     * @param \MediaMine\CoreBundle\Entity\System\Job $previousJob
     *
     * @return Job
     */
    public function setPreviousJob(\MediaMine\CoreBundle\Entity\System\Job $previousJob = null)
    {
        $this->previousJob = $previousJob;

        return $this;
    }

    /**
     * Get previousJob
     *
     * @return \MediaMine\CoreBundle\Entity\System\Job
     */
    public function getPreviousJob()
    {
        return $this->previousJob;
    }

    /**
     * Set nextJob
     *
     * @param \MediaMine\CoreBundle\Entity\System\Job $nextJob
     *
     * @return Job
     */
    public function setNextJob(\MediaMine\CoreBundle\Entity\System\Job $nextJob = null)
    {
        $this->nextJob = $nextJob;

        return $this;
    }

    /**
     * Get nextJob
     *
     * @return \MediaMine\CoreBundle\Entity\System\Job
     */
    public function getNextJob()
    {
        return $this->nextJob;
    }
}
