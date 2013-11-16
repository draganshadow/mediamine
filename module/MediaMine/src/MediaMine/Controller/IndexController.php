<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace MediaMine\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use MediaMine\Initializer\EntityManagerAware;
use MediaMine\Initializer\ElasticsearchAware;
use MediaMine\Entity\User;


class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        /*
         * Example: Use a custom finder
         * $result = $this->em->getRepository('MediaMine\Entity\User')->myCustomFinder();
         */

        /* Example: Add new user to Elasticsearch and search for it
         * $type = $this->es->getIndex('user')->getType('user');
         * $user = new User();
         * $user->setId(1);
         * $user->setUsername('testuser');
         * $user->setEmail('testuser@example.com');
         * $user->setPassword('test123');

         * $doc = new \Elastica\Document(
         *     $user->getId(),
         *     array(
         *         'id' => $user->getId(),
         *         'username' => $user->getUsername(),
         *         'email' => $user->getEmail()
         *     )
         * );
         * $this->es->getIndex('user')->getType('user')->addDocument($doc);
         * $this->es->getIndex('user')->refresh();
         *
         * $result = $this->es->getIndex('user')->getType('user')->search('*');
         */

        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        return $viewModel;
    }
    public function groupAction()
    {

        $id = (int)$this->getEvent()->getRouteMatch()->getParam('id');

        $serie = $this->getEm()->find('MediaMine\Entity\Video\Group', $id);

        $qb = $this->getEm()->createQueryBuilder();
        $qb->select('Season', 'g', 'Image')
            ->from('MediaMine\Entity\Video\Season','Season')
            ->innerJoin('Season.group', 'g', 'WITH', 'g.id = :id')
            ->join('Season.image', 'Image')
            ->setParameter('id', $id)
            ->orderBy('Season.name', 'ASC');
        $resultSet = $qb->getQuery()->getResult();

        return new ViewModel(array(
            'debug' => '',
            'serie' => $serie,
            'seasons' => $resultSet
        ));
    }

    public function seasonAction()
    {

        $id = (int)$this->getEvent()->getRouteMatch()->getParam('id');

        $season = $this->getEm()->find('MediaMine\Entity\Video\Season', $id);

        $qb = $this->getEm()->createQueryBuilder();
        $qb->select('Video', 'Season', 'g', 'files', 'f')
            ->from('MediaMine\Entity\Video\Video','Video')
            ->innerJoin('Video.season', 'Season', 'WITH', 'Season.id = :id')
            ->join('Season.group', 'g')
            ->join('Video.files', 'files')
            ->join('files.file', 'f')
            ->setParameter('id', $id)
            ->orderBy('Video.episode', 'ASC');
        $resultSet = $qb->getQuery()->getResult();

//        $eps = $resultSet[0];
//var_dump($eps->files);
        return new ViewModel(array(
            'debug' => '',
            'serie' => $season->group,
            'season' => $season,
            'episodes' => $resultSet
        ));
    }

    public function viewAction()
    {
        $id = (int)$this->getEvent()->getRouteMatch()->getParam('id');

        return new ViewModel(array(
            'debug' => '',
            'id' => $id
        ));
    }
}
