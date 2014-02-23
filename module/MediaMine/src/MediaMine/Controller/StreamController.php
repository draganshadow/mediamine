<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace MediaMine\Controller;

use Netsyos\Common\Controller\AbstractController;
use Zend\View\Model\ViewModel;

use Doctrine\ORM\Query;

class StreamController extends AbstractController
{
    const DEFAULT_FORMAT = 'flv';
    const DEFAULT_SAVE_PATH = 'public/stream/';

    protected $mimeTypes = array(
        'flv' => 'video/x-flv',
        'mp4' => 'video/mp4',
    );

    public function indexAction()
    {
        ini_set('memory_limit', '2000M');
        set_time_limit(0);

        $pathKey = $this->params('pathKey', null);
        if (!$pathKey) {
            return $this->notFoundAction();
        }
        $files = $this->getRepository('File\File')->findFullBy(null, null, null, null, null, null, $pathKey);
        if (!count($files)){
            return $this->notFoundAction();
        } else {
            $file = $files[0];
            $filePath    = $file->getPath();
            $format  = $this->params('format', self::DEFAULT_FORMAT);

            if (!in_array($format, array_keys($this->mimeTypes))) {
                $format = self::DEFAULT_FORMAT;
            }
            $cacheName = $pathKey . '.' . $format;

            $height  = (int) $this->params('height', 0);
            if ($height) {
                $cacheName = $height . '-' . $cacheName;
            }
            $width   = (int) $this->params('width', 0);
            if ($width) {
                $cacheName = $width . '-' . $cacheName;
            }
            $bitrate   = $this->params('bitrate', null);
            if ($bitrate) {
                $cacheName = $bitrate . '-' . $cacheName;
            }

            $file= self::DEFAULT_SAVE_PATH . $cacheName;
            $tmpFile= self::DEFAULT_SAVE_PATH . 'tmp-' . $cacheName;

            header('Content-Type: ' . $this->mimeTypes[$format]);
            header('Content-Disposition: inline; filename=' . $cacheName);
            header('Content-Transfer-Encoding: binary');

            if (!file_exists($tmpFile) && !file_exists($file)) {
                if ($format == 'mp4') {
                    $ffmpeg = 'ffmpeg -ss 0 -i "' . $filePath . '" -async 1 -b ' . $bitrate . 'k -s 640x352 -ar 44100 -ac 2 -v 0 -vcodec mpeg4 -preset superfast -threads 0 ' . $tmpFile;
                } else {
                    $ffmpeg = 'ffmpeg -ss 0 -i "' . $filePath . '" -async 1 -b ' . $bitrate . 'k -s 640x352 -ar 44100 -ac 2 -v 0 -f ' . $format . ' -vcodec libx264 -preset superfast -threads 0 ' . $tmpFile;
                };
                $rename = ' mv ' . $tmpFile . ' ' . $file;
                shell_exec('(' . $ffmpeg . '&& ' . $rename . ') >/dev/null 2>/dev/null &');
            }

            $this->readOnTheFly($tmpFile);

            $viewModel = new ViewModel();
            $viewModel->setTerminal(true);
            return $viewModel;
        }
    }

    protected function readOnTheFly($file) {
        $lastpos = 0;
        while (true) {
            usleep(1000000); //3 s
            clearstatcache(false, $file);
            $len = filesize($file);
            if ($len <= $lastpos) {
                break;
            }
            elseif ($len > $lastpos) {
                $f = fopen($file, "rb");
                if ($f === false)
                    die();
                fseek($f, $lastpos);
                while (!feof($f)) {
                    $buffer = fread($f, 4096);
                    echo $buffer;
                }
                $lastpos = ftell($f);
                fclose($f);
            }
        }
    }
}
