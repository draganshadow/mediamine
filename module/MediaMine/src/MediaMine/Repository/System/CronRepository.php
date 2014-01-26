<?php
namespace MediaMine\Repository\System;
use MediaMine\Repository\EntityRepository;
use MediaMine\Entity\System\Cron;

class CronRepository extends EntityRepository
{
    public function createCron($fields) {
        $cron = new Cron();
        $cron->exchangeArray($fields);
        $this->getEntityManager()->persist($cron);
        return $cron;
    }
}