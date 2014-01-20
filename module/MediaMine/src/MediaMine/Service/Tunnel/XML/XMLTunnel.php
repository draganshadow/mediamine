<?php
namespace MediaMine\Service\Tunnel\XML;

use MediaMine\Entity\Video\Staff;
use MediaMine\Service\Tunnel\XML\Parser\MovieParser;
use MediaMine\Service\Tunnel\AbstractTunnel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use MediaMine\Entity\File\Directory,
    MediaMine\Entity\File\File,
    MediaMine\Entity\Video\Group,
    MediaMine\Entity\Video\Season,
    MediaMine\Entity\Video\Video,
    MediaMine\Entity\Video\VideoFile,
    Doctrine\ORM\Query,
    MediaMine\Service\Tunnel\XML\Parser\SerieParser,
    MediaMine\Service\Tunnel\XML\Parser\EpisodeParser;

class XMLTunnel extends AbstractTunnel implements ServiceLocatorAwareInterface
{


    protected $serieParser;

    protected $episodeParser;

    protected $movieParser;

    public function getAbilities() {
        return array(
            'Movie' => array(),
            'Serie' => array(),
            'Episode' => array()
        );
    }

    public function searchSeries() {
        $this->getEs()->getIndex('mediamine')->delete();
        $this->getRoles();
        $series = $this->getRepository('File\File')->findFullBy(null, 'series', 'xml', null);
        foreach ($series as $serie) {
            $serieMeta = $this->getSerieParser()->parse($serie->getPath());
            $group = $this->createGroup($serie);
            if ($group) {
                // Search Seasons
                $subDirectories = $this->getRepository('File\Directory')->findFullBy(array('parent' => $serie->directory, 'status' => $this->statusList));
                foreach ($subDirectories as $seasonDirectory) {
                    $season = $this->createSeason($group, $seasonDirectory);
                    $metadata = $this->getRepository('File\Directory')->findFullBy(array('parent' => $seasonDirectory, 'name' => 'metadata'));
                    $videoFiles = $this->getRepository('File\File')->findFullBy($seasonDirectory, null, null, 'video');

                    if (count($metadata)) {
                        $metadata = $metadata[0];
                        foreach ($videoFiles as $videoFile) {
                            $video = $this->createEpisode($season, $videoFile, $metadata, $serieMeta);
                        }
                        $this->markAdded($metadata);
                    }
                    $this->markAdded($seasonDirectory);
                }
            }
            $this->markAdded($serie);
            $this->markAdded($serie->directory);
        }
        $this->flush(true);
    }

    public function searchMovies() {
        $this->getTypes();
        $this->getRoles();
        $movieXmls = $this->getRepository('File\File')->findFullBy(null, 'movie', 'xml', null);
        foreach ($movieXmls as $movieXml) {
            $movieMeta = $this->getMovieParser()->parse($movieXml->getPath());
            $movieMeta['image'] = 'folder.jpg';
            $videoFiles = $this->getRepository('File\File')->findFullBy($movieXml->directory, null, null, 'video');
            if (count($videoFiles)) {
                $videoFile = $videoFiles[0];
                $video = $this->createVideo($movieMeta, $videoFile, $movieXml->directory, null, $this->typesList['movie']);
                $this->markAdded($videoFile);
            }
            $this->markAdded($movieXml);
        }
        $this->flush(true);
    }

    protected function getSerieParser() {
        if (null === $this->serieParser) {
            $this->serieParser = new SerieParser();
        }
        return $this->serieParser;
    }

    protected function getEpisodeParser() {
        if (null === $this->episodeParser) {
            $this->episodeParser = new EpisodeParser();
        }
        return $this->episodeParser;
    }

    protected function getMovieParser() {
        if (null === $this->movieParser) {
            $this->movieParser = new MovieParser();
        }
        return $this->movieParser;
    }

}
