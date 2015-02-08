<?php
namespace MediaMine\CoreBundle\Repository;

use Doctrine\Common\Proxy\Proxy;
use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation as DI;
use MediaMine\CoreBundle\Entity\AbstractEntity;

abstract class AbstractRepository extends \Doctrine\ORM\EntityRepository
{
    const GLOBAL_CACHE_PREFIX = 'mediamine.';
    const QUERY_MULTIPLE_RESULT = 0;
    const QUERY_SINGLE_RESULT = 1;
    const QUERY_ITERABLE_RESULT = 2;
    const DEFAULT_CACHE_TIME = 86400;

    /**
     * @DI\Inject("snc_redis.default", required=true)
     * @var \Redis
     */
    public $redis;

    /**
     * @DI\InjectParams({
     *     "redis" = @DI\Inject("snc_redis.default"),
     * })
     */
    public function setRedis($redis)
    {
        $this->redis = $redis;
    }


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

    public function getBaseCacheKey($context = false) {
        return self::GLOBAL_CACHE_PREFIX . ($context ? $context . '.' : '') . $this->getAlias() . '.';
    }

    public function getDiscriminatorValue($discriminator, $values) {
        $disValues = array_intersect_key($values, array_flip($discriminator));
        foreach ($disValues as $k => $dv) {
            if ($dv instanceof AbstractEntity || $dv instanceof Proxy) {
                $disValues[$k] = (int) $dv->getId();
            } elseif (is_array($dv)) {
                if (array_key_exists('id', $dv)) {
                    $disValues[$k] = (int) $dv['id'];
                } else {
                    unset($disValues[$k]);
                }
            }
        }
        return $disValues;
    }

    public function getCacheKey($discriminator, $context = false) {
        ksort($discriminator);
        return $this->getBaseCacheKey($context) . md5(serialize($discriminator));
    }

    public function clearCache($discriminator = false, $context = false) {
        if ($discriminator) {
            $this->redis->del($this->getCacheKey($discriminator, $context));
        } else {
            $this->redis->eval("return redis.call('del', unpack(redis.call('keys', ARGV[1])))", [$this->getBaseCacheKey($context) . '*']);
        }
    }

    public function getDiscrimitators() {
        return [
            ['id']
        ];
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
        $entity = $this->exchangeArray($values, $entity);
        $this->getEntityManager()->persist($entity);
        return $entity;
    }

    /**
     * @param $entity
     */
    public function remove($entity) {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * @param $fields
     * @return AbstractEntity
     */
    public function create($values, $cache = false, $context = false, $discriminator = false) {
        /**
         * @var $entity AbstractEntity
         */
        $entity = $this->exchangeArray($values);
        foreach($values as $key => $value)
        {
            if ($this->_class->hasAssociation($key) && is_scalar($value)) {
                $entity->{$key} = $this->getEntityManager()->getReference($this->_class->associationMappings[$key]['targetEntity'], $value);
            } else {
                $entity->{$key} = $value;
            }
        }
        $this->getEntityManager()->persist($entity);
        if ($cache) {
            $this->getEntityManager()->flush();
            $arrayCopy = $entity->getArrayCopy();
            if ($discriminator) {
                $disValues = $this->getDiscriminatorValue($discriminator, $arrayCopy);
                $key = $this->getCacheKey($disValues, $context);
                $this->redis->set($key, serialize($arrayCopy), self::DEFAULT_CACHE_TIME);
            } else {
                foreach ($this->getDiscrimitators() as $discriminator) {
                    $disValues = $this->getDiscriminatorValue($discriminator, $arrayCopy);
                    $this->redis->set($this->getCacheKey($disValues, $context), serialize($arrayCopy), self::DEFAULT_CACHE_TIME);
                }
            }
        }
        return $entity;
    }

    public function getCachedOrCreate($values, $discriminator = false, $context = false, &$cached = false) {
        if (!$discriminator) {
            $discriminator = $this->getDiscrimitators()[0];
        }
        $disValues = $this->getDiscriminatorValue($discriminator, $values);
        $key = $this->getCacheKey($disValues, $context);

        $cachedVal = $this->redis->get($key);

        /**
         * @var $entity AbstractEntity
         */
        if ($cachedVal) {
            $cachedVal = unserialize($cachedVal);
            $entity = $this->getEntityManager()->getReference($this->_entityName, $cachedVal['id']);
            $entity = $this->exchangeArray($cachedVal, $entity);
            $cached = true;
        } else {
            $entity = $this->create($values, true, $context, $discriminator);
        }
        return $entity;
    }

    public function getOrCreate($values, $discriminator = false, $context = false, &$found = false) {
        if (!$discriminator) {
            $discriminator = $this->getDiscrimitators()[0];
        }
        $disValues = $this->getDiscriminatorValue($discriminator, $values);
        $key = $this->getCacheKey($disValues, $context);
        $results = $this->findFullBy($disValues);
        $entity = false;
        if (count($results) > 0) {
            $entity = $results[0];
        }

        /**
         * @var $entity AbstractEntity
         */
        if ($entity instanceof AbstractEntity) {
            $arrayCopy = $entity->getArrayCopy();
            $this->redis->set($key, serialize($arrayCopy), self::DEFAULT_CACHE_TIME);
            $found = true;
        } else {
            $entity = $this->create($values, true, $context, $discriminator);
        }
        return $entity;
    }

    public function getCached($values, $discriminator = false, $context = false) {
        if (!$discriminator) {
            $discriminator = $this->getDiscrimitators()[0];
        }
        $disValues = $this->getDiscriminatorValue($discriminator, $values);
        $key = $this->getCacheKey($disValues, $context);
        $cachedVal = $this->redis->get($key);

        /**
         * @var $entity AbstractEntity
         */
        if ($cachedVal) {
            $cachedVal = unserialize($cachedVal);
            $entity = $this->getEntityManager()->getReference($this->_entityName, $cachedVal['id']);
            $entity = $this->exchangeArray($cachedVal, $entity);
        } else {
            $entity = false;
        }
        return $entity;
    }

    public function getCachedOrFindFullBy($options = array(), $mode = false, $queryOnly = false, $qb = false, $params = array(), $context = false) {
        $options['hydrate'] = Query::HYDRATE_ARRAY;
        $cached = $this->redis->get($this->getCacheKey($options, $context));
        if ($cached) {
            $result = unserialize($cached);
        } else {
            $result = $this->findFullBy($options, $mode, $queryOnly, $qb, $params);
            $this->redis->set($this->getCacheKey($options, $context), serialize($result), self::DEFAULT_CACHE_TIME);
        }
        return $result;
    }

    public function cacheAll(array $entities, $discriminator = false, $context = false) {
        if (!$discriminator) {
            $discriminator = $this->getDiscrimitators()[0];
        }
        foreach ($entities as $entity) {
            $disValues = $this->getDiscriminatorValue($discriminator, $entity);
            $this->redis->set($this->getCacheKey($disValues, $context), serialize($entity), self::DEFAULT_CACHE_TIME);
        }
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

    public function enumerateValues($field = 'id', $hydrate = Query::HYDRATE_ARRAY) {
        $qb = $this->createBaseQueryBuilder();
        $qb->select($this->getField($field));
        $qb->where($this->getField($field) . ' IS NOT NULL');
        $qb->groupBy($this->getField($field));
        $qb->orderBy($this->getField($field), 'ASC');
        $q = $qb->getQuery();
        $result = $q->getResult($hydrate);
        return array_filter($result, function ($v) {
            return count($v) > 0;
        });
    }

    public function findFullBy($options = array(), $mode = false, $queryOnly = false, $qb = false, $params = array()) {
        if (!$qb) {
            $qb = $this->createBaseQueryBuilder();
        }
        $count = false;
        if (array_key_exists('count', $options)) {
            $count = true;
            $qb->select('COUNT(' . $this->getAlias() . '.id' . ')');
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
                    if (!$count) {
                        $qb->addSelect($joinAlias);
                    }
                }
            }
        }

        if (!$count) {
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
        }
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
        if ($count) {
            $result = $q->getSingleScalarResult($hydrate);
        } else {
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
        }
        return $result;
    }

    public function exchangeArray($array, $entity = null, $withAssociation = false)
    {
        if (!$entity) {
            $entity = new $this->_entityName;
        }
        foreach($array as $key => $value)
        {
            if ($this->_class->hasField($key) || $withAssociation) {
                $entity->{$key} = $value;
            }
        }
        return $entity;
    }

    public function exchangeArrayComplete($array, $entity = null, $withAssociation = false)
    {
        if (!$entity) {
            $entity = new $this->_entityName;
        }
        foreach($array as $key => $value)
        {
            if (($this->_class->hasField($key) || $withAssociation) && empty($entity->{$key})) {
                $entity->{$key} = $value;
            }
        }
        return $entity;
    }

    public function exchangeArrayNoEmpty($array, $entity = null, $withAssociation = false)
    {
        if (!$entity) {
            $entity = new $this->_entityName;
        }
        foreach($array as $key => $value)
        {
            if (($this->_class->hasField($key) || $withAssociation) && !empty($value)) {
                $entity->{$key} = $value;
            }
        }
        return $entity;
    }
}