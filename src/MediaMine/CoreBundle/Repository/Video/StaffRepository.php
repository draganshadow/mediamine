<?php
namespace MediaMine\CoreBundle\Repository\Video;

use MediaMine\CoreBundle\Repository\AbstractRepository;
use JMS\DiExtraBundle\Annotation as DI;

class StaffRepository extends AbstractRepository
{
    public function getDiscrimitators() {
        return [
            ['id'],
            ['video', 'person', 'character', 'role']
        ];
    }
}