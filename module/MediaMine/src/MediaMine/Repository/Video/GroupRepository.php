<?php
namespace MediaMine\Repository\Video;

use MediaMine\Repository\EntityRepository;
use MediaMine\Entity\Video\Group;

class GroupRepository extends EntityRepository
{
    public function createGroup($name, $summary, $image) {
        $group = new Group();
        $group->name = $name;
        $group->summary = $summary;
        if ($image) {
            $group->addImage($image);
        }
        $this->getEntityManager()->persist($group);
        return $group;
    }

    public function findFullBy($name = null) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $params = array();
        $qb->select('G')
            ->from('MediaMine\Entity\Video\Group','G');
        if ($name != null) {
            $qb->where('G.name = :name');
            $params['name'] = $name;
        }

        $videos = $qb->setParameters($params)->getQuery()->getResult();
        return $videos;
    }
}