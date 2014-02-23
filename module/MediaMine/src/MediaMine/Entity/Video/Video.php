<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use MediaMine\Entity\File\File;
use Netsyos\Common\Entity\AbstractEntity;

/**
 * Group Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\Video\VideoRepository")
 * @ORM\Table(name="video_video")
 * @property int $id
 * @property string $name
 * @property string $summary
 * @property int $episode
 * @property MediaMine\Entity\File\File $image
 * @property MediaMine\Entity\Video\Season $season
 */
class Video extends AbstractEntity
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
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $originalName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $summary;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $year;

    /**
     * @ORM\Column(name="episode", type="integer", nullable=true)
     */
    protected $episode;


    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\Group")
     * @ORM\JoinColumn(name="group_ref", referencedColumnName="id", onDelete="CASCADE", unique=false)
     */
    protected $group;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\Season")
     * @ORM\JoinColumn(name="season_ref", referencedColumnName="id", onDelete="CASCADE", unique=false, nullable=true)
     */
    protected $season;

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\Entity\File\File")
     * @ORM\JoinTable(name="video_video_image",
     *      joinColumns={@ORM\JoinColumn(name="video_ref", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_ref", referencedColumnName="id")}
     *      )
     */
    protected $images;

    /**
     * @ORM\OneToMany(targetEntity="MediaMine\Entity\Video\VideoFile", mappedBy="video")
     */
    protected $files;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Common\Country")
     * @ORM\JoinColumn(name="country_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $country;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\Rating")
     * @ORM\JoinColumn(name="rating_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $rating;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\Review")
     * @ORM\JoinColumn(name="review_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $review;

    /**
     * @ORM\ManyToOne(targetEntity="MediaMine\Entity\Video\Type")
     * @ORM\JoinColumn(name="type_ref", referencedColumnName="id", onDelete="SET NULL", unique=false)
     */
    protected $type;

    /**
     * @ORM\OneToMany(targetEntity="MediaMine\Entity\Video\Genre", mappedBy="video")
     */

    /**
     * @ORM\ManyToMany(targetEntity="MediaMine\Entity\Video\Genre")
     * @ORM\JoinTable(name="video_video_genre",
     *      joinColumns={@ORM\JoinColumn(name="video_ref", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="genre_ref", referencedColumnName="id")}
     *      )
     */
    protected $genres;

    /**
     * @ORM\OneToMany(targetEntity="MediaMine\Entity\Video\Staff", mappedBy="video")
     */
    protected $staffs;

    /**
     * Add Image
     *
     * @param File $image
     */
    public function addImage(File $image)
    {
        $this->images[] = $image;
    }

    /**
     * Add categories
     *
     * @param Genre $genre
     */
    public function addGenre(Genre $genre)
    {
        $this->genres[] = $genre;
    }

    /**
     * Add categories
     *
     * @param array $genres
     */
    public function addAllGenre(array $genres)
    {
        foreach($genres as $genre) {
            $this->genres[] = $genre;
        }
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
