<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\EntityRepository;

class VideoRepository extends EntityRepository
{
    public function createVideo($name, $summary, $image, $season, $episode) {
        $video = new Video();
        $video->name = $name;
        $video->summary = $summary;
        if ($image) {
            $video->addImage($image);
        }
        $video->season = $season;
        $video->episode = $episode;
        $this->getEntityManager()->persist($video);
        return $video;
    }

    public function findFullBy($file = null, $name = null) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $params = array();
        $qb->select('Video')
            ->from('MediaMine\Entity\Video\Video','Video');
        if ($file != null) {
            $qb->innerJoin('Video.files', 'VideoFile', 'WITH', 'VideoFile.file = :file');
            $params['file'] = $file->id;
        }
        if ($name != null) {
            $qb->where('Video.name = :name');
            $params['name'] = $name;
        }

        $videos = $qb->setParameters($params)->getQuery()->getResult();
        return $videos;
    }
}