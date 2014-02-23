<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\Mapping as ORM;
use Netsyos\Common\Entity\AbstractEntity;

/**
 * GroupType Entity.
 *
 * @ORM\Entity(repositoryClass="MediaMine\Repository\Video\GroupTypeRepository")
 * @ORM\Table(name="video_group_type")
 * @property int $id
 * @property string $name
 */
class GroupType extends AbstractEntity
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
     * @ORM\Column(type="text")
     */
    protected $summary;

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
