<?php
namespace MediaMine\Service;

use Doctrine\ORM\Query;

class AbstractService extends \Netsyos\Common\Service\AbstractService
{
    protected $insert = 0;
    protected $batchSize = 20000;

    /**
     * @param bool $force
     */
    protected function flush($force = false) {
        //TODO move that function to a better place

        $this->insert++;
        if ($this->insert % $this->batchSize == 0 || $force) {
            $this->getEntityManager()->flush();
        }
    }
}
