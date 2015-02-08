<?php
namespace MediaMine\CoreBundle\Repository\Video;

use JMS\DiExtraBundle\Annotation as DI;
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

        if (array_key_exists('minYear', $options)) {
            $qb->andWhere($this->getField('year') . ' >= :minYear');
            $params['minYear'] = $options['minYear'];
            unset($options['minYear']);
        }

        if (array_key_exists('maxYear', $options)) {
            $qb->andWhere($this->getField('year') . ' <= :maxYear');
            $params['maxYear'] = $options['maxYear'];
            unset($options['maxYear']);
        }

        if (array_key_exists('genres', $options)) {
            $qb->innerJoin('Video.genres', 'Genre', 'WITH', 'Genre.id IN (:genres)');
            $params['genres'] = $options['genres'];
            unset($options['genres']);
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
        if (array_key_exists('person', $options)) {
            $qb->innerJoin('Video.staffs', 'Staffs');
            $qb->innerJoin('Staffs.person', 'Person', 'WITH', 'Person.id = :person');
            $params['person'] = $options['person'];
            unset($options['person']);
        }
        if (array_key_exists('addStaffs', $options)) {
            if (!array_key_exists('person', $options)) {
                $qb->leftJoin('Video.staffs', 'Staffs');
                $qb->leftJoin('Staffs.person', 'Person');
            }
            $qb->addSelect('Staffs');
            $qb->addSelect('Person');
            unset($options['addStaffs']);
        }
        return parent::findFullBy($options, $singleResult, $queryOnly, $qb, $params);
    }
}