<?php
namespace MediaMine\CoreBundle\Repository\Video;

use MediaMine\CoreBundle\Repository\AbstractRepository;

class GenreRepository extends AbstractRepository
{
    public function getDiscrimitators() {
        return [
            ['id'],
            ['name']
        ];
    }
}