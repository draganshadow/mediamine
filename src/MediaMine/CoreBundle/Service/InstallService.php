<?php
namespace MediaMine\CoreBundle\Service;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;

/**
 * @Service("mediamine.service.module.mediamine.install")
 */
class InstallService extends AbstractService
{

    /**
     * @Inject("mediamine.service.module")
     * @var \MediaMine\CoreBundle\Service\ModuleService
     */
    public $moduleService;

    public function check() {
        $checks = [];
        try {
            $this->getEntityManager()->getConnection()->connect();
            $checks['database'] = true;
        } catch (\Exception $e) {
            $checks['database'] = false;
        }
        return $checks;
    }

    public function reset() {
        $resets = [];
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->getEntityManager());
        $tool->dropDatabase();
        $meta = $this->getEntityManager()->getMetadataFactory()->getAllMetadata();
        $tool->createSchema($meta);
        return $resets;
    }

    public function coreInstall() {
        $this->moduleService->install('mediamine');
    }

    public function install()
    {

    }
}