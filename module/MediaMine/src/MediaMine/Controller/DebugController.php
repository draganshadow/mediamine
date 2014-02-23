<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace MediaMine\Controller;

use Netsyos\Common\Controller\AbstractController;
use Zend\View\Model\ViewModel;

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