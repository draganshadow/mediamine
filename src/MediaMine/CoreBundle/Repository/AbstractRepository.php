<?php
namespace MediaMine\CoreBundle\Repository;

use Doctrine\ORM\Query;
use MediaMine\CoreBundle\Entity\AbstractEntity;

abstract class AbstractRepository extends \Doctrine\ORM\EntityRepository
{
    const QUERY_MULTIPLE_RESULT = 0;
    const QUERY_SINGLE_RESULT = 1;
    const QUERY_ITERABLE_RESULT = 2;
    /**
     * @var string
     */
    protected $_alias;

    public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $enSplit = explode('\\', $this->getEntityName());
        $this->_alias = $enSplit[count($enSplit) - 1];
    }

    public function getAlias() {
        return $this->_alias;
    }

    public function getField($field) {
        return $this->getAlias() . '.' . $field;
    }

    /**
     * @param $fields
     * @return AbstractEntity
     */
    public function update($id, $values, $fieldsOnly = false) {
        /**
         * @var $entity AbstractEntity
         */
        $entity = $this->find($id);
        if ($fieldsOnly) {
            $values = $this->filterFields($values);
        }
        $entity->exchangeArray($values);
        $this->getEntityManager()->persist($entity);
        return $entity;
    }

    /**
     * @param $fields
     * @return AbstractEntity
     */
    public function create($values) {
        $entity = new $this->_entityName;
        $entity->exchangeArray($values);
        $this->getEntityManager()->persist($entity);
        return $entity;
    }

    public function persist($entity) {
        $this->getEntityManager()->persist($entity);
    }

    public function flush() {
        $this->getEntityManager()->flush();
    }

    public function filterFields($values) {
        $fields = array();
        foreach ($values as $k => $v) {
            if ($this->_class->hasField($k)) {
                $fields[$k] = $v;
            }
        }
        return $fields;
    }

    public function createBaseQueryBuilder() {
        return $this->createQueryBuilder($this->getAlias());
    }

    public function findFullBy($options = array(), $mode = false, $queryOnly = false, $qb = false, $params = array()) {
        if (!$qb) {
            $qb = $this->createBaseQueryBuilder();
        }
        foreach ($options as $k => $o) {
            if (!array_key_exists($k, $params)) {
                if ($this->_class->hasField($k)) {
                    if (is_null($o)) {
                        $qb->andWhere($this->getField($k) . ' is null');
                    } elseif (is_array($o)) {
                        $qb->andWhere($this->getField($k) . ' IN (:' . $k . ')');
                        $params[$k] = $o;
                    } else {
                        $qb->andWhere($this->getField($k) . ' = :' . $k);
                        if (is_object($o)) {
                            $params[$k] = $o->id;
                        } else {
                            $params[$k] = $o;
                        }
                    }
                } elseif ($this->_class->hasAssociation($k)) {
                    if (is_null($o)) {
                        $qb->andWhere($this->getField($k) . ' is null');
                    } else {
                        $joinAlias = 'J' . strtoupper($k);
                        $qb->innerJoin($this->getField($k), $joinAlias, 'WITH', $joinAlias . '.id = :' . $k);
                        if (is_object($o)) {
                            $params[$k] = $o->id;
                        } else {
                            $params[$k] = $o;
                        }
                    }
                }  elseif (strpos($k, 'add') === 0 ) {
                    $f = lcfirst(substr($k, 3));
                    $joinAlias = 'J' . strtoupper($f);
                    $qb->leftJoin($this->getField($f), $joinAlias);
                    $qb->addSelect($joinAlias);
                }
            }
        }

        if (array_key_exists('limit', $options)) {
            $page = array_key_exists('page', $options) ? $options['page'] : 0;
            $qb->setFirstResult($page * $options['limit'])->setMaxResults($options['limit']);
        }

        $order = 'ASC';
        if (array_key_exists('order', $options)) {
            if ($options['order'] == 'DESC') {
                $order = $options['order'];
            }
        }
        $orderBy = 'id';
        if (array_key_exists('orderBy', $options)) {
            $orderBy = $options['orderBy'];
        }
        $qb->orderBy($this->getField($orderBy), $order);
        $hydrate = array_key_exists('hydrate', $options) ? $options['hydrate'] : Query::HYDRATE_OBJECT;
        $qb->setParameters($params);
        $q = $qb->getQuery();
        if (array_key_exists('foreignKeys', $options)) {
            $q->setHint(Query::HINT_INCLUDE_META_COLUMNS, true);
        }
        if ($queryOnly) {
            return $q;
        }
        $result = false;

        //retro compatibility
        if ($mode === true) {
            $mode = self::QUERY_SINGLE_RESULT;
        }
        if ($mode === false) {
            $mode = self::QUERY_MULTIPLE_RESULT;
        }
        switch ($mode) {
            case self::QUERY_MULTIPLE_RESULT :
                $result = $q->getResult($hydrate);
                break;
            case self::QUERY_SINGLE_RESULT :
                $result = $q->getSingleResult($hydrate);
                break;
            case self::QUERY_ITERABLE_RESULT :
                $result = $q->iterate($params, $hydrate);
                break;
        }
        return $result;
    }
}