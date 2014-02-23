<?php
namespace MediaMine\Repository\Video;

use Netsyos\Common\Repository\EntityRepository;
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