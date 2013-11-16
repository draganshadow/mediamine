<?php
namespace MediaMine\Entity\File;

use Doctrine\ORM\EntityRepository;

class DirectoryRepository extends EntityRepository
{
    /**
     * @param $path
     * @param null $parentDirectory
     * @return Directory
     */
    public function createDirectory($path, $parentDirectory = null) {
        $directory = new Directory();
        $directory->path = $path;
        $directory->status = 'new';
        $part = explode('/', $path);
        $directory->name = $part[count($part)-1];
        $directory->parentDirectory = $parentDirectory;
        $dateModified =  new \DateTime();
        $dateModified->setTimestamp(filemtime($path));
        $directory->dateModified = $dateModified;
        $this->getEntityManager()->persist($directory);
        return $directory;
    }

    /**
     * @param MediaMine\Entity\File\Directory $parent
     * @param String $name
     * @return array
     */
    public function findFullBy($parent = null, $name = null, $path = null, $status = null) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $params = array();
        $qb->select('Directory')
            ->from('MediaMine\Entity\File\Directory','Directory');
        if ($parent != null) {
            $qb->andWhere('Directory.parentDirectory = :id');
            $params['id'] = $parent->id;
        }
        if ($name != null) {
            $qb->andWhere('Directory.name = :name');
            $params['name'] = $name;
        }
        if ($path != null) {
            $qb->andWhere('Directory.path = :path');
            $params['path'] = $path;
        }
        if ($status != null) {
            $qb->andwhere('Directory.status IN (:status)');
            $params['status'] = $status;
        }
        $directories = $qb->setParameters($params)->getQuery()->getResult();
        return $directories;
    }
}