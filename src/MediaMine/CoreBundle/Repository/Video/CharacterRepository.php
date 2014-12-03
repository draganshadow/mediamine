<?php
namespace MediaMine\CoreBundle\Repository\Video;

use MediaMine\CoreBundle\Repository\AbstractRepository;

class CharacterRepository extends AbstractRepository
{
    public function getDiscrimitators() {
        return [
            ['id'],
            ['video', 'name']
        ];
    }
}