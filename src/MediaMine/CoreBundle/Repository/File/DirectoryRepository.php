<?php
namespace MediaMine\CoreBundle\Repository\File;

use MediaMine\CoreBundle\Repository\AbstractRepository;
use Doctrine\ORM\Query;
use MediaMine\CoreBundle\Entity\File\Directory;

class DirectoryRepository extends AbstractRepository
{
    /**
     * @param $path
     * @param null $parentDirectory
     * @return Directory
     */
    public function create($fields = array()) {
        $directory = new Directory();
        $directory->exchangeArray($fields);
        $directory->status = 'new';

        $part = explode('/', $directory->path);
        $directory->name = $part[count($part)-1];

        $dateModified =  new \DateTime();
        $dateModified->setTimestamp(filemtime($directory->path));
        $directory->modificationDate = $dateModified;

        $this->getEntityManager()->persist($directory);
        return $directory;
    }

    public function findFullBy($options = array(), $singleResult = false, $queryOnly = false, $qb = false, $params = array()) {
        if (!$qb) {
            $qb = $this->createQueryBuilder($this->getAlias());
        }

        if (array_key_exists('root', $options)) {
            $params['parentDirectory'] = null;
        }

        return parent::findFullBy($options, $singleResult, $queryOnly, $qb, $params);
    }
}