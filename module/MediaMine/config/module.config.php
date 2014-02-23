<?php

namespace MediaMine;
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Index' => 'MediaMine\Controller\IndexController',
        ),
        'initializers' => array(
            'setEntityManager' => function ($instance, $serviceLocator) {
                    if ($instance instanceof \Netsyos\Common\Initializer\EntityManagerAwareInterface) {
                        $instance->setBaseNameSpace();
                        $instance->setEntityManager(
                            $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default')
                        );
                    }
                },
            'setElasticsearch' => function ($instance, $serviceLocator) {
                    if ($instance instanceof \MediaMine\Initializer\ElasticsearchAwareInterface) {
                        $instance->setElasticsearch($serviceLocator->getServiceLocator()->get('elasticsearch'));
                    }
                },
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    'port'     => '3306',
                    'charset'  => 'UTF8'
                )
            )
        ),
        'eventmanager' => array(
            'orm_default' => array(
                'subscribers' => array(
                    // pick any listeners you need
                    'Gedmo\Timestampable\TimestampableListener',
                )
            )
        ),
        'driver' => array(
            __NAMESPACE__ .'_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        ),
        'migrations' => array(
            'migrations_table' => 'migrations',
            'migrations_namespace' => 'Application',
            'migrations_directory' => 'data/migrations',
        ),
    ),
    'data-fixture' => array(
        'fixtures' => __DIR__ . '/../src/Application/Fixture',
    ),
);
