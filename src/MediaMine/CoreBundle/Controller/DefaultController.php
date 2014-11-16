<?php

namespace MediaMine\CoreBundle\Controller;

use JMS\DiExtraBundle\Annotation\Inject;
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
        var_dump($tt);
//        $this->get('old_sound_rabbit_mq.upload_picture_producer')->publish(serialize($msg));
        $this->logger->debug("TEST TEST");
        return $this->render('MediaMineCoreBundle:Default:index.html.twig', array('name' => $name));
    }
}
