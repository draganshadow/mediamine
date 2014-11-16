<?php
namespace MediaMine\CoreBundle\Controller;

use JMS\DiExtraBundle\Annotation\Inject;

use MediaMine\CoreBundle\Shared\LoggerAware;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AbstractController extends Controller
{
    use LoggerAware;
}
