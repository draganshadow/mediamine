<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace MediaMine;

use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Console\Adapter\AdapterInterface;
use Zend\Console\Adapter\ConsoleAdapter;

class Module implements ConsoleBannerProviderInterface, ConsoleUsageProviderInterface
{
    const NAME    = 'MediaMine';

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        $config = array();

        $configFiles = array(
            __DIR__ . '/config/module.config.php',
            __DIR__ . '/config/module.config.assets.php', // Assets
            __DIR__ . '/config/module.config.routes.php', // Routes
        );

        // Merge all module config options
        foreach ($configFiles as $configFile) {
            $config = \Zend\Stdlib\ArrayUtils::merge($config, include $configFile);
        }

        return $config;
    }

    public function getServiceConfig()
    {
        return include __DIR__ . '/config/services.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * This method is defined in ConsoleBannerProviderInterface
     *
     * @param AdapterInterface $console
     * @return string|null
     */
    public function getConsoleBanner(AdapterInterface $console){
        return self::NAME;
    }

    public function getConsoleUsage(AdapterInterface $console)
    {
        return array(
            'Scan',
            'scan [path]'  => 'scan folder',
            array('[path]'          , 'Path to a media folder'),
            array('-v --verbose'    , 'Display detailed information.'),
            array('-b --break'      , 'Stop testing on first failure'),
            array('-q --quiet'      , 'Do not display any output unless an error occurs.'),
            array('--debug'         , 'Display raw debug info from tests.'),


        );
    }
}
