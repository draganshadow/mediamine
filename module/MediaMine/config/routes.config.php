<?php
/**
 * This configuration should be put in your module `configs` directory.
 */
return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'install' => array(
                    'options' => array(
                        'route'    => 'install [--verbose|-v]',
                        'defaults' => array(
                            'controller' => 'Console',
                            'action'     => 'install'
                        )
                    )
                ),
                'scann' => array(
                    'options' => array(
                        'route'    => 'scan [--verbose|-v] <path>',
                        'defaults' => array(
                            'controller' => 'Console',
                            'action'     => 'scan'
                        )
                    )
                ),
                'searchSeries' => array(
                    'options' => array(
                        'route'    => 'searchSeries [--verbose|-v]',
                        'defaults' => array(
                            'controller' => 'Console',
                            'action'     => 'searchSeries'
                        )
                    )
                ),
                'searchMovies' => array(
                    'options' => array(
                        'route'    => 'searchMovies [--verbose|-v]',
                        'defaults' => array(
                            'controller' => 'Console',
                            'action'     => 'searchMovies'
                        )
                    )
                ),
                'execute' => array(
                    'options' => array(
                        'route'    => 'execute <id>',
                        'defaults' => array(
                            'controller' => 'Console',
                            'action'     => 'execute'
                        )
                    )
                ),
                'cron' => array(
                    'options' => array(
                        'route'    => 'cron',
                        'defaults' => array(
                            'controller' => 'Console',
                            'action'     => 'cron'
                        )
                    )
                )
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'debug' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/debug[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Debug',
                        'action'     => 'index',
                    ),
                ),
            ),

            'images' => array(
                'type' => 'regex',
                'options' => array(
                    'regex'    => '/images/((?<transformations>[a-zA-Z_]+)-)?((?<width>[0-9]+)-(?<height>[0-9]+)-)?(?<pathKey>[a-zA-Z0-9]+)(\.(?<format>(jpg|png|gif)))?',
                    'defaults' => array(
                        'controller' => 'Image',
                        'action'     => 'index',
                        'width'     => 0,
                        'height'     => 0,
                        'format'     => 'jpg',
                    ),
                    'spec' => '/images/%transformations%-%width%-%height%-%pathKey%.%format%',
                ),
            ),

            'stream' => array(
                'type' => 'regex',
                'options' => array(
                    'regex'    => '/stream/((?<bitrate>[0-9]+)-)?((?<width>[0-9]+)-(?<height>[0-9]+)-)?(?<pathKey>[a-zA-Z0-9]+)(\.(?<format>(flv|mp4)))?',
                    'defaults' => array(
                        'controller' => 'Stream',
                        'action'     => 'index',
                        'width'     => 0,
                        'height'     => 0,
                        'format'     => 'flv',
                    ),
                    'spec' => '/stream/%bitrate%-%width%-%height%-%pathKey%.%format%',
                ),
            ),

            // API Documentation
            'api-resources' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/doc',
                    'defaults' => array(
                        'controller' => 'Api',
                        'action'     => 'display'
                    )
                )
            ),

            'api-resource-detail' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/doc/:resource',
                    'defaults' => array(
                        'controller' => 'Api',
                        'action'     => 'details'
                    )
                )
            ),
            // REST API ROUTING
            'api' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/api/:controller[/:id]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Rest',
                        'controller' => 'Video'
                    ),
                ),
            ),

            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'MediaMine\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);