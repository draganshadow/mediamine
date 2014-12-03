<?php
namespace MediaMine\CoreBundle\Repository\Video;

use MediaMine\CoreBundle\Repository\AbstractRepository;
use Doctrine\ORM\Query;

class SeasonRepository extends AbstractRepository
{
    public function getDiscrimitators() {
        return [
            ['id'],
            ['group', 'number']
        ];
    }

    public function findFullBy($options = array(), $singleResult = false, $queryOnly = false, $qb = false, $params = array()) {
        if (!$qb) {
            $qb = $this->createQueryBuilder($this->getAlias());
        }
        if (array_key_exists('noEpisode', $options)) {
            $qb->where('(SELECT COUNT(Video.id) FROM MediaMine\CoreBundle\Entity\Video\Video Video WHERE Video.season = Season.id) = 0');
        }
        return parent::findFullBy($options, $singleResult, $queryOnly, $qb, $params);
    }
}