<?php
namespace MediaMine\Repository\Common;

use MediaMine\Entity\Common\Person;
use Netsyos\Common\Repository\EntityRepository;

class PersonRepository extends EntityRepository
{
    public function createPerson($name, $country = null, $birthDate = null, $deathDate = null) {
        $person = new Person();
        $person->name = $name;
        $person->country = $country;
        $person->birthDate = $birthDate;
        $person->deathDate = $deathDate;
        $this->getEntityManager()->persist($person);
        return $person;
    }

    public function findFullBy($name = null) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $params = array();
        $qb->select('Person')
            ->from('MediaMine\Entity\Common\Person','Person');
        if ($name != null) {
            $qb->andwhere('Person.name = :name');
            $params['name'] = $name;
        }
        $persons = $qb->setParameters($params)->getQuery()->getResult();
        return $persons;
    }
}