<?php
namespace MediaMine\CoreBundle\Repository\Video;

use MediaMine\CoreBundle\Repository\AbstractRepository;
use JMS\DiExtraBundle\Annotation as DI;

class GenreRepository extends AbstractRepository
{
    public function getDiscrimitators() {
        return [
            ['id'],
            ['name']
        ];
    }
}