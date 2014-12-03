<?php
namespace MediaMine\CoreBundle\Repository\File;

use MediaMine\CoreBundle\Repository\AbstractRepository;
use MediaMine\CoreBundle\Entity\File\File;

class FileRepository extends AbstractRepository
{
    /**
     * @param $name
     * @param null $parentDirectory
     * @return File
     */
    public function create($values, $cache = false, $context = false, $discriminator = false) {

        if (!array_key_exists('status', $values)) {
            $values['status'] = 'new';
        }
        $name = $values['name'];
        $parentDirectory = $values['parentDirectory'];
        $path = $parentDirectory->path . '/' . $name;
        $pathinfo = pathinfo($path);

        $values['name'] = $pathinfo['filename'];
        $values['directory'] = $parentDirectory;

        if (!array_key_exists('modificationDate', $values)) {
            $dateModified =  new \DateTime();
            $dateModified->setTimestamp(filemtime($values['path']));
            $values['modificationDate'] = $dateModified;
        }

        $values['extension'] = array_key_exists('extension', $pathinfo) ? $pathinfo['extension'] : '';
        $values['size'] = filesize($path);
        $values['status'] = 'new';
        $values['pathKey'] = md5($path);

        return parent::create($values, $cache, $context, $discriminator);
    }

    public function findFullBy($options = array(), $singleResult = false, $queryOnly = false, $qb = false, $params = array()) {
        if (!$qb) {
            $qb = $this->createQueryBuilder($this->getAlias());
        }

        if (array_key_exists('addDirectory', $options)) {
            $qb->leftJoin('File.directory', 'Directory');
            $qb->addSelect('Directory');
            unset($options['addDirectory']);
        }
        if (array_key_exists('type', $options)) {
            $qb->from('MediaMine\CoreBundle\Entity\File\Extension','Extension');
            $qb->where('File.extension = Extension.name');
            $qb->andWhere('Extension.type = :type');
            $params['type'] = $options['type'];
        }

        return parent::findFullBy($options, $singleResult, $queryOnly, $qb, $params);
    }
}