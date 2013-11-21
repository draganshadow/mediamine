<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace MediaMine\Controller;

use MediaMine\Parser\MovieParser;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use MediaMine\Initializer\EntityManagerAware;
use MediaMine\Initializer\ElasticsearchAware;
use MediaMine\Entity\User;


class DebugController extends AbstractActionController
{
    public function indexAction()
    {
        $parser = new MovieParser();
        $result = $parser->parse('/opt/data/02 - FILMS/MCM_DIVX_OK/1001 Pattes (1998)/movie.xml');
        echo '<pre>';
        var_dump($result);
        echo '</pre>';
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        return $viewModel;
    }
}
