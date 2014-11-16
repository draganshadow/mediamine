<?php
namespace MediaMine\CoreBundle\Repository\Tunnel;

use Doctrine\ORM\Query;
use MediaMine\CoreBundle\Repository\AbstractRepository;

class GroupRepository extends AbstractRepository
{

    public function getAlias() {
        return 'TVGroup';
    }

}