<?php
namespace MediaMine\CoreBundle\Service;

use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use MediaMine\CoreBundle\Shared\LoggerAware;

use JMS\DiExtraBundle\Annotation\Inject;

abstract class AbstractService
{
    use EntitityManagerAware;
    use LoggerAware;
}
