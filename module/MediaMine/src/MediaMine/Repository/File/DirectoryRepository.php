<?php
namespace MediaMine\Repository\File;

use Netsyos\Common\Repository\EntityRepository;
use Doctrine\ORM\Query;
use MediaMine\Entity\File\Directory;

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
    public function findFullBy($options) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $params = array();
        $qb->select('Directory')
            ->from('MediaMine\Entity\File\Directory','Directory');

        if (array_key_exists('parent', $options)) {
            $qb->andWhere('Directory.parentDirectory = :parent');
            if (is_object($options['parent'])) {
                $params['parent'] = $options['parent']->id;
            } else {
                $params['parent'] = $options['parent'];
            }
        } else if (array_key_exists('root', $options)) {
            $qb->andWhere('Directory.parentDirectory IS NULL');
        }
        if (array_key_exists('name', $options)) {
            $qb->andWhere('Directory.name = :name');
            $params['name'] = $options['name'];
        }
        if (array_key_exists('path', $options)) {
            $qb->andWhere('Directory.path = :path');
            $params['path'] = $options['path'];
        }
        if (array_key_exists('status', $options)) {
            $qb->andwhere('Directory.status IN (:status)');
            $params['status'] = $options['status'];
        }
        $order = 'ASC';
        if (array_key_exists('order', $options)) {
            if ($options['status'] == 'DESC') {
                $order = $options['status'];
            }
        }
        $qb->orderBy('Directory.name', $order);
        $hydrate = array_key_exists('hydrate', $options) ? $options['hydrate'] : Query::HYDRATE_OBJECT;
        $directories = $qb->setParameters($params)->getQuery()->getResult($hydrate);
        return $directories;
    }
}