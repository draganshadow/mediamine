<?php
namespace MediaMine\Entity\System;

use Doctrine\ORM\Mapping as ORM;
use Netsyos\Common\Entity\AbstractEntity;

/**
 * Parameter Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\System\Parameter")
 * @ORM\Table(name="system_option",uniqueConstraints={@ORM\UniqueConstraint(name="unique_group_key", columns={"group", "key"})})
 * @property int $id
 * @property string $key
 * @property string $reference
 */
class Option extends AbstractEntity
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $group;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $key;

    /**
     * @ORM\Column(type="text");
     */
    protected $value;

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function exchangeArray(array $data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
    }
}
