<?php
namespace MediaMine\Repository;

abstract class EntityRepository extends \Doctrine\ORM\EntityRepository
{
    public function create($fields) {
        $entity = new $this->_entityName;
        $entity->exchangeArray($fields);
        $this->getEntityManager()->persist($entity);
        return $entity;
    }
}