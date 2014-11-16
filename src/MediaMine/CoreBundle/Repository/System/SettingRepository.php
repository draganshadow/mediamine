<?php
namespace MediaMine\CoreBundle\Repository\System;

use Doctrine\ORM\Query;
use MediaMine\CoreBundle\Repository\AbstractRepository;

class SettingRepository extends AbstractRepository
{
    public function findFullBy($options = array(), $singleResult = false, $queryOnly = false, $qb = false, $params = array()) {
        if (!$qb) {
            $qb = $this->createQueryBuilder($this->getAlias());
        }
        if (!array_key_exists('orderBy', $options)) {
            $options['orderBy'] = 'key';
        }
        return parent::findFullBy($options, $singleResult, $queryOnly, $qb, $params);
    }
}