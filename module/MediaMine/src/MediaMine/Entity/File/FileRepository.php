<?php
namespace MediaMine\Entity\File;

use Doctrine\ORM\EntityRepository;

class FileRepository extends EntityRepository
{
    /**
     * @param $name
     * @param null $parentDirectory
     * @return File
     */
    public function createFile($name, $parentDirectory = null) {
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
        $this->getEntityManager()->persist($file);
        return $file;
    }

    public function findFullBy($directory = null, $name = null, $extension = null, $type = null, $status = null, $id = null) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $params = array();
        $qb->select('File')
            ->from('MediaMine\Entity\File\File','File');
        if ($directory != null) {
            $qb->innerJoin('File.directory', 'Directory', 'WITH', 'Directory.id = :directory');
            $params['directory'] = $directory->id;
        }
        if ($name != null) {
            $qb->where('File.name = :name');
            $params['name'] = $name;
        }
        if ($type != null) {
            $qb->from('MediaMine\Entity\File\Extension','Extension');
            //$qb->innerJoin('File.extension', 'Extension', 'WITH', 'Extension.type = :type');
            $qb->where('File.extension = Extension.name');
            $qb->andWhere('Extension.type = :type');
            $params['type'] = $type;
        }
        if ($extension != null) {
            $qb->andwhere('File.extension = :extension');
            $params['extension'] = $extension;
        }
        if ($status != null) {
            $qb->andwhere('File.status IN (:status)');
            $params['status'] = $status;
        }
        if ($id != null) {
            $qb->andwhere('File.id IN (:id)');
            $params['id'] = $id;
        }
        $files = $qb->setParameters($params)->getQuery()->getResult();
        return $files;
    }
}