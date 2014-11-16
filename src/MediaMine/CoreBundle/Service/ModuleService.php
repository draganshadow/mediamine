<?php
namespace MediaMine\CoreBundle\Service;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Service;
use MediaMine\CoreBundle\Entity\File\Extension;
use MediaMine\CoreBundle\Entity\Video\GroupType;
use MediaMine\CoreBundle\Entity\Video\StaffRole;
use MediaMine\CoreBundle\Entity\Video\Type;
use MediaMine\CoreBundle\Shared\BatchAware;
use MediaMine\CoreBundle\Shared\ContainerAware;
use Symfony\Component\DependencyInjection\Container;

use JMS\DiExtraBundle\Annotation\Inject;
/**
 * @Service("mediamine.service.module")
 */
class ModuleService extends AbstractService
{
    const BASE_SERVICE_ID = 'mediamine.service.module.%s.install';

    use BatchAware;
    use ContainerAware;

    /**
     * @Inject("%config%")
     */
    public $config;

    public function install($key)
    {
        $moduleRepository = $this->getRepository('System\Module');

        $queryParam = array(
            'key'     => $key,
            'hydrate' => Query::HYDRATE_ARRAY
        );
        $modules = $moduleRepository->findFullBy($queryParam);
        if (count($modules)) {
            return array('error' => 1);
        }

        $config = $this->config;
        if (!array_key_exists($key, $config['modules'])) {
            return array('error' => 2);
        }
        $moduleConfig = $config['modules'][$key];
        $moduleConfig['module']['installed'] = true;
        $moduleConfig['module']['enabled'] = true;
        /**
         * @var $module \MediaMine\CoreBundle\Entity\System\Module
         */
        $module = $moduleRepository->create($moduleConfig['module']);

//        if (array_key_exists('crons', $moduleConfig)) {
//            $cronRepository = $this->getEntityManager()->getRepository('Netsyos\Cron\Entity\Cron');
//            foreach ($moduleConfig['crons'] as $c) {
//                $cronRepository->create($c);
//            }
//        }
        if (array_key_exists('tunnels', $moduleConfig)) {
            $tunnelRepository = $this->getRepository('System\Tunnel');
            foreach ($moduleConfig['tunnels'] as $t) {
                $t['module'] = $module;
                $tunnelRepository->create($t);
            }
        }
        if (array_key_exists('settings', $moduleConfig)) {
            $settingRepository = $this->getRepository('System\Setting');
            foreach ($moduleConfig['settings'] as $g => $ops) {
                foreach ($ops as $k => $v) {
                    $settingRepository->create(array(
                        'module'   => $module,
                        'groupKey' => $g,
                        'key'      => $k,
                        'value'    => $v
                    ));
                }
            }
        }
        if (array_key_exists('filetypes', $moduleConfig)) {
            foreach ($moduleConfig['filetypes'] as $type => $extList) {
                foreach ($extList as $ext) {
                    $extension = new Extension();
                    $extension->name = $ext;
                    $extension->type = $type;
                    $this->getEntityManager()->persist($extension);
                }
            }
        }
        if (array_key_exists('staffroles', $moduleConfig)) {
            foreach ($moduleConfig['staffroles'] as $role) {
                $staffRole = new StaffRole();
                $staffRole->name = $role;
                $this->getEntityManager()->persist($staffRole);
            }
        }

        if (array_key_exists('videotypes', $moduleConfig)) {
            foreach ($moduleConfig['videotypes'] as $t) {
                $type = new Type();
                $type->name = $t;
                $this->getEntityManager()->persist($type);
            }
        }

        if (array_key_exists('grouptypes', $moduleConfig)) {
            foreach ($moduleConfig['grouptypes'] as $t) {
                $type = new GroupType();
                $type->name = $t;
                $this->getEntityManager()->persist($type);
            }
        }
        $this->batch(1);

        $moduleInstallservice = $this->getModuleInstallService(strtolower($module->getName()));
        if ($moduleInstallservice) {
            $moduleInstallservice->install();
        }

        return $module->getArrayCopy();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getModuleInstallService($name)
    {
        return $this->getContainer()->get(sprintf(self::BASE_SERVICE_ID, $name));
    }
}