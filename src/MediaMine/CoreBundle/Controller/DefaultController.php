<?php

namespace MediaMine\CoreBundle\Controller;

use JMS\DiExtraBundle\Annotation\Inject;
use MediaMine\CoreBundle\Entity\AbstractEntity;
use MediaMine\CoreBundle\Service\FileService;

class DefaultController extends AbstractController
{
    /**
     * @Inject("mediamine.service.module")
     * @var FileService
     */
    public $test;

    /**
     * @Inject("snc_redis.default")
     * @var \Redis
     */
    public $redis;

    public function indexAction($name)
    {
        $msg = array('user_id' => 1235, 'image_path' => '/path/to/new/pic.png');
        $this->redis->set('test','blablabla');
        $tt = $this->redis->get('test');
//        $this->get('old_sound_rabbit_mq.upload_picture_producer')->publish(serialize($msg));
        $this->logger->debug("TEST TEST");

        /**
         * @var $finder \FOS\ElasticaBundle\Finder\TransformedFinder
         */
        $finder = $this->container->get('fos_elastica.finder.search');

//        \Kint::dump($finder);
// Returns a mixed array of any objects mapped
        $paginator = $finder->findPaginated($name);
        $paginator->setMaxPerPage(5);
        $paginator->setCurrentPage(1);
        $result = [];
        foreach ($paginator->getCurrentPageResults() as $e) {
            /**
             * @var $e AbstractEntity
             */
            $result[] = $e->getArrayCopy(1);
        }
        echo '<pre>';
        \Doctrine\Common\Util\Debug::dump($result);
        echo '</pre>';
//        echo '</hr>';
//        $paginator->setCurrentPage(2);
//        echo '<pre>';
//        \Doctrine\Common\Util\Debug::dump($paginator->getCurrentPageResults());
//        echo '</pre>';
        return $this->render('MediaMineCoreBundle:Default:index.html.twig', array('name' => $name));
    }
}
