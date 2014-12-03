<?php
namespace MediaMine\CoreBundle\Repository\Video;

use MediaMine\CoreBundle\Repository\AbstractRepository;

class StaffRepository extends AbstractRepository
{
    public function getDiscrimitators() {
        return [
            ['id'],
            ['video', 'person', 'character', 'role']
        ];
    }
}