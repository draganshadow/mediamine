<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace MediaMine\Controller;
use Heartsentwined\Cron\Service\Cron;
use Heartsentwined\Cron\Service\Registry;
use Zend\Debug\Debug;
use MediaMine\Parser\MovieParser;
use Zend\Log\Logger;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Dom\Query;
use MediaMine\Initializer\EntityManagerAware;
use MediaMine\Initializer\ElasticsearchAware;
use MediaMine\Entity\User;


class DebugController extends AbstractController
{
    public function indexAction()
    {
        $viewModel = new ViewModel();
        $alloHelper = new \AlloHelper();
        $result = $alloHelper->search('Bruce Willis', 1, 10, false, array('person','movie'));
        \Kint::dump($result);
        return $viewModel;
    }
}