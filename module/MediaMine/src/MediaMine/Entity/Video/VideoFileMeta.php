<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use Netsyos\Common\Entity\AbstractEntity;

/**
 * VideoFileMeta Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\Video\VideoFileMetaRepository")
 * @ORM\Table(name="video_video_file_meta")
 * @property int $id
 * @property string $name
 */
class VideoFileMeta extends AbstractEntity
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="MediaMine\Entity\File\File")
     * @ORM\JoinColumn(name="file_ref", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $file;

    /**
     * @ORM\Column(type="text")
     */
    protected $width;

    /**
     * @ORM\Column(type="text")
     */
    protected $height;

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
