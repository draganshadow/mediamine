<?php
namespace MediaMine\CoreBundle\Repository\Video;

use MediaMine\CoreBundle\Repository\AbstractRepository;
use Doctrine\ORM\Query;

class GroupRepository extends AbstractRepository
{

    public function getAlias() {
        return 'VGroup';
    }

    public function findFullBy($options = array(), $singleResult = false, $queryOnly = false, $qb = false, $params = array()) {
        if (!$qb) {
            $qb = $this->createQueryBuilder($this->getAlias());
        }

        if (array_key_exists('noSeason', $options)) {
            $qb->where('(SELECT COUNT(Season.id) FROM MediaMine\CoreBundle\Entity\Video\Season Season WHERE Season.group = G.id) = 0');
        }

        return parent::findFullBy($options, $singleResult, $queryOnly, $qb, $params);
    }
}