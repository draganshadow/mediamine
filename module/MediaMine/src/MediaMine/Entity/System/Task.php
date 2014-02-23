<?php
namespace MediaMine\Entity\System;

use Doctrine\ORM\Mapping as ORM;
use Netsyos\Common\Entity\AbstractEntity;

/**
 * Task Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\System\Task")
 * @ORM\Table(name="system_task")
 * @property int $id
 * @property string $key
 * @property string $reference
 */
class Task extends AbstractEntity
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     */
    protected $key;

    /**
     * @ORM\Column(type="integer");
     */
    protected $reference;

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function exchangeArray(array $data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
    }
}
