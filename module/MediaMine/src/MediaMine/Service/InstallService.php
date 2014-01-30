<?php
namespace MediaMine\Service;

use MediaMine\Entity\Video\StaffRole;
use MediaMine\Entity\Video\Type;
use MediaMine\Entity\File\Extension,
    Doctrine\ORM\Query;

class InstallService extends AbstractService
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

    protected $defaultTypes = array(
        'series', 'movie'
    );

    protected $defaultCrons = array(
//        array('key' => 'scanFiles',
//            'frequency' => '0 3 * * *',
//            'service' => 'File',
//            'callback' => 'scan',
//            'arguments' => array()),
        array('key' => 'searchSeries',
            'frequency' => '0 */12 * * *',
            'service' => 'XMLTunnel',
            'callback' => 'searchSeries',
            'arguments' => array()),
        array('key' => 'searchMovies',
            'frequency' => '0 */12 * * *',
            'service' => 'XMLTunnel',
            'callback' => 'searchMovies',
            'arguments' => array()),
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
        foreach ($this->defaultTypes as $t) {
            $type = new Type();
            $type->name = $t;
            $this->getEntityManager()->persist($type);
        }
        foreach ($this->defaultCrons as $c) {
            $this->getRepository('System\Cron')->create($c);
        }
        $this->flush(true);
    }
}