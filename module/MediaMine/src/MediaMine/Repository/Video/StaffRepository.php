<?php
namespace MediaMine\Repository\Video;

use MediaMine\Repository\EntityRepository;
use MediaMine\Entity\Video\Staff;

class StaffRepository extends EntityRepository
{
    public function createStaff($video, $person, $character = null, $role = null) {
        $staff = new Staff();
        $staff->video = $video;
        $staff->person = $person;
        $staff->character = $character;
        $staff->role = $role;
        $this->getEntityManager()->persist($staff);
        return $staff;
    }
}