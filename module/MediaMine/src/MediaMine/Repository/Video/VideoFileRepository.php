<?php
namespace MediaMine\Repository\Video;

use Doctrine\ORM\EntityRepository;
use MediaMine\Entity\Video\VideoFile;

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