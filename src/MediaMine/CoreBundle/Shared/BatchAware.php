<?php
namespace MediaMine\CoreBundle\Shared;

trait BatchAware {

    protected $insert = 0;

    /**
     * @param int $nb
     */
    protected function batch($nb = 1000) {
        $this->insert++;
        if ($this->insert % $nb == 0) {
            $this->getEntityManager()->flush();
        }
    }
} 