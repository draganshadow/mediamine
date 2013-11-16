<?php
namespace MediaMine\Entity\Video;

use Doctrine\ORM\EntityRepository;

class VideoFileRepository extends EntityRepository
{
    public function createVideoFile($video, $file) {
        $videoFile = new VideoFile();
        $videoFile->video = $video;
        $videoFile->file = $file;
        $this->getEntityManager()->persist($videoFile);
        return $videoFile;
    }
}