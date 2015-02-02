<?php
namespace MediaMine\CoreBundle\Controller;

use Alchemy\BinaryDriver\Configuration;
use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use MediaMine\CoreBundle\Entity\File\File;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadController extends AbstractController
{
    use EntitityManagerAware;

    const DEFAULT_SAVE_PATH = 'web/zip/';
    /**
     * @Inject("%kernel.root_dir%")
     */
    public $rootDir;

    /**
     * @Inject("%config%")
     */
    public $config;

    public function indexAction(Request $request, $pathKey)
    {
        $response = new StreamedResponse();
        $response->setStatusCode(200);
        if (!$pathKey) {
            throw new NotFoundHttpException();
        }
        $files = $this->getRepository('File\File')->findFullBy(array('pathKey' => $pathKey));
        if (!count($files)) {
            throw new NotFoundHttpException();
        } else {
            /**
             * @var $file File
             */
            $file = $files[0];
            $filePath = $file->getPath();
            $self = $this;
            $len = filesize($filePath);
            $response->headers->set('Content-Type', 'application/octet-stream');
            $response->headers->set('Content-Transfer-Encoding', 'binary');
            $response->headers->set('Content-Length', $len);

            $contentDisposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file->getPathKey() . '.' . $file->getExtension());
            $response->headers->set('Content-Disposition', $contentDisposition);
            $response->sendHeaders();
            $response->setCallback(function () use ($self, $filePath) {
                $self->readFile($filePath);
            });
            return $response;
        }
    }

    public function zipAction(Request $request, $id)
    {
        $response = new StreamedResponse();
        $response->setStatusCode(200);
        if (!$id) {
            throw new NotFoundHttpException();
        }
        $files = $this->getRepository('File\File')->findFullBy(array('directory' => $id));
        if (!count($files)) {
            throw new NotFoundHttpException();
        } else {
            /**
             * @var $files File[]
             */

            $self = $this;
            $zip = new \ZipArchive();
            $filePath = $this->rootDir . '/../' . self::DEFAULT_SAVE_PATH . $id . '.zip';

            if ($zip->open($filePath, \ZipArchive::CREATE)!==TRUE) {
                throw new NotFoundHttpException();
            }
            foreach ($files as $f) {
                $zip->addFile($f->getPath(), $f->getName() . '.' . $f->getExtension());
            }
            $zip->close();
            $len = filesize($filePath);
            $response->headers->set('Content-Type', 'application/octet-stream');
            $response->headers->set('Content-Transfer-Encoding', 'binary');
            $response->headers->set('Content-Length', $len);

            $contentDisposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $id . '.zip');
            $response->headers->set('Content-Disposition', $contentDisposition);
            $response->setCallback(function () use ($self, $filePath) {
                $self->readFile($filePath);
            });
            return $response;
        }
    }

    protected function readFile($file, $start = 0, $end = -1)
    {
        $lastpos = $start;
        while (true) {
            sleep(1); // 1s
            clearstatcache(false, $file);
            $len = filesize($file);
            if ($len <= $lastpos) {
                break;
            } elseif ($len > $lastpos) {
                $f = fopen($file, "rb");
                if ($f === false)
                    die();
                fseek($f, $lastpos);
                while (!feof($f)) {
                    $buffer = fread($f, 4096);
                    echo $buffer;
                    flush();
                }
                $lastpos = ftell($f);
                fclose($f);
            }
        }
    }
}
