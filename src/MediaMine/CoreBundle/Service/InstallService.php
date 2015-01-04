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

    /**
     * @Inject("%kernel.root_dir%")
     */
    public $rootDir;

    public function check() {
        $checks = [];
        try {
            $this->getEntityManager()->getConnection()->connect();
            $checks['database'] = true;
            $schemaManager = $this->getEntityManager()->getConnection()->getSchemaManager();
            if ($schemaManager->tablesExist(array('system_module')) == true) {
                $checks['schema'] = true;
            } else {
                $checks['schema'] = false;
            }
        } catch (\Exception $e) {
            $checks['database'] = false;
        }
        return $checks;
    }

    public function createdb() {
        $resets = [];
        $commands = [
            'php ' . $this->rootDir . '/console doctrine:schema:drop --force',
            'php ' . $this->rootDir . '/console doctrine:mongodb:schema:drop'
        ];
        $exec = '(' . implode(' && ', $commands) . ') >> "' . $this->rootDir . '/logs/reset.log' . '" 2>&1';
        shell_exec($exec);

        $commands = [
            'php ' . $this->rootDir . '/console doctrine:schema:create',
            'php ' . $this->rootDir . '/console doctrine:mongodb:schema:create'
        ];
        $exec = '(' . implode(' && ', $commands) . ') >> "' . $this->rootDir . '/logs/reset.log' . '" 2>&1';
        shell_exec($exec);
        return $resets;
    }

    public function createAdmin() {
        $resets = [];
        $commands = [
            'php ' . $this->rootDir . '/console fos:user:create --super-admin admin admin@mediamine admin'
        ];

        $exec = '(' . implode(' && ', $commands) . ') >> "' . $this->rootDir . '/logs/reset.log' . '" 2>&1';
        shell_exec($exec);
        $token = shell_exec('php ' . $this->rootDir . '/console mediamine:oauth:client:create  --redirect-uri="http://localhost:8080/" --grant-type="authorization_code" --grant-type="password" --grant-type="refresh_token" --grant-type="token" --grant-type="client_credentials"');
        file_put_contents($this->rootDir . '/../web/client.json', $token);
        return $resets;
    }

    public function coreInstall() {
        $this->moduleService->install('mediamine');
    }

    public function install()
    {

    }
}