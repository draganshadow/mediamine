<?php
namespace MediaMine\CoreBundle\Repository\Tunnel;

use Doctrine\ORM\Query;
use MediaMine\CoreBundle\Repository\AbstractRepository;
use JMS\DiExtraBundle\Annotation as DI;

class GroupRepository extends AbstractRepository
{

    public function getAlias() {
        return 'TVGroup';
    }

}