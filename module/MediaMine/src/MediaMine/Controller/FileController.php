<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace MediaMine\Controller;

use MediaMine\Entity\Directory;
use MediaMine\Controller\AbstractController;
use MediaMine\Initializer\EntityManagerAware;
use Zend\View\Model\ViewModel;

use MediaMine\Entity\File\File,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Query;

class FileController extends AbstractController implements EntityManagerAware
{

    public function indexAction()
    {
        $id = (int)$this->getEvent()->getRouteMatch()->getParam('id');
        //$image = $this->getRepository('File\File')->findFullBy(null, null, null, null, null, $id);
        $image = $this->getEm()->find('MediaMine\Entity\File\File', $id);
        //var_dump($image->getPath());
        header('Content-Type: image/jpg');
        readfile($image->getPath());
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function streamAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        ini_set('memory_limit', '2000M');
        set_time_limit(0);
        $id = (int)$this->getEvent()->getRouteMatch()->getParam('id');
        $video = $this->getEm()->find('MediaMine\Entity\File\File', $id);

        $infile=$video->getPath();
        $tmpName=md5($infile);
        $type = 'flv';
        $file='/tmp/' . $tmpName . '.' . $type;
        if ($type == 'mp4') {
            header('Content-Type: video/mp4');
            header('Content-type: video/mpeg');
        } else {
            header('Content-Type: video/x-flv');
        }
        header('Content-Disposition: inline; filename=' . $tmpName);
//        header('Content-disposition: inline');
//        header('Content-Disposition: attachment; filename="' . $tmpName . '";' );
        header('Content-Transfer-Encoding: binary');
//        header('Accept-Ranges: bytes');
//        header ( "Pragma: public" );
//        header ( "Expires: " . gmdate('D, d M Y H:i:s \G\M\T', time() + 3600 * 24));
//        header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        //header("Cache-Control: post-check=0, pre-check=0", false);
        //header("Cache-Control: no-store, no-cache, must-revalidate");
        //header('Content-Length: ' . filesize($video->getPath()));
        //header ( "Content-Type: application/force-download" );
        //header ( "Content-Type: application/octet-stream" );
        //header ( "Content-Type: application/download" );

        /**
        Valeur a envoy� a FFMPEG :
         -ss : Temps de d�part
         -i : Fichier d'entr�
         -b : bitrate
         -s : resolution
         -ar : frequence d'echantillonage son
         -ac : nombre de canaux : stereo/mono/surround...
         -v
         -f :
         -vcodec : codec a utiliser pour la video
         -preset : parametre gerant les temps d'encodage/qualite
         -threads : nb de cpu par processus
        **/
        if (!file_exists($file)) {
            $cmd = 'ffmpeg -ss 0 -i "' . $infile . '" -async 1 -b 500k -s 640x352 -ar 44100 -ac 2 -v 0 -f ' . $type . ' -vcodec libx264 -preset superfast -threads 0 ' . $file . ' >/dev/null 2>/dev/null &';
//            $cmd = 'ffmpeg -ss 0 -i "' . $infile . '" -async 1 -b 1000k -s 640x352 -ar 44100 -ac 2 -v 0 -vcodec mpeg4 -preset superfast -threads 0 ' . $file . ' >/dev/null 2>/dev/null &';
            shell_exec($cmd);
        }
        //*
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
        //*/
//        readfile($video->getPath());
//        exit(0);
//        echo $cmd;
        return $viewModel;
    }

    function rangeDownload($file)
    {
        $fp = @fopen($file, 'rb');

        $size   = filesize($file); // File size
        $length = $size;           // Content length
        $start  = 0;               // Start byte
        $end    = $size - 1;       // End byte
        // Now that we've gotten so far without errors we send the accept range header
        /* At the moment we only support single ranges.
         * Multiple ranges requires some more work to ensure it works correctly
         * and comply with the spesifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
         *
         * Multirange support annouces itself with:
         * header('Accept-Ranges: bytes');
         *
         * Multirange content must be sent with multipart/byteranges mediatype,
         * (mediatype = mimetype)
         * as well as a boundry header to indicate the various chunks of data.
         */
        header("Accept-Ranges: 0-$length");
        // header('Accept-Ranges: bytes');
        // multipart/byteranges
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
        if (isset($_SERVER['HTTP_RANGE']))
        {
            $c_start = $start;
            $c_end   = $end;
            // Extract the range string
            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            // Make sure the client hasn't sent us a multibyte range
            if (strpos($range, ',') !== false)
            {
                // (?) Shoud this be issued here, or should the first
                // range be used? Or should the header be ignored and
                // we output the whole content?
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                // (?) Echo some info to the client?
                exit;
            }
            // If the range starts with an '-' we start from the beginning
            // If not, we forward the file pointer
            // And make sure to get the end byte if spesified
            if ($range{0} == '-')
            {
                // The n-number of the last bytes is requested
                $c_start = $size - substr($range, 1);
            }
            else
            {
                $range  = explode('-', $range);
                $c_start = $range[0];
                $c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
            }
            /* Check the range and make sure it's treated according to the specs.
             * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
             */
            // End bytes can not be larger than $end.
            $c_end = ($c_end > $end) ? $end : $c_end;
            // Validate the requested range and return an error if it's not correct.
            if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size)
            {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                // (?) Echo some info to the client?
                exit;
            }
            $start  = $c_start;
            $end    = $c_end;
            $length = $end - $start + 1; // Calculate new content length
            fseek($fp, $start);
            header('HTTP/1.1 206 Partial Content');
        }
        // Notify the client the byte range we'll be outputting
        header("Content-Range: bytes $start-$end/$size");
        header("Content-Length: $length");

        // Start buffered download
        $buffer = 1024 * 8;
        while(!feof($fp) && ($p = ftell($fp)) <= $end)
        {
            if ($p + $buffer > $end)
            {
                // In case we're only outputtin a chunk, make sure we don't
                // read past the length
                $buffer = $end - $p + 1;
            }
            set_time_limit(0); // Reset time limit for big files
            echo fread($fp, $buffer);
            flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
        }

        fclose($fp);
    }
}
