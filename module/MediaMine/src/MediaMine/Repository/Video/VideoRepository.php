<?php
namespace MediaMine\Repository\Video;

use MediaMine\Repository\EntityRepository;
use MediaMine\Entity\Video\Video;

class VideoRepository extends EntityRepository
{
    public function createVideo($name, $summary, $image, $season, $episode, $type, $originalName, $year) {
        $video = new Video();
        $video->name = $name;
        $video->summary = $summary;
        if ($image) {
            $video->addImage($image);
        }
        $video->season = $season;
        $video->episode = $episode;
        $video->type = $type;
        $video->year = intval($year);

        $video->originalName = $originalName;
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