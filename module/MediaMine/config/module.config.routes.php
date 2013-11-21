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
                'test' => array(
                    'options' => array(
                        'route'    => 'test [--verbose|-v]',
                        'defaults' => array(
                            'controller' => 'Console',
                            'action'     => 'test'
                        )
                    )
                )
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'debug' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/debug',
                    'defaults' => array(
                        'controller' => 'Debug',
                        'action'     => 'index',
                    ),
                ),
            ),
            'serie' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/serie[/:id]',
                    'defaults' => array(
                        'controller' => 'Index',
                        'action'     => 'group',
                    ),
                ),
            ),
            'season' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/season[/:id]',
                    'defaults' => array(
                        'controller' => 'Index',
                        'action'     => 'season',
                    ),
                ),
            ),
            'view' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/view[/:id]',
                    'defaults' => array(
                        'controller' => 'Index',
                        'action'     => 'view',
                    ),
                ),
            ),
            'image' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/image[/:id]',
                    'defaults' => array(
                        'controller' => 'File',
                        'action'     => 'index',
                    ),
                ),
            ),
            'stream' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/stream[/:id].:type',
                    'defaults' => array(
                        'controller' => 'File',
                        'action'     => 'stream',
                    ),
                ),
            ),
            // API Documentation
            'api-resources' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/api/doc',
                    'defaults' => array(
                        'controller' => 'Api',
                        'action'     => 'display'
                    )
                )
            ),

            'api-resource-detail' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/api/doc/:resource',
                    'defaults' => array(
                        'controller' => 'Api',
                        'action'     => 'details'
                    )
                )
            ),
            // REST ROUTING
            'api-series' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/api/series[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Rest\Series'
                    ),
                ),
            ),
            'api-season' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/api/season[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Rest\Season'
                    ),
                ),
            ),
            'api-video' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/api/video[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Rest\Video'
                    ),
                ),
            ),
            'api-directory' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/api/directory[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Rest\Directory'
                    ),
                ),
            ),
            'api-file' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/api/file[/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Rest\File'
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