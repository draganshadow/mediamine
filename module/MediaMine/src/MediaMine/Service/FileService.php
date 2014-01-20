<?php
namespace MediaMine\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use MediaMine\Entity\File\Directory,
    MediaMine\Entity\File\File,
    Doctrine\ORM\Query;

class FileService extends AbstractService implements ServiceLocatorAwareInterface
{

    /**
     * @param $path
     * @param \MediaMine\Entity\Directory $parentDirectory
     */
    public function scan($path) {
        $path = realpath($path);
        $directory = $this->getRepository('File\Directory')->findFullBy(array('path' => $path));
        if (empty($directory)) {
            $this->add($path);
        } else {
            $this->update($directory[0]);
        }
        $this->flush(true);
    }

    /**
     * @param $path
     * @param \MediaMine\Entity\Directory $parentDirectory
     */
    protected function add($path, $parentDirectory = null) {
        $parentDirectory = $this->getRepository('File\Directory')->createDirectory($path, $parentDirectory);
        $this->flush();
        $content = $this->getFSContent($path);
        foreach ($content['directories'] as $entry) {
            $directoryPath = $path . '/' . $entry;
            $this->add($directoryPath, $parentDirectory);
        }
        foreach ($content['files'] as $entry) {
            $this->getRepository('File\File')
                ->createFile($entry, $parentDirectory);
            $this->flush();
        }
    }

    /**
     * @param $path
     * @param \MediaMine\Entity\Directory $parentDirectory
     */
    protected function update($parentDirectory) {
        $path = $parentDirectory->path;
        echo $path . PHP_EOL;
        $this->setModifiedDirectory($parentDirectory);

        $dBContent = $this->getDBContent($parentDirectory);
        $fSContent = $this->getFSContent($path);
        foreach ($fSContent['directories'] as $entry) {
            $directoryPath = $path . '/' . $entry;
            if (!array_key_exists($directoryPath, $dBContent['directories'])) {
                $this->add($directoryPath, $parentDirectory);
            } else {
                $this->update($dBContent['directories'][$directoryPath]);
                unset($dBContent['directories'][$directoryPath]);
            }
        }
        foreach ($dBContent['directories'] as $d) {
            $this->getEntityManager()->remove($d);
            $this->flush();
        }
        foreach ($fSContent['files'] as $entry) {
            $filePath = $path . '/' . $entry;
            if (!array_key_exists($filePath, $dBContent['files'])) {
                $this->getRepository('File\File')->createFile($entry, $parentDirectory);
                $this->flush();
            } else {
                $f = $dBContent['files'][$filePath];
                $dateModified =  new \DateTime();
                $dateModified->setTimestamp(filemtime($filePath));
                if ($dateModified != $f->dateModified) {
                    $f->dateModified = $dateModified;
                    $f->status = 'modified';
                    $this->getEntityManager()->persist($f);
                    $this->flush();
                }
                unset($dBContent['files'][$filePath]);
            }
        }
        foreach ($dBContent['files'] as $f) {
            $this->getEntityManager()->remove($f);
            $this->flush();
        }
    }

    protected function setModifiedDirectory($directory, $force=false) {
        if ($directory != null) {
            $dateModified =  new \DateTime();
            $dateModified->setTimestamp(filemtime($directory->path));
            if ($force || $dateModified != $directory->dateModified) {
                $directory->dateModified = $dateModified;
                $directory->status = 'modified';
                $this->getEntityManager()->persist($directory);
                $this->flush();
                $this->setModifiedDirectory($directory->parentDirectory, true);
            }
        }
    }

    protected function getFSContent($path) {
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

    protected function getDBContent($parentDirectory) {
        $directories = $this->getRepository('File\Directory')->findFullBy(array('parent' => $parentDirectory));
        $files = $this->getRepository('File\File')->findFullBy($parentDirectory);
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
}
