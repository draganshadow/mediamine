<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use Netsyos\Common\Entity\AbstractEntity;

/**
 * Rating Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\Video\RatingRepository")
 * @ORM\Table(name="video_rating")
 * @property int $id
 * @property int $localRate
 */
class Rating extends AbstractEntity
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
    protected $localRate;

    /**
     * @ORM\Column(type="integer")
     */
    protected $localNbVote;

    /**
     * @ORM\Column(type="integer")
     */
    protected $webRate;

    /**
     * @ORM\Column(type="integer")
     */
    protected $webNbVote;

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
