<?php
namespace MediaMine\CoreBundle\Repository\File;

use MediaMine\CoreBundle\Repository\AbstractRepository;
use Doctrine\ORM\Query;
use MediaMine\CoreBundle\Entity\File\Directory;
use JMS\DiExtraBundle\Annotation as DI;

class DirectoryRepository extends AbstractRepository
{
    /**
     * @param $path
     * @param null $parentDirectory
     * @return Directory
     */
    public function create($values, $cache = false, $context = false, $discriminator = false) {
        if (!array_key_exists('status', $values)) {
            $values['status'] = 'new';
        }
        if (!array_key_exists('name', $values)) {
            $part = explode('/', $values['path']);
            $values['name'] = $part[count($part)-1];
        }

        if (!array_key_exists('modificationDate', $values)) {
            $dateModified =  new \DateTime();
            $dateModified->setTimestamp(filemtime($values['path']));
            $values['modificationDate'] = $dateModified;
        }

        return parent::create($values, $cache, $context, $discriminator);
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