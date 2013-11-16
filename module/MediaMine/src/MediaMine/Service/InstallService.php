<?php
namespace MediaMine\Service;

use MediaMine\Entity\Video\StaffRole;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use MediaMine\Entity\File\Extension,
    Doctrine\ORM\Query;

class InstallService extends AbstractService implements ServiceLocatorAwareInterface
{
    protected $defaultExtensions = array(
        'video' => array(
            'avi', 'mkv', 'mov', 'mpg', 'mpeg', '3gp', 'asf', 'mp2', 'vob', 'flv', 'divx', 'bin', 'mp4', 'h264', 'webm'
        , 'wmv', 'xvid'
        )
    );

    protected $defaultRoles = array(
        'actor', 'writer', 'director', 'guest'
    );
    
    /**
     * @param $path
     * @param \MediaMine\Entity\Directory $parentDirectory
     */
    public function install() {
        foreach ($this->defaultExtensions as $type => $extList) {
            foreach ($extList as $ext) {
                $extension = new Extension();
                $extension->name = $ext;
                $extension->type = $type;
                $this->getEntityManager()->persist($extension);
            }
        }
        foreach ($this->defaultRoles as $role) {
            $staffRole = new StaffRole();
            $staffRole->name = $role;
            $this->getEntityManager()->persist($staffRole);
        }
        $this->flush(true);
    }
}