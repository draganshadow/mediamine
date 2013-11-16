<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\EntityRepository;

class SeasonRepository extends EntityRepository
{
    public function createSeason($group, $number, $name, $summary, $image) {
        $season = new Season();
        $season->number = $number;
        $season->name = $name;
        $season->summary = $summary;
        if ($image) {
            $season->addImage($image);
        }
        $season->group = $group;
        $this->getEntityManager()->persist($season);
        return $season;
    }
}