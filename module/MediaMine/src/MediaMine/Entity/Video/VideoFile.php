<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use Netsyos\Common\Entity\AbstractEntity;

/**
 * Group Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\Video\VideoFileRepository")
 * @ORM\Table(name="video_video_file")
 * @property MediaMine\Entity\Video\Video $video
 * @property MediaMine\Entity\File\File $file
 */
class VideoFile extends AbstractEntity
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\Video")
     * @ORM\JoinColumn(name="video_ref", referencedColumnName="id", onDelete="CASCADE", unique=false)
     */
    protected $video;

    /**
     * @ORM\OneToOne(targetEntity="MediaMine\Entity\File\File")
     * @ORM\JoinColumn(name="file_ref", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $file;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\Picture")
     * @ORM\JoinColumn(name="picture_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $picture;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\Quality")
     * @ORM\JoinColumn(name="quality_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $quality;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\Stat")
     * @ORM\JoinColumn(name="stat_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $stat;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\Entity\Common\Country")
     * @ORM\JoinTable(name="video_file_language",
     *      joinColumns={@ORM\JoinColumn(name="video_file_ref", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="country_ref", referencedColumnName="id")}
     *      )
     */
    protected $languages;

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function exchangeArray(array $data) {
    }
}
