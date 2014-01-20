<?php
return array(
    'abstract_factories' => array(
        'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
        'Zend\Log\LoggerAbstractServiceFactory',
        'MediaMine\Factory\ServiceAbstractFactory',
    ),
    'invokables' => array(
        'imagine-service' => 'Imagine\Gd\Imagine'
    ),
    'aliases' => array(
        'translator' => 'MvcTranslator',
    ),
    'factories' => array(
        'elasticsearch' => function ($sm) {
            $config = $sm->get('config');
            $srv = new \Elastica\Client(
                array(
                    'host' => $config['elasticsearch']['connection']['params']['host'],
                    'port' => $config['elasticsearch']['connection']['params']['port']
                )
            );
            return $srv;
        },
        'doctrine.cache.my_memcache' => function ($sm) {
            $cache = new \Doctrine\Common\Cache\MemcacheCache();
            $memcache = new \Memcache();
            $config = $sm->get('config');
            $memcache->connect(
                $config['memcache']['host'],
                $config['memcache']['port']
            );
            $cache->setMemcache($memcache);
            return $cache;
        }
    )
);
