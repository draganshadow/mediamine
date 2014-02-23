<?php
namespace MediaMine\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Netsyos\Common\Entity\AbstractEntity;

/**
 * Group Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\Common\PersonRepository")
 * @ORM\Table(name="common_person")
 * @property int $id
 * @property string $name
 * @property string $summary
 */
class Person extends AbstractEntity
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
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Common\Country")
     * @ORM\JoinColumn(name="country_ref", referencedColumnName="id", onDelete="SET NULL", unique=false, nullable=true)
     */
    protected $country;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $birthDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deathDate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $summary;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\Entity\File\File")
     * @ORM\JoinTable(name="common_person_image",
     *      joinColumns={@ORM\JoinColumn(name="person_ref", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_ref", referencedColumnName="id")}
     *      )
     */
    protected $images;

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function exchangeArray(array $data) {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->summary = (isset($data['summary'])) ? $data['summary'] : null;
    }
}
