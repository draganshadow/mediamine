<?php
return array(
    'abstract_factories' => array(
        'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
        'Zend\Log\LoggerAbstractServiceFactory',
        'MediaMine\Factory\ServiceAbstractFactory',
        'MediaMine\Factory\TunnelAbstractFactory',
    ),
    'invokables' => array(
        'imagine-service' => 'Imagine\Gd\Imagine'
    ),
    'aliases' => array(
        'translator' => 'MvcTranslator',
    ),
    'initializers' => array(
        'setEntityManager' => function ($instance, \Zend\ServiceManager\ServiceManager $sm) {
                if ($instance instanceof \Netsyos\Common\Initializer\EntityManagerAwareInterface) {
                    $instance->setBaseNameSpace();
                    $instance->setEntityManager(
                        $sm->get('doctrine.entitymanager.orm_default')
                    );
                }
            },
        'setServiceLocator' => function ($instance, \Zend\ServiceManager\ServiceManager $sm) {
                if ($instance instanceof \Zend\ServiceManager\ServiceLocatorAwareInterface) {
                    $instance->setServiceLocator($sm);
                }
            },
        'setLogger' => function ($instance, \Zend\ServiceManager\ServiceManager $sm) {
                if ($instance instanceof \MediaMine\Tunnel\AbstractTunnel) {
                    $logger = $sm->get('mediamine-tunnel-log');
                    $instance->setLogger($logger);
                } elseif ($instance instanceof \Netsyos\Common\Initializer\LoggerAwareInterface) {
                    $logger = $sm->get('mediamine-log');
                    $instance->setLogger($logger);
                }
            },
        'setElasticsearch' => function ($instance, $sm) {
                if ($instance instanceof \MediaMine\Initializer\ElasticsearchAwareInterface) {
                    $instance->setElasticsearch($sm->get('elasticsearch'));
                }
            },
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
        },
        'mediamine-tunnel-log' => function ($sm) {
                $filename = 'tunnel.log';
                $log = new \Zend\Log\Logger();
                $writer = new \Zend\Log\Writer\Stream('./data/log/' . $filename);
                $log->addWriter($writer);
                return $log;
            },
        'mediamine-cron-log' => function ($sm) {
                $filename = 'cron.log';
                $log = new \Zend\Log\Logger();
                $writer = new \Zend\Log\Writer\Stream('./data/log/' . $filename);
                $log->addWriter($writer);
                return $log;
            },
        'mediamine-cron' =>  function($sm) {
                $logger = $sm->get('mediamine-cron-log');
                $service = new MediaMine\Service\CronService();
                $service->setLogger($logger);
                return $service;
            },
        'mediamine-log' => function ($sm) {
                $filename = 'mediamine.log';
                $log = new \Zend\Log\Logger();
                $writer = new \Zend\Log\Writer\Stream('./data/log/' . $filename);
                $log->addWriter($writer);
                return $log;
            },
        'mediamine-error-handling' =>  function($sm) {
            $service = new MediaMine\Service\ErrorHandlingService();
            return $service;
        },
    ),
);
