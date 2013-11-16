<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\EntityRepository;

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