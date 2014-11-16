<?php
namespace MediaMine\CoreBundle\Repository\Video;

use MediaMine\CoreBundle\Repository\AbstractRepository;
use Doctrine\ORM\Query;

class VideoRepository extends AbstractRepository
{
    public function findFullBy($options = array(), $singleResult = false, $queryOnly = false, $qb = false, $params = array()) {
        if (!$qb) {
            $qb = $this->createBaseQueryBuilder();
        }

        if (array_key_exists('file', $options)) {
            $qb->innerJoin('Video.files', 'VideoFile');
            $qb->innerJoin('VideoFile.file', 'File', 'WITH', 'File.id = :file');
            $params['file'] = $options['file'];
            unset($options['file']);
        }
        if (array_key_exists('addFile', $options)) {
            if (!array_key_exists('file', $options)) {
                $qb->leftJoin('Video.files', 'VideoFile');
                $qb->leftJoin('VideoFile.file', 'File');
            }
            $qb->addSelect('VideoFile');
            $qb->addSelect('File');
            unset($options['addFile']);
        }
        if (array_key_exists('addDirectory', $options)) {
            $qb->leftJoin('File.directory', 'Directory');
            $qb->addSelect('Directory');
            unset($options['addDirectory']);
        }
        if (array_key_exists('addStaffs', $options)) {
            $qb->leftJoin('Video.staffs', 'Staffs');
            $qb->leftJoin('Staffs.person', 'Person');
            $qb->addSelect('Staffs');
            $qb->addSelect('Person');
            unset($options['addStaffs']);
        }
        return parent::findFullBy($options, $singleResult, $queryOnly, $qb, $params);
    }
}