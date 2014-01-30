<?php
namespace MediaMine\Controller;

use MediaMine\Controller\AbstractController;
use MediaMine\Initializer\EntityManagerAware;
use MediaMine\Parser\SerieParser;
use Zend\Mvc\Controller\AbstractActionController;
use MediaMine\Service\ScanService;

class ConsoleController extends AbstractController implements EntityManagerAware
{

    /**
     * @var \MediaMine\Service\ScanService
     */
    protected $scanService;

    /**
     * @var \MediaMine\Service\InstallService
     */
    protected $installService;

    /**
     * @var \MediaMine\Service\TunnelService
     */
    protected $tunnelService;

    public function installAction()
    {
        echo 'Install DB...';
        $request = $this->getRequest();
        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (get_class($request) != 'Zend\Console\Request'){
            throw new \RuntimeException('You can only use this action from a console!');
        }
        $this->getInstallService()->install();
        return "Done!\n";
    }

    public function scanAction()
    {
        $request = $this->getRequest();
        $path = $request->getParam('path', false);
        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (get_class($request) != 'Zend\Console\Request'){
            throw new \RuntimeException('You can only use this action from a console!');
        }
        if (!$path) {
            throw new \RuntimeException('You must specify a path to add');
        }
        echo 'Add files from: ' . realpath($path), PHP_EOL;
        $this->getScanService()->scan($path);
        return "Done!\n";
    }

    public function searchSeriesAction()
    {
        echo 'Search for series', PHP_EOL;
            $request = $this->getRequest();
        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (get_class($request) != 'Zend\Console\Request'){
            throw new \RuntimeException('You can only use this action from a console!');
        }
        $this->getTunnelService()->getXmlTunnel()->searchSeries();
        return "Done!\n";
    }

    public function searchMoviesAction()
    {
        echo 'Search for movies', PHP_EOL;
        $request = $this->getRequest();
        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (get_class($request) != 'Zend\Console\Request'){
            throw new \RuntimeException('You can only use this action from a console!');
        }
        $this->getTunnelService()->getXmlTunnel()->searchMovies();
        return "Done!\n";
    }

    public function executeAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', false);
        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (get_class($request) != 'Zend\Console\Request'){
            throw new \RuntimeException('You can only use this action from a console!');
        }
        if (!$id) {
            throw new \RuntimeException('You must specify an id');
        }
        $result = $this->getServiceLocator()->get('mediamine-cron')->execute($id);
        return $result;
    }

    public function cronAction()
    {
        $request = $this->getRequest();
        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (get_class($request) != 'Zend\Console\Request'){
            throw new \RuntimeException('You can only use this action from a console!');
        }
        $result = $this->getServiceLocator()->get('mediamine-cron')->cron();
        return $result;
    }

    /**
     * @return \MediaMine\Service\InstallService
     */
    public function getInstallService()
    {
        if (null === $this->installService) {
            $this->installService = $this->getServiceLocator()->get('Install');
        }
        return $this->installService;
    }

    /**
     * @return \MediaMine\Service\ScanService
     */
    public function getScanService()
    {
        if (null === $this->scanService) {
            $this->scanService = $this->getServiceLocator()->get('File');
        }
        return $this->scanService;
    }

    /**
     * @return \MediaMine\Service\TunnelService
     */
    public function getTunnelService()
    {
        if (null === $this->tunnelService) {
            $this->tunnelService = $this->getServiceLocator()->get('Tunnel');
        }
        return $this->tunnelService;
    }
}
