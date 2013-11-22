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
     * @var MediaMine\Service\ScanService
     */
    protected $scanService;

    /**
     * @var MediaMine\Service\InstallService
     */
    protected $installService;

    /**
     * @var MediaMine\Service\XMLSearchService
     */
    protected $xmlSearchService;

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
        $this->getXMLSearchService()->searchSeries();
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
        $this->getXMLSearchService()->searchMovies();
        return "Done!\n";
    }

    public function testAction()
    {
        $request = $this->getRequest();
        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (get_class($request) != 'Zend\Console\Request'){
            throw new \RuntimeException('You can only use this action from a console!');
        }
        $seriesParser = new SerieParser();
        $serie = $seriesParser->parse('/opt/data/03 - SERIES TV/Sherlock/series.xml');
        var_dump($serie);
        return "Done!\n";
    }

    /**
     * @return MediaMine\Service\InstallService
     */
    public function getInstallService()
    {
        if (null === $this->installService) {
            $this->installService = $this->getServiceLocator()->get('install-service');
        }
        return $this->installService;
    }

    /**
     * @return MediaMine\Service\ScanService
     */
    public function getScanService()
    {
        if (null === $this->scanService) {
            $this->scanService = $this->getServiceLocator()->get('scan-service');
        }
        return $this->scanService;
    }

    /**
     * @return MediaMine\Service\XMLSearchService
     */
    public function getXMLSearchService()
    {
        if (null === $this->xmlSearchService) {
            $this->xmlSearchService = $this->getServiceLocator()->get('xml-meta-search-service');
        }
        return $this->xmlSearchService;
    }
}
