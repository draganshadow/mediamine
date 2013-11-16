<?php
/**
 * This configuration should be put in your module `configs` directory.
 */
return array(
    'assetic_configuration' => array(
        // Use on production environment
        // 'debug'              => false,
        // 'buildOnRequest'     => false,

        // Use on development environment
        'debug' => true,
        'buildOnRequest' => true,

        // This is optional flag, by default set to `true`.
        // In debug mode allow you to combine all assets to one file.
        // 'combine' => false,

        // this is specific to this project
        'webPath' => 'public/assets', //realpath('public/assets'),
        'basePath' => 'assets',
        'cachePath' => 'data/cache',

        'controllers' => array(
            'MediaMine\Controller\Index' => array(
                '@head_css',
                '@head_js',
            ),
        ),

        'modules' => array(
            'MediaMine' => array(
                'root_path' => __DIR__ . '/../assets',

                'collections' => array(
                    'head_css' => array(
                        'assets' => array(
                            'bower_components/bootstrap/dist/css/bootstrap.min.css',
                            'css/style.css',
                        ),
                        'filters' => array(
                            '?CssRewriteFilter' => array(
                                'name' => 'Assetic\Filter\CssRewriteFilter'
                            ),
                            '?CssMinFilter' => array(
                                'name' => 'Assetic\Filter\CssMinFilter'
                            ),
                        ),
                    ),
                    'head_js' => array(
                        'assets' => array(
                            'bower_components/jquery/jquery.min.js',
                            'bower_components/bootstrap/dist/js/bootstrap.min.js',
                        ),
                        'filters' => array(
                            '?JSMinFilter' => array(
                                'name' => 'Assetic\Filter\JSMinFilter'
                            ),
                        ),
                    ),

                    'base_images' => array(
                        'assets' => array(
                            'images/*.png',
                            'images/*.ico',
                        ),
                        'options' => array(
                            'move_raw' => true,
                        )
                    ),
                ),
            ),
        ),
    ),
);