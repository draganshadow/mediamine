<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use MediaMine\Entity\File\File;
use Netsyos\Common\Entity\AbstractEntity;

/**
 * Group Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\Video\GroupRepository")
 * @ORM\Table(name="video_group")
 * @property int $id
 * @property string $name
 * @property string $summary
 * @property MediaMine\Entity\File\File $image
 */
class Group extends AbstractEntity
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
     * @ORM\Column(type="text", nullable=true)
     */
    protected $summary;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\GroupType")
     * @ORM\JoinColumn(name="group_type_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $type;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\Entity\File\File")
     * @ORM\JoinTable(name="video_group_image",
     *      joinColumns={@ORM\JoinColumn(name="group_ref", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_ref", referencedColumnName="id")}
     *      )
     */
    protected $images;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\Genre")
     * @ORM\JoinColumn(name="genre_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $genre;

    /**
     * Add categories
     *
     * @param File $image
     */
    public function addImage(File $image)
    {
        $this->images[] = $image;
    }

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
