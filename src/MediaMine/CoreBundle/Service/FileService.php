<?php
namespace MediaMine\CoreBundle\Service;

use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use MediaMine\CoreBundle\Entity\File\File;
use MediaMine\CoreBundle\Entity\Video\Type;
use MediaMine\CoreBundle\Entity\File\Directory;
use MediaMine\CoreBundle\Shared\BatchAware;

/**
 * @Service("mediamine.service.file")
 */
class FileService extends AbstractService
{
    use BatchAware;

    /**
     * @Inject("%mediamine%")
     */
    public $mediamine;

    /**
     * @Inject("mediamine.service.task")
     * @var TaskService
     */
    public $taskService;

    /**
     * @param array $paths
     */
    public function scan($paths = array())
    {
        if (!count($paths)) {
            $this->getLogger()->info('File::scan: no path');
            $pathGroups = $this->getRepository('System\Setting')->findFullBy(array('groupKey' => 'paths', 'hydrate' => Query::HYDRATE_ARRAY));
            foreach ($pathGroups as $ps) {
                $this->getLogger()->info('File::scan: ' . $ps['groupKey'] . '.' . $ps['key']);
                $paths = array_merge($paths, $ps['value']);
            }
        }
        foreach ($paths as $path) {
            $this->getLogger()->info('File::scan: process ' . $path);
            $path = realpath($path);
            if (!empty($path)) {
                $this->getLogger()->info('File::scan: process realpath ' . $path);
                $directory = $this->getRepository('File\Directory')->findFullBy(array('path' => $path));
                if (empty($directory)) {
                    $this->add($path);
                } else {
                    $this->update($directory[0]);
                }
                $this->batch(1);
            }
        }
    }

    /**
     * @param $path
     * @param \MediaMine\CoreBundle\Entity\File\Directory $parentDirectory
     */
    protected function add($path, Directory $parentDirectory = null)
    {
        $this->getLogger()->info('File::scan: add ' . $path);
        $directory = array(
            'path'            => $path
        );
        if ($parentDirectory) {
//            $directory['parentDirectory'] = $this->entityManager->getReference('\MediaMine\CoreBundle\Entity\File\Directory', $parentDirectory->getId());
            $directory['parentDirectory'] = $parentDirectory;
        }
        $directory = $this->getRepository('File\Directory')->create($directory);
        $this->batch();
        $content = $this->getFSContent($path);
        foreach ($content['directories'] as $entry) {
            $directoryPath = $path . '/' . $entry;
            $this->add($directoryPath, $directory);
        }
        foreach ($content['files'] as $entry) {
            $f = $this->getRepository('File\File')
                ->create(array(
                    'name'            => $entry,
                    'parentDirectory' => $directory
                ));
            $this->checkLibrary($f);
            $this->batch();
        }
    }

    protected function getFSContent($path)
    {
        $content['directories'] = array();
        $content['files'] = array();
        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if (is_dir($path . '/' . $entry)) {
                    if ($entry != '..' && $entry != '.') {
                        $content['directories'][] = $entry;
                    }
                } else {
                    $content['files'][] = $entry;
                }
            }
            closedir($handle);
        }
        return $content;
    }

    protected function checkLibrary(File $f)
    {
        if (array_search($f->extension, $this->mediamine['filetypes']['video']) !== false) {
            $this->getLogger()->info('File::update: ' . $f->name . '.' . $f->extension);
            // if file not referenced with a movie create movie
            $vfs = $this->getRepository('Video\VideoFile')->findFullBy(array('file' => $f));
            if (!count($vfs)) {
                $this->getLogger()->info('File::update: add');
                $video = $this->getRepository('Video\Video')->create(array(
                    'name'         => $f->name,
                    'originalName' => $f->name,
                    'type'         => Type::UNKNOWN
                ));
                $vf = $this->getRepository('Video\VideoFile')->create(array('video' => $video, 'file' => $f));
                $this->getEntityManager()->persist($video);
            }
        }
    }

    /**
     * @param $path
     * @param \MediaMine\CoreBundle\Entity\File\Directory $parentDirectory
     */
    protected function update(Directory $parentDirectory = null)
    {
        $path = $parentDirectory->path;
        $this->getLogger()->info('File::update: update(' . $path . ')');
        $this->setModifiedDirectory($parentDirectory);

        $dBContent = $this->getDBContent($parentDirectory);
        $fSContent = $this->getFSContent($path);
        foreach ($fSContent['directories'] as $entry) {
            $directoryPath = $path . '/' . $entry;

            $this->getLogger()->info('File::update: ' . $directoryPath);
            if (!array_key_exists($directoryPath, $dBContent['directories'])) {
                $this->getLogger()->info('File::update:add ' . $directoryPath);
                $this->add($directoryPath, $parentDirectory);
            } else {
                $this->getLogger()->info('File::update:update ' . $directoryPath);
//                $this->update($dBContent['directories'][$directoryPath]);
                unset($dBContent['directories'][$directoryPath]);
                $this->getLogger()->info('File::update:isset ' . (array_key_exists($directoryPath,$dBContent['directories']) ? 'yes' : 'no'));
            }
        }
        foreach ($dBContent['directories'] as $k => $d) {
            $this->getLogger()->info('File::update:remove ' . $k);
            $this->getEntityManager()->remove($d);
            $this->batch();
        }
        foreach ($fSContent['files'] as $entry) {
            $filePath = $path . '/' . $entry;
            /**
             * @var $f \MediaMine\CoreBundle\Entity\File\File
             */
            $f;
            if (!array_key_exists($filePath, $dBContent['files'])) {
                $f = $this->getRepository('File\File')->create(array(
                    'name'            => $entry,
                    'parentDirectory' => $parentDirectory
                ));
                $this->batch();
            } else {
                $f = $dBContent['files'][$filePath];
                $dateModified = new \DateTime();
                $dateModified->setTimestamp(filemtime($filePath));
                if ($dateModified != $f->modificationDate) {
                    $f->modificationDate = $dateModified;
                    $f->status = 'modified';
                    $this->getEntityManager()->persist($f);
                    $this->batch();
                }
                unset($dBContent['files'][$filePath]);
            }
            $this->checkLibrary($f);
        }
        foreach ($dBContent['files'] as $f) {
            $this->getEntityManager()->remove($f);
            $this->batch();
        }
    }

    protected function setModifiedDirectory($directory, $force = false)
    {
        if ($directory != null) {
            $dateModified = new \DateTime();
            $dateModified->setTimestamp(filemtime($directory->path));
            if ($force || $dateModified != $directory->modificationDate) {
                $directory->modificationDate = $dateModified;
                $directory->status = 'modified';
                $this->getEntityManager()->persist($directory);
                $this->batch();
                $this->setModifiedDirectory($directory->parentDirectory, true);
            }
        }
    }

    protected function getDBContent($parentDirectory)
    {
        $directories = $this->getRepository('File\Directory')->findFullBy(array('parentDirectory' => $parentDirectory));
        $files = $this->getRepository('File\File')->findFullBy(array('directory' => $parentDirectory));
        $content['directories'] = array();
        $content['files'] = array();
        foreach ($directories as $d) {
            $content['directories'][$d->path] = $d;
        }
        foreach ($files as $f) {
            $content['files'][$f->getPath()] = $f;
        }
        return $content;
    }

    /**
     * @param $path
     */
    public function scanPath($path)
    {
        $path = realpath($path);
        $directory = $this->getRepository('File\Directory')->findFullBy(array('path' => $path));
        if (empty($directory)) {
            $this->add($path);
        } else {
            $this->update($directory[0]);
        }
        $this->batch(1);
    }
}
