<?php
namespace MediaMine\CoreBundle\Repository\Video;

use MediaMine\CoreBundle\Repository\AbstractRepository;
use JMS\DiExtraBundle\Annotation as DI;

class CharacterRepository extends AbstractRepository
{
    public function getDiscrimitators() {
        return [
            ['id'],
            ['video', 'name']
        ];
    }
}