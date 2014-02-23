<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use MediaMine\Entity\File\File;
use Netsyos\Common\Entity\AbstractEntity;

/**
 * Season Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\Video\SeasonRepository")
 * @ORM\Table(name="video_season")
 * @property int $id
 * @property int $number
 * @property string $name
 * @property string $summary
 * @property MediaMine\Entity\Video\Group $group
 * @property MediaMine\Entity\File\File $image
 */
class Season extends AbstractEntity
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $number;

    /**
     * @ORM\Column(type="text")
     */
    protected $name;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $summary;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\Group")
     * @ORM\JoinColumn(name="group_ref", referencedColumnName="id", onDelete="CASCADE", unique=false)
     */
    protected $group;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\Entity\File\File")
     * @ORM\JoinTable(name="video_season_image",
     *      joinColumns={@ORM\JoinColumn(name="season_ref", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_ref", referencedColumnName="id")}
     *      )
     */
    protected $images;

    /**
     * @ORM\OneToMany(targetEntity="MediaMine\Entity\Video\Video", mappedBy="season")
     */
    protected $episodes;

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
    }
}
