<?php
namespace MediaMine\Repository\System;
use MediaMine\Repository\EntityRepository;

class ExecutionRepository extends EntityRepository
{
    public function create($fields) {
        $fields['createTime'] = new \DateTime();
        return parent::create($fields);
    }
}