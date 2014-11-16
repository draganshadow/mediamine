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
    public function create($fields = array()) {
        $name = $fields['name'];
        $parentDirectory = $fields['parentDirectory'];
        $path = $parentDirectory->path . '/' . $name;
        $pathinfo = pathinfo($path);

        $file = new File();
        $file->name = $pathinfo['filename'];
        $file->directory = $parentDirectory;

        $dateModified =  new \DateTime();
        $dateModified->setTimestamp(filemtime($path));
        $file->modificationDate = $dateModified;

        $file->extension = array_key_exists('extension', $pathinfo) ? $pathinfo['extension'] : '';
        $file->size = filesize($path);
        $file->status = 'new';
        $file->pathKey = md5($path);
        $this->getEntityManager()->persist($file);
        return $file;
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