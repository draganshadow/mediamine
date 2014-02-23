<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use Netsyos\Common\Entity\AbstractEntity;

/**
 * Picture Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\Video\PictureRepository")
 * @ORM\Table(name="video_picture")
 * @property int $id
 * @property string $name
 */
class Picture extends AbstractEntity
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
